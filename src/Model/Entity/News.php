<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * News Entity
 *
 * @property int $id
 * @property string $title
 * @property string $details
 * @property \Cake\I18n\FrozenTime|null $dateposted
 * @property int $user_id
 * @property int|null $viewcount
 * @property string $status
 * @property string|null $newsimage
 *
 * @property \App\Model\Entity\User $user
 */
class News extends Entity
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
        'details' => true,
        'dateposted' => true,
        'admin_id' => true,
        'viewcount' => true,
        'status' => true,
        'newsimage' => true,
        'user' => true,
    ];
}
