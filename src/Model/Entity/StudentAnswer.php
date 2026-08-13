<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * StudentAnswer Entity
 *
 * @property int $id
 * @property int $assignment_id
 * @property int $question_id
 * @property int|null $selected_option_id
 * @property string|null $theory_answer
 * @property int|null $theory_score
 * @property \Cake\I18n\FrozenTime|null $answered_at
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Assignment $assignment
 * @property \App\Model\Entity\Question $question
 * @property \App\Model\Entity\QuestionOption $question_option
 */
class StudentAnswer extends Entity
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
        'assignment_id' => true,
        'question_id' => true,
        'selected_option_id' => true,
        'theory_answer' => true,
        'theory_score' => true,
        'answered_at' => true,
        'created' => true,
        'modified' => true,
        'assignment' => true,
        'question' => true,
        'question_option' => true,
    ];

    /**
     * Check if this answer is for a multiple choice question
     *
     * @return bool
     */
    public function isMultipleChoice(): bool
    {
        return !empty($this->selected_option_id);
    }

    /**
     * Check if this answer is for a theory question
     *
     * @return bool
     */
    public function isTheory(): bool
    {
        return !empty($this->theory_answer);
    }

    /**
     * Check if the answer is correct (for multiple choice)
     *
     * @return bool|null
     */
    public function isCorrect(): ?bool
    {
        if (!$this->isMultipleChoice() || empty($this->question_option)) {
            return null;
        }

        return $this->question_option->is_correct;
    }

    /**
     * Get the selected option text
     *
     * @return string|null
     */
    public function getSelectedOptionText(): ?string
    {
        if (!$this->isMultipleChoice() || empty($this->question_option)) {
            return null;
        }

        return $this->question_option->option_text;
    }

    /**
     * Get the theory answer text
     *
     * @return string|null
     */
    public function getTheoryAnswerText(): ?string
    {
        return $this->theory_answer;
    }

    /**
     * Check if the answer has been graded
     *
     * @return bool
     */
    public function isGraded(): bool
    {
        if ($this->isMultipleChoice()) {
            return true; // Multiple choice is auto-graded
        }

        return $this->theory_score !== null;
    }

    /**
     * Get the score for this answer
     *
     * @return int|null
     */
    public function getScore(): ?int
    {
        if ($this->isMultipleChoice()) {
            return $this->isCorrect() ? $this->question->points : 0;
        }

        return $this->theory_score;
    }

    /**
     * Get the maximum possible score for this question
     *
     * @return int|null
     */
    public function getMaxScore(): ?int
    {
        if (empty($this->question)) {
            return null;
        }

        return $this->question->points;
    }

    /**
     * Get the score percentage
     *
     * @return float|null
     */
    public function getScorePercentage(): ?float
    {
        $score = $this->getScore();
        $maxScore = $this->getMaxScore();

        if ($score === null || $maxScore === null || $maxScore === 0) {
            return null;
        }

        return $score / $maxScore * 100;
    }
}
