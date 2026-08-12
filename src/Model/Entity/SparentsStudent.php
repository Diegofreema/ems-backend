<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * SparentsStudent Entity
 *
 * @property int $id
 * @property int $student_id
 * @property int $parent_id
 *
 * @property \App\Model\Entity\Student $student
 * @property \App\Model\Entity\ParentSparentsStudent $parent_sparents_student
 * @property \App\Model\Entity\ChildSparentsStudent[] $child_sparents_students
 */
class SparentsStudent extends Entity
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
        'student_id' => true,
        'parent_id' => true,
        'student' => true,
        'parent_sparents_student' => true,
        'child_sparents_students' => true,
    ];
}
