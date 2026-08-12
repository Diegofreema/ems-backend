<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Event Entity
 *
 * @property int $id
 * @property string $eventtitle
 * @property string $details
 * @property \Cake\I18n\FrozenTime|null $datecreated
 * @property string $venue
 * @property string $eventdate
 * @property string|null $eventtime
 * @property int $admin_id
 * @property int|null $viewscount
 *
 * @property \App\Model\Entity\Admin $admin
 */
class Event extends Entity
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
        'eventtitle' => true,
        'details' => true,
        'datecreated' => true,
        'venue' => true,
        'eventdate' => true,
        'eventtime' => true,
        'admin_id' => true,
        'viewscount' => true,
        'admin' => true,
    ];
}
