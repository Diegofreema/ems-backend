<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * QuestionOption Entity
 *
 * @property int $id
 * @property int $question_id
 * @property string $option_text
 * @property bool $is_correct
 * @property int $order_number
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Question $question
 * @property \App\Model\Entity\StudentAnswer[] $student_answers
 */
class QuestionOption extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to false, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'question_id' => true,
        'option_text' => true,
        'is_correct' => true,
        'order_number' => true,
        'created' => true,
        'modified' => true,
        'question' => true,
        'student_answers' => true,
    ];

    /**
     * Get a formatted label for the option
     *
     * @return string
     */
    protected function _getFormattedLabel(): string
    {
        $letters = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
        $index = $this->order_number - 1;
        $letter = $letters[$index] ?? $this->order_number;

        return $letter . '. ' . $this->option_text;
    }

    /**
     * Get the option letter (A, B, C, D, etc.)
     *
     * @return string
     */
    protected function _getOptionLetter(): string
    {
        $letters = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
        $index = $this->order_number - 1;

        return $letters[$index] ?? (string)$this->order_number;
    }

    /**
     * Check if this option is correct
     *
     * @return bool
     */
    public function isCorrect(): bool
    {
        return $this->is_correct === true;
    }

    /**
     * Get the option text with letter prefix
     *
     * @return string
     */
    public function getLabel(): string
    {
        return $this->option_letter . '. ' . $this->option_text;
    }

    /**
     * Get the option text without letter prefix
     *
     * @return string
     */
    public function getText(): string
    {
        return $this->option_text;
    }
}
