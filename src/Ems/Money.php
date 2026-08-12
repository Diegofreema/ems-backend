<?php
declare(strict_types=1);

namespace App\Ems;

/**
 * Every money judgement the fees module makes, in pure functions over plain
 * values (document.md §3.7 "Core arithmetic"). A faithful port of the frontend's
 * `features/fees/money.ts` plus the derivations in `fees.service.ts`, so the
 * invoice screen and the financial report can never disagree about what an award
 * is worth or when a bill is late.
 *
 * Nothing here reads the database or the clock — `today` is always passed in.
 * All amounts are integer **kobo**. Rounding replicates JavaScript `Math.round`
 * (round half up) via floor(v + 0.5): every value rounded here is non-negative
 * (award bases, instalment splits, ingested amounts), so this agrees with
 * Math.round exactly and never diverges the way PHP's round() can.
 */
final class Money
{
    /** `all` is not a fee name, so it can never collide with a real charge. */
    public const ALL_ITEMS = 'all';

    /** "2025/2026" + First → "2526T1" — the invoice-number term segment. */
    private const TERM_LETTER = ['First' => 'T1', 'Second' => 'T2', 'Third' => 'T3'];

    /**
     * JS Math.round replicated exactly for non-negative values.
     *
     * @param int|float $v
     */
    public static function jsRound($v): int
    {
        return (int)floor((float)$v + 0.5);
    }

    // --- lines --------------------------------------------------------------

    /**
     * What the school is charging, before anything comes off.
     *
     * @param array<int, array<string, mixed>> $lines
     */
    public static function chargeTotal(array $lines): int
    {
        $sum = 0;
        foreach ($lines as $l) {
            if (($l['kind'] ?? 'charge') !== 'award') {
                $sum += (int)$l['amount'];
            }
        }

        return $sum;
    }

    /**
     * What the school took off. Zero or negative.
     *
     * @param array<int, array<string, mixed>> $lines
     */
    public static function awardTotal(array $lines): int
    {
        $sum = 0;
        foreach ($lines as $l) {
            if (($l['kind'] ?? null) === 'award') {
                $sum += (int)$l['amount'];
            }
        }

        return $sum;
    }

    /**
     * How an award reads on a bill (money.ts awardLineLabel). The award is a
     * FeeAward wire array.
     *
     * @param array<string, mixed> $award
     */
    public static function awardLineLabel(array $award): string
    {
        $isAll = ($award['appliesToItem'] ?? self::ALL_ITEMS) === self::ALL_ITEMS;
        $target = $isAll ? 'the bill' : (string)$award['appliesToItem'];
        if (($award['basis'] ?? '') === 'percentage') {
            return sprintf('%s (%s%% of %s)', $award['name'], $award['value'], $target);
        }

        return $isAll ? (string)$award['name'] : sprintf('%s (off %s)', $award['name'], $target);
    }

    /**
     * Comparator: a personal scholarship gets first claim on a fee, then
     * level-wide discounts oldest-first, then by name (money.ts awardOrder).
     *
     * @param array<string, mixed> $a
     * @param array<string, mixed> $b
     */
    public static function awardOrder(array $a, array $b): int
    {
        if (($a['scope'] ?? '') !== ($b['scope'] ?? '')) {
            return ($a['scope'] ?? '') === 'student' ? -1 : 1;
        }
        $byDate = strcmp((string)($a['awardedOn'] ?? ''), (string)($b['awardedOn'] ?? ''));

        return $byDate !== 0 ? $byDate : strcmp((string)($a['name'] ?? ''), (string)($b['name'] ?? ''));
    }

    /**
     * Put the awards onto a set of charges (money.ts applyAwards). Awards never
     * compound (each worked out on the ORIGINAL charge) and the bill floors at
     * zero. `$charges` are cleaned charge lines; `$awards` are FeeAward wire
     * arrays. Returns ['lineItems' => [...], 'applied' => [...]].
     *
     * @param array<int, array<string, mixed>> $charges
     * @param array<int, array<string, mixed>> $awards
     * @return array{lineItems: array<int, array<string, mixed>>, applied: array<int, array<string, mixed>>}
     */
    public static function applyAwards(array $charges, array $awards): array
    {
        $charged = self::chargeTotal($charges);
        $lineItems = [];
        foreach ($charges as $c) {
            $lineItems[] = ['name' => (string)$c['name'], 'amount' => (int)$c['amount'], 'kind' => 'charge'];
        }
        $applied = [];
        $taken = 0;

        $sorted = $awards;
        usort($sorted, [self::class, 'awardOrder']);
        foreach ($sorted as $award) {
            if (($award['appliesToItem'] ?? self::ALL_ITEMS) === self::ALL_ITEMS) {
                $base = $charged;
            } else {
                $base = 0;
                $needle = strtolower((string)$award['appliesToItem']);
                foreach ($charges as $c) {
                    if (strtolower((string)$c['name']) === $needle) {
                        $base += (int)$c['amount'];
                    }
                }
            }
            // The award names a fee this bill does not carry: worth nothing here.
            if ($base <= 0) {
                continue;
            }
            $raw = ($award['basis'] ?? '') === 'percentage'
                ? self::jsRound($base * (int)$award['value'] / 100)
                : self::jsRound((int)$award['value']);
            $amount = min($raw, $base, $charged - $taken);
            if ($amount <= 0) {
                continue;
            }
            $taken += $amount;
            $applied[] = ['award' => $award, 'amount' => $amount, 'base' => $base];
            $lineItems[] = [
                'name' => self::awardLineLabel($award),
                'amount' => -$amount,
                'kind' => 'award',
                'awardId' => (string)$award['id'],
            ];
        }

        return ['lineItems' => $lineItems, 'applied' => $applied];
    }

