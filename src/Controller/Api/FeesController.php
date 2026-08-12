<?php
declare(strict_types=1);

namespace App\Controller\Api;

/**
 * Fees API — CRUD (filter by session_id, feetype, status, …) plus activate /
 * deactivate, mirroring the web app's status toggle (status 1 = active, 0 = off).
 * POST /api/v1/fees/{id}/activate
 * POST /api/v1/fees/{id}/deactivate
 */
class FeesController extends CrudController
{
    protected array $searchFields = ['name', 'feetype', 'itemcode'];

    public function activate($id = null)
    {
        return $this->setStatus($id, 1);
    }

    public function deactivate($id = null)
    {
        return $this->setStatus($id, 0);
    }

    protected function setStatus($id, int $status)
    {
        $this->request->allowMethod(['post', 'put', 'patch']);
        $this->requireRole($this->writeRoles);
        $fee = $this->findOrFail($id);
        $fee->set('status', $status);
        if (!$this->Model->save($fee)) {
            return $this->respondError('Could not update fee status.', 422, $fee->getErrors());
        }

        return $this->respond($fee);
    }
}
