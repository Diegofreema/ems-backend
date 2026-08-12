<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Approvedresult Entity
 *
 * @property int $id
 * @property int $session_id
 * @property int $semester_id
 * @property string $status
 * @property int $admin_id
 *
 * @property \App\Model\Entity\Session $session
 * @property \App\Model\Entity\Semester $semester
 * @property \App\Model\Entity\Admin $admin
 */
class Approvedresult extends Entity
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
        'session_id' => true,
        'semester_id' => true,
        'status' => true,
        'admin_id' => true,
        'session' => true,
        'semester' => true,
        'admin' => true,
    ];
}
