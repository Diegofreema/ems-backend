<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ClassArm Entity
 *
 * @property int $id
 * @property int $department_id
 * @property string $arm_name
 * @property string|null $arm_description
 * @property int|null $class_teacher_id
 * @property string $status
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Department $department
 * @property \App\Model\Entity\Teacher $teacher
 * @property \App\Model\Entity\Student[] $students
 * @property \App\Model\Entity\Result[] $results
 * @property \App\Model\Entity\Attendance[] $attendances
 */
class ClassArm extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'department_id' => true,
        'arm_name' => true,
        'arm_description' => true,
        'class_teacher_id' => true,
        'status' => true,
        'created' => true,
        'modified' => true,
        'department' => true,
        'teacher' => true,
        'students' => true,
        'results' => true,
        'attendances' => true,
    ];

    /**
     * Get the full class name (e.g., "JSS 1A")
     *
     * @return string
     */
    public function getFullClassName(): string
    {
        if (!empty($this->department)) {
            return $this->department->name . ' ' . $this->arm_name;
        }

        return $this->arm_name;
    }

    /**
     * Get the class teacher's full name
     *
     * @return string|null
     */
    public function getClassTeacherName(): ?string
    {
        if (!empty($this->teacher) && !empty($this->teacher->user)) {
            return $this->teacher->user->fname . ' ' . $this->teacher->user->lname;
        }

        return null;
    }

    /**
     * Get student count for this class arm
     *
     * @return int
     */
    public function getStudentCount(): int
    {
        if (!empty($this->students)) {
            return count($this->students);
        }

        return 0;
    }
}
