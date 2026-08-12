<?php
declare(strict_types=1);

namespace App\Controller\Ems;

use App\Ems\Messages;
use App\Ems\Money;
use App\Ems\Serializer\FeeSerializer;
use Cake\Datasource\EntityInterface;
use Cake\Http\Response;
use Cake\I18n\FrozenDate;

/**
 * Scholarships and discounts (document.md §3.7). A standing decision to take
 * money off a bill: a scholarship for a named child, a discount for a whole
 * level. Applied only at issue and never deleted — ending one leaves invoices
 * already issued under it untouched.
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
        usort($filtered, static fn ($a, $b) =>
            strcmp((string)$a->status, (string)$b->status)
            ?: (strcmp((string)$b->awarded_on, (string)$a->awarded_on)
                ?: strcmp((string)$a->name, (string)$b->name)));

        $total = count($filtered);
        $page = array_slice($filtered, ($params['page'] - 1) * $params['pageSize'], $params['pageSize']);

        return $this->paginated(
            array_map([FeeSerializer::class, 'award'], $page),
            $total,
            $params['page'],
            $params['pageSize']
        );
    }

    /**
     * POST /fee-awards — validates then records; audits the grant.
     */
    public function add(): Response
    {
        $this->fail(410, 'Direct awards are retired pending independent award approval.');
        $body = $this->body();
        $name = trim((string)($body['name'] ?? ''));
        if ($name === '') {
            $this->fail(422, Messages::AWARD_NAME_REQUIRED);
        }
        $value = Money::jsRound($body['value'] ?? 0);
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
            'status' => 'active',
            'note' => $note !== '' ? $note : null,
            'awarded_by' => $this->viewer->name,
            'awarded_on' => FrozenDate::today(),
        ], ['validate' => false]);
        $awards->saveOrFail($award);

        $who = $studentName ?? $level;
        $worth = $basis === 'percentage' ? $value . '%' : Money::formatCurrency($value);
        $target = $appliesToItem === Money::ALL_ITEMS ? 'the bill' : $appliesToItem;
        $this->audit()->log(
            $this->viewer,
            'fee_award.granted',
            'fee_award',
            (string)$award->id,
            sprintf('%s awarded to %s — %s off %s.', $name, $who, $worth, $target)
        );

        return $this->json(FeeSerializer::award($award), 201);
    }

    /**
     * POST /fee-awards/{id}/end — never deleted; issued invoices keep it.
     */
    public function endAward(string $id): Response
    {
        $this->fail(410, 'Award termination requires an independently approved finance adjustment.');
        $award = $this->findAward($id);
        if ((string)$award->status === 'ended') {
            $this->fail(422, Messages::AWARD_ALREADY_ENDED);
        }
        $reason = trim((string)($this->body()['reason'] ?? ''));
        if ($reason === '') {
            $this->fail(422, Messages::AWARD_END_REASON);
        }
        $awards = $this->fetchTable('EmsFeeAwards');
        $award->status = 'ended';
        $award->ended_on = FrozenDate::today();
        $award->ended_reason = $reason;
        $awards->saveOrFail($award);

        $who = $award->student_name ?? $award->level;
        $this->audit()->log(
            $this->viewer,
            'fee_award.ended',
            'fee_award',
            (string)$award->id,
            sprintf('%s for %s ended. Invoices already issued keep it.', (string)$award->name, (string)$who),
            $reason
        );

        return $this->json(FeeSerializer::award($award));
    }

    private function findAward(string $id): EntityInterface
    {
        return $this->findOr404('EmsFeeAwards', $id, Messages::AWARD_NOT_FOUND);
    }
}
