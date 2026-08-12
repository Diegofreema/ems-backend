<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Sponsor Entity
 *
 * @property int $id
 * @property string $name
 * @property string|null $phone
 * @property string|null $address
 * @property string|null $emailaddress
 * @property int $admin_id
 * @property \Cake\I18n\FrozenTime|null $dateadded
 *
 * @property \App\Model\Entity\Admin $admin
 * @property \App\Model\Entity\Sponsorship[] $sponsorships
 */
class Sponsor extends Entity
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
        'phone' => true,
        'address' => true,
        'emailaddress' => true,
        'admin_id' => true,
        'dateadded' => true,
        'admin' => true,
        'sponsorships' => true,
    ];
}
