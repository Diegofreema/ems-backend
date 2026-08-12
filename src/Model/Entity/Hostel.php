<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Hostel Entity
 *
 * @property int $id
 * @property string $name
 * @property string $type
 * @property string $address
 * @property string|null $phone
 *
 * @property \App\Model\Entity\Hostelroom[] $hostelrooms
 */
class Hostel extends Entity
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
        'type' => true,
        'address' => true,
        'phone' => true,
        'hostelrooms' => true,
    ];
}
