<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller\Ems;

use Cake\Utility\Text;

/**
 * The exam status lifecycle transition that the Edit screen used to imply but
 * the API never carried: POST /exams/{id}/start-grading. Before this endpoint
 * an exam created in draft/scheduled could never reach grading, so results
 * could never be released. These tests pin the one forward step and its guards:
 *
 *  - draft   → grading (200, row actually flips, audit written)
 *  - scheduled → grading (200)
 *  - grading  → 409 (already grading — nothing to start)
 *  - published → 409 (published leaves only via the audited reopen)
 *  - a teacher is refused by the Policy gate (403), like release/reopen.
 */
class ExamLifecycleTest extends EmsIntegrationTestCase
{
    /** FK-safe clear order: exam rows and the audit trail, then the base tenant. */
    protected const CLEANUP_TABLES = [
        'ems_audit_events',
        'ems_exams',
        'ems_users',
        'ems_schools',
    ];

    private function seedExam(string $status): string
    {
        $id = Text::uuid();
        $this->insertRow('ems_exams', [
            'id' => $id,
            'school_id' => $this->schoolId,
            'title' => 'Third Term Examination',
            'session' => '2025/2026',
            'term' => 'Third',
            'status' => $status,
            'start_date' => '2026-05-01',
            'end_date' => '2026-05-20',
            'ca_max' => 40,
            'exam_max' => 60,
        ]);

        return $id;
    }

    public function testDraftStartsGrading(): void
    {
        $this->authAsAdmin();
        $id = $this->seedExam('draft');

        $this->post($this->schoolPath('/exams/' . $id . '/start-grading'), []);

        $this->assertResponseOk();
        $this->assertSame('grading', $this->responseJson()['status']);
        // The row actually moved — not just the serialized echo.
        $this->assertTrue($this->rowExists('ems_exams', ['id' => $id, 'status' => 'grading']));
        // Audited, exactly as release/reopen are.
        $this->assertTrue($this->rowExists('ems_audit_events', [
            'entity_id' => $id,
            'action' => 'exam.grading_started',
        ]));
    }

    public function testScheduledStartsGrading(): void
    {
        $this->authAsAdmin();
        $id = $this->seedExam('scheduled');

        $this->post($this->schoolPath('/exams/' . $id . '/start-grading'), []);

        $this->assertResponseOk();
        $this->assertSame('grading', $this->responseJson()['status']);
    }

    public function testAlreadyGradingIsRejected(): void
    {
        $this->authAsAdmin();
        $id = $this->seedExam('grading');

        $this->post($this->schoolPath('/exams/' . $id . '/start-grading'), []);

        $this->assertResponseCode(409);
        $this->assertSame(
            'Only a draft or scheduled examination can start grading.',
            $this->responseJson()['message']
        );
    }

    public function testPublishedIsRejected(): void
    {
        $this->authAsAdmin();
        $id = $this->seedExam('published');

        $this->post($this->schoolPath('/exams/' . $id . '/start-grading'), []);

        $this->assertResponseCode(409);
        // The row stayed published — a status edit can never undo a release.
        $this->assertTrue($this->rowExists('ems_exams', ['id' => $id, 'status' => 'published']));
    }

    public function testTeacherCannotStartGrading(): void
    {
        $this->authAs('teacher', Text::uuid(), 'Tayo Teacher');
        $id = $this->seedExam('draft');

        $this->post($this->schoolPath('/exams/' . $id . '/start-grading'), []);

        $this->assertResponseCode(403);
        $this->assertSame(
            'A teacher cannot start grading — that needs the academic lead.',
            $this->responseJson()['message']
        );
        // The gate fired before the action: the exam is untouched.
        $this->assertTrue($this->rowExists('ems_exams', ['id' => $id, 'status' => 'draft']));
    }
}
