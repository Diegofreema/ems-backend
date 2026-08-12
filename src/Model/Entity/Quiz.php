<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Quiz Entity
 *
 * @property int $id
 * @property int $faculty_id
 * @property int $department_id
 * @property int $semester_id
 * @property int $session_id
 * @property int $subject_id
 * @property string|null $quizname
 *
 * @property \App\Model\Entity\Faculty $faculty
 * @property \App\Model\Entity\Department $department
 * @property \App\Model\Entity\Semester $semester
 * @property \App\Model\Entity\Session $session
 * @property \App\Model\Entity\Subject $subject
 * @property \App\Model\Entity\Quizquestion[] $quizquestions
 */
class Quiz extends Entity
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
        'faculty_id' => true,
        'department_id' => true,
        'semester_id' => true,
        'session_id' => true,
        'subject_id' => true,
        'quizname' => true,
        'faculty' => true,
        'department' => true,
        'semester' => true,
        'session' => true,
        'subject' => true,
        'quizquestions' => true,
    ];
}
