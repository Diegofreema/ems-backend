<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Donation Entity
 *
 * @property int $id
 * @property string $donator
 * @property \Cake\I18n\FrozenTime $donationdate
 * @property string $phone
 * @property string $email
 * @property string $address
 * @property string $amount
 * @property string $rrr
 * @property string $status
 */
class Donation extends Entity
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
        'donator' => true,
        'donationdate' => true,
        'phone' => true,
        'email' => true,
        'address' => true,
        'amount' => true,
        'rrr' => true,
        'status' => true,
    ];
}
