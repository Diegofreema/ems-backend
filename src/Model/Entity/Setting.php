<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Setting Entity
 *
 * @property int $id
 * @property int $semester_id
 * @property string $description
 * @property int $regfee
 * @property string $name
 * @property string $address
 * @property string $email
 * @property string $phone
 * @property string $invoiceprefix
 * @property string $adminprefix
 * @property string $logo
 * @property string $staffprefix
 * @property string $regnoformat
 * @property int $session_id
 * @property string $application_no_prefix
 * @property string $rector
 * @property string $registrar
 * @property string $rectorcerts
 * @property string $registrarcerts
 * @property string $school_stamp
 *
 * @property \App\Model\Entity\Semester $semester
 * @property \App\Model\Entity\Session $session
 */
class Setting extends Entity
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
        'description' => true,
        'regfee' => true,
        'name' => true,
        'address' => true,
        'email' => true,
        'phone' => true,
        'invoiceprefix' => true,
        'adminprefix' => true,
        'logo' => true,
        'staffprefix' => true,
        'regnoformat' => true,
        'session_id' => true,
        'application_no_prefix' => true,
        'rector' => true,
        'registrar' => true,
        'rectorcerts' => true,
        'registrarcerts' => true,
        'school_stamp' => true,
        'semester' => true,
        'doa' => true,
        'session' => true,
    ];
}
