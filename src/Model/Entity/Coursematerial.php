<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Coursematerial Entity
 *
 * @property int $id
 * @property int $subject_id
 * @property int $teacher_id
 * @property string $title
 * @property string $fileurl
 * @property \Cake\I18n\FrozenTime $date_created
 * @property int $department_id
 * @property string|null $comment
 * @property string|null $updatedon
 *
 * @property \App\Model\Entity\Subject $subject
 * @property \App\Model\Entity\Teacher $teacher
 * @property \App\Model\Entity\Department $department
 */
class Coursematerial extends Entity
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
        'subject_id' => true,
        'teacher_id' => true,
        'title' => true,
        'fileurl' => true,
        'date_created' => true,
        'department_id' => true,
        'comment' => true,
        'updatedon' => true,
        'subject' => true,
        'teacher' => true,
        'department' => true,
    ];
}
