<?php
declare(strict_types=1);

namespace App\Ems\Serializer;

use Cake\Datasource\EntityInterface;

/**
 * Wire shapes for the admissions module (document.md §3.6). Hand-written
 * camelCase whitelists. `guardian` and `offer` are stored as JSON value
 * objects already in camelCase, so they round-trip untouched.
 */
final class AdmissionSerializer
{
    public static function cycle(EntityInterface $c): array
    {
        return [
            'id' => (string)$c->id,
            'schoolId' => (string)$c->school_id,
            'name' => (string)$c->name,
            'session' => (string)$c->session,
            'opensOn' => Wire::date($c->opens_on),
            'closesOn' => Wire::date($c->closes_on),
            'status' => (string)$c->status,
        ];
    }

    public static function application(EntityInterface $a): array
    {
        $guardian = is_array($a->guardian) ? $a->guardian : [];
        $out = [
            'id' => (string)$a->id,
            'schoolId' => (string)$a->school_id,
            'cycleId' => (string)$a->cycle_id,
            'applicationNumber' => (string)$a->application_number,
            'firstName' => (string)$a->first_name,
            'lastName' => (string)$a->last_name,
            'dateOfBirth' => Wire::date($a->date_of_birth),
            'gender' => (string)$a->gender,
            'desiredLevel' => (string)$a->desired_level,
            'previousSchool' => (string)$a->previous_school,
            'guardian' => self::guardian($guardian),
            'note' => (string)$a->note,
            'submittedOn' => Wire::date($a->submitted_on),
            'status' => (string)$a->status,
        ];
        if (is_array($a->offer) && $a->offer !== []) {
            $out['offer'] = [
                'madeOn' => (string)($a->offer['madeOn'] ?? ''),
                'expiresOn' => (string)($a->offer['expiresOn'] ?? ''),
                'note' => (string)($a->offer['note'] ?? ''),
            ];
        }
        if (!empty($a->student_id)) {
            $out['studentId'] = (string)$a->student_id;
        }

        return $out;
    }

    /** @param array<string, mixed> $g */
    public static function guardian(array $g): array
    {
        return [
            'firstName' => (string)($g['firstName'] ?? ''),
            'lastName' => (string)($g['lastName'] ?? ''),
            'relationship' => (string)($g['relationship'] ?? 'guardian'),
            'phone' => (string)($g['phone'] ?? ''),
            'email' => (string)($g['email'] ?? ''),
            'occupation' => (string)($g['occupation'] ?? ''),
        ];
    }

    public static function review(EntityInterface $r): array
    {
        return [
            'id' => (string)$r->id,
            'schoolId' => (string)$r->school_id,
            'applicationId' => (string)$r->application_id,
            'reviewer' => (string)$r->reviewer,
            'action' => (string)$r->action,
            'note' => (string)$r->note,
            'decidedOn' => Wire::date($r->decided_on),
        ];
    }
}
