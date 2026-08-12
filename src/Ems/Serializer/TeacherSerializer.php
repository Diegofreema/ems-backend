<?php
declare(strict_types=1);

namespace App\Ems\Serializer;

use App\Ems\SubjectCatalog;
use Cake\Datasource\EntityInterface;

/**
 * Wire shape for the teachers module (document.md §3.11). `subjects` is stored
 * as a JSON array of catalogue ids; the wire keeps speaking NAMES (with the
 * ids alongside, additively).
 */
final class TeacherSerializer
{
    public static function one(EntityInterface $t): array
    {
        $subjects = $t->subjects;
        if (is_string($subjects)) {
            $subjects = json_decode($subjects, true);
        }
        $ids = is_array($subjects) ? array_values(array_map('strval', $subjects)) : [];
        $names = [];
        foreach ($ids as $sid) {
            $name = SubjectCatalog::name((string)$t->school_id, $sid);
            if ($name !== '') {
                $names[] = $name;
            }
        }

        return [
            'id' => (string)$t->id,
            'schoolId' => (string)$t->school_id,
            'staffNumber' => (string)$t->staff_number,
            'firstName' => (string)$t->first_name,
            'lastName' => (string)$t->last_name,
            'email' => (string)$t->email,
            'phone' => (string)$t->phone,
            'gender' => (string)$t->gender,
            'subjects' => $names,
            'subjectIds' => $ids,
            'status' => (string)$t->status,
            'hiredOn' => Wire::date($t->hired_on),
        ];
    }

    private function __construct()
    {
    }
}
