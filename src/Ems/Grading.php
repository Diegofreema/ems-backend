<?php
declare(strict_types=1);

namespace App\Ems;

use Cake\Datasource\EntityInterface;
use Cake\I18n\FrozenDate;
use Cake\ORM\Locator\LocatorInterface;

/**
 * The grading-scheme authority (document.md §3.3), a port of the frontend's
 * grading.service.ts. It answers the single question every academics read
 * depends on: WHICH scale grades a total.
 *
 *  - a school always has an active scale — the synthesised national default
 *    (version 1, "System") when it has never saved its own;
 *  - a published exam is graded on the scheme version PINNED to its released
 *    row, so editing the scale later never rewrites an issued report card;
 *  - a version is immutable: an edit retires the active row and appends a new
 *    version (never mutates in place), which is what keeps releases durable.
 */
class Grading
{
    /**
     * The scale a school starts on and the fallback if it somehow has none.
     * Tones match the frontend DEFAULT_GRADE_BANDS verbatim (A/B success,
     * C info, D/E warning, F destructive) — the pill colour is part of the wire.
     *
     * @var array<int, array{letter:string, min:int, label:string, tone:string}>
     */
    public const DEFAULT_BANDS = [
        ['letter' => 'A', 'min' => 70, 'label' => 'Excellent', 'tone' => 'text-success'],
        ['letter' => 'B', 'min' => 60, 'label' => 'Very good', 'tone' => 'text-success'],
        ['letter' => 'C', 'min' => 50, 'label' => 'Credit', 'tone' => 'text-info'],
        ['letter' => 'D', 'min' => 45, 'label' => 'Pass', 'tone' => 'text-warning'],
        ['letter' => 'E', 'min' => 40, 'label' => 'Fair', 'tone' => 'text-warning'],
        ['letter' => 'F', 'min' => 0, 'label' => 'Fail', 'tone' => 'text-destructive'],
    ];

    /** @var \Cake\ORM\Locator\LocatorInterface */
    private $locator;

    /** @var string */
    private $schoolId;

    /** @var \App\Ems\Tenant|null */
    private $tenantScope;

    public function __construct(LocatorInterface $locator, string $schoolId)
    {
        $this->locator = $locator;
        $this->schoolId = $schoolId;
    }

    /**
     * This engine's tenant-scope choke point — reads narrowed to $this->schoolId
     * by construction. See App\Ems\Tenant.
     */
    private function tenant(): Tenant
    {
        return $this->tenantScope ??= new Tenant($this->locator, $this->schoolId);
    }

    /**
     * The scale in force today — the active row, or the synthesised default so
     * a school is never left without a scale.
     *
     * @return array{id:string, schoolId:string, version:int, status:string, bands:array, effectiveFrom:?string, createdBy:string, note?:string}
     */
    public function activeScheme(): array
    {
        $active = $this->tenant()->query('EmsGradingSchemes')
            ->where(['status' => 'active'])
            ->first();
        if ($active !== null) {
            return self::schemeWire($active);
        }

        return [
            'id' => 'grs_' . $this->schoolId . '_default',
            'schoolId' => $this->schoolId,
            'version' => 1,
            'status' => 'active',
            'bands' => self::cleanBands(self::DEFAULT_BANDS),
            'effectiveFrom' => FrozenDate::today()->format('Y-m-d'),
            'createdBy' => 'System',
        ];
    }

    /**
     * The scale one examination is graded on. A published exam pins the scheme
     * version its results were released with; anything unpublished grades on the
     * current active scale.
     */
    public function schemeForExam(EntityInterface $exam): array
    {
        if ((string)$exam->status === 'published') {
            $release = $this->tenant()->query('EmsResultReleases')
                ->where([
                    'exam_id' => (string)$exam->id,
                    'status' => 'released',
                ])
                ->first();
            if ($release !== null && $release->scheme_version !== null) {
                $pinned = $this->tenant()->query('EmsGradingSchemes')
                    ->where([
                        'version' => (int)$release->scheme_version,
                    ])
                    ->first();
                if ($pinned !== null) {
                    return self::schemeWire($pinned);
                }
            }
        }

        return $this->activeScheme();
    }

