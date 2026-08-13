<?php
declare(strict_types=1);

namespace App\Controller\Ems;

use App\Ems\Serializer\CalendarSerializer;
use Cake\Http\Response;

/**
 * The school's audit trail (document.md §3.23). Administrator-only, read-only
 * and append-only — there is no endpoint anywhere that edits or removes an
 * event. Newest first, filterable by entity type and a free-text query over
 * actor / summary / action.
 */
class AuditController extends AppController
{
    /** GET /audit/events */
    public function events(): Response
    {
        $params = $this->pageParams();
        $entityType = (string)$this->request->getQuery('entityType', 'all');
        $needle = strtolower(trim((string)$this->request->getQuery('query', '')));

        // The filter, count and page are all resolved in SQL — the audit trail is
        // append-only and unbounded, so it must never be materialised in PHP.
        $query = $this->tenant()->query('EmsAuditEvents');

        if ($entityType !== 'all') {
            $query->where(['entity_type' => $entityType]);
        }
        if ($needle !== '') {
            // Case-insensitive substring over actor / summary / action, with the
            // LIKE metacharacters escaped so a typed % or _ stays literal (the
            // str_contains semantics this replaced).
            $like = '%' . addcslashes($needle, '%_\\') . '%';
            $query->where(['OR' => [
                'LOWER(actor) LIKE' => $like,
                'LOWER(summary) LIKE' => $like,
                'LOWER(action) LIKE' => $like,
            ]]);
        }

        $total = $query->count();

        // Newest first: order by the monotonic insertion sequence so a
        // same-second burst still reads in true reverse-insertion order.
        $page = $query
            ->orderByDesc('seq')
            ->limit($params['pageSize'])
            ->offset(($params['page'] - 1) * $params['pageSize'])
            ->all()
            ->toList();

        return $this->paginated(
            array_map([CalendarSerializer::class, 'auditEvent'], $page),
            $total,
            $params['page'],
            $params['pageSize'],
        );
    }
}
