<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Student Entity
 *
 * @property int $id
 * @property string $fname
 * @property string $lname
 * @property string|null $mname
 * @property string $dob
 * @property \Cake\I18n\FrozenTime $joindate
 * @property int $department_id
 * @property string $olevelresulturl
 * @property int|null $jamb
 * @property string $birthcerturl
 * @property string|null $othercerts
 * @property string $email
 * @property int $state_id
 * @property int $country_id
 * @property string $address
 * @property string $phone
 * @property string $fathersname
 * @property string $mothersname
 * @property string $fatherphone
 * @property string $motherphone
 * @property int|null $lga_id
 * @property string|null $community
 * @property string|null $passporturl
 * @property int $user_id
 * @property string|null $regno
 * @property string $status
 * @property string|null $admissiondate
 * @property string $gender
 * @property string|null $application_no
 * @property int $level_id
 * @property int $faculty_id
 * @property string|null $jambregno
 * @property string $previousschool
 * @property int $programe_id
 * @property string|null $fathersjob
 * @property string|null $mothersjob
 * @property string|null $studentstatus
 *
 * @property \App\Model\Entity\Department $department
 * @property \App\Model\Entity\State $state
 * @property \App\Model\Entity\Country $country
 * @property \App\Model\Entity\Lga $lga
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Level $level
 * @property \App\Model\Entity\Faculty $faculty
 * @property \App\Model\Entity\Programe $programe
 * @property \App\Model\Entity\Borrowedbook[] $borrowedbooks
 * @property \App\Model\Entity\Courseregistration[] $courseregistrations
 * @property \App\Model\Entity\Invoice[] $invoices
 * @property \App\Model\Entity\Result[] $results
 * @property \App\Model\Entity\Studentmessage[] $studentmessages
 * @property \App\Model\Entity\Transaction[] $transactions
 * @property \App\Model\Entity\Trequest[] $trequests
 * @property \App\Model\Entity\Fee[] $fees
 * @property \App\Model\Entity\Hostelroom[] $hostelrooms
 * @property \App\Model\Entity\Sparent[] $sparents
 * @property \App\Model\Entity\Subject[] $subjects
 */
class Student extends Entity
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
        'fname' => true,
        'lname' => true,
        'mname' => true,
        'dob' => true,
        'joindate' => true,
        'department_id' => true,
        'class_arm_id' => true,
        'olevelresulturl' => true,
        'jamb' => true,
        'birthcerturl' => true,
        'othercerts' => true,
        'email' => true,
        'state_id' => true,
        'mode_id' => true,
        'country_id' => true,
        'address' => true,
        'phone' => true,
        'fathersname' => true,
        'mothersname' => true,
        'fatherphone' => true,
        'motherphone' => true,
        'lga_id' => true,
        'community' => true,
        'passporturl' => true,
        'passporturls' => true,
        'webcam_passport' => true,
        'passport_data' => true,
        'user_id' => true,
        'regno' => true,
        'status' => true,
        'nin' => true,
        'admissiondate' => true,
        'gender' => true,
        'application_no' => true,
        'level_id' => true,
        'faculty_id' => true,
        'jambregno' => true,
        'previousschool' => true,
        'programme_id' => true,
        'fathersjob' => true,
        'mothersjob' => true,
        'studentstatus' => true,
        'sparent_id' => true,
        'department' => true,
        'class_arm' => true,
        'state' => true,
        'country' => true,
        'universitymail' => true,
        'mode' => true,
        'lga' => true,
        'user' => true,
        'level' => true,
        'nin' => true,
        'faculty' => true,
        'programme' => true,
        'borrowedbooks' => true,
        'courseregistrations' => true,
        'invoices' => true,
        'results' => true,
        'studentmessages' => true,
        'transactions' => true,
        'trequests' => true,
        'fees' => true,
        'hostelrooms' => true,
        'sparents' => true,
        'sparent' => true,
        'subjects' => true,
        'landlocation' => true,
        'landsize' => true,
        'landowner' => true,
        'landaccessurl' => true,
        'category_id' => true,
        'programetype_id' => true,
        'nin' => true,
        'hufu' => true,
        'duration_id' => true,
        'isclaretian' => true,
        'religion' => true,
        'pschools' => true,
    ];
}
