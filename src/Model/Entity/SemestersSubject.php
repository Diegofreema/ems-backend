<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * SemestersSubject Entity
 *
 * @property int $id
 * @property int $semester_id
 * @property int $subject_id
 *
 * @property \App\Model\Entity\Semester $semester
 * @property \App\Model\Entity\Subject $subject
 */
class SemestersSubject extends Entity
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
        'semester_id' => true,
        'subject_id' => true,
        'semester' => true,
        'subject' => true,
    ];
}
