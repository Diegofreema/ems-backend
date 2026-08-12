<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Cafcredit Entity
 *
 * @property string $matricnum
 * @property int $amount
 * @property \Cake\I18n\FrozenTime $date1
 * @property int $id
 */
class Cafcredit extends Entity
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
        'matricnum' => true,
        'amount' => true,
        'date1' => true,
    ];
}
