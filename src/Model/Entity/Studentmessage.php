<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Studentmessage Entity
 *
 * @property int $id
 * @property string $title
 * @property string $messages
 * @property int $student_id
 * @property int $user_id
 * @property \Cake\I18n\FrozenTime|null $date_created
 * @property string|null $status
 * @property string $mfor
 *
 * @property \App\Model\Entity\Student $student
 * @property \App\Model\Entity\User $user
 */
class Studentmessage extends Entity
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
        'title' => true,
        'messages' => true,
        'student_id' => true,
        'user_id' => true,
        'date_created' => true,
        'status' => true,
        'mfor' => true,
        'student' => true,
        'user' => true,
    ];
}
