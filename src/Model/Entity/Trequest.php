<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Trequest Entity
 *
 * @property int $id
 * @property int $student_id
 * @property \Cake\I18n\FrozenTime $orderdate
 * @property string $institution
 * @property string $status
 * @property int $continent_id
 * @property int $country_id
 * @property int $state_id
 * @property string $address
 * @property int $courier_id
 * @property string $amount
 * @property string|null $deliverystatus
 * @property int $fee_id
 *
 * @property \App\Model\Entity\Student $student
 * @property \App\Model\Entity\Continent $continent
 * @property \App\Model\Entity\Country $country
 * @property \App\Model\Entity\State $state
 * @property \App\Model\Entity\Courier $courier
 * @property \App\Model\Entity\Fee $fee
 */
class Trequest extends Entity
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
        'orderdate' => true,
        'institution' => true,
        'status' => true,
        'continent_id' => true,
        'country_id' => true,
        'state_id' => true,
        'address' => true,
        'courier_id' => true,
        'amount' => true,
        'deliverystatus' => true,
        'fee_id' => true,
        'student' => true,
        'continent' => true,
        'country' => true,
        'state' => true,
        'courier' => true,
        'fee' => true,
    ];
}
