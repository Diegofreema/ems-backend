<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Muted Entity
 *
 * @property int $id
 * @property int $student_id
 * @property \Cake\I18n\FrozenTime|null $datemuted
 *
 * @property \App\Model\Entity\Student $student
 */
class Muted extends Entity
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
        'student_id' => true,
        'datemuted' => true,
        'student' => true,
    ];
}