    // --- schedules ----------------------------------------------------------

    /**
     * How much of each instalment has been met (money.ts instalmentStates).
     * Money settles the oldest instalment first.
     *
     * @param array<int, array<string, mixed>> $instalments
     * @return array<int, array<string, mixed>>
     */
    public static function instalmentStates(array $instalments, int $paid, string $today): array
    {
        $consumed = 0;
        $out = [];
        foreach ($instalments as $inst) {
            $amount = (int)$inst['amount'];
            $paidHere = max(0, min($amount, $paid - $consumed));
            $consumed += $amount;
            $balance = $amount - $paidHere;
            if ($balance <= 0) {
                $status = 'paid';
            } elseif ((string)$inst['dueOn'] < $today) {
                $status = 'overdue';
            } elseif ($paidHere > 0) {
                $status = 'part_paid';
            } else {
                $status = 'upcoming';
            }
            $out[] = [
                'number' => (int)$inst['number'],
                'label' => (string)$inst['label'],
                'dueOn' => (string)$inst['dueOn'],
                'amount' => $amount,
                'paid' => $paidHere,
                'balance' => $balance,
                'status' => $status,
            ];
        }

        return $out;
    }

    /**
     * The oldest instalment still owing (money.ts nextDueInstalment).
     *
     * @param array<int, array<string, mixed>> $states
     * @return array<string, mixed>|null
     */
    public static function nextDueInstalment(array $states): ?array
    {
        foreach ($states as $s) {
            if ((int)$s['balance'] > 0) {
                return $s;
            }
        }

        return null;
    }

    // --- status -------------------------------------------------------------

    /**
     * The derived collection state of an invoice (money.ts paymentStatusFor).
     *
     * @param array<string, mixed> $invoice total|dueDate|instalments|status
     */
    public static function paymentStatusFor(array $invoice, int $paid, string $today): string
    {
        if (($invoice['status'] ?? '') === 'cancelled') {
            return 'cancelled';
        }
        if ((int)$invoice['total'] - $paid <= 0) {
            return 'paid';
        }
        $instalments = $invoice['instalments'] ?? null;
        if (is_array($instalments) && $instalments !== []) {
            foreach (self::instalmentStates($instalments, $paid, $today) as $s) {
                if ($s['status'] === 'overdue') {
                    return 'overdue';
                }
            }
        } elseif ((string)$invoice['dueDate'] < $today) {
            return 'overdue';
        }

        return $paid > 0 ? 'part_paid' : 'unpaid';
    }

    // --- numbering & display ------------------------------------------------

    /** "2025/2026" + First → "2526T1". */
    public static function termCode(string $session, string $term): string
    {
        $digits = preg_replace('/\D/', '', $session) ?? '';
        $short = strlen($digits) >= 8
            ? substr($digits, 2, 2) . substr($digits, 6, 2)
            : substr($digits, -4);

        return $short . (self::TERM_LETTER[$term] ?? 'T1');
    }

    /** "JSS 1A" → "JSS 1": the level a class group belongs to. */
    public static function levelOf(string $classGroup): string
    {
        return trim(substr($classGroup, 0, -1));
    }

    /**
     * Kobo → display Naira, matching format.ts formatCurrency exactly:
     * 9000000 → "₦90,000"; 833325 → "₦8,333.25"; -50000 → "-₦500". Used in the
     * verbatim error messages and audit summaries, so it must match to the byte.
     *
     * @param int|float $amount
     */
    public static function formatCurrency($amount): string
    {
        $sign = $amount < 0 ? '-' : '';
        $kobo = abs(self::jsRound($amount));
        $naira = intdiv($kobo, 100);
        $rem = $kobo % 100;

        return $sign . '₦' . number_format($naira, 0, '.', ',')
            . ($rem ? '.' . str_pad((string)$rem, 2, '0', STR_PAD_LEFT) : '');
    }

    private function __construct()
    {
    }
}
