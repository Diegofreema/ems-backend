<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Payslip Entity
 *
 * @property int $id
 * @property int $teacher_id
 * @property string $formonth
 * @property int|null $deduction
 * @property int $grosspay
 * @property int $netpay
 * @property \Cake\I18n\FrozenTime $dategenerated
 *
 * @property \App\Model\Entity\Teacher $teacher
 */
class Payslip extends Entity
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
        'teacher_id' => true,
        'formonth' => true,
        'deduction' => true,
        'grosspay' => true,
        'netpay' => true,
        'dategenerated' => true,
        'teacher' => true,
    ];
}
