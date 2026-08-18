<?php
declare(strict_types=1);

namespace App\Controller\Ems;

use App\Ems\Messages;
use App\Ems\Serializer\DemoRequestSerializer;
use Cake\Datasource\EntityInterface;
use Cake\Http\Response;

/**
 * The platform demo-requests inbox (CRM-lite, §platform surface).
 *
 * Reads the same `ems_demo_requests` rows the public {@see DemoRequestsController}
 * writes, plus their internal notes, so NetPro platform staff can work each lead:
 * filter the pipeline, read a request, move its stage, and keep a note trail.
 *
 * Deliberately NOT public and NOT tenant-scoped. Every action is gated to the
 * `platform_staff` role by App\Ems\Policy (the PLATFORM tier), and the routes are
 * tenant-less (`/platform/*`, no `{schoolId}`), so these queries hit the tables
 * DIRECTLY — never through `Tenant`, which a lead (with no school) has no place
 * in. Contrast every school controller, which must scope by `school_id`.
 */
class DemoInboxController extends AppController
{
    /** The lead pipeline, in order. `new` is the column default. */
    private const STATUSES = ['new', 'contacted', 'qualified', 'won', 'lost'];

    /** How long an internal note may be (the column is TEXT; this is a sanity cap). */
    private const NOTE_MAX = 5000;

    /**
     * GET /platform/demo-requests?status=&q=&page=&pageSize= — the inbox list,
     * newest first. `status` filters to one pipeline stage (ignored if unknown);
     * `q` matches institution, contact or e-mail.
     */
    public function index(): Response
    {
        $requests = $this->fetchTable('EmsDemoRequests');
        $query = $requests->find();

        $status = trim((string)$this->request->getQuery('status', ''));
        if (in_array($status, self::STATUSES, true)) {
            $query->where(['status' => $status]);
        }

        $q = trim((string)$this->request->getQuery('q', ''));
        if ($q !== '') {
            $like = '%' . str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $q) . '%';
            $query->where(['OR' => [
                'institution_name LIKE' => $like,
                'contact_name LIKE' => $like,
                'email LIKE' => $like,
            ]]);
        }

        $total = (clone $query)->count();
        $params = $this->pageParams();
        $rows = $query
            ->orderByDesc('created')
            ->orderByDesc('id')
            ->limit($params['pageSize'])
            ->offset(($params['page'] - 1) * $params['pageSize'])
            ->all()
            ->toArray();

        $items = array_map(
            static fn(EntityInterface $r): array => DemoRequestSerializer::request($r),
            $rows,
        );

        return $this->paginated($items, $total, $params['page'], $params['pageSize']);
    }

    /**
     * GET /platform/demo-requests/summary — the count in each pipeline stage,
     * for the inbox's status tabs. Every known stage is present (0 when empty).
     */
    public function summary(): Response
    {
        $requests = $this->fetchTable('EmsDemoRequests');
        $counts = array_fill_keys(self::STATUSES, 0);

        $query = $requests->find();
        $rows = $query
            ->select(['status' => 'status', 'n' => $query->func()->count('*')])
            ->groupBy('status')
            ->all();
        foreach ($rows as $row) {
            $stage = (string)$row->status;
            if (array_key_exists($stage, $counts)) {
                $counts[$stage] = (int)$row->n;
            }
        }

        return $this->json([
            'total' => $requests->find()->count(),
            'byStatus' => $counts,
        ]);
    }

    /**
     * GET /platform/demo-requests/{id} — one request with its full note trail.
     */
    public function view(string $id): Response
    {
        $request = $this->findRequestOr404($id);

        return $this->json(DemoRequestSerializer::detail($request, $this->notesFor($id)));
    }

    /**
     * PATCH /platform/demo-requests/{id}/status { status } — move a lead along
     * the pipeline. Returns the fresh detail (row + notes).
     */
    public function updateStatus(string $id): Response
    {
        $request = $this->findRequestOr404($id);
        $status = trim((string)($this->body()['status'] ?? ''));
        if (!in_array($status, self::STATUSES, true)) {
            $this->fail(422, Messages::DEMO_STATUS_INVALID);
        }

        $request->status = $status;
        $this->fetchTable('EmsDemoRequests')->saveOrFail($request);

        return $this->json(DemoRequestSerializer::detail($request, $this->notesFor($id)));
    }

    /**
     * POST /platform/demo-requests/{id}/notes { body } — append an internal
     * note (authored by the acting staffer). Returns the fresh detail so the
     * timeline updates in one round-trip.
     */
    public function addNote(string $id): Response
    {
        $request = $this->findRequestOr404($id);
        $body = trim((string)($this->body()['body'] ?? ''));
        if ($body === '') {
            $this->fail(422, Messages::DEMO_NOTE_REQUIRED);
        }

        $notes = $this->fetchTable('EmsDemoRequestNotes');
        $notes->saveOrFail($notes->newEntity([
            'demo_request_id' => $id,
            'author_user_id' => $this->viewer->userId,
            'author_name' => $this->viewer->name,
            'body' => mb_substr($body, 0, self::NOTE_MAX),
        ], ['validate' => false]));

        return $this->json(DemoRequestSerializer::detail($request, $this->notesFor($id)), 201);
    }

    /** Find a demo request by id (non-tenant), or 404. */
    private function findRequestOr404(string $id): EntityInterface
    {
        $request = $this->fetchTable('EmsDemoRequests')->find()->where(['id' => $id])->first();
        if ($request === null) {
            $this->fail(404, Messages::DEMO_NOT_FOUND);
        }

        return $request;
    }

    /**
     * The note trail for a request, oldest first.
     *
     * @return array<int, \Cake\Datasource\EntityInterface>
     */
    private function notesFor(string $id): array
    {
        return $this->fetchTable('EmsDemoRequestNotes')->find()
            ->where(['demo_request_id' => $id])
            ->orderByAsc('created')
            ->orderByAsc('id')
            ->all()
            ->toArray();
    }
}
