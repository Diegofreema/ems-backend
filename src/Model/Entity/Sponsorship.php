<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Sponsorship Entity
 *
 * @property int $id
 * @property int $sponsor_id
 * @property int $session_id
 * @property int $student_id
 * @property int $admin_id
 * @property \Cake\I18n\FrozenTime|null $datecreated
 *
 * @property \App\Model\Entity\Sponsor $sponsor
 * @property \App\Model\Entity\Session $session
 * @property \App\Model\Entity\Student[] $students
 * @property \App\Model\Entity\Admin $admin
 * @property \App\Model\Entity\Sponsorshippayment[] $sponsorshippayments
 * @property \App\Model\Entity\Fee[] $fees
 */
class Sponsorship extends Entity
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
        'sponsor_id' => true,
        'session_id' => true,
        'student_id' => true,
        'admin_id' => true,
        'datecreated' => true,
        'sponsor' => true,
        'session' => true,
        'students' => true,
        'admin' => true,
        'sponsorshippayments' => true,
        'fees' => true,
    ];
}
