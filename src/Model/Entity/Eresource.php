<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Eresource Entity
 *
 * @property int $id
 * @property string $title
 * @property string $pubdate
 * @property string|null $isbn
 * @property string $author
 * @property int $department_id
 * @property \Cake\I18n\FrozenTime $dateadded
 * @property int|null $viewcount
 *
 * @property \App\Model\Entity\Department $department
 */
class Eresource extends Entity
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
        'pubdate' => true,
        'isbn' => true,
        'author' => true,
        'department_id' => true,
        'dateadded' => true,
        'viewcount' => true,
        'filenameurl' => true,
        'department' => true,
    ];
}
