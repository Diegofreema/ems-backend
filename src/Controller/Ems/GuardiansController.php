<?php
declare(strict_types=1);

namespace App\Controller\Ems;

use App\Ems\Messages;
use App\Ems\Serializer\StudentSerializer;
use Cake\Datasource\EntityInterface;
use Cake\Http\Response;
use Cake\I18n\FrozenTime;

/**
 * Guardian mutations addressed by guardian id (document.md §3.10). Every
 * mutation ends with a primary-contact re-sync onto the student row.
 */
class GuardiansController extends AppController
{
    /**
     * PUT /guardians/{id} — GuardianInput; an explicit primary demotes the
     * others.
     */
    public function edit(string $id): Response
    {
        $guardians = $this->fetchTable('EmsGuardians');
        $guardian = $this->findGuardian($id);
        $body = $this->body();
        $isPrimary = (bool)($body['isPrimary'] ?? $guardian->is_primary);

        $guardians->getConnection()->transactional(function () use ($guardians, $guardian, $body, $isPrimary) {
            $guardian->first_name = trim((string)($body['firstName'] ?? $guardian->first_name));
            $guardian->last_name = trim((string)($body['lastName'] ?? $guardian->last_name));
            $guardian->relationship = (string)($body['relationship'] ?? $guardian->relationship);
            $guardian->phone = trim((string)($body['phone'] ?? $guardian->phone));
            $guardian->email = trim((string)($body['email'] ?? $guardian->email));
            $guardian->occupation = trim((string)($body['occupation'] ?? $guardian->occupation));
            $guardian->is_primary = $isPrimary;
            $guardians->saveOrFail($guardian);

            if ($isPrimary) {
                $this->demoteOtherPrimaries((string)$guardian->student_id, (string)$guardian->id);
            }
            $this->syncPrimaryContact((string)$guardian->student_id);
        });

        return $this->json(StudentSerializer::guardian($guardian));
    }

    /**
     * POST /guardians/{id}/primary — makes this the single primary.
     */
    public function setPrimary(string $id): Response
    {
        $guardians = $this->fetchTable('EmsGuardians');
        $guardian = $this->findGuardian($id);

        $guardians->getConnection()->transactional(function () use ($guardians, $guardian) {
            $guardian->is_primary = true;
            $guardians->saveOrFail($guardian);
            $this->demoteOtherPrimaries((string)$guardian->student_id, (string)$guardian->id);
            $this->syncPrimaryContact((string)$guardian->student_id);
        });

        return $this->json(StudentSerializer::guardian($guardian));
    }

    /**
     * DELETE /guardians/{id} — idempotent. A guardian is student-related data,
     * so the row is soft-archived (stamped archived_at), never destroyed; the
     * table's default scope then hides it from every read. Removing the primary
     * promotes the first remaining (active) sibling (§3.10). A repeat delete of
     * an already-archived guardian finds nothing and is a no-op 204.
     */
    public function delete(string $id): Response
    {
        $guardians = $this->fetchTable('EmsGuardians');
        $guardian = $this->tenant()->query('EmsGuardians')
            ->where(['id' => $id])
            ->first();
        if ($guardian === null) {
            return $this->json(null, 204);
        }

        $guardians->getConnection()->transactional(function () use ($guardians, $guardian) {
            $studentId = (string)$guardian->student_id;
            $wasPrimary = (bool)$guardian->is_primary;
            $guardian->archived_at = FrozenTime::now();
            $guardian->is_primary = false;
            $guardians->saveOrFail($guardian);

            if ($wasPrimary) {
                // The default scope already excludes the just-archived row, so
                // this is the first still-active sibling.
                $next = $this->tenant()->query('EmsGuardians')
                    ->where(['student_id' => $studentId])
                    ->orderByAsc('first_name')
                    ->first();
                if ($next !== null) {
                    $next->is_primary = true;
                    $guardians->saveOrFail($next);
                }
            }
            $this->syncPrimaryContact($studentId);
        });

        return $this->json(null, 204);
    }

    private function findGuardian(string $id): EntityInterface
    {
        return $this->findOr404('EmsGuardians', $id, Messages::GUARDIAN_NOT_FOUND);
    }

    private function demoteOtherPrimaries(string $studentId, ?string $keepId): void
    {
        $conditions = [
            'school_id' => $this->viewer->schoolId,
            'student_id' => $studentId,
            'is_primary' => true,
        ];
        if ($keepId !== null) {
            $conditions['id !='] = $keepId;
        }
        $this->fetchTable('EmsGuardians')->updateAll(['is_primary' => false], $conditions);
    }

    private function syncPrimaryContact(string $studentId): void
    {
        $primary = $this->tenant()->query('EmsGuardians')
            ->where([
                'student_id' => $studentId,
                'is_primary' => true,
            ])
            ->first();

        $students = $this->fetchTable('EmsStudents');
        $student = $this->tenant()->query('EmsStudents')
            ->where(['id' => $studentId])
            ->first();
        if ($student === null) {
            return;
        }
        $student->guardian_name = $primary === null
            ? ''
            : trim($primary->first_name . ' ' . $primary->last_name);
        $student->guardian_phone = $primary === null ? '' : (string)$primary->phone;
        $students->saveOrFail($student);
    }
}
