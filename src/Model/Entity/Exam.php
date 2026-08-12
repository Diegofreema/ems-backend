<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Exam Entity
 *
 * @property int $id
 * @property int $semester_id
 * @property int $session_id
 * @property string $examname
 * @property string|null $examdate
 * @property string|null $examtime
 * @property \Cake\I18n\FrozenTime|null $dateadded
 * @property int $admin_id
 *
 * @property \App\Model\Entity\Department $department
 * @property \App\Model\Entity\Faculty $faculty
 * @property \App\Model\Entity\Semester $semester
 * @property \App\Model\Entity\Session $session
 * @property \App\Model\Entity\Admin $admin
 */
class Exam extends Entity
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
        'semester_id' => true,
        'session_id' => true,
        'examname' => true,
        'examdate' => true,
        'examtime' => true,
        'dateadded' => true,
        'admin_id' => true,
        'department' => true,
        'faculty' => true,
        'semester' => true,
        'session' => true,
        'admin' => true,
    ];
}
