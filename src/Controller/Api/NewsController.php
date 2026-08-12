<?php
declare(strict_types=1);

namespace App\Controller\Api;

/**
 * News API — CRUD + publish/unpublish (mirrors the web golive/takedown:
 * status "live" / "offline").
 * POST /api/v1/news/{id}/publish     (golive)
 * POST /api/v1/news/{id}/unpublish   (takedown)
 */
class NewsController extends CrudController
{
    protected array $searchFields = ['title', 'details'];

    public function publish($id = null)
    {
        return $this->setStatus($id, 'live');
    }

    public function unpublish($id = null)
    {
        return $this->setStatus($id, 'offline');
    }

    protected function setStatus($id, string $status)
    {
        $this->request->allowMethod(['post', 'put', 'patch']);
        $this->requireRole($this->writeRoles);
        $news = $this->findOrFail($id);
        $news->set('status', $status);
        if (!$this->Model->save($news)) {
            return $this->respondError('Could not update news status.', 422, $news->getErrors());
        }

        return $this->respond($news);
    }
}
