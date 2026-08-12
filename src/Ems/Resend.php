<?php
declare(strict_types=1);

namespace App\Ems;

use Cake\Core\Configure;
use Cake\Http\Client;
use RuntimeException;

/** Sends EMS e-mail through Resend's HTTPS API. */
final class Resend
{
    private const ENDPOINT = 'https://api.resend.com/emails';

    public static function deliver(string $to, string $subject, string $text): void
    {
        $apiKey = trim((string)Configure::read('Ems.resendApiKey', ''));
        $from = trim((string)Configure::read('Ems.emailFrom', ''));
        if ($apiKey === '' || $from === '') {
            throw new RuntimeException('Resend mail configuration is missing.');
        }

        $response = (new Client())->post(self::ENDPOINT, json_encode([
            'from' => $from,
            'to' => [$to],
            'subject' => $subject,
            'text' => $text,
        ], JSON_THROW_ON_ERROR), [
            'type' => 'json',
            'timeout' => 30,
            'headers' => ['Authorization' => 'Bearer ' . $apiKey],
        ]);

        if (!$response->isOk()) {
            throw new RuntimeException(sprintf('Resend returned HTTP %d.', $response->getStatusCode()));
        }
    }

    private function __construct()
    {
    }
}
