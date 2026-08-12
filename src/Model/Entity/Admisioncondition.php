<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Admisioncondition Entity
 *
 * @property int $id
 * @property string $conditiond
 * @property int $user_id
 * @property \Cake\I18n\FrozenTime $lastupdate
 *
 * @property \App\Model\Entity\User $user
 */
class Admisioncondition extends Entity
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
        'conditiond' => true,
        'user_id' => true,
        'lastupdate' => true,
        'user' => true,
    ];
}
