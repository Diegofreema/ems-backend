<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Post Entity
 *
 * @property int $id
 * @property int $postcategory_id
 * @property int $user_id
 * @property string $allowcomments
 * @property string $title
 * @property string $postbody
 * @property string $status
 * @property \Cake\I18n\FrozenTime $dateadded
 * @property \Cake\I18n\FrozenTime $lastedited
 *
 * @property \App\Model\Entity\Postcategory $postcategory
 * @property \App\Model\Entity\User $user
 */
class Post extends Entity
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
        'postcategory_id' => true,
        'user_id' => true,
        'allowcomments' => true,
        'title' => true,
        'postdetails' => true,
        'status' => true,
        'dateadded' => true,
        'lastedited' => true,
        'postcategory' => true,
        'user' => true,
    ];
}
