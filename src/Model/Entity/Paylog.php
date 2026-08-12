<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Paylog Entity
 *
 * @property int $id
 * @property \Cake\I18n\FrozenTime|null $transdate
 * @property int $student_id
 * @property int $tref
 * @property string $responsecode
 * @property string $amount
 * @property string $paymethod
 *
 * @property \App\Model\Entity\Student $student
 */
class Paylog extends Entity
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
        'transdate' => true,
        'student_id' => true,
        'tref' => true,
        'responsecode' => true,
        'amount' => true,
        'paymethod' => true,
        'student' => true,
    ];
}
