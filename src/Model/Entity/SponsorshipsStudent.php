<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * SponsorshipsStudent Entity
 *
 * @property int $id
 * @property int $student_id
 * @property int $sponsorship_id
 *
 * @property \App\Model\Entity\Student $student
 * @property \App\Model\Entity\Sponsorship $sponsorship
 */
class SponsorshipsStudent extends Entity
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
        'student_id' => true,
        'sponsorship_id' => true,
        'student' => true,
        'sponsorship' => true,
    ];
}
