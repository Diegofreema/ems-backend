<?php
declare(strict_types=1);

namespace App\Ems\Serializer;

use Cake\Datasource\EntityInterface;

/**
 * Wire shapes for the fees module (document.md §3.7): fee structures, awards,
 * invoices, payments and refunds. The computed read models — InvoiceWithBalance,
 * InvoicePreview, StudentLedger, ReconciliationReport, FeesReport, Receipt — are
 * assembled by App\Ems\Fees, which layers derived figures on top of the stored
 * shapes below. Optional fields are OMITTED (never sent as null) so the bytes
 * match the mock's JSON.stringify, which drops `undefined`.
 */
final class FeeSerializer
{
    /**
     * One priced line. Charge lines carry kind:'charge'; award lines add awardId.
     *
     * @param array<string, mixed> $l
     * @return array<string, mixed>
     */
    public static function lineItem(array $l): array
    {
        $out = ['name' => (string)$l['name'], 'amount' => (int)$l['amount']];
        if (isset($l['kind'])) {
            $out['kind'] = (string)$l['kind'];
        }
        if (isset($l['awardId'])) {
            $out['awardId'] = (string)$l['awardId'];
        }

        return $out;
    }

    /**
     * @param array<int, array<string, mixed>> $lines
     * @return array<int, array<string, mixed>>
     */
    public static function lineItems(array $lines): array
    {
        return array_values(array_map([self::class, 'lineItem'], $lines));
    }

    /**
     * @param array<string, mixed> $i
     * @return array<string, mixed>
     */
    public static function instalment(array $i): array
    {
        return [
            'number' => (int)$i['number'],
            'label' => (string)$i['label'],
            'dueOn' => (string)$i['dueOn'],
            'amount' => (int)$i['amount'],
        ];
    }

    public static function structure(EntityInterface $s): array
    {
        $out = [
            'id' => (string)$s->id,
            'schoolId' => (string)$s->school_id,
            'session' => (string)$s->session,
            'term' => (string)$s->term,
            'level' => (string)$s->level,
            'items' => self::lineItems(self::decode($s->items)),
            'total' => (int)$s->total,
        ];
        $schedule = self::decode($s->schedule);
        if (is_array($schedule) && $schedule !== []) {
            $out['schedule'] = array_values(array_map(static fn($r) => [
                'label' => (string)$r['label'],
                'dueOn' => (string)$r['dueOn'],
                'percent' => (int)$r['percent'],
            ], $schedule));
        }

        return $out;
    }

    public static function award(EntityInterface $a): array
    {
        $out = [
            'id' => (string)$a->id,
            'schoolId' => (string)$a->school_id,
            'name' => (string)$a->name,
            'kind' => (string)$a->kind,
            'basis' => (string)$a->basis,
            'value' => (int)$a->value,
            'appliesToItem' => (string)$a->applies_to_item,
            'scope' => (string)$a->scope,
        ];
        if ((string)$a->scope === 'student') {
            $out['studentId'] = (string)$a->student_id;
            if ($a->student_name !== null) {
                $out['studentName'] = (string)$a->student_name;
            }
        } elseif ($a->level !== null) {
            $out['level'] = (string)$a->level;
        }
        $out['session'] = (string)$a->session;
        $out['term'] = (string)$a->term;
        $out['status'] = (string)$a->status;
        if ($a->note !== null && (string)$a->note !== '') {
            $out['note'] = (string)$a->note;
        }
        $out['awardedBy'] = (string)$a->awarded_by;
        $out['awardedOn'] = Wire::date($a->awarded_on);
        if ($a->ended_on !== null) {
            $out['endedOn'] = Wire::date($a->ended_on);
        }
        if ($a->ended_reason !== null && (string)$a->ended_reason !== '') {
            $out['endedReason'] = (string)$a->ended_reason;
        }

        return $out;
    }

