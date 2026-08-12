<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * HostelroomsStudent Entity
 *
 * @property int $id
 * @property int $hostelroom_id
 * @property int $student_id
 *
 * @property \App\Model\Entity\Hostelroom $hostelroom
 * @property \App\Model\Entity\Student $student
 */
class HostelroomsStudent extends Entity
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
        'hostelroom_id' => true,
        'student_id' => true,
        'hostelroom' => true,
        'student' => true,
    ];
}
