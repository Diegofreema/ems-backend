<?php
declare(strict_types=1);

namespace App\Controller\Api;

/**
 * Exams API — CRUD + the exam's question list.
 * GET /api/v1/exams/{id}/questions
 */
class ExamsController extends CrudController
{
    protected array $searchFields = ['examname'];

    public function questions($id = null)
    {
        return $this->relatedList($id, 'Examquestions');
    }
}
