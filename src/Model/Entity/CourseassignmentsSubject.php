<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * CourseassignmentsSubject Entity
 *
 * @property int $id
 * @property int $courseassignment_id
 * @property int $subject_id
 *
 * @property \App\Model\Entity\Courseassignment $courseassignment
 * @property \App\Model\Entity\Subject $subject
 */
class CourseassignmentsSubject extends Entity
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
        'courseassignment_id' => true,
        'subject_id' => true,
        'courseassignment' => true,
        'subject' => true,
    ];
}
