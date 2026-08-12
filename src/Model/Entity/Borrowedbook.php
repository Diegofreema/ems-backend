<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Borrowedbook Entity
 *
 * @property int $id
 * @property int $student_id
 * @property int $book_id
 * @property \Cake\I18n\FrozenTime $date
 * @property string $datetoreturn
 * @property string $status
 *
 * @property \App\Model\Entity\Student $student
 * @property \App\Model\Entity\Book $book
 */
class Borrowedbook extends Entity
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
        'book_id' => true,
        'date' => true,
        'datetoreturn' => true,
        'status' => true,
        'student' => true,
        'book' => true,
    ];
}
