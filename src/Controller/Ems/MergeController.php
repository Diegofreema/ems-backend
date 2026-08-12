<?php
declare(strict_types=1);

namespace App\Controller\Ems;

use App\Ems\Dedup;
use App\Ems\Messages;
use Cake\Datasource\EntityInterface;
use Cake\Http\Response;

/**
 * The register merge (document.md §3.15). Merging people is a registrar's job,
 * done with approval — tighter than the ordinary edit rights the whole office
 * has. A merge requires a reason, previews every record that moves, reattaches
 * each of the retired person's records to the survivor (which keeps its own id,
 * admission number and class), then removes the retired row — its identifier is
 * never reused. The audit event names both ids and the retired person's full
 * identity, so a privileged correction could reverse the merge by hand.
 */
class MergeController extends AppController
{
    /** studentId → its category counts, memoized within one request. */
    private array $countsCache = [];

    /** GET /merge/candidates — all-pairs scoring, score ≥ 0.6, strongest first. */
    public function candidates(): Response
    {
        $params = $this->pageParams();
        $people = $this->students();
        $pairs = [];
        $n = count($people);
        for ($i = 0; $i < $n; $i++) {
            for ($j = $i + 1; $j < $n; $j++) {
                $scored = Dedup::scorePair($this->partyForScoreA($people[$i]), $this->partyForScore($people[$j]));
                if ($scored === null || $scored['score'] < 0.6) {
                    continue;
                }
                $pairs[] = [
                    'id' => (string)$people[$i]->id . '__' . (string)$people[$j]->id,
                    'score' => $scored['score'],
                    'reasons' => $scored['reasons'],
                    'a' => $this->party($people[$i]),
                    'b' => $this->party($people[$j]),
                    'aRecords' => $this->totalOf($this->countsFor((string)$people[$i]->id)),
                    'bRecords' => $this->totalOf($this->countsFor((string)$people[$j]->id)),
                ];
            }
        }
        usort($pairs, fn ($x, $y) => $y['score'] <=> $x['score']);

        $total = count($pairs);
        $page = array_slice($pairs, ($params['page'] - 1) * $params['pageSize'], $params['pageSize']);

        return $this->paginated($page, $total, $params['page'], $params['pageSize']);
    }

    /** GET /merge/preview — read-only counts of what a merge would move. */
    public function preview(): Response
    {
        $survivorId = (string)$this->request->getQuery('survivorId', '');
        $retiredId = (string)$this->request->getQuery('retiredId', '');
        if ($survivorId === $retiredId) {
            $this->fail(422, Messages::MERGE_SAME_RECORD);
        }
        $survivor = $this->findStudent($survivorId);
        $retired = $this->findStudent($retiredId);
        $moving = $this->countsFor($retiredId);

        return $this->json([
            'survivor' => $this->party($survivor),
            'retired' => $this->party($retired),
            'moving' => $moving,
            'movingTotal' => $this->totalOf($moving),
            'survivorExisting' => $this->countsFor($survivorId),
        ]);
    }

    /** POST /merge — { survivorId, retiredId, reason }. One transaction. */
    public function merge(): Response
    {
        $body = $this->body();
        $survivorId = (string)($body['survivorId'] ?? '');
        $retiredId = (string)($body['retiredId'] ?? '');
        if ($survivorId === $retiredId) {
            $this->fail(422, Messages::MERGE_SAME_RECORD);
        }
        $reason = trim((string)($body['reason'] ?? ''));
        if ($reason === '') {
            $this->fail(422, Messages::MERGE_REASON_REQUIRED);
        }
        $survivor = $this->findStudent($survivorId);
        $retired = $this->findStudent($retiredId);
        $moving = $this->countsFor($retiredId);
        $movingTotal = $this->totalOf($moving);

        $students = $this->fetchTable('EmsStudents');
        $students->getConnection()->transactional(function () use ($survivorId, $retiredId, $students) {
            $sid = $this->viewer->schoolId;
            // Reattach every studentId-keyed record to the survivor.
            $this->fetchTable('EmsGuardians')->updateAll(
                ['student_id' => $survivorId, 'is_primary' => false],
                ['school_id' => $sid, 'student_id' => $retiredId]
            );
            foreach (['EmsAttendanceRecords', 'EmsAcademicTermRecords', 'EmsEnrolments',
                'EmsExamGrades', 'EmsAssessmentScores', 'EmsInvoices', 'EmsPayments',
                'EmsRefunds', 'EmsFeeAwards'] as $table) {
                $this->fetchTable($table)->updateAll(
                    ['student_id' => $survivorId],
                    ['school_id' => $sid, 'student_id' => $retiredId]
                );
            }
            $this->fetchTable('EmsDocuments')->updateAll(
                ['owner_id' => $survivorId],
                ['school_id' => $sid, 'owner' => 'student', 'owner_id' => $retiredId]
            );
            $this->fetchTable('EmsAdmissionApplications')->updateAll(
                ['student_id' => $survivorId],
                ['school_id' => $sid, 'student_id' => $retiredId]
            );
            $this->repointUsers($survivorId, $retiredId);
            $this->syncPrimaryContact($survivorId);
            // Retire the duplicate — removed, not tombstoned.
            $students->deleteAll(['school_id' => $sid, 'id' => $retiredId]);
        });

        $this->audit()->log(
            $this->viewer,
            'student.merged',
            'student',
            $survivorId,
            sprintf(
                'Merged duplicate %s into %s (%s %s). The surviving record kept its identifier %s; '
                . 'the retired identifier %s is never reused. Records moved: %d in total. '
                . 'Retired record was %s %s, born %s, %s, class %s, status %s, guardian %s %s, enrolled %s.',
                (string)$retired->admission_number,
                (string)$survivor->admission_number,
                (string)$survivor->first_name,
                (string)$survivor->last_name,
                $survivorId,
                $retiredId,
                $movingTotal,
                (string)$retired->first_name,
                (string)$retired->last_name,
                \App\Ems\Serializer\Wire::date($retired->date_of_birth),
                (string)$retired->gender,
                (string)$retired->class_group,
                (string)$retired->status,
                (string)$retired->guardian_name,
                (string)$retired->guardian_phone,
                \App\Ems\Serializer\Wire::date($retired->enrolled_on)
            ),
            $reason
        );

        return $this->json([
            'survivorId' => $survivorId,
            'retiredId' => $retiredId,
            'moving' => $moving,
            'movingTotal' => $movingTotal,
        ]);
    }

