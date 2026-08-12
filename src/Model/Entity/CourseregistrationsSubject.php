<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * CourseregistrationsSubject Entity
 *
 * @property int $id
 * @property int $courseregistration_id
 * @property int $subject_id
 *
 * @property \App\Model\Entity\Courseregistration $courseregistration
 * @property \App\Model\Entity\Subject $subject
 */
class CourseregistrationsSubject extends Entity
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
        'courseregistration_id' => true,
        'subject_id' => true,
        'courseregistration' => true,
        'subject' => true,
        'ca' => true,
        'exam' => true,
    ];
}
