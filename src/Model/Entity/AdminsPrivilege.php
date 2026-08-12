<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * AdminsPrivilege Entity
 *
 * @property int $id
 * @property int $admin_id
 * @property int $privilege_id
 *
 * @property \App\Model\Entity\Admin $admin
 * @property \App\Model\Entity\Privilege $privilege
 */
class AdminsPrivilege extends Entity
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
        'admin_id' => true,
        'privilege_id' => true,
        'admin' => true,
        'privilege' => true,
    ];
}