    /**
     * The band a 0..100 total falls into: the first band (in stored,
     * descending-min order) whose min the total clears; the last band is the
     * fallback so every mark has a grade.
     *
     * @param array<int, array> $bands
     * @return array{letter:string, min:int, label:string, tone:string}
     */
    public static function gradeFor(float $total, array $bands): array
    {
        foreach ($bands as $band) {
            if ($total >= $band['min']) {
                return $band;
            }
        }

        return $bands[count($bands) - 1];
    }

    /**
     * Reject a scale that could not grade every mark 0..100. Returns the first
     * plain-language failure, or null when sound (§3.3, order preserved).
     *
     * @param array<int, array> $bands
     */
    public static function validateBands(array $bands): ?string
    {
        if (count($bands) < 2) {
            return Messages::GRADING_MIN_BANDS;
        }
        foreach ($bands as $b) {
            $letter = trim((string)($b['letter'] ?? ''));
            $label = trim((string)($b['label'] ?? ''));
            $min = $b['min'] ?? null;
            if ($letter === '') {
                return Messages::GRADING_LETTER_REQUIRED;
            }
            if ($label === '') {
                return sprintf(Messages::GRADING_LABEL_REQUIRED, $letter !== '' ? $letter : '?');
            }
            // The frontend guard rejects a non-integer min; a whole float (45.0)
            // still passes because JSON has no integer type.
            if (!is_int($min) && !(is_numeric($min) && (float)$min == floor((float)$min))) {
                return sprintf(Messages::GRADING_MIN_RANGE, $letter !== '' ? $letter : '?');
            }
            $minInt = (int)$min;
            if ($minInt < 0 || $minInt > 100) {
                return sprintf(Messages::GRADING_MIN_RANGE, $letter !== '' ? $letter : '?');
            }
        }
        for ($i = 1; $i < count($bands); $i++) {
            if ((int)$bands[$i]['min'] >= (int)$bands[$i - 1]['min']) {
                return Messages::GRADING_DESCENDING;
            }
        }
        if ((int)$bands[count($bands) - 1]['min'] !== 0) {
            return Messages::GRADING_LAST_ZERO;
        }
        $letters = array_map(fn ($b) => strtolower(trim((string)$b['letter'])), $bands);
        if (count(array_unique($letters)) !== count($letters)) {
            return Messages::GRADING_DISTINCT_LETTERS;
        }

        return null;
    }

    /**
     * Two scales are identical when every band's letter/min/label/tone matches
     * in order — the no-op guard so an unchanged save never spends a version.
     *
     * @param array<int, array> $a
     * @param array<int, array> $b
     */
    public static function bandsEqual(array $a, array $b): bool
    {
        if (count($a) !== count($b)) {
            return false;
        }
        foreach ($a as $i => $x) {
            $y = $b[$i];
            if (
                (string)$x['letter'] !== (string)$y['letter'] ||
                (int)$x['min'] !== (int)$y['min'] ||
                (string)$x['label'] !== (string)$y['label'] ||
                (string)$x['tone'] !== (string)$y['tone']
            ) {
                return false;
            }
        }

        return true;
    }

    /**
     * Trim + retype a submitted band list into the canonical stored shape.
     *
     * @param array<int, array> $bands
     * @return array<int, array{letter:string, min:int, label:string, tone:string}>
     */
    public static function cleanBands(array $bands): array
    {
        $out = [];
        foreach ($bands as $b) {
            $out[] = [
                'letter' => trim((string)($b['letter'] ?? '')),
                'min' => (int)($b['min'] ?? 0),
                'label' => trim((string)($b['label'] ?? '')),
                'tone' => (string)($b['tone'] ?? ''),
            ];
        }

        return $out;
    }

    /**
     * A stored scheme row → the canonical wire shape, bands re-typed.
     */
    public static function schemeWire(EntityInterface $s): array
    {
        $bands = $s->bands;
        if (is_string($bands)) {
            $bands = json_decode($bands, true);
        }
        $out = [
            'id' => (string)$s->id,
            'schoolId' => (string)$s->school_id,
            'version' => (int)$s->version,
            'status' => (string)$s->status,
            'bands' => self::cleanBands(is_array($bands) ? $bands : []),
            'effectiveFrom' => Serializer\Wire::date($s->effective_from),
            'createdBy' => (string)$s->created_by,
        ];
        if ($s->note !== null && (string)$s->note !== '') {
            $out['note'] = (string)$s->note;
        }

        return $out;
    }
}