    /**
     * The stored invoice (no derived balance — that is Fees::enrich's job).
     */
    public static function invoice(EntityInterface $i): array
    {
        $out = [
            'id' => (string)$i->id,
            'schoolId' => (string)$i->school_id,
            'invoiceNumber' => (string)$i->invoice_number,
            'studentId' => (string)$i->student_id,
            'studentName' => (string)$i->student_name,
            'classGroup' => (string)$i->class_group,
            'session' => (string)$i->session,
            'term' => (string)$i->term,
            'issuedOn' => Wire::date($i->issued_on),
            'dueDate' => Wire::date($i->due_date),
            'lineItems' => self::lineItems(self::decode($i->line_items)),
            'total' => (int)$i->total,
            'status' => (string)$i->status,
        ];
        $instalments = self::decode($i->instalments);
        if (is_array($instalments) && $instalments !== []) {
            $out['instalments'] = array_values(array_map([self::class, 'instalment'], $instalments));
        }
        $revisions = self::decode($i->schedule_revisions);
        if (is_array($revisions) && $revisions !== []) {
            $out['scheduleRevisions'] = array_values(array_map(static fn($r) => [
                'revisedOn' => (string)$r['revisedOn'],
                'revisedBy' => (string)$r['revisedBy'],
                'agreedWith' => (string)$r['agreedWith'],
                'reason' => (string)$r['reason'],
                'previous' => array_values(array_map([self::class, 'instalment'], $r['previous'] ?? [])),
            ], $revisions));
        }
        if ($i->cancelled_on !== null) {
            $out['cancelledOn'] = Wire::date($i->cancelled_on);
            $out['cancellationReason'] = (string)$i->cancellation_reason;
        }

        return $out;
    }

    public static function payment(EntityInterface $p): array
    {
        $out = [
            'id' => (string)$p->id,
            'schoolId' => (string)$p->school_id,
            'invoiceId' => (string)$p->invoice_id,
            'studentId' => (string)$p->student_id,
            'receiptNumber' => (string)$p->receipt_number,
            'amount' => (int)$p->amount,
            'method' => (string)$p->method,
        ];
        if ($p->reference !== null && (string)$p->reference !== '') {
            $out['reference'] = (string)$p->reference;
        }
        $out['paidOn'] = Wire::date($p->paid_on);
        if ($p->note !== null && (string)$p->note !== '') {
            $out['note'] = (string)$p->note;
        }
        $out['state'] = (string)$p->state;
        if ($p->reversed_on !== null) {
            $out['reversedOn'] = Wire::date($p->reversed_on);
            $out['reversalReason'] = (string)$p->reversal_reason;
        }
        if ($p->channel !== null && (string)$p->channel !== '') {
            $out['channel'] = (string)$p->channel;
        }
        if ($p->failed_on !== null) {
            $out['failedOn'] = Wire::date($p->failed_on);
            $out['failureReason'] = (string)$p->failure_reason;
        }

        return $out;
    }

    public static function refund(EntityInterface $r): array
    {
        $out = [
            'id' => (string)$r->id,
            'schoolId' => (string)$r->school_id,
            'paymentId' => (string)$r->payment_id,
            'invoiceId' => (string)$r->invoice_id,
            'studentId' => (string)$r->student_id,
            'amount' => (int)$r->amount,
            'reason' => (string)$r->reason,
            'status' => (string)$r->status,
            'requestedBy' => (string)$r->requested_by,
            'requestedOn' => Wire::date($r->requested_on),
        ];
        if ($r->decided_by !== null) {
            $out['decidedBy'] = (string)$r->decided_by;
        }
        if ($r->decided_on !== null) {
            $out['decidedOn'] = Wire::date($r->decided_on);
        }
        if ($r->decision_note !== null && (string)$r->decision_note !== '') {
            $out['decisionNote'] = (string)$r->decision_note;
        }

        return $out;
    }

    /**
     * MySQL JSON comes back as an array already, but be defensive about strings.
     *
     * @param mixed $value
     * @return array<int|string, mixed>|null
     */
    private static function decode(mixed $value): ?array
    {
        if ($value === null) {
            return null;
        }
        if (is_string($value)) {
            $decoded = json_decode($value, true);

            return is_array($decoded) ? $decoded : null;
        }

        return is_array($value) ? $value : null;
    }

    private function __construct()
    {
    }
}
