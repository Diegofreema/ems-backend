<?php
declare(strict_types=1);

namespace App\Controller\Ems;

use App\Ems\Messages;
use App\Ems\Money;
use App\Ems\Serializer\FeeSerializer;
use Cake\Datasource\EntityInterface;
use Cake\Http\Response;
use Cake\I18n\FrozenDate;
use Cake\I18n\FrozenTime;
use Cake\Utility\Text;

/**
 * Scholarships and discounts (document.md §3.7). A standing decision to take
 * money off a bill: a scholarship for a named child, a discount for a whole
 * level. Applied only at issue and never deleted — ending one leaves invoices
 * already issued under it untouched.
 *
 * Two-person flow throughout: a bursar drafts the grant (the award row itself
 * is the request, status `pending`) and requests an ending through its own
 * request row; a different administrator decides both. Only decision-approved
 * awards are `active` and price invoices.
 */
class FeeAwardsController extends AppController
{
    /**
     * GET /fee-awards — paginated; query over name/studentName/level, term and
     * status filters (an all-terms award matches every term). Active first,
     * then awardedOn desc, then name.
     */
    public function index(): Response
    {
        $params = $this->pageParams();
        $query = strtolower(trim((string)$this->request->getQuery('query', '')));
        $term = (string)$this->request->getQuery('term', 'all');
        $status = (string)$this->request->getQuery('status', 'all');

        $rows = $this->tenant()->query('EmsFeeAwards')
            ->all()
            ->toList();
        $filtered = [];
        foreach ($rows as $a) {
            $termOk = $term === 'all' || (string)$a->term === $term || (string)$a->term === 'all';
            $statusOk = $status === 'all' || (string)$a->status === $status;
            $matches = $query === ''
                || str_contains(strtolower((string)$a->name), $query)
                || str_contains(strtolower((string)($a->student_name ?? '')), $query)
                || str_contains(strtolower((string)($a->level ?? '')), $query);
            if ($termOk && $statusOk && $matches) {
                $filtered[] = $a;
            }
        }
        usort($filtered, static fn($a, $b) => strcmp((string)$a->status, (string)$b->status)
            ?: (strcmp((string)$b->awarded_on, (string)$a->awarded_on)
                ?: strcmp((string)$a->name, (string)$b->name)));

        $total = count($filtered);
        $page = array_slice($filtered, ($params['page'] - 1) * $params['pageSize'], $params['pageSize']);

        return $this->paginated(
            array_map([FeeSerializer::class, 'award'], $page),
            $total,
            $params['page'],
            $params['pageSize'],
        );
    }

