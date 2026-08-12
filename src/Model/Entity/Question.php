<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Question Entity
 *
 * @property int $id
 * @property int $setassignment_id
 * @property string $question_text
 * @property string $question_type
 * @property int $points
 * @property int $order_number
 * @property string $difficulty_level
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Setassignment $setassignment
 * @property \App\Model\Entity\QuestionOption[] $question_options
 * @property \App\Model\Entity\StudentAnswer[] $student_answers
 */
class Question extends Entity
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
        'setassignment_id' => true,
        'question_text' => true,
        'question_type' => true,
        'points' => true,
        'order_number' => true,
        'difficulty_level' => true,
        'created' => true,
        'modified' => true,
        'setassignment' => true,
        'question_options' => true,
        'student_answers' => true,
    ];

    /**
     * Get the question type as a human-readable string
     *
     * @return string
     */
    protected function _getQuestionTypeLabel()
    {
        $types = [
            'multiple_choice' => 'Multiple Choice',
            'theory' => 'Theory/Essay'
        ];
        
        return $types[$this->question_type] ?? $this->question_type;
    }

    /**
     * Get the difficulty level as a human-readable string
     *
     * @return string
     */
    protected function _getDifficultyLevelLabel()
    {
        $levels = [
            'easy' => 'Easy',
            'medium' => 'Medium',
            'hard' => 'Hard'
        ];
        
        return $levels[$this->difficulty_level] ?? $this->difficulty_level;
    }

    /**
     * Check if this is a multiple choice question
     *
     * @return bool
     */
    public function isMultipleChoice()
    {
        return $this->question_type === 'multiple_choice';
    }

    /**
     * Check if this is a theory question
     *
     * @return bool
     */
    public function isTheory()
    {
        return $this->question_type === 'theory';
    }

    /**
     * Get the correct option for multiple choice questions
     *
     * @return \App\Model\Entity\QuestionOption|null
     */
    public function getCorrectOption()
    {
        if (!$this->isMultipleChoice() || empty($this->question_options)) {
            return null;
        }

        foreach ($this->question_options as $option) {
            if ($option->is_correct) {
                return $option;
            }
        }

        return null;
    }

    /**
     * Get options ordered by order_number
     *
     * @return \App\Model\Entity\QuestionOption[]
     */
    public function getOrderedOptions()
    {
        if (empty($this->question_options)) {
            return [];
        }

        $options = $this->question_options;
        usort($options, function($a, $b) {
            return $a->order_number <=> $b->order_number;
        });

        return $options;
    }
}
