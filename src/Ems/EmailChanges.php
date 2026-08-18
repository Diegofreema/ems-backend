<?php
declare(strict_types=1);

namespace App\Ems;

use Cake\Core\Configure;
use Cake\Datasource\EntityInterface;
use Cake\I18n\FrozenTime;
use Cake\ORM\Table;

/**
 * Changing a login e-mail, proved by a link to the NEW address (§3.18).
 *
 * The same shape as EmailVerifications: a raw 128-bit token, only its SHA-256
 * hex stored, single-use, 30-minute expiry. The pending new address rides with
 * the row so opening the link is what actually swaps it — until then the old
 * address keeps working. A new request retires the account's earlier unspent
 * ones so only the newest link is live.
 */
final class EmailChanges
{
    public const TTL_MINUTES = 30;

    /**
     * Stage a pending change to `$newEmail` for the user, retiring earlier
     * unspent ones.
     *
     * @return array{raw:string,expiresAt:\Cake\I18n\FrozenTime}
     */
    public static function issue(Table $changes, string $userId, string $newEmail): array
    {
        $changes->updateAll(
            ['used_at' => FrozenTime::now()],
            ['user_id' => $userId, 'used_at IS' => null],
        );

        $raw = bin2hex(random_bytes(16));
        $expiresAt = FrozenTime::now()->addMinutes(self::TTL_MINUTES);
        $changes->saveOrFail($changes->newEntity([
            'user_id' => $userId,
            'new_email' => $newEmail,
            'token' => self::hash($raw),
            'expires_at' => $expiresAt,
        ]));

        return ['raw' => $raw, 'expiresAt' => $expiresAt];
    }

    /** Normalize and hash a raw token for storage and lookup. */
    public static function hash(string $token): string
    {
        return hash('sha256', trim($token));
    }

    /** The frontend URL the confirmation e-mail points at. */
    public static function url(string $rawToken): string
    {
        $base = rtrim((string)Configure::read('Ems.frontendBaseUrl', 'http://localhost:5173'), '/');

        return $base . '/verify-email-change?token=' . rawurlencode($rawToken);
    }

    /** Send the confirm link to the NEW address (proving the mailbox is theirs). */
    public static function deliver(
        EntityInterface $user,
        EntityInterface $school,
        string $newEmail,
        string $rawToken,
    ): void {
        $message = Email::emailChange(
            (string)$school->name,
            (string)$user->name,
            $newEmail,
            self::url($rawToken),
        );

        Resend::deliver(
            $newEmail,
            sprintf('Confirm your new e-mail for %s on EMS', (string)$school->name),
            $message['text'],
            $message['html'],
        );
    }

    /** Static utility class. */
    private function __construct()
    {
    }
}
