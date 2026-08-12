<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Level Entity
 *
 * @property int $id
 * @property string $name
 *
 * @property \App\Model\Entity\Courseassignment[] $courseassignments
 * @property \App\Model\Entity\Courseregistration[] $courseregistrations
 * @property \App\Model\Entity\Result[] $results
 * @property \App\Model\Entity\Student[] $students
 * @property \App\Model\Entity\Subject[] $subjects
 * @property \App\Model\Entity\Timetable[] $timetables
 * @property \App\Model\Entity\Department[] $departments
 * @property \App\Model\Entity\Fee[] $fees
 */
class Level extends Entity
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
        'name' => true,
        'courseassignments' => true,
        'courseregistrations' => true,
        'results' => true,
        'students' => true,
        'subjects' => true,
        'timetables' => true,
        'departments' => true,
        'fees' => true,
    ];
}
