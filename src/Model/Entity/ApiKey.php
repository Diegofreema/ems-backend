<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\Auth\DefaultPasswordHasher;
use Cake\ORM\Entity;

/**
 * ApiKey Entity
 *
 * Represents an API client credential pair. The public `api_key` is sent by the
 * client in the X-Api-Key header; the matching secret is verified against the
 * hashed `api_secret_hash` (never stored in plain text).
 *
 * @property int $id
 * @property string $name
 * @property string $api_key
 * @property string $api_secret_hash
 * @property string|null $scopes
 * @property bool $active
 * @property \Cake\I18n\FrozenTime|null $last_used_at
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 */
class ApiKey extends Entity
{
    /**
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'name' => true,
        'api_key' => true,
        'api_secret_hash' => true,
        'scopes' => true,
        'active' => true,
        'last_used_at' => true,
        'created' => true,
        'modified' => true,
    ];

    /**
     * @var array<int, string>
     */
    protected array $_hidden = [
        'api_secret_hash',
    ];

    /**
     * Hash the secret automatically when set via the virtual `secret` field.
     *
     * @param string $value Plain secret.
     * @return void
     */
    protected function _setSecret(string $value): void
    {
        $this->api_secret_hash = (new DefaultPasswordHasher())->hash($value);
    }

    /**
     * Verify a plain secret against the stored hash.
     *
     * @param string $secret Plain secret supplied by the client.
     * @return bool
     */
    public function verifySecret(string $secret): bool
    {
        return (new DefaultPasswordHasher())->check($secret, (string)$this->api_secret_hash);
    }
}