    /**
     * POST /fee-awards — a bursar drafts the award for independent approval.
     * It prices nothing until a different administrator approves it.
     */
    public function add(): Response
    {
        if ($this->viewer->role !== 'bursar') {
            $this->fail(403, 'Only a bursar can draft a scholarship or discount.');
        }
        $body = $this->body();
        $key = trim((string)$this->request->getHeaderLine('Idempotency-Key'));
        $replay = $this->financeSecurity()->replay($this->viewer, 'fee_award.create', $key, $body);
        if ($replay !== null) {
            return $this->json($replay['body'], $replay['status']);
        }
        $name = trim((string)($body['name'] ?? ''));
        if ($name === '') {
            $this->fail(422, Messages::AWARD_NAME_REQUIRED);
        }
        $value = Money::jsRound((float)($body['value'] ?? 0));
        if ($value <= 0) {
            $this->fail(422, Messages::AWARD_VALUE_REQUIRED);
        }
        $basis = (string)($body['basis'] ?? 'amount');
        if ($basis === 'percentage' && $value > 100) {
            $this->fail(422, Messages::AWARD_PERCENT_RANGE);
        }
        $scope = (string)($body['scope'] ?? 'student');

        $studentName = null;
        $studentId = null;
        $level = null;
        if ($scope === 'student') {
            $studentId = (string)($body['studentId'] ?? '');
            $student = $this->tenant()->query('EmsStudents')
                ->where(['id' => $studentId])
                ->first();
            if ($student === null) {
                $this->fail(422, Messages::AWARD_STUDENT_REQUIRED);
            }
            $studentName = trim((string)$student->first_name . ' ' . (string)$student->last_name);
        } else {
            $level = (string)($body['level'] ?? '');
            if ($level === '') {
                $this->fail(422, Messages::AWARD_LEVEL_REQUIRED);
            }
        }

        $appliesToItem = trim((string)($body['appliesToItem'] ?? '')) ?: Money::ALL_ITEMS;
        $note = trim((string)($body['note'] ?? ''));
        $awards = $this->fetchTable('EmsFeeAwards');
        $result = $awards->getConnection()->transactional(
            function () use ($awards, $body, $key, $name, $basis, $value, $scope, $studentId, $studentName, $level, $appliesToItem, $note) {
                $this->financeSecurity()->assertWritable();
                $award = $awards->newEntity([
                    'school_id' => $this->viewer->schoolId,
                    'name' => $name,
                    'kind' => (string)($body['kind'] ?? 'scholarship'),
                    'basis' => $basis,
                    'value' => $value,
                    'applies_to_item' => $appliesToItem,
                    'scope' => $scope,
                    'student_id' => $scope === 'student' ? $studentId : null,
                    'student_name' => $studentName,
                    'level' => $scope === 'level' ? $level : null,
                    'session' => (string)($body['session'] ?? ''),
                    'term' => (string)($body['term'] ?? 'all'),
                    'status' => 'pending',
                    'note' => $note !== '' ? $note : null,
                    'awarded_by' => $this->viewer->name,
                    'awarded_on' => FrozenDate::today(),
                    'created_by_user_id' => $this->viewer->userId,
                ], ['validate' => false]);
                $awards->saveOrFail($award);

                $who = $studentName ?? $level;
                $worth = $basis === 'percentage' ? $value . '%' : Money::formatCurrency($value);
                $target = $appliesToItem === Money::ALL_ITEMS ? 'the bill' : $appliesToItem;
                $result = FeeSerializer::award($award);
                $this->audit()->log(
                    $this->viewer,
                    'fee_award.requested',
                    'fee_award',
                    (string)$award->id,
                    sprintf('%s drafted for %s — %s off %s. Awaiting independent approval.', $name, $who, $worth, $target),
                );
                $this->financeSecurity()->remember($this->viewer, 'fee_award.create', $key, $body, 201, $result);

                return $result;
            },
        );

        return $this->json($result, 201);
    }

    /**
     * POST /fee-awards/{id}/decision — a different administrator approves or
     * rejects the drafted award. Only approval makes it price invoices.
     */
    public function decide(string $id): Response
    {
        $body = $this->body();
        $key = trim((string)$this->request->getHeaderLine('Idempotency-Key'));
        $request = $body + ['id' => $id];
        $replay = $this->financeSecurity()->replay($this->viewer, 'fee_award.decide', $key, $request);
        if ($replay !== null) {
            return $this->json($replay['body'], $replay['status']);
        }
        $awards = $this->fetchTable('EmsFeeAwards');
        $result = $awards->getConnection()->transactional(function () use ($id, $body, $key, $request, $awards) {
            $this->financeSecurity()->assertWritable();
            $award = $this->tenant()->query('EmsFeeAwards')->where(['id' => $id])->epilog('FOR UPDATE')->first();
            if ($award === null) {
                $this->fail(404, Messages::AWARD_NOT_FOUND);
            }
            if ((string)$award->status !== 'pending') {
                $this->fail(422, 'Only a pending award can be decided.');
            }
            if ((string)$award->created_by_user_id === $this->viewer->userId) {
                $this->fail(403, 'The award drafter cannot decide their own award.');
            }
            $decision = (string)($body['decision'] ?? '');
            $reason = trim((string)($body['reason'] ?? ''));
            if (!in_array($decision, ['approved', 'rejected'], true) || $reason === '') {
                $this->fail(422, 'Choose approve or reject and give a reason.');
            }
            $decisions = $this->fetchTable('EmsFinanceDecisions');
            $decisions->saveOrFail($decisions->newEntity([
                'id' => Text::uuid(),
                'school_id' => $this->viewer->schoolId,
                'request_type' => 'fee_award',
                'request_id' => $id,
                'decision' => $decision,
                'reason' => $reason,
                'requested_by_user_id' => (string)$award->created_by_user_id,
                'decided_by_user_id' => $this->viewer->userId,
                'decided_by_name' => $this->viewer->name,
                'decided_at' => FrozenTime::now('UTC'),
            ], ['validate' => false]));
            $award->status = $decision === 'approved' ? 'active' : 'rejected';
            $awards->saveOrFail($award);
            $result = FeeSerializer::award($award);
            $this->audit()->log(
                $this->viewer,
                'fee_award.' . $decision,
                'fee_award',
                $id,
                'The drafted award was ' . $decision . '.',
                $reason,
            );
            $this->financeSecurity()->remember($this->viewer, 'fee_award.decide', $key, $request, 200, $result);

            return $result;
        });

        return $this->json($result);
    }

