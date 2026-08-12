<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Courseassignment Entity
 *
 * @property int $id
 * @property int $department_id
 * @property int $semester_id
 * @property int $level_id
 * @property \Cake\I18n\FrozenTime $assignedon
 * @property string|null $updatedon
 * @property int $user_id
 *
 * @property \App\Model\Entity\Department $department
 * @property \App\Model\Entity\Semester $semester
 * @property \App\Model\Entity\Level $level
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Subject[] $subjects
 */
class Courseassignment extends Entity
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
        'department_id' => true,
        'semester_id' => true,
        'level_id' => true,
        'assignedon' => true,
        'updatedon' => true,
        'user_id' => true,
        'department' => true,
        'semester' => true,
        'level' => true,
        'user' => true,
        'subjects' => true,
    ];
}
