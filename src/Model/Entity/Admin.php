<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Admin Entity
 *
 * @property int $id
 * @property int $user_id
 * @property string $surname
 * @property string $lastname
 * @property string $status
 * @property \Cake\I18n\FrozenTime $date_created
 * @property string|null $adminphoto
 * @property string $gender
 * @property int $department_id
 * @property string $phone
 * @property string $address
 * @property string|null $dob
 * @property string|null $profile
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Department $department
 * @property \App\Model\Entity\Privilege[] $privileges
 */
class Admin extends Entity
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
        'user_id' => true,
        'surname' => true,
        'lastname' => true,
        'status' => true,
        'date_created' => true,
        'adminphoto' => true,
        'gender' => true,
        'department_id' => true,
        'phone' => true,
        'address' => true,
        'dob' => true,
        'profile' => true,
        'user' => true,
        'department' => true,
        'privileges' => true,
    ];
}