    /**
     * POST /fee-awards/{id}/end — a bursar requests the ending; a different
     * administrator approves it. Issued invoices keep their award lines either
     * way; only future issues change.
     */
    public function endAward(string $id): Response
    {
        if ($this->viewer->role !== 'bursar') {
            $this->fail(403, 'Only a bursar can request that an award end.');
        }
        $body = $this->body();
        $key = trim((string)$this->request->getHeaderLine('Idempotency-Key'));
        $request = $body + ['id' => $id];
        $replay = $this->financeSecurity()->replay($this->viewer, 'fee_award.end_request', $key, $request);
        if ($replay !== null) {
            return $this->json($replay['body'], $replay['status']);
        }
        $award = $this->findAward($id);
        if ((string)$award->status === 'ended') {
            $this->fail(422, Messages::AWARD_ALREADY_ENDED);
        }
        if ((string)$award->status !== 'active') {
            $this->fail(422, 'Only an active award can be ended.');
        }
        $reason = trim((string)($body['reason'] ?? ''));
        if ($reason === '') {
            $this->fail(422, Messages::AWARD_END_REASON);
        }
        $table = $this->fetchTable('EmsFeeAwardEndRequests');
        $result = $table->getConnection()->transactional(function () use ($id, $body, $key, $request, $reason, $award, $table) {
            $this->financeSecurity()->assertWritable();
            if ($this->openEndRequestExists($id)) {
                $this->fail(409, 'This award already has an end request awaiting a decision.');
            }
            $row = $table->newEntity([
                'id' => Text::uuid(),
                'school_id' => $this->viewer->schoolId,
                'award_id' => $id,
                'reason' => $reason,
                'requested_by_user_id' => $this->viewer->userId,
                'requested_by_name' => $this->viewer->name,
                'created' => FrozenTime::now('UTC'),
            ], ['validate' => false]);
            $table->saveOrFail($row);
            $result = $this->endRequestWire($row, $award, null);
            $this->audit()->log(
                $this->viewer,
                'fee_award.end_requested',
                'fee_award',
                $id,
                sprintf('An end request for %s was recorded and awaits independent approval.', (string)$award->name),
                $reason,
            );
            $this->financeSecurity()->remember($this->viewer, 'fee_award.end_request', $key, $request, 201, $result);

            return $result;
        });

        return $this->json($result, 201);
    }

    /**
     * GET /fee-award-end-requests — every end request with its decision state,
     * newest first, joined to its award for the approvals desk.
     */
    public function endRequests(): Response
    {
        $awardsById = [];
        foreach ($this->tenant()->query('EmsFeeAwards')->all() as $a) {
            $awardsById[(string)$a->id] = $a;
        }
        $items = [];
        foreach ($this->tenant()->query('EmsFeeAwardEndRequests')->orderByDesc('created')->all() as $r) {
            $decision = $this->tenant()->query('EmsFinanceDecisions')
                ->where(['request_type' => 'fee_award_end', 'request_id' => (string)$r->id])
                ->first();
            $items[] = $this->endRequestWire($r, $awardsById[(string)$r->award_id] ?? null, $decision);
        }

        return $this->json([
            'items' => $items,
            'total' => count($items),
            'page' => 1,
            'pageSize' => count($items),
        ]);
    }

