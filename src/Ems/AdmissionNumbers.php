<?php
declare(strict_types=1);

namespace App\Ems;

use Cake\ORM\Locator\LocatorInterface;

/**
 * The ONE admission-number generator (§3.17 / §3.6). Every path that mints a
 * number — admissions enrolment, CSV import, the manual student form — goes
 * through the same per-school `ems_sequences` counter, so two paths can never
 * hand out the same number. The prefix follows the school's existing style
 * (first-created student's prefix, else the short name's letters, else ADM),
 * and the counter is seeded above any numbers that predate the sequence.
 */
final class AdmissionNumbers
{
    /** e.g. "GFC/0042" */
    public static function next(LocatorInterface $locator, string $schoolId): string
    {
        $number = (new Sequences($locator))->next(
            $schoolId,
            'admission',
            self::maxSuffix($locator, $schoolId),
        );

        return sprintf('%s/%04d', self::prefix($locator, $schoolId), $number);
    }

    private static function prefix(LocatorInterface $locator, string $schoolId): string
    {
        $existing = $locator->get('EmsStudents')->find()
            ->select(['admission_number'])
            ->where(['school_id' => $schoolId])
            ->orderByAsc('created')
            ->first();
        if ($existing !== null && strpos((string)$existing->admission_number, '/') !== false) {
            return explode('/', (string)$existing->admission_number)[0];
        }
        $school = $locator->get('EmsSchools')->find()
            ->where(['id' => $schoolId])
            ->first();
        if ($school !== null && (string)$school->short_name !== '') {
            $letters = strtoupper((string)preg_replace('/[^A-Za-z]/', '', (string)$school->short_name));

            return substr($letters, 0, 3) ?: 'ADM';
        }

        return 'ADM';
    }

    private static function maxSuffix(LocatorInterface $locator, string $schoolId): int
    {
        $row = $locator->get('EmsStudents')->find()
            ->select(['m' => 'MAX(CAST(SUBSTRING_INDEX(admission_number, "/", -1) AS UNSIGNED))'])
            ->where(['school_id' => $schoolId])
            ->first();

        return (int)($row->m ?? 0);
    }

    /** Static utility class. */
    private function __construct()
    {
    }
}
