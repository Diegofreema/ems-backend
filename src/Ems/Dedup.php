<?php
declare(strict_types=1);

namespace App\Ems;

/**
 * One definition of "duplicate", shared by the CSV import (§3.17) and the
 * register merge (§3.15) so the two can never disagree on what counts as the
 * same person. The safeguard the spec names explicitly: a birth date on its own,
 * or a phone number on its own, is NEVER enough — households share phones and a
 * school of any size has birthday collisions. A match needs the name to agree,
 * or two weaker signals together, and even then it is only ever held for review.
 */
final class Dedup
{
    /** Case/punctuation-insensitive comparison key. */
    public static function norm(string $value): string
    {
        return trim((string)preg_replace('/[^a-z0-9]+/', ' ', mb_strtolower(trim($value))));
    }

    /** Compare on the last 10 digits, so 0803… and +234 803… are the same line. */
    public static function phoneKey(string $value): string
    {
        $digits = (string)preg_replace('/\D/', '', $value);

        return strlen($digits) > 10 ? substr($digits, -10) : $digits;
    }

    public static function editDistance(string $a, string $b): int
    {
        return levenshtein($a, $b);
    }

    /**
     * Score an incoming row's values against an existing person. First matching
     * rule wins; below 0.6 (or null) is not a candidate.
     *
     * @param array<string, string> $values keys: admission_number, first_name,
     *   last_name, date_of_birth, guardian_phone
     * @param array{firstName:string,lastName:string,dateOfBirth:string,phone:string,admissionNumber:string} $person
     * @return array{score: float, reasons: array<int, string>}|null
     */
    public static function scoreMatch(array $values, array $person): ?array
    {
        $admission = (string)($values['admission_number'] ?? '');
        $sameAdmission = $admission !== '' && self::norm($admission) === self::norm($person['admissionNumber']);
        $rowFirst = self::norm((string)($values['first_name'] ?? ''));
        $rowLast = self::norm((string)($values['last_name'] ?? ''));
        $sameName = $rowFirst === self::norm($person['firstName']) && $rowLast === self::norm($person['lastName']);
        $nearName = !$sameName
            && $rowLast === self::norm($person['lastName'])
            && self::editDistance($rowFirst, self::norm($person['firstName'])) <= 1;
        $dob = (string)($values['date_of_birth'] ?? '');
        $sameDob = $dob !== '' && $dob === $person['dateOfBirth'];
        $phone = self::phoneKey((string)($values['guardian_phone'] ?? ''));
        $samePhone = $phone !== '' && $phone === self::phoneKey($person['phone']);

        if ($sameAdmission) {
            return ['score' => 1.0, 'reasons' => ['Same admission number']];
        }
        if ($sameName && $sameDob) {
            return ['score' => 0.95, 'reasons' => ['Same name and date of birth']];
        }
        if ($sameName && $samePhone) {
            return ['score' => 0.9, 'reasons' => ['Same name and guardian phone number']];
        }
        if ($nearName && $sameDob && $samePhone) {
            return ['score' => 0.85, 'reasons' => ['Very similar name, same date of birth and same guardian phone number']];
        }
        if ($sameName) {
            return ['score' => 0.6, 'reasons' => ['Same name']];
        }
        if ($sameDob && $samePhone) {
            return ['score' => 0.5, 'reasons' => ['Same date of birth and guardian phone number, so this may be a sibling rather than the same child']];
        }

        return null;
    }

    /**
     * Score two register students against each other (§3.15). Distinct people
     * never share an admission number, so that strong signal never fires here.
     *
     * @param array{firstName:string,lastName:string,dateOfBirth:string,guardianPhone:string} $a
     * @param array{firstName:string,lastName:string,dateOfBirth:string,guardianPhone:string,admissionNumber:string} $b
     * @return array{score: float, reasons: array<int, string>}|null
     */
    public static function scorePair(array $a, array $b): ?array
    {
        return self::scoreMatch(
            [
                'first_name' => $a['firstName'],
                'last_name' => $a['lastName'],
                'date_of_birth' => $a['dateOfBirth'],
                'guardian_phone' => $a['guardianPhone'],
                'admission_number' => '',
            ],
            [
                'firstName' => $b['firstName'],
                'lastName' => $b['lastName'],
                'dateOfBirth' => $b['dateOfBirth'],
                'phone' => $b['guardianPhone'],
                'admissionNumber' => $b['admissionNumber'],
            ],
        );
    }

    private function __construct()
    {
    }
}
