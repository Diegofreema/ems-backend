<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Fee Entity
 *
 * @property int $id
 * @property string $name
 * @property int $amount
 * @property int $user_id
 * @property int $status
 * @property string|null $startdate
 * @property string|null $enddate
 * @property string $feetype
 * @property string|null $itemcode
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Feeallocation[] $feeallocations
 * @property \App\Model\Entity\Invoice[] $invoices
 * @property \App\Model\Entity\Transaction[] $transactions
 * @property \App\Model\Entity\Trequest[] $trequests
 * @property \App\Model\Entity\Department[] $departments
 * @property \App\Model\Entity\Level[] $levels
 * @property \App\Model\Entity\Student[] $students
 */
class Fee extends Entity
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
        'name' => true,
        'amount' => true,
        'user_id' => true,
        'status' => true,
        'startdate' => true,
        'enddate' => true,
        'feetype' => true,
        'itemcode' => true,
        'user' => true,
        'feeallocations' => true,
        'invoices' => true,
        'transactions' => true,
        'trequests' => true,
        'departments' => true,
        'levels' => true,
        'students' => true,
    ];
}
