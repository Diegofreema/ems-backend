<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Vote Entity
 *
 * @property int $id
 * @property int $candidate_id
 * @property int $student_id
 * @property int $vote
 *
 * @property \App\Model\Entity\Candidate $candidate
 * @property \App\Model\Entity\Student $student
 */
class Vote extends Entity
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
        'candidate_id' => true,
        'student_id' => true,
        'vote' => true,
        'candidate' => true,
        'student' => true,
    ];
}
