<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\Auth\DefaultPasswordHasher;
use Cake\ORM\Entity;

/**
 * User Entity
 *
 * @property int $id
 * @property string $username
 * @property string $password
 * @property int $role_id
 * @property string $fname
 * @property string $lname
 * @property string|null $mname
 * @property string $gender
 * @property string $address
 * @property int $country_id
 * @property int $state_id
 * @property string $phone
 * @property int $department_id
 * @property string|null $profile
 * @property string|null $dob
 * @property \Cake\I18n\FrozenTime $created_date
 * @property int $created_by
 * @property string|null $passport
 * @property string $useruniquid
 * @property string $userstatus
 * @property string|null $verification_key
 *
 * @property \App\Model\Entity\Role $role
 * @property \App\Model\Entity\Country $country
 * @property \App\Model\Entity\State $state
 * @property \App\Model\Entity\Department $department
 * @property \App\Model\Entity\Admin[] $admins
 * @property \App\Model\Entity\Admisioncondition[] $admisionconditions
 * @property \App\Model\Entity\Book[] $books
 * @property \App\Model\Entity\Courseassignment[] $courseassignments
 * @property \App\Model\Entity\Dstudent[] $dstudents
 * @property \App\Model\Entity\Feeallocation[] $feeallocations
 * @property \App\Model\Entity\Fee[] $fees
 * @property \App\Model\Entity\Log[] $logs
 * @property \App\Model\Entity\News[] $news
 * @property \App\Model\Entity\Notification[] $notifications
 * @property \App\Model\Entity\Result[] $results
 * @property \App\Model\Entity\Session[] $sessions
 * @property \App\Model\Entity\Sparent[] $sparents
 * @property \App\Model\Entity\Staffmessage[] $staffmessages
 * @property \App\Model\Entity\Studentmessage[] $studentmessages
 * @property \App\Model\Entity\Student[] $students
 * @property \App\Model\Entity\Subject[] $subjects
 * @property \App\Model\Entity\Teacher[] $teachers
 * @property \App\Model\Entity\Topic[] $topics
 * @property \App\Model\Entity\Userlogin[] $userlogins
 */
class User extends Entity
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
        'username' => true,
        'password' => true,
        'role_id' => true,
        'fname' => true,
        'lname' => true,
        'mname' => true,
        'gender' => true,
        'address' => true,
        'country_id' => true,
        'state_id' => true,
        'phone' => true,
        'department_id' => true,
        'profile' => true,
        'dob' => true,
        'created_date' => true,
        'created_by' => true,
        'passport' => true,
        'useruniquid' => true,
        'userstatus' => true,
        'verification_key' => true,
        'otp_code' => true,
        'otp_expires' => true,
        'role' => true,
        'country' => true,
        'state' => true,
        'department' => true,
        'admins' => true,
        'admisionconditions' => true,
        'books' => true,
        'courseassignments' => true,
        'dstudents' => true,
        'feeallocations' => true,
        'fees' => true,
        'logs' => true,
        'news' => true,
        'notifications' => true,
        'results' => true,
        'sessions' => true,
        'sparents' => true,
        'staffmessages' => true,
        'studentmessages' => true,
        'students' => true,
        'subjects' => true,
        'teachers' => true,
        'topics' => true,
        'userlogins' => true,
    ];

    /**
     * Fields that are excluded from JSON versions of the entity.
     *
     * @var array
     */
    protected array $_hidden = [
        'password',
    ];

    // Password hashing method.
    protected function _setPassword(mixed $value): string
    {
        $hasher = new DefaultPasswordHasher();

        return $hasher->hash($value);
    }
}
