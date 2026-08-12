<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * FeesSponsorship Entity
 *
 * @property int $id
 * @property int $fee_id
 * @property int $sponsorship_id
 *
 * @property \App\Model\Entity\Fee $fee
 * @property \App\Model\Entity\Sponsorship $sponsorship
 */
class FeesSponsorship extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'fee_id' => true,
        'sponsorship_id' => true,
        'fee' => true,
        'sponsorship' => true,
    ];
}
