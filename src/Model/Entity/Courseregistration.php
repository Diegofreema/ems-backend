<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Courseregistration Entity
 *
 * @property int $id
 * @property int $student_id
 * @property int $session_id
 * @property int $semester_id
 * @property int $level_id
 * @property \Cake\I18n\FrozenTime|null $date_created
 *
 * @property \App\Model\Entity\Student $student
 * @property \App\Model\Entity\Session $session
 * @property \App\Model\Entity\Semester $semester
 * @property \App\Model\Entity\Level $level
 * @property \App\Model\Entity\Subject[] $subjects
 */
class Courseregistration extends Entity
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
        'session_id' => true,
        'semester_id' => true,
        'level_id' => true,
        'date_created' => true,
        'student' => true,
        'session' => true,
        'semester' => true,
        'level' => true,
        'subjects' => true,
    ];
}
