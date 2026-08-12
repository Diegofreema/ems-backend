<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Attemptedexamquestion Entity
 *
 * @property int $id
 * @property int $examquestion_id
 * @property int $student_id
 * @property string $sanswer
 * @property string $correctans
 * @property \Cake\I18n\FrozenTime $examdate
 *
 * @property \App\Model\Entity\Examquestion $examquestion
 * @property \App\Model\Entity\Student $student
 */
class Attemptedexamquestion extends Entity
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
        'examquestion_id' => true,
        'student_id' => true,
        'sanswer' => true,
        'correctans' => true,
        'examdate' => true,
        'examquestion' => true,
        'student' => true,
    ];
}
