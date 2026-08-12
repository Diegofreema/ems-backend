<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Department Entity
 *
 * @property int $id
 * @property int $faculty_id
 * @property string $name
 * @property string $deptcode
 *
 * @property \App\Model\Entity\Faculty $faculty
 * @property \App\Model\Entity\Admin[] $admins
 * @property \App\Model\Entity\Courseassignment[] $courseassignments
 * @property \App\Model\Entity\Coursematerial[] $coursematerials
 * @property \App\Model\Entity\Dstudent[] $dstudents
 * @property \App\Model\Entity\Examquestion[] $examquestions
 * @property \App\Model\Entity\Feeallocation[] $feeallocations
 * @property \App\Model\Entity\Quiz[] $quizzes
 * @property \App\Model\Entity\Result[] $results
 * @property \App\Model\Entity\Student[] $students
 * @property \App\Model\Entity\Subject[] $subjects
 * @property \App\Model\Entity\Teacher[] $teachers
 * @property \App\Model\Entity\User[] $users
 * @property \App\Model\Entity\Fee[] $fees
 * @property \App\Model\Entity\Level[] $levels
 * @property \App\Model\Entity\Programme[] $programmes
 * @property \App\Model\Entity\Semester[] $semesters
 */
class Department extends Entity
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
        'faculty_id' => true,
        'name' => true,
        'deptcode' => true,
        'faculty' => true,
        'admins' => true,
        'courseassignments' => true,
        'coursematerials' => true,
        'dstudents' => true,
        'examquestions' => true,
        'feeallocations' => true,
        'quizzes' => true,
        'results' => true,
        'students' => true,
        'subjects' => true, // Keep accessible but will be handled with empty value
        'teachers' => true,
        'users' => true,
        'fees' => true,
        'levels' => true,
        'maxunit' => true,
        'iscdl' => true,
        'programmes' => true,
        'semesters' => true,
    ];
}
