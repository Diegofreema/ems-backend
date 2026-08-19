<?php
declare(strict_types=1);

namespace App\Ems;

use Cake\Datasource\EntityInterface;
use Cake\I18n\FrozenDate;
use Cake\ORM\Locator\LocatorInterface;

/**
 * Placement history (§3.10). Every path that lands an ENROLLED student on the
 * register — the manual form, atomic admission, and the CSV import — records
 * the same active enrolment row for the open session, so imported students
 * carry the placement history that promotion and transcripts read.
 */
final class Enrolments
{
    /**
     * Record an active enrolment for the open academic session. A no-op when
     * the student is not `enrolled` or no session is open (the school can
     * open one later; enrolment rows then start from the next placement).
     *
     * Level comes from the student's linked class row (admin-defined levels);
     * a still-unlinked student falls back to the class name minus its
     * trailing stream letter ("JSS 1A" → "JSS 1").
     */
    public static function createIfCurrent(
        LocatorInterface $locator,
        string $schoolId,
        EntityInterface $student,
    ): void {
        if ((string)$student->status !== 'enrolled') {
            return;
        }
        $session = $locator->get('EmsAcademicSessions')->find()
            ->where(['school_id' => $schoolId, 'status' => 'open'])
            ->orderByDesc('name')
            ->first();
        if ($session === null) {
            return;
        }

        $className = (string)$student->class_group;
        $level = $className === '' ? '' : trim(substr($className, 0, -1));
        $classGroupId = (string)($student->class_group_id ?? '');
        if ($classGroupId !== '') {
            $class = $locator->get('EmsClassGroups')->find()
                ->where(['school_id' => $schoolId, 'id' => $classGroupId])
                ->first();
            if ($class !== null) {
                $className = (string)$class->name;
                $level = (string)$class->level;
            }
        }

        $enrolments = $locator->get('EmsEnrolments');
        $enrolments->saveOrFail($enrolments->newEntity([
            'school_id' => $schoolId,
            'student_id' => (string)$student->id,
            'session' => (string)$session->name,
            'class_group' => $className,
            'level' => $level,
            'started_on' => FrozenDate::today()->format('Y-m-d'),
            'status' => 'active',
        ]));
    }

    /** Static utility class. */
    private function __construct()
    {
    }
}
