<?php
declare(strict_types=1);

namespace App\Controller\Ems;

use Cake\Datasource\EntityInterface;
use Cake\Http\Response;
use Cake\I18n\FrozenTime;
use Cake\Utility\Text;

final class FeePlanVersionsController extends AppController
{
    /**
     * Creates a fee-plan version for independent approval.
     */
    public function add(): Response
    {
        if ($this->viewer->role !== 'bursar') {
            $this->fail(403, 'Only a bursar can draft a fee plan.');
        }
        $body = $this->body();
        $key = trim((string)$this->request->getHeaderLine('Idempotency-Key'));
        $replay = $this->financeSecurity()->replay($this->viewer, 'fee_plan.create', $key, $body);
        if ($replay) {
            return $this->json($replay['body'], $replay['status']);
        }
        $items = [];
        $total = 0;
        foreach ((array)($body['items'] ?? []) as $item) {
            $name = trim((string)($item['name'] ?? ''));
            $amount = (int)($item['amount'] ?? 0);
            if ($name === '' || $amount <= 0) {
                $this->fail(422, 'Each fee plan item needs a name and positive amount.');
            }
            $items[] = ['name' => $name, 'amount' => $amount, 'kind' => 'charge'];
            $total += $amount;
        }
        if ($items === []) {
            $this->fail(422, 'Add at least one charge to the fee plan.');
        }
        $table = $this->fetchTable('EmsFeePlanVersions');
        $result = $table->getConnection()->transactional(function () use ($body, $key, $items, $total, $table) {
            $this->financeSecurity()->assertWritable();
            $source = (string)($body['sourceStructureId'] ?? Text::uuid());
            $version = (int)$this->tenant()->query('EmsFeePlanVersions')->where(['source_structure_id' => $source])->count() + 1;
            $row = $table->newEntity(['id' => Text::uuid(),'school_id' => $this->viewer->schoolId,'source_structure_id' => $source,'version' => $version,'session' => (string)($body['session'] ?? ''),'term' => (string)($body['term'] ?? 'First'),'level' => (string)($body['level'] ?? ''),'items' => $items,'schedule' => $body['schedule'] ?? null,'total' => $total,'created_by_user_id' => $this->viewer->userId,'created' => FrozenTime::now('UTC')], ['validate' => false]);
            $table->saveOrFail($row);
            $result = $this->wire($row, 'pending');
            $this->audit()->log($this->viewer, 'fee_plan.created', 'fee_plan_version', (string)$row->id, 'A fee plan version was submitted for approval.');
            $this->financeSecurity()->remember($this->viewer, 'fee_plan.create', $key, $body, 201, $result);

            return $result;
        });

        return $this->json($result, 201);
    }

    /**
     * Lists fee-plan versions with their independent decision state.
     */
    public function index(): Response
    {
        $items = [];
        foreach ($this->tenant()->query('EmsFeePlanVersions')->orderByDesc('created')->all() as $r) {
            $d = $this->tenant()->query('EmsFinanceDecisions')
                ->where(['request_type' => 'fee_plan_version', 'request_id' => (string)$r->id])
                ->first();
            $items[] = $this->wire($r, $d ? (string)$d->decision : 'pending');
        }

        return $this->json([
            'items' => $items,
            'total' => count($items),
            'page' => 1,
            'pageSize' => count($items),
        ]);
    }

    /**
     * Records the independent decision for a fee-plan version.
     */
    public function decide(string $id): Response
    {
        $body = $this->body();
        $key = trim((string)$this->request->getHeaderLine('Idempotency-Key'));
        $request = $body + ['id' => $id];
        $replay = $this->financeSecurity()->replay($this->viewer, 'fee_plan.decide', $key, $request);
        if ($replay) {
            return $this->json($replay['body'], $replay['status']);
        }
        $table = $this->fetchTable('EmsFinanceDecisions');
        $result = $table->getConnection()->transactional(function () use ($id, $body, $key, $request, $table) {
            $this->financeSecurity()->assertWritable();
            $plan = $this->tenant()->query('EmsFeePlanVersions')->where(['id' => $id])->first();
            if (!$plan) {
                $this->fail(404, 'Fee plan version not found.');
            }
            if ((string)$plan->created_by_user_id === $this->viewer->userId) {
                $this->fail(403, 'The plan creator cannot decide their own plan.');
            }
            $decision = (string)($body['decision'] ?? '');
            $reason = trim((string)($body['reason'] ?? ''));
            if (!in_array($decision, ['approved','rejected'], true) || $reason === '') {
                $this->fail(422, 'Choose approve or reject and give a reason.');
            }
            $d = $table->newEntity(['id' => Text::uuid(),'school_id' => $this->viewer->schoolId,'request_type' => 'fee_plan_version','request_id' => $id,'decision' => $decision,'reason' => $reason,'requested_by_user_id' => (string)$plan->created_by_user_id,'decided_by_user_id' => $this->viewer->userId,'decided_by_name' => $this->viewer->name,'decided_at' => FrozenTime::now('UTC')], ['validate' => false]);
            $table->saveOrFail($d);
            $result = $this->wire($plan, $decision);
            $this->audit()->log($this->viewer, 'fee_plan.' . $decision, 'fee_plan_version', $id, 'The fee plan version was ' . $decision . '.', $reason);
            $this->financeSecurity()->remember($this->viewer, 'fee_plan.decide', $key, $request, 200, $result);

            return $result;
        });

        return $this->json($result);
    }

    /**
     * @return array<string, mixed>
     */
    private function wire(EntityInterface $r, string $status): array
    {
        $items = is_string($r->items) ? json_decode($r->items, true) : $r->items;
        $schedule = is_string($r->schedule) ? json_decode($r->schedule, true) : $r->schedule;
        $out = [
            'id' => (string)$r->id,
            'sourceStructureId' => (string)$r->source_structure_id,
            'version' => (int)$r->version,
            'session' => (string)$r->session,
            'term' => (string)$r->term,
            'level' => (string)$r->level,
            'items' => (array)$items,
            'total' => (int)$r->total,
            'status' => $status,
        ];
        if ($schedule) {
            $out['schedule'] = (array)$schedule;
        }

        return $out;
    }
}
