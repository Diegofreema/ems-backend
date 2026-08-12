<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Country Entity
 *
 * @property int $id
 * @property string $sortname
 * @property string $name
 * @property int $phonecode
 * @property string $cost
 *
 * @property \App\Model\Entity\Dstudent[] $dstudents
 * @property \App\Model\Entity\State[] $states
 * @property \App\Model\Entity\Student[] $students
 * @property \App\Model\Entity\Teacher[] $teachers
 * @property \App\Model\Entity\Trequest[] $trequests
 * @property \App\Model\Entity\User[] $users
 */
class Country extends Entity
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
        'sortname' => true,
        'name' => true,
        'phonecode' => true,
        'cost' => true,
        'dstudents' => true,
        'states' => true,
        'students' => true,
        'teachers' => true,
        'trequests' => true,
        'users' => true,
    ];
}
