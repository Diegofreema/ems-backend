<?php
declare(strict_types=1);

namespace App\Controller\Ems;

use App\Ems\Messages;
use App\Ems\Money;
use App\Ems\Serializer\FeeSerializer;
use Cake\Http\Response;

/**
 * Fee structures — the bill a whole level owes for a session + term, from which
 * invoices are issued (document.md §3.7). Items are charges only; an optional
 * percentage schedule says how the level may pay.
 */
class FeeStructuresController extends AppController
{
    /**
     * GET /fee-structures — paginated; query matches level, term filter. Sorted
     * by term then level.
     */
    public function index(): Response
    {
        $params = $this->pageParams();
        $query = trim((string)$this->request->getQuery('query', ''));
        $term = (string)$this->request->getQuery('term', 'all');

        $q = $this->tenant()->query('EmsFeeStructures');
        if ($term !== 'all' && $term !== '') {
            $q->where(['term' => $term]);
        }
        if ($query !== '') {
            $q->where(['level LIKE' => '%' . $query . '%']);
        }
        $total = $q->count();
        $rows = $q->orderByAsc('term')->orderByAsc('level')
            ->limit($params['pageSize'])
            ->offset(($params['page'] - 1) * $params['pageSize'])
            ->all();

        return $this->paginated(
            array_map([FeeSerializer::class, 'structure'], $rows->toList()),
            $total,
            $params['page'],
            $params['pageSize']
        );
    }

    /**
     * POST /fee-structures — blank-named items dropped; schedule rows without a
     * date dropped; surviving schedule percents must sum to 100.
     */
    public function add(): Response
    {
        $this->fail(410, 'Direct fee structures are retired. Create an approved fee plan version.');
        $body = $this->body();
        $items = [];
        foreach (is_array($body['items'] ?? null) ? $body['items'] : [] as $i) {
            if (trim((string)($i['name'] ?? '')) === '') {
                continue;
            }
            $item = ['name' => (string)$i['name'], 'amount' => (int)($i['amount'] ?? 0)];
            if (isset($i['kind'])) {
                $item['kind'] = (string)$i['kind'];
            }
            $items[] = $item;
        }

        $schedule = [];
        foreach (is_array($body['schedule'] ?? null) ? $body['schedule'] : [] as $r) {
            if ((string)($r['dueOn'] ?? '') === '') {
                continue;
            }
            $schedule[] = [
                'label' => (string)($r['label'] ?? ''),
                'dueOn' => (string)$r['dueOn'],
                'percent' => (int)($r['percent'] ?? 0),
            ];
        }
        if ($schedule !== []) {
            $percent = 0;
            foreach ($schedule as $r) {
                $percent += Money::jsRound($r['percent']);
            }
            if ($percent !== 100) {
                $this->fail(422, sprintf(Messages::FEE_SCHEDULE_PERCENT, $percent));
            }
            usort($schedule, static fn ($a, $b) => strcmp((string)$a['dueOn'], (string)$b['dueOn']));
        }

        $total = 0;
        foreach ($items as $i) {
            $total += (int)$i['amount'];
        }

        $structures = $this->fetchTable('EmsFeeStructures');
        $structure = $structures->newEntity([
            'school_id' => $this->viewer->schoolId,
            'session' => (string)($body['session'] ?? ''),
            'term' => (string)($body['term'] ?? 'First'),
            'level' => (string)($body['level'] ?? ''),
            'items' => $items,
            'total' => $total,
            'schedule' => $schedule !== [] ? $schedule : null,
        ], ['validate' => false]);

        return $this->json(FeeSerializer::structure($structures->saveOrFail($structure)), 201);
    }

    /** GET /fee-structures/{id}. */
    public function view(string $id): Response
    {
        return $this->json(FeeSerializer::structure($this->findStructure($id)));
    }

    /** PUT /fee-structures/{id} — correct pricing before invoices are cut. */
    public function edit(string $id): Response
    {
        $this->fail(410, 'Fee plan versions are immutable. Create a new version for approval.');
        $row = $this->findStructure($id);
        $body = $this->body();
        foreach (['name' => 'name', 'level' => 'level', 'term' => 'term', 'session' => 'session'] as $in => $col) {
            if (array_key_exists($in, $body)) {
                $row->{$col} = (string)$body[$in];
            }
        }
        if (array_key_exists('items', $body) && is_array($body['items'])) {
            $row->items = $body['items'];
        }
        if (array_key_exists('schedule', $body) && is_array($body['schedule'])) {
            $row->schedule = $body['schedule'];
        }
        $this->fetchTable('EmsFeeStructures')->saveOrFail($row);

        return $this->json(FeeSerializer::structure($row));
    }

    /** DELETE /fee-structures/{id}. */
    public function delete(string $id): Response
    {
        $this->fail(410, 'Approved finance records cannot be deleted.');
        $row = $this->findStructure($id);
        $this->fetchTable('EmsFeeStructures')->deleteOrFail($row);

        return $this->response->withStatus(204);
    }

    private function findStructure(string $id): \Cake\Datasource\EntityInterface
    {
        return $this->findOr404('EmsFeeStructures', $id, Messages::STRUCTURE_NOT_FOUND);
    }
}
