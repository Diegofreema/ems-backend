<?php
declare(strict_types=1);

namespace App\Controller\Api;

use Cake\I18n\FrozenTime;

/**
 * Results API — REST CRUD plus the assessment domain actions that matter to a
 * frontend: student transcript and the result-approval workflow.
 *
 * CRUD + filters (via CrudController):
 *   GET /api/v1/results?student_id=&subject_id=&session_id=&semester_id=
 *       &department_id=&class_arm_id=&level_id=&faculty_id=&regno=&approval_status=
 *   GET/POST/PUT/PATCH/DELETE as usual.
 *
 * Custom actions:
 *   GET  /api/v1/results/transcript?student_id=   approved results for a student
 *   GET  /api/v1/results/pending-approval          results awaiting approval
 *   POST /api/v1/results/{id}/approve              approve one
 *   POST /api/v1/results/{id}/reject               reject one (rejection_reason)
 *   POST /api/v1/results/approve-batch             approve many ({ids:[...]})
 *   POST /api/v1/results/reject-batch              reject many ({ids:[...], rejection_reason})
 */
class ResultsController extends CrudController
{
    /**
     * @var array<int, string>
     */
    protected array $searchFields = ['regno', 'grade', 'remark'];

    /**
     * @var array<int, string>|null
     */
    protected ?array $viewContain = ['Students', 'Subjects', 'Departments', 'Sessions', 'Semesters', 'Levels', 'ClassArms'];

    /**
     * All approved results for one student, ordered for transcript display.
     *
     * @return \Cake\Http\Response
     */
    public function transcript()
    {
        $this->request->allowMethod(['get']);
        $studentId = (int)$this->request->getQuery('student_id');
        if ($studentId <= 0) {
            return $this->respondError('A student_id query parameter is required.', 422);
        }

        $rows = $this->Model->find()
            ->contain(['Subjects', 'Sessions', 'Semesters'])
            ->where([
                'Results.student_id' => $studentId,
                'Results.approval_status' => 'approved',
            ])
            ->order(['Results.session_id' => 'ASC', 'Results.semester_id' => 'ASC'])
            ->all()
            ->toList();

        return $this->respond($rows, 200, ['student_id' => $studentId, 'count' => count($rows)]);
    }

    /**
     * Results awaiting approval (optionally scoped by the usual filters).
     *
     * @return \Cake\Http\Response
     */
    public function pendingApproval()
    {
        $this->request->allowMethod(['get']);
        $this->requireRole([1, 5, 7]);
        ['page' => $page, 'limit' => $limit] = $this->paginationParams();

        $query = $this->Model->find()
            ->contain(['Students', 'Subjects', 'Departments'])
            ->where(['Results.approval_status' => 'pending']);
        $this->applyFilters($query);

        $total = (clone $query)->count();
        $rows = $query->order(['Results.id' => 'DESC'])
            ->limit($limit)->offset(($page - 1) * $limit)->all()->toList();

        return $this->respond($rows, 200, [
            'page' => $page, 'limit' => $limit, 'total' => $total, 'pages' => (int)ceil($total / max(1, $limit)),
        ]);
    }

    /**
     * Approve a single pending result.
     *
     * @param string|null $id Result id.
     * @return \Cake\Http\Response
     */
    public function approve($id = null)
    {
        $this->request->allowMethod(['post', 'put', 'patch']);
        $this->requireRole([1, 5, 7]);

        $result = $this->findOrFail($id);
        $this->setApproval($result, 'approved');
        if (!$this->Model->save($result)) {
            return $this->respondError('Could not approve result.', 422, $result->getErrors());
        }

        return $this->respond($result);
    }

    /**
     * Reject a single pending result with a reason.
     *
     * @param string|null $id Result id.
     * @return \Cake\Http\Response
     */
    public function reject($id = null)
    {
        $this->request->allowMethod(['post', 'put', 'patch']);
        $this->requireRole([1, 5, 7]);

        $reason = trim((string)$this->request->getData('rejection_reason'));
        if ($reason === '') {
            return $this->respondError('A rejection_reason is required.', 422);
        }

        $result = $this->findOrFail($id);
        $this->setApproval($result, 'rejected', $reason);
        if (!$this->Model->save($result)) {
            return $this->respondError('Could not reject result.', 422, $result->getErrors());
        }

        return $this->respond($result);
    }

    /**
     * Approve many results at once.
     *
     * @return \Cake\Http\Response
     */
    public function approveBatch()
    {
        return $this->batchDecision('approved');
    }

    /**
     * Reject many results at once.
     *
     * @return \Cake\Http\Response
     */
    public function rejectBatch()
    {
        return $this->batchDecision('rejected');
    }

    /**
     * Shared batch approve/reject implementation.
     *
     * @param string $status 'approved' or 'rejected'.
     * @return \Cake\Http\Response
     */
    protected function batchDecision(string $status)
    {
        $this->request->allowMethod(['post']);
        $this->requireRole([1, 5, 7]);

        $ids = (array)$this->request->getData('ids');
        $ids = array_values(array_filter(array_map('intval', $ids)));
        if (!$ids) {
            return $this->respondError('An "ids" array is required.', 422);
        }
        $reason = null;
        if ($status === 'rejected') {
            $reason = trim((string)$this->request->getData('rejection_reason'));
            if ($reason === '') {
                return $this->respondError('A rejection_reason is required for rejection.', 422);
            }
        }

        $updated = 0;
        foreach ($this->Model->find()->where(['Results.id IN' => $ids])->all() as $result) {
            $this->setApproval($result, $status, $reason);
            if ($this->Model->save($result)) {
                $updated++;
            }
        }

        return $this->respond(['status' => $status, 'updated' => $updated, 'requested' => count($ids)]);
    }

    /**
     * Stamp the approval fields on a result entity (mirrors the web workflow).
     *
     * @param \Cake\Datasource\EntityInterface $result Result entity.
     * @param string $status New approval_status.
     * @param string|null $reason Rejection reason when rejecting.
     * @return void
     */
    protected function setApproval($result, string $status, ?string $reason = null): void
    {
        $result->set('approval_status', $status);
        $result->set('approved_by', $this->currentUserId());
        $result->set('approved_at', FrozenTime::now());
        if ($status === 'rejected') {
            $result->set('rejection_reason', $reason);
        }
    }
}
