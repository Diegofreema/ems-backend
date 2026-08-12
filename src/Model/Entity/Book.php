<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Book Entity
 *
 * @property int $id
 * @property string $title
 * @property string $author
 * @property string $pubdate
 * @property string|null $isavailable
 * @property \Cake\I18n\FrozenTime $date_created
 * @property int $user_id
 * @property string $isbn
 * @property string|null $coverphoto
 * @property int|null $copies
 * @property string|null $section
 * @property string|null $callno
 * @property int $department_id
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Department $department
 * @property \App\Model\Entity\Borrowedbook[] $borrowedbooks
 */
class Book extends Entity
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
        'title' => true,
        'author' => true,
        'pubdate' => true,
        'isavailable' => true,
        'date_created' => true,
        'user_id' => true,
        'isbn' => true,
        'coverphoto' => true,
        'copies' => true,
        'section' => true,
        'callno' => true,
        'department_id' => true,
        'user' => true,
        'department' => true,
        'borrowedbooks' => true,
    ];
}
