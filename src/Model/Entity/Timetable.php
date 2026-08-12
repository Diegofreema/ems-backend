<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Timetable Entity
 *
 * @property int $id
 * @property int $session_id
 * @property int $department_id
 * @property int $level_id
 * @property int $semester_id
 * @property string $timetable
 * @property \Cake\I18n\FrozenTime $dateadded
 *
 * @property \App\Model\Entity\Session $session
 * @property \App\Model\Entity\Department $department
 * @property \App\Model\Entity\Level $level
 * @property \App\Model\Entity\Semester $semester
 */
class Timetable extends Entity
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
        'session_id' => true,
        'department_id' => true,
        'level_id' => true,
        'semester_id' => true,
        'timetable' => true,
        'dateadded' => true,
        'session' => true,
        'department' => true,
        'level' => true,
        'semester' => true,
    ];
}
