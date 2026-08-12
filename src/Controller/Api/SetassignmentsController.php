<?php
declare(strict_types=1);

namespace App\Controller\Api;

/**
 * Setassignments API (teacher-created assignments/CBTs) — CRUD plus the
 * assignment's questions and student submissions.
 * GET /api/v1/setassignments/{id}/questions
 * GET /api/v1/setassignments/{id}/submissions
 */
class SetassignmentsController extends CrudController
{
    public function questions($id = null)
    {
        return $this->relatedList($id, 'Questions');
    }

    public function submissions($id = null)
    {
        return $this->relatedList($id, 'Assignments', ['Students']);
    }
}
