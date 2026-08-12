<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Setassignment Entity
 *
 * @property int $id
 * @property int $subject_id
 * @property string $title
 * @property string $details
 * @property string $test_type
 * @property int $total_questions
 * @property int $time_limit
 * @property int $passing_score
 * @property int $teacher_id
 * @property int $semester_id
 * @property string $status
 * @property string $closedate
 * @property string $opendate
 * @property \Cake\I18n\FrozenTime $datecreated
 *
 * @property \App\Model\Entity\Subject $subject
 * @property \App\Model\Entity\Teacher $teacher
 * @property \App\Model\Entity\Semester $semester
 */
class Setassignment extends Entity
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
        'title' => true,
        'details' => true,
        'test_type' => true,
        'total_questions' => true,
        'time_limit' => true,
        'passing_score' => true,
        'teacher_id' => true,
        'semester_id' => true,
        'status' => true,
        'closedate' => true,
        'opendate' => true,
        'datecreated' => true,
        'subject' => true,
        'teacher' => true,
        'semester' => true,
    ];
}
