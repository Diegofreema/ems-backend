<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Subcategory Entity
 *
 * @property int $s_c_id
 * @property int $category_id
 * @property string $subcategory_name
 * @property int $subcategory_status
 *
 * @property \App\Model\Entity\Category $category
 */
class Subcategory extends Entity
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
        'category_id' => true,
        'subcategory_name' => true,
        'subcategory_status' => true,
        'category' => true,
    ];
}
