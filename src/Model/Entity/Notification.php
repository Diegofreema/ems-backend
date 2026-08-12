<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Notification Entity
 *
 * @property int $id
 * @property string $title
 * @property string $message
 * @property \Cake\I18n\FrozenTime $datecreated
 * @property int $user_id
 * @property string $recipients
 * @property string $status
 * @property int $viewcount
 *
 * @property \App\Model\Entity\User $user
 */
class Notification extends Entity
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
        'message' => true,
        'datecreated' => true,
        'user_id' => true,
        'recipients' => true,
        'status' => true,
        'viewcount' => true,
        'user' => true,
    ];
}