    /**
     * POST /fee-award-end-requests/{id}/decision — a different administrator
     * decides the end request; approval ends the award today.
     */
    public function decideEnd(string $id): Response
    {
        $body = $this->body();
        $key = trim((string)$this->request->getHeaderLine('Idempotency-Key'));
        $request = $body + ['id' => $id];
        $replay = $this->financeSecurity()->replay($this->viewer, 'fee_award.end_decide', $key, $request);
        if ($replay !== null) {
            return $this->json($replay['body'], $replay['status']);
        }
        $awards = $this->fetchTable('EmsFeeAwards');
        $result = $awards->getConnection()->transactional(function () use ($id, $body, $key, $request, $awards) {
            $this->financeSecurity()->assertWritable();
            $row = $this->tenant()->query('EmsFeeAwardEndRequests')->where(['id' => $id])->first();
            if ($row === null) {
                $this->fail(404, 'Award end request not found.');
            }
            if ((string)$row->requested_by_user_id === $this->viewer->userId) {
                $this->fail(403, 'The person who requested this ending cannot decide it.');
            }
            $existing = $this->tenant()->query('EmsFinanceDecisions')
                ->where(['request_type' => 'fee_award_end', 'request_id' => $id])
                ->count();
            if ($existing > 0) {
                $this->fail(409, 'This end request already has a decision.');
            }
            $decision = (string)($body['decision'] ?? '');
            $reason = trim((string)($body['reason'] ?? ''));
            if (!in_array($decision, ['approved', 'rejected'], true) || $reason === '') {
                $this->fail(422, 'Choose approve or reject and give a reason.');
            }
            $award = $this->tenant()->query('EmsFeeAwards')
                ->where(['id' => (string)$row->award_id])
                ->epilog('FOR UPDATE')
                ->first();
            if ($award === null) {
                $this->fail(404, Messages::AWARD_NOT_FOUND);
            }
            $decisions = $this->fetchTable('EmsFinanceDecisions');
            $decisionRow = $decisions->newEntity([
                'id' => Text::uuid(),
                'school_id' => $this->viewer->schoolId,
                'request_type' => 'fee_award_end',
                'request_id' => $id,
                'decision' => $decision,
                'reason' => $reason,
                'requested_by_user_id' => (string)$row->requested_by_user_id,
                'decided_by_user_id' => $this->viewer->userId,
                'decided_by_name' => $this->viewer->name,
                'decided_at' => FrozenTime::now('UTC'),
            ], ['validate' => false]);
            $decisions->saveOrFail($decisionRow);
            if ($decision === 'approved') {
                $award->status = 'ended';
                $award->ended_on = FrozenDate::today();
                $award->ended_reason = (string)$row->reason;
                $awards->saveOrFail($award);
                $who = $award->student_name ?? $award->level;
                $this->audit()->log(
                    $this->viewer,
                    'fee_award.ended',
                    'fee_award',
                    (string)$award->id,
                    sprintf('%s for %s ended. Invoices already issued keep it.', (string)$award->name, (string)$who),
                    (string)$row->reason,
                );
            } else {
                $this->audit()->log(
                    $this->viewer,
                    'fee_award.end_rejected',
                    'fee_award',
                    (string)$award->id,
                    sprintf('The end request for %s was rejected; the award stays active.', (string)$award->name),
                    $reason,
                );
            }
            $result = $this->endRequestWire($row, $award, $decisionRow);
            $this->financeSecurity()->remember($this->viewer, 'fee_award.end_decide', $key, $request, 200, $result);

            return $result;
        });

        return $this->json($result);
    }

    /** True when this award has an end request no administrator has decided. */
    private function openEndRequestExists(string $awardId): bool
    {
        $ids = $this->tenant()->query('EmsFeeAwardEndRequests')
            ->select(['id'])
            ->where(['award_id' => $awardId])
            ->all()
            ->extract('id')
            ->toList();
        if ($ids === []) {
            return false;
        }
        $decided = $this->tenant()->query('EmsFinanceDecisions')
            ->where(['request_type' => 'fee_award_end', 'request_id IN' => $ids])
            ->count();

        return count($ids) > $decided;
    }

    /**
     * @return array<string, mixed>
     */
    private function endRequestWire(EntityInterface $row, ?EntityInterface $award, ?EntityInterface $decision): array
    {
        $out = [
            'id' => (string)$row->id,
            'awardId' => (string)$row->award_id,
            'reason' => (string)$row->reason,
            'requestedBy' => (string)$row->requested_by_name,
            'requestedOn' => (string)$row->created,
            'status' => $decision ? (string)$decision->decision : 'pending',
        ];
        if ($award !== null) {
            $out['awardName'] = (string)$award->name;
            $out['holder'] = (string)($award->student_name ?? 'Everyone in ' . (string)$award->level);
            $out['awardStatus'] = (string)$award->status;
        }
        if ($decision) {
            $out['decision'] = [
                'reason' => (string)$decision->reason,
                'decidedBy' => (string)$decision->decided_by_name,
                'decidedAt' => (string)$decision->decided_at,
            ];
        }

        return $out;
    }

    private function findAward(string $id): EntityInterface
    {
        return $this->findOr404('EmsFeeAwards', $id, Messages::AWARD_NOT_FOUND);
    }
}
