<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Quizquestion Entity
 *
 * @property int $id
 * @property int $quiz_id
 * @property string $question
 * @property string $op1
 * @property string $op2
 * @property string $op3
 * @property string $op4
 * @property string $correctans
 * @property int|null $mark
 *
 * @property \App\Model\Entity\Quiz $quiz
 * @property \App\Model\Entity\Attemptedquiz[] $attemptedquizzes
 */
class Quizquestion extends Entity
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
        'quiz_id' => true,
        'question' => true,
        'op1' => true,
        'op2' => true,
        'op3' => true,
        'op4' => true,
        'correctans' => true,
        'mark' => true,
        'quiz' => true,
        'attemptedquizzes' => true,
    ];
}
