<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Examquestion Entity
 *
 * @property int $id
 * @property int $subject_id
 * @property string $question
 * @property string $op1
 * @property string $op2
 * @property string $op3
 * @property string $op4
 * @property string $correctans
 * @property int|null $mark
 * @property \Cake\I18n\FrozenTime $dateadded
 * @property int $admin_id
 * @property int $exam_id
 * @property int $department_id
 * @property int $level_id
 * @property int $faculty_id
 *
 * @property \App\Model\Entity\Subject $subject
 * @property \App\Model\Entity\Admin $admin
 * @property \App\Model\Entity\Exam $exam
 * @property \App\Model\Entity\Department $department
 * @property \App\Model\Entity\Level $level
 * @property \App\Model\Entity\Attemptedexamquestion[] $attemptedexamquestions
 */
class Examquestion extends Entity
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
        'subject_id' => true,
        'question' => true,
        'op1' => true,
        'op2' => true,
        'op3' => true,
        'op4' => true,
        'correctans' => true,
        'mark' => true,
        'dateadded' => true,
        'admin_id' => true,
        'exam_id' => true,
        'department_id' => true,
        'level_id' => true,
        'faculty_id' => true,
        'subject' => true,
        'admin' => true,
        'exam' => true,
        'department' => true,
        'level' => true,
        'attemptedexamquestions' => true,
    ];
}