    // --- helpers -------------------------------------------------------------

    /** @return array<int, \Cake\Datasource\EntityInterface> */
    private function students(): array
    {
        return $this->tenant()->query('EmsStudents')
            ->orderByAsc('created')->orderByAsc('id')->all()->toList();
    }

    private function findStudent(string $id): EntityInterface
    {
        return $this->findOr404('EmsStudents', $id, Messages::MERGE_NOT_ON_REGISTER);
    }

    private function party(EntityInterface $s): array
    {
        return [
            'id' => (string)$s->id,
            'admissionNumber' => (string)$s->admission_number,
            'name' => trim((string)$s->first_name . ' ' . (string)$s->last_name),
            'classGroup' => (string)$s->class_group,
            'status' => (string)$s->status,
            'dateOfBirth' => \App\Ems\Serializer\Wire::date($s->date_of_birth),
            'guardianName' => (string)$s->guardian_name,
            'guardianPhone' => (string)$s->guardian_phone,
            'enrolledOn' => \App\Ems\Serializer\Wire::date($s->enrolled_on),
        ];
    }

    /** @return array{firstName:string,lastName:string,dateOfBirth:string,guardianPhone:string} */
    private function partyForScoreA(EntityInterface $s): array
    {
        return [
            'firstName' => (string)$s->first_name,
            'lastName' => (string)$s->last_name,
            'dateOfBirth' => \App\Ems\Serializer\Wire::date($s->date_of_birth),
            'guardianPhone' => (string)$s->guardian_phone,
        ];
    }

    /** @return array{firstName:string,lastName:string,dateOfBirth:string,guardianPhone:string,admissionNumber:string} */
    private function partyForScore(EntityInterface $s): array
    {
        return $this->partyForScoreA($s) + ['admissionNumber' => (string)$s->admission_number];
    }

    /** @return array<string, int> */
    private function countsFor(string $studentId): array
    {
        if (isset($this->countsCache[$studentId])) {
            return $this->countsCache[$studentId];
        }
        $count = fn (string $table, array $extra = []) => $this->tenant()->query($table)
            ->where(['student_id' => $studentId] + $extra)->count();

        return $this->countsCache[$studentId] = [
            'guardians' => $count('EmsGuardians'),
            'attendance' => $count('EmsAttendanceRecords'),
            'academics' => $count('EmsAcademicTermRecords'),
            'enrolments' => $count('EmsEnrolments'),
            'examGrades' => $count('EmsExamGrades'),
            'assessmentScores' => $count('EmsAssessmentScores'),
            'invoices' => $count('EmsInvoices'),
            'payments' => $count('EmsPayments'),
            'refunds' => $count('EmsRefunds'),
            'feeAwards' => $count('EmsFeeAwards'),
            'documents' => $this->tenant()->query('EmsDocuments')
                ->where(['owner' => 'student', 'owner_id' => $studentId])->count(),
        ];
    }

    /** @param array<string, int> $counts */
    private function totalOf(array $counts): int
    {
        return array_sum($counts);
    }

    /** Repoint any portal accounts tied to the retired person. */
    private function repointUsers(string $survivorId, string $retiredId): void
    {
        $users = $this->fetchTable('EmsUsers');
        foreach ($this->tenant()->query('EmsUsers')->all() as $user) {
            $kind = (string)($user->link_kind ?? '');
            if ($kind === 'student' && (string)$user->link_student_id === $retiredId) {
                $user->link_student_id = $survivorId;
                $users->saveOrFail($user);
            } elseif ($kind === 'parent') {
                $ids = $user->link_student_ids;
                if (is_string($ids)) {
                    $ids = json_decode($ids, true);
                }
                $ids = is_array($ids) ? array_map('strval', $ids) : [];
                if (in_array($retiredId, $ids, true)) {
                    $mapped = array_map(fn ($id) => $id === $retiredId ? $survivorId : $id, $ids);
                    $user->link_student_ids = array_values(array_unique($mapped));
                    $users->saveOrFail($user);
                }
            }
        }
    }

    /** Re-derive the survivor's denormalized primary contact (§3.10). */
    private function syncPrimaryContact(string $studentId): void
    {
        $primary = $this->tenant()->query('EmsGuardians')
            ->where(['student_id' => $studentId, 'is_primary' => true])
            ->first();
        $students = $this->fetchTable('EmsStudents');
        $student = $this->tenant()->query('EmsStudents')
            ->where(['id' => $studentId])->first();
        if ($student === null) {
            return;
        }
        $student->guardian_name = $primary === null ? '' : trim((string)$primary->first_name . ' ' . (string)$primary->last_name);
        $student->guardian_phone = $primary === null ? '' : (string)$primary->phone;
        $students->saveOrFail($student);
    }
}
