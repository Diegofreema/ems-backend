<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Attendance Entity
 *
 * @property int $id
 * @property int $student_id
 * @property int $teacher_id
 * @property int $department_id
 * @property \Cake\I18n\FrozenDate $attendance_date
 * @property string $status
 * @property string|null $notes
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Student $student
 * @property \App\Model\Entity\Teacher $teacher
 * @property \App\Model\Entity\Department $department
 */
class Attendance extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected array $_accessible = [
        'student_id' => true,
        'teacher_id' => true,
        'department_id' => true,
        'class_arm_id' => true,
        'attendance_date' => true,
        'status' => true,
        'notes' => true,
        'created' => true,
        'modified' => true,
        'student' => true,
        'teacher' => true,
        'department' => true,
        'class_arm' => true,
    ];

    /**
     * Get status badge class for display
     *
     * @return string
     */
    public function getStatusBadgeClass()
    {
        switch ($this->status) {
            case 'present':
                return 'badge-success';
            case 'absent':
                return 'badge-danger';
            case 'late':
                return 'badge-warning';
            case 'excused':
                return 'badge-info';
            default:
                return 'badge-secondary';
        }
    }

    /**
     * Get status display text
     *
     * @return string
     */
    public function getStatusDisplay()
    {
        switch ($this->status) {
            case 'present':
                return 'Present';
            case 'absent':
                return 'Absent';
            case 'late':
                return 'Late';
            case 'excused':
                return 'Excused';
            default:
                return ucfirst($this->status);
        }
    }
}
