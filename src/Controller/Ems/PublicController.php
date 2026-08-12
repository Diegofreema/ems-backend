<?php
declare(strict_types=1);

namespace App\Controller\Ems;

use App\Ems\DocumentWriter;
use App\Ems\Messages;
use App\Ems\Serializer\AdmissionSerializer;
use Cake\Datasource\EntityInterface;
use Cake\Http\Response;
use Cake\I18n\FrozenDate;

/**
 * The unauthenticated admissions surface (document.md §3.6).
 *
 * `intake` and `apply` are public actions — beforeFilter never runs the
 * membership check for them, so there is no viewer. The public apply page has
 * already resolved the tenant, so the submit is keyed by schoolId; the intake
 * page is keyed by slug because the visitor has no session at all.
 */
class PublicController extends AppController
{
    protected array $publicActions = ['intake', 'apply'];

    /**
     * GET /public/apply/{slug} — everything the public form needs; null (200)
     * for an unknown slug, never a 404.
     */
    public function intake(string $slug): Response
    {
        $school = $this->fetchTable('EmsSchools')->find()
            ->where(['slug' => $slug])->first();
        if ($school === null) {
            return $this->json(null);
        }

        $cycle = $this->openCycleFor((string)$school->id);
        $levels = $this->fetchTable('EmsClassGroups')->find()
            ->select(['level'])
            ->distinct(['level'])
            ->where(['school_id' => $school->id])
            ->all()
            ->extract('level')
            ->filter()
            ->toList();

        $schoolOut = [
            'id' => (string)$school->id,
            'name' => (string)$school->name,
            'shortName' => (string)$school->short_name,
            'motto' => (string)$school->motto,
        ];
        if ($school->logo !== null && $school->logo !== '') {
            $schoolOut['logo'] = (string)$school->logo;
        }

        return $this->json([
            'school' => $schoolOut,
            'cycle' => $cycle === null ? null : AdmissionSerializer::cycle($cycle),
            'levels' => array_values($levels),
        ]);
    }

    /**
     * POST /public/schools/{schoolId}/apply — a public submission. Files are
     * validated before the row is inserted; the whole thing is one transaction
     * (§3.6). No audit event — the review trail is the record.
     */
    public function apply(string $schoolId): Response
    {
        $this->rateLimit('apply', 10, 900);
        $cycle = $this->openCycleFor($schoolId);
        if ($cycle === null) {
            $this->fail(422, Messages::ADMISSIONS_CLOSED);
        }

        $body = $this->body();
        $firstName = trim((string)($body['firstName'] ?? ''));
        $lastName = trim((string)($body['lastName'] ?? ''));
        if ($firstName === '' || $lastName === '') {
            $this->fail(422, Messages::APPLICANT_NAME_REQUIRED);
        }

        $uploads = is_array($body['documents'] ?? null) ? $body['documents'] : [];
        // Validate every file up front — nothing is written if one is bad.
        foreach ($uploads as $upload) {
            $this->storage()->assertAcceptable(
                (string)($upload['contentType'] ?? ''),
                (int)($upload['sizeBytes'] ?? 0)
            );
            if (trim((string)($upload['name'] ?? '')) === '') {
                $this->fail(422, Messages::DOCUMENT_NAME_REQUIRED);
            }
        }

        $guardian = is_array($body['guardian'] ?? null) ? $body['guardian'] : [];
        $applications = $this->fetchTable('EmsAdmissionApplications');
        $writer = new DocumentWriter($this->getTableLocator(), $this->storage());

        $application = $applications->getConnection()->transactional(
            function () use ($applications, $writer, $schoolId, $cycle, $body, $firstName, $lastName, $guardian, $uploads) {
                $number = $this->sequences()->next($schoolId, 'application', $this->maxApplicationSuffix($schoolId));
                $application = $applications->saveOrFail($applications->newEntity([
                    'school_id' => $schoolId,
                    'cycle_id' => (string)$cycle->id,
                    'application_number' => sprintf('APP-%04d', $number),
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'date_of_birth' => (string)($body['dateOfBirth'] ?? '') !== '' ? (string)$body['dateOfBirth'] : null,
                    'gender' => (string)($body['gender'] ?? 'other'),
                    'desired_level' => trim((string)($body['desiredLevel'] ?? '')),
                    'previous_school' => trim((string)($body['previousSchool'] ?? '')),
                    'guardian' => AdmissionSerializer::guardian($guardian),
                    'note' => trim((string)($body['note'] ?? '')),
                    'submitted_on' => FrozenDate::today(),
                    'status' => 'submitted',
                ], ['validate' => false]));

                $sender = trim(sprintf('%s %s', $guardian['firstName'] ?? '', $guardian['lastName'] ?? ''));
                foreach ($uploads as $upload) {
                    $writer->store($schoolId, 'application', (string)$application->id, [
                        'name' => (string)($upload['name'] ?? ''),
                        'type' => (string)($upload['type'] ?? 'other'),
                        'contentType' => (string)($upload['contentType'] ?? ''),
                        'sizeBytes' => (int)($upload['sizeBytes'] ?? 0),
                        'body' => (string)($upload['body'] ?? ''),
                    ], $sender !== '' ? $sender : 'Applicant');
                }

                return $application;
            }
        );

        return $this->json(AdmissionSerializer::application($application), 201);
    }

    private function openCycleFor(string $schoolId): ?EntityInterface
    {
        $today = FrozenDate::today()->format('Y-m-d');

        return $this->fetchTable('EmsAdmissionCycles')->find()
            ->where([
                'school_id' => $schoolId,
                'status' => 'open',
                'opens_on <=' => $today,
                'closes_on >=' => $today,
            ])
            ->orderByDesc('opens_on')
            ->first();
    }

    private function maxApplicationSuffix(string $schoolId): int
    {
        $row = $this->fetchTable('EmsAdmissionApplications')->find()
            ->select(['m' => 'MAX(CAST(SUBSTRING_INDEX(application_number, "-", -1) AS UNSIGNED))'])
            ->where(['school_id' => $schoolId])
            ->first();

        return (int)($row->m ?? 0);
    }
}
