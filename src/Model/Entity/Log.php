<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Log Entity
 *
 * @property int $id
 * @property string $title
 * @property \Cake\I18n\FrozenTime $timestamp
 * @property int $user_id
 * @property string $description
 * @property string $ip
 * @property string $type
 *
 * @property \App\Model\Entity\User $user
 */
class Log extends Entity
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
        'title' => true,
        'timestamp' => true,
        'user_id' => true,
        'description' => true,
        'ip' => true,
        'type' => true,
        'user' => true,
    ];
}
