<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Candidate Entity
 *
 * @property int $id
 * @property int $student_id
 * @property int $position_id
 * @property int $session_id
 *
 * @property \App\Model\Entity\Student $student
 * @property \App\Model\Entity\Position $position
 * @property \App\Model\Entity\Session $session
 * @property \App\Model\Entity\Vote[] $votes
 */
class Candidate extends Entity
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
        'student_id' => true,
        'position_id' => true,
        'session_id' => true,
        'student' => true,
        'position' => true,
        'session' => true,
        'votes' => true,
    ];
}
