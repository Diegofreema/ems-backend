<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Hostelroom Entity
 *
 * @property int $id
 * @property int $hostel_id
 * @property string $floor
 * @property string $room_number
 * @property int|null $available_beds
 * @property int|null $occupiedbeds
 * @property string $description
 *
 * @property \App\Model\Entity\Hostel $hostel
 * @property \App\Model\Entity\Student[] $students
 */
class Hostelroom extends Entity
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
        'hostel_id' => true,
        'floor' => true,
        'room_number' => true,
        'available_beds' => true,
        'occupiedbeds' => true,
        'description' => true,
        'hostel' => true,
        'students' => true,
    ];
}
