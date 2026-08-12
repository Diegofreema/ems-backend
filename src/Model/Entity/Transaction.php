<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Transaction Entity
 *
 * @property int $id
 * @property int $student_id
 * @property \Cake\I18n\FrozenTime $transdate
 * @property string $amount
 * @property string $paystatus
 * @property string $payref
 * @property string $gresponse
 * @property int $session_id
 * @property int $fee_id
 * @property int $invoice_id
 * @property string|null $pgateway
 *
 * @property \App\Model\Entity\Student $student
 * @property \App\Model\Entity\Session $session
 * @property \App\Model\Entity\Fee $fee
 * @property \App\Model\Entity\Invoice $invoice
 */
class Transaction extends Entity
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
        'transdate' => true,
        'amount' => true,
        'paystatus' => true,
        'payref' => true,
        'gresponse' => true,
        'session_id' => true,
        'fee_id' => true,
        'invoice_id' => true,
        'pgateway' => true,
        'student' => true,
        'session' => true,
        'fee' => true,
        'invoice' => true,
    ];
}
