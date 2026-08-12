<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Invoice Entity
 *
 * @property int $id
 * @property int $fee_id
 * @property int $student_id
 * @property \Cake\I18n\FrozenTime $createdate
 * @property string $amount
 * @property string $paystatus
 * @property string|null $invoiceid
 * @property int $session_id
 * @property string|null $payday
 *
 * @property \App\Model\Entity\Fee $fee
 * @property \App\Model\Entity\Student $student
 * @property \App\Model\Entity\Session $session
 * @property \App\Model\Entity\Transaction[] $transactions
 */
class Invoice extends Entity
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
        'fee_id' => true,
        'student_id' => true,
        'createdate' => true,
        'amount' => true,
        'paystatus' => true,
        'invoiceid' => true,
        'session_id' => true,
        'payday' => true,
        'fee' => true,
        'student' => true,
        'session' => true,
        'transactions' => true,
    ];
}
