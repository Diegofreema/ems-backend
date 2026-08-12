<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Attemptedquiz Entity
 *
 * @property int $id
 * @property int $quizquestion_id
 * @property int $student_id
 * @property string $sanswer
 * @property string $correctans
 * @property \Cake\I18n\FrozenTime|null $quizdate
 *
 * @property \App\Model\Entity\Quizquestion $quizquestion
 * @property \App\Model\Entity\Student $student
 */
class Attemptedquiz extends Entity
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
        'quizquestion_id' => true,
        'student_id' => true,
        'sanswer' => true,
        'correctans' => true,
        'quizdate' => true,
        'quizquestion' => true,
        'student' => true,
    ];
}
