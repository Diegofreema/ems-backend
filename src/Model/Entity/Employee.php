<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Employee Entity
 *
 * @property int $id
 * @property string $fname
 * @property string $mname
 * @property string $sname
 * @property string $empid
 * @property int $state_id
 * @property int $lga_id
 * @property string $address
 * @property string $phone
 * @property string|null $photo
 * @property string $dod
 * @property string $hqn
 * @property int $staffgrade_id
 * @property int $staffdepartment_id
 * @property string|null $profile
 * @property int $user_id
 * @property string $gender
 * @property \Cake\I18n\FrozenTime|null $dateadded
 * @property int $admin_id
 *
 * @property \App\Model\Entity\State $state
 * @property \App\Model\Entity\Lga $lga
 * @property \App\Model\Entity\Staffgrade $staffgrade
 * @property \App\Model\Entity\Staffdepartment $staffdepartment
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Admin $admin
 */
class Employee extends Entity
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
        'fname' => true,
        'mname' => true,
        'sname' => true,
        'empid' => true,
        'state_id' => true,
        'lga_id' => true,
        'address' => true,
        'phone' => true,
        'payslip_id' => true,
        'photo' => true,
        'dod' => true,
        'hqn' => true,
        'staffgrade_id' => true,
        'staffdepartment_id' => true,
        'profile' => true,
        'user_id' => true,
        'gender' => true,
        'dateadded' => true,
        'admin_id' => true,
        'state' => true,
        'lga' => true,
        'staffgrade' => true,
        'staffdepartment' => true,
        'user' => true,
        'payslip' => true,
        'admin' => true,
    ];
}
