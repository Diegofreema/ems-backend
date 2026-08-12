<?php
declare(strict_types=1);

namespace App\Controller\Ems;

use App\Ems\Messages;
use Cake\Http\Response;

/**
 * Transcripts — the cumulative, cross-year academic record (document.md §3.5).
 * Fully read-only. Assembled by App\Ems\Academics from exactly the two sources
 * the contract names: released examinations (graded on the scale pinned at
 * release) and approved academic-history summaries. A grading examination
 * contributes nothing until it is released.
 */
class TranscriptsController extends AppController
{
    /**
     * GET /students/{studentId}/transcript — family-scoped.
     */
    public function show(string $studentId): Response
    {
        $this->scope()->assertStudentAccess($studentId);

        $school = $this->fetchTable('EmsSchools')->find()
            ->where(['id' => $this->viewer->schoolId])->first();
        if ($school === null) {
            $this->fail(404, Messages::SCHOOL_NOT_FOUND);
        }
        $student = $this->tenant()->query('EmsStudents')
            ->where(['id' => $studentId])->first();
        if ($student === null) {
            $this->fail(404, Messages::STUDENT_NOT_FOUND);
        }

        return $this->json($this->academicsEngine()->transcript($school, $student));
    }
}
