<?php
declare(strict_types=1);

namespace App\Ems\Serializer;

use Cake\Datasource\EntityInterface;

/**
 * Wire shapes for classes, allocations, timetable and registers
 * (document.md §3.12). Owns the shared period grid both sides must agree on.
 */
final class ClassSerializer
{
    /**
     * The shared constant: period number → [start, end] (§3.12).
     * Breaks (10:15–10:35 and 12:05–12:45) are not periods.
     *
     * @var array<int, array{0:string, 1:string}>
     */
    public const PERIODS = [
        1 => ['08:00', '08:45'],
        2 => ['08:45', '09:30'],
        3 => ['09:30', '10:15'],
        4 => ['10:35', '11:20'],
        5 => ['11:20', '12:05'],
        6 => ['12:45', '13:30'],
        7 => ['13:30', '14:15'],
    ];

    public static function group(EntityInterface $c): array
    {
        return [
            'id' => (string)$c->id,
            'schoolId' => (string)$c->school_id,
            'name' => (string)$c->name,
            'level' => (string)$c->level,
            'stream' => (string)$c->stream,
            'formTeacherId' => $c->form_teacher_id === null ? null : (string)$c->form_teacher_id,
            'capacity' => (int)$c->capacity,
        ];
    }

    /**
     * ClassSummary = ClassGroup + studentCount + formTeacherName.
     */
    public static function summary(EntityInterface $c, int $studentCount, ?string $formTeacherName): array
    {
        return self::group($c) + [
            'studentCount' => $studentCount,
            'formTeacherName' => $formTeacherName,
        ];
    }

    /**
     * AllocationView — teacherName falls back to "Unassigned" (§3.12).
     */
    public static function allocation(EntityInterface $a, ?string $teacherName): array
    {
        return [
            'id' => (string)$a->id,
            'schoolId' => (string)$a->school_id,
            'classGroupId' => (string)$a->class_group_id,
            'subject' => (string)$a->subject,
            'subjectId' => (string)$a->subject_id,
            'teacherId' => $a->teacher_id === null ? null : (string)$a->teacher_id,
            'teacherName' => $teacherName ?? 'Unassigned',
        ];
    }

    /**
     * TimetableSlotView.
     */
    public static function slot(EntityInterface $s, ?string $teacherName): array
    {
        return [
            'id' => (string)$s->id,
            'schoolId' => (string)$s->school_id,
            'classGroupId' => (string)$s->class_group_id,
            'day' => (string)$s->day,
            'period' => (int)$s->period,
            'subject' => (string)$s->subject,
            'subjectId' => (string)$s->subject_id,
            'teacherId' => $s->teacher_id === null ? null : (string)$s->teacher_id,
            'teacherName' => $teacherName ?? 'Unassigned',
        ];
    }

    /**
     * AttendanceSession — one submitted register per class per date; the
     * corrections JSON trail rides along verbatim.
     */
    public static function attendanceSession(EntityInterface $s): array
    {
        $corrections = $s->corrections;
        if (is_string($corrections)) {
            $corrections = json_decode($corrections, true);
        }

        return [
            'id' => (string)$s->id,
            'schoolId' => (string)$s->school_id,
            'classGroupId' => (string)$s->class_group_id,
            'date' => Wire::date($s->date),
            'submittedBy' => (string)$s->submitted_by,
            'submittedOn' => Wire::datetime($s->submitted_on),
            'corrections' => is_array($corrections) ? array_values($corrections) : [],
        ];
    }

    private function __construct()
    {
    }
}
