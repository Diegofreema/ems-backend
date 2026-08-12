<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * State Entity
 *
 * @property int $id
 * @property string $name
 * @property int $country_id
 *
 * @property \App\Model\Entity\Country $country
 * @property \App\Model\Entity\Dstudent[] $dstudents
 * @property \App\Model\Entity\Lga[] $lgas
 * @property \App\Model\Entity\Student[] $students
 * @property \App\Model\Entity\Teacher[] $teachers
 * @property \App\Model\Entity\Trequest[] $trequests
 * @property \App\Model\Entity\User[] $users
 */
class State extends Entity
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
        'name' => true,
        'country_id' => true,
        'country' => true,
        'dstudents' => true,
        'lgas' => true,
        'students' => true,
        'teachers' => true,
        'trequests' => true,
        'users' => true,
    ];
}
