<?php
declare(strict_types=1);

namespace App\Ems;

use Cake\Core\Configure;
use Cake\Datasource\EntityInterface;
use Cake\I18n\FrozenTime;
use Cake\ORM\Table;

/**
 * E-mail verification for self-served registrations (§3.18).
 *
 * A school creator proves their address by opening a link mailed to it. The
 * link carries a raw 128-bit token; only its SHA-256 hex is stored, so the DB
 * never holds a usable link. Tokens are single-use and expire after 30
 * minutes — an unverified sign-in attempt simply issues a fresh one.
 */
final class EmailVerifications
{
    public const TTL_MINUTES = 30;

    /**
     * Issue a fresh verification token for the user. Earlier unspent tokens
     * are retired so only the newest link works — a resend invalidates any
     * copy still sitting in an older e-mail.
     *
     * @param \Cake\ORM\Table $verifications ems_email_verifications table.
     * @return array{raw:string,expiresAt:\Cake\I18n\FrozenTime}
     */
    public static function issue(Table $verifications, string $userId): array
    {
        $verifications->updateAll(
            ['used_at' => FrozenTime::now()],
            ['user_id' => $userId, 'used_at IS' => null],
        );

        $raw = bin2hex(random_bytes(16));
        $expiresAt = FrozenTime::now()->addMinutes(self::TTL_MINUTES);
        $verifications->saveOrFail($verifications->newEntity([
            'user_id' => $userId,
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

    /** The frontend URL a verification e-mail points at. */
    public static function url(string $rawToken): string
    {
        $base = rtrim((string)Configure::read('Ems.frontendBaseUrl', 'http://localhost:5173'), '/');

        return $base . '/verify-email?token=' . rawurlencode($rawToken);
    }

    /** Send the verification link to the account's address. */
    public static function deliver(EntityInterface $user, EntityInterface $school, string $rawToken): void
    {
        $message = Email::verifyEmail(
            (string)$school->name,
            (string)$user->name,
            self::url($rawToken),
        );

        Resend::deliver(
            (string)$user->email,
            sprintf('Verify your e-mail to activate %s on EMS', (string)$school->name),
            $message['text'],
            $message['html'],
        );
    }

    /** Send the post-verification welcome message. */
    public static function deliverWelcome(EntityInterface $user, EntityInterface $school): void
    {
        $base = rtrim((string)Configure::read('Ems.frontendBaseUrl', 'http://localhost:5173'), '/');
        $message = Email::welcome(
            (string)$school->name,
            (string)$user->name,
            $base . '/signin',
        );

        Resend::deliver(
            (string)$user->email,
            sprintf('Welcome to EMS — %s is ready', (string)$school->name),
            $message['text'],
            $message['html'],
        );
    }

    /** Static utility class. */
    private function __construct()
    {
    }
}
