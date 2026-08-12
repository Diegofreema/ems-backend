<?php
declare(strict_types=1);

namespace App\Controller\Api;

/**
 * Quizzes API — CRUD + the quiz's question list.
 * GET /api/v1/quizzes/{id}/questions
 */
class QuizzesController extends CrudController
{
    public function questions($id = null)
    {
        return $this->relatedList($id, 'Quizquestions');
    }
}
