<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Topic Entity
 *
 * @property int $id
 * @property int $subject_id
 * @property string $contents
 * @property int $user_id
 * @property \Cake\I18n\FrozenTime $uploaddate
 * @property string|null $updatedon
 * @property string $title
 *
 * @property \App\Model\Entity\Subject $subject
 * @property \App\Model\Entity\User $user
 */
class Topic extends Entity
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
        'subject_id' => true,
        'contents' => true,
        'user_id' => true,
        'uploaddate' => true,
        'updatedon' => true,
        'title' => true,
        'subject' => true,
        'user' => true,
    ];
}
