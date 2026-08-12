<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Assignment Entity
 *
 * @property int $subject_id
 * @property int $student_id
 * @property string $details
 * @property \Cake\I18n\FrozenTime $datecreated
 * @property string $status
 * @property int $session_id
 * @property int $id
 * @property int $setassignment_id
 *
 * @property \App\Model\Entity\Subject $subject
 * @property \App\Model\Entity\Student $student
 * @property \App\Model\Entity\Session $session
 */
class Assignment extends Entity
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
        'subject_id' => true,
        'student_id' => true,
        'details' => true,
        'datecreated' => true,
        'status' => true,
        'session_id' => true,
        'setassignment_id' => true,
        'subject' => true,
        'student' => true,
        'session' => true,
    ];
}
