<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Liveclass Entity
 *
 * @property int $id
 * @property string $meetinglink
 * @property int $teacher_id
 * @property \Cake\I18n\FrozenTime $datecreated
 *
 * @property \App\Model\Entity\Teacher $teacher
 */
class Liveclass extends Entity
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
        'meetinglink' => true,
        'teacher_id' => true,
        'datecreated' => true,
        'teacher' => true,
    ];
}
