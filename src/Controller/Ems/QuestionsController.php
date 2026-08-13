<?php
declare(strict_types=1);

namespace App\Controller\Ems;

use App\Ems\Messages;
use App\Ems\Serializer\ExamSerializer;
use App\Ems\SubjectCatalog;
use Cake\Datasource\EntityInterface;
use Cake\Http\Response;

/**
 * The question bank (document.md §3.4) — a reusable store of assessment items
 * teachers draw on when composing exam papers. No audit events, no referential
 * check on delete: exam papers drop dangling ids at read time.
 */
class QuestionsController extends AppController
{
    /**
     * GET /questions — paginated; query matches text/topic/subject, filters by
     * subject/type/difficulty, sorted subject → topic.
     */
    public function index(): Response
    {
        $params = $this->pageParams();
        $query = trim((string)$this->request->getQuery('query', ''));
        $subject = (string)$this->request->getQuery('subject', 'all');
        $type = (string)$this->request->getQuery('type', 'all');
        $difficulty = (string)$this->request->getQuery('difficulty', 'all');

        $q = $this->tenant()->query('EmsQuestions');

        if ($subject !== 'all' && $subject !== '') {
            $q->where(['EmsQuestions.subject_id' => $this->subjectIdOrNone($subject)]);
        }
        if ($type !== 'all' && $type !== '') {
            $q->where(['type' => $type]);
        }
        if ($difficulty !== 'all' && $difficulty !== '') {
            $q->where(['difficulty' => $difficulty]);
        }
        if ($query !== '') {
            $like = '%' . $query . '%';
            $or = [
                'text LIKE' => $like,
                'topic LIKE' => $like,
            ];
            // A search term may be a subject name — match against the
            // catalogue and pull in every question filed under a hit.
            $subjectIds = [];
            foreach (SubjectCatalog::all($this->viewer->schoolId) as $sid => $name) {
                if (mb_stripos($name, $query) !== false) {
                    $subjectIds[] = $sid;
                }
            }
            if ($subjectIds !== []) {
                $or['EmsQuestions.subject_id IN'] = $subjectIds;
            }
            $q->where(['OR' => $or]);
        }

        $total = $q->count();
        $q->leftJoin(['Subj' => 'ems_subjects'], ['Subj.id = EmsQuestions.subject_id']);
        $rows = $q->orderByAsc('Subj.name')->orderByAsc('EmsQuestions.topic')
            ->limit($params['pageSize'])
            ->offset(($params['page'] - 1) * $params['pageSize'])
            ->all();

        return $this->paginated(
            array_map([ExamSerializer::class, 'question'], $rows->toList()),
            $total,
            $params['page'],
            $params['pageSize'],
        );
    }

    /**
     * GET /questions/subjects — distinct subjects, sorted.
     */
    public function subjects(): Response
    {
        // The school's subject catalogue (active entries), the dropdown source.
        $values = $this->tenant()->query('EmsSubjects')
            ->where(['active' => true])
            ->orderByAsc('name')
            ->all()
            ->extract('name')
            ->toList();
        $values = array_values(array_map('strval', $values));

        return $this->json($values);
    }

    /**
     * GET /questions/{id}.
     */
    public function view(string $id): Response
    {
        return $this->json(ExamSerializer::question($this->findQuestion($id)));
    }

    /**
     * POST /questions.
     */
    public function add(): Response
    {
        $questions = $this->fetchTable('EmsQuestions');
        $question = $questions->newEntity($this->fromInput($this->body()), ['validate' => false]);

        return $this->json(ExamSerializer::question($questions->saveOrFail($question)), 201);
    }

    /**
     * PUT /questions/{id}.
     */
    public function edit(string $id): Response
    {
        $questions = $this->fetchTable('EmsQuestions');
        $question = $this->findQuestion($id);
        $question = $questions->patchEntity($question, $this->fromInput($this->body()), ['validate' => false]);

        return $this->json(ExamSerializer::question($questions->saveOrFail($question)));
    }

    /**
     * DELETE /questions/{id} — idempotent (no 404).
     */
    public function delete(string $id): Response
    {
        $questions = $this->fetchTable('EmsQuestions');
        $question = $this->tenant()->query('EmsQuestions')
            ->where(['id' => $id])
            ->first();
        if ($question !== null) {
            $questions->deleteOrFail($question);
        }

        return $this->json(null, 204);
    }

    // --- helpers ----------------------------------------------------------

    private function findQuestion(string $id): EntityInterface
    {
        return $this->findOr404('EmsQuestions', $id, Messages::QUESTION_NOT_FOUND);
    }

    /**
     * QuestionInput → column map.
     */
    private function fromInput(array $body): array
    {
        $options = is_array($body['options'] ?? null) ? array_values(array_map('strval', $body['options'])) : [];

        return [
            'school_id' => $this->viewer->schoolId,
            'subject' => (string)($body['subject'] ?? ''),
            'level' => (string)($body['level'] ?? ''),
            'type' => (string)($body['type'] ?? 'objective'),
            'difficulty' => (string)($body['difficulty'] ?? 'medium'),
            'topic' => (string)($body['topic'] ?? ''),
            'text' => (string)($body['text'] ?? ''),
            'options' => $options,
            'answer' => (string)($body['answer'] ?? ''),
            'marks' => (int)($body['marks'] ?? 1),
        ];
    }
}
