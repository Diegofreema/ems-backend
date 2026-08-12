<?php
declare(strict_types=1);

namespace App\Ems;

use Cake\Core\Configure;
use Cake\Datasource\EntityInterface;
use Cake\I18n\FrozenTime;
use Cake\ORM\Table;

final class Invitations
{
    private const CODE_ALPHABET = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    private const TTL_HOURS = 48;

    /**
     * @param \Cake\ORM\Table $users Users table used for collision checks.
     * @return array{raw:string,hash:string,expiresAt:\Cake\I18n\FrozenTime}
     */
    public static function issue(Table $users): array
    {
        do {
            $chars = '';
            for ($i = 0; $i < 8; $i++) {
                $chars .= self::CODE_ALPHABET[random_int(0, strlen(self::CODE_ALPHABET) - 1)];
            }
            $raw = substr($chars, 0, 4) . '-' . substr($chars, 4);
            $hash = self::hash($raw);
        } while ($users->exists(['invite_code' => $hash]));

        return [
            'raw' => $raw,
            'hash' => $hash,
            'expiresAt' => FrozenTime::now()->addHours(self::TTL_HOURS),
        ];
    }

    /** Normalize and hash an invitation code for storage and lookup. */
    public static function hash(string $code): string
    {
        return hash('sha256', strtoupper(trim($code)));
    }

    /** Send the one-time invitation link to its account email address. */
    public static function deliver(EntityInterface $user, EntityInterface $school, string $rawCode): void
    {
        $base = rtrim((string)Configure::read('Ems.frontendBaseUrl', 'http://localhost:5173'), '/');
        $url = $base . '/join?code=' . rawurlencode($rawCode);
        $template = "Hello %s,\n\n%s invited you to join its EMS portal as %s.\n\n"
            . "Open this link within 48 hours:\n%s\n\n"
            . "If you were not expecting this invitation, you can ignore this message.\n";
        $body = sprintf(
            $template,
            (string)$user->name,
            (string)$school->name,
            (string)$user->role,
            $url
        );

        Resend::deliver(
            (string)$user->email,
            sprintf('Join %s on EMS', (string)$school->name),
            $body,
        );
    }

    /** Static utility class. */
    private function __construct()
    {
    }
}
