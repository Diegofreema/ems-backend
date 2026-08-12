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

    /** Send one text message with an optional HTML alternative. */
    public static function deliver(string $to, string $subject, string $text, ?string $html = null): void
    {
        $apiKey = trim((string)Configure::read('Ems.resendApiKey', ''));
        $from = trim((string)Configure::read('Ems.emailFrom', ''));
        if ($apiKey === '' || $from === '') {
            throw new RuntimeException('Resend mail configuration is missing.');
        }

        $payload = [
            'from' => $from,
            'to' => [$to],
            'subject' => $subject,
            'text' => $text,
        ];
        if ($html !== null) {
            $payload['html'] = $html;
        }

        $response = (new Client())->post(self::ENDPOINT, json_encode($payload, JSON_THROW_ON_ERROR), [
            'type' => 'json',
            'timeout' => 30,
            'headers' => ['Authorization' => 'Bearer ' . $apiKey],
        ]);

        if (!$response->isOk()) {
            throw new RuntimeException(sprintf('Resend returned HTTP %d.', $response->getStatusCode()));
        }
    }

    /** Static utility class. */
    private function __construct()
    {
    }
}
