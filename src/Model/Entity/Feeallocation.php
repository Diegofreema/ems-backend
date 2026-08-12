<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Feeallocation Entity
 *
 * @property int $id
 * @property int $fee_id
 * @property int $department_id
 * @property string|null $startdate
 * @property string|null $enddate
 * @property int $user_id
 *
 * @property \App\Model\Entity\Fee $fee
 * @property \App\Model\Entity\Department $department
 * @property \App\Model\Entity\User $user
 */
class Feeallocation extends Entity
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
        'fee_id' => true,
        'department_id' => true,
        'startdate' => true,
        'enddate' => true,
        'user_id' => true,
        'fee' => true,
        'department' => true,
        'user' => true,
    ];
}
