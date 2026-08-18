<?php
declare(strict_types=1);

namespace App\Ems\Serializer;

use Cake\Datasource\EntityInterface;

/**
 * Wire shapes for the students module (document.md §3.10). Hand-written
 * camelCase whitelists — extra columns can never leak.
 */
final class StudentSerializer
{
    public static function one(EntityInterface $s): array
    {
        return [
            'id' => (string)$s->id,
            'schoolId' => (string)$s->school_id,
            'admissionNumber' => (string)$s->admission_number,
            'firstName' => (string)$s->first_name,
            'lastName' => (string)$s->last_name,
            'dateOfBirth' => Wire::date($s->date_of_birth),
            'gender' => (string)$s->gender,
            'classId' => $s->class_group_id === null ? null : (string)$s->class_group_id,
            'classGroup' => (string)$s->class_group,
            'status' => (string)$s->status,
            'guardianName' => (string)$s->guardian_name,
            'guardianPhone' => (string)$s->guardian_phone,
            'enrolledOn' => Wire::date($s->enrolled_on),
        ];
    }

    public static function guardian(EntityInterface $g): array
    {
        return [
            'id' => (string)$g->id,
            'schoolId' => (string)$g->school_id,
            'studentId' => (string)$g->student_id,
            'firstName' => (string)$g->first_name,
            'lastName' => (string)$g->last_name,
            'relationship' => (string)$g->relationship,
            'phone' => (string)$g->phone,
            'email' => (string)$g->email,
            'occupation' => (string)$g->occupation,
            'isPrimary' => (bool)$g->is_primary,
        ];
    }

    public static function enrolment(EntityInterface $e): array
    {
        $out = [
            'id' => (string)$e->id,
            'schoolId' => (string)$e->school_id,
            'studentId' => (string)$e->student_id,
            'session' => (string)$e->session,
            'classGroup' => (string)$e->class_group,
            'level' => (string)$e->level,
            'startedOn' => Wire::date($e->started_on),
            'status' => (string)$e->status,
        ];
        if ($e->ended_on !== null) {
            $out['endedOn'] = Wire::date($e->ended_on);
        }
        if ($e->outcome !== null) {
            $out['outcome'] = (string)$e->outcome;
        }
        if ($e->promoted_to !== null) {
            $out['promotedTo'] = (string)$e->promoted_to;
        }
        if ($e->average !== null) {
            $out['average'] = Wire::decimal($e->average);
        }

        return $out;
    }

    public static function attendanceRecord(EntityInterface $r): array
    {
        $out = [
            'id' => (string)$r->id,
            'schoolId' => (string)$r->school_id,
            'studentId' => (string)$r->student_id,
            'date' => Wire::date($r->date),
            'status' => (string)$r->status,
        ];
        if ($r->note !== null && $r->note !== '') {
            $out['note'] = (string)$r->note;
        }

        return $out;
    }

    /**
     * Derived on read, never stored (§3.10). rate = present ÷ total, 0..1.
     *
     * @param array<\Cake\Datasource\EntityInterface> $records
     */
    public static function attendanceSummary(array $records): array
    {
        $counts = ['present' => 0, 'absent' => 0, 'late' => 0, 'excused' => 0];
        foreach ($records as $r) {
            $status = (string)$r->status;
            if (isset($counts[$status])) {
                $counts[$status]++;
            }
        }
        $total = count($records);

        return [
            'present' => $counts['present'],
            'absent' => $counts['absent'],
            'late' => $counts['late'],
            'excused' => $counts['excused'],
            'total' => $total,
            'rate' => $total === 0 ? 0 : $counts['present'] / $total,
        ];
    }

    public static function academicRecord(EntityInterface $a): array
    {
        return [
            'id' => (string)$a->id,
            'schoolId' => (string)$a->school_id,
            'studentId' => (string)$a->student_id,
            'session' => (string)$a->session,
            'term' => (string)$a->term,
            'classGroup' => (string)$a->class_group,
            'average' => Wire::decimal($a->average),
            'position' => (int)$a->position,
            'classSize' => (int)$a->class_size,
            'remark' => (string)$a->remark,
        ];
    }

    private function __construct()
    {
    }
}
