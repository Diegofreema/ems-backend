<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Session Entity
 *
 * @property int $id
 * @property string $name
 * @property int $user_id
 * @property \Cake\I18n\FrozenTime $createdate
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Courseregistration[] $courseregistrations
 * @property \App\Model\Entity\Invoice[] $invoices
 * @property \App\Model\Entity\Result[] $results
 * @property \App\Model\Entity\Setting[] $settings
 * @property \App\Model\Entity\Transaction[] $transactions
 */
class Session extends Entity
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
        'name' => true,
        'user_id' => true,
        'createdate' => true,
        'user' => true,
        'courseregistrations' => true,
        'invoices' => true,
        'results' => true,
        'settings' => true,
        'transactions' => true,
    ];
}
