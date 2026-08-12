<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * EMS account row. Credential material is hidden from any accidental
 * serialization — the wire shape is produced only by SettingsSerializer,
 * which whitelists fields, but this is the belt-and-braces layer
 * (document.md §3.14: "the real backend must NEVER return password,
 * inviteCode, reset").
 */
class EmsUser extends Entity
{
    protected array $_hidden = [
        'password_hash',
        'invite_code',
        'invite_expires_at',
    ];
}
