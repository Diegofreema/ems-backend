<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Sponsorshippayment Entity
 *
 * @property int $id
 * @property string $sref
 * @property int $sponsorship_id
 * @property int $amount
 * @property \Cake\I18n\FrozenTime|null $datecreated
 * @property string $paystatus
 *
 * @property \App\Model\Entity\Sponsorship $sponsorship
 */
class Sponsorshippayment extends Entity
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
        'sref' => true,
        'sponsorship_id' => true,
        'amount' => true,
        'datecreated' => true,
        'paystatus' => true,
        'sponsorship' => true,
    ];
}
