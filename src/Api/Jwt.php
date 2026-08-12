<?php
declare(strict_types=1);

namespace App\Api;

use Cake\Core\Configure;
use RuntimeException;

/**
 * Minimal, dependency-free JWT (HS256) encoder/decoder.
 *
 * Kept self-contained on purpose so the API works on any XAMPP box without
 * requiring `composer require firebase/php-jwt`. If you later add a JWT library
 * you can swap the internals of encode()/decode() without touching callers.
 */
class Jwt
{
    /**
     * The signing secrets that ship in version control as fill-me-in defaults.
     * Any of these means "nobody set a real key" — refuse to sign or verify
     * with them rather than run on a publicly-known, forgeable secret.
     *
     * @var array<int, string>
     */
    private const PLACEHOLDER_SECRETS = [
        // The committed Jwt.secret default in config/app_local.example.php.
        '__JWT_SECRET__',
        // The committed Security.salt default in config/app_local.example.php.
        '__SALT__',
        // The values that historically shipped in config/app_local.php itself.
        'a3f1c9e7b2d84605f7a1c3e5d9b8074628f1a2b3c4d5e6f70819cd',
        'e84a42a75300082ce06301f4daf9fd64b8c7e65e1fcc97f6799f648787bde5df',
    ];

    /** A signing key shorter than this is too weak to sign auth tokens with. */
    private const MIN_SECRET_LENGTH = 32;

    /**
     * Create a signed JWT.
     *
     * @param array $claims Payload claims (e.g. sub, role_id, type).
     * @param int|null $ttl Lifetime in seconds. Defaults to Jwt.accessTtl config.
     * @param int $now Current unix timestamp (pass time() from caller).
     * @return string The encoded token.
     */
    public static function encode(array $claims, ?int $ttl, int $now): string
    {
        $ttl = $ttl ?? (int)Configure::read('Jwt.accessTtl', 3600);

        $payload = $claims + [
            'iss' => Configure::read('Jwt.issuer', 'ltalms-api'),
            'iat' => $now,
            'nbf' => $now,
            'exp' => $now + $ttl,
        ];

        $header = ['alg' => 'HS256', 'typ' => 'JWT'];

        $segments = [
            self::base64UrlEncode(json_encode($header)),
            self::base64UrlEncode(json_encode($payload)),
        ];
        $signingInput = implode('.', $segments);
        $segments[] = self::base64UrlEncode(self::sign($signingInput));

        return implode('.', $segments);
    }

    /**
     * Verify and decode a JWT. Throws on any validation failure.
     *
     * @param string $token The compact JWT string.
     * @param int $now Current unix timestamp (pass time() from caller).
     * @return array The decoded payload claims.
     * @throws \RuntimeException When the token is malformed, tampered or expired.
     */
    public static function decode(string $token, int $now): array
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            throw new RuntimeException('Malformed token.');
        }
        [$headB64, $payloadB64, $sigB64] = $parts;

        $header = json_decode(self::base64UrlDecode($headB64), true);
        if (!is_array($header) || ($header['alg'] ?? null) !== 'HS256') {
            throw new RuntimeException('Unsupported token algorithm.');
        }

        $expectedSig = self::sign($headB64 . '.' . $payloadB64);
        $providedSig = self::base64UrlDecode($sigB64);
        if (!hash_equals($expectedSig, $providedSig)) {
            throw new RuntimeException('Invalid token signature.');
        }

        $payload = json_decode(self::base64UrlDecode($payloadB64), true);
        if (!is_array($payload)) {
            throw new RuntimeException('Invalid token payload.');
        }

        // Allow a small clock-skew tolerance.
        $leeway = 10;
        if (isset($payload['nbf']) && $now + $leeway < (int)$payload['nbf']) {
            throw new RuntimeException('Token not yet valid.');
        }
        if (isset($payload['exp']) && $now - $leeway >= (int)$payload['exp']) {
            throw new RuntimeException('Token has expired.');
        }

        return $payload;
    }

    /**
     * @param string $input Data to sign.
     * @return string Raw HMAC signature.
     */
    private static function sign(string $input): string
    {
        return hash_hmac('sha256', $input, self::secret(), true);
    }

    /**
     * Resolve the JWT signing secret, failing closed. Both encode() and
     * decode() sign through here, so a server that is missing or misconfigured
     * cannot mint a token OR accept one — there is no weak-default mode.
     *
     * Only Jwt.secret is consulted (the old Security.salt fallback is gone): one
     * key, one source. An empty, placeholder, or too-short secret is a hard
     * configuration error, never a silent downgrade to a guessable key.
     *
     * @return string The validated signing secret.
     * @throws \RuntimeException When no strong Jwt.secret is configured.
     */
    private static function secret(): string
    {
        $secret = (string)Configure::read('Jwt.secret');

        if (
            $secret === ''
            || strlen($secret) < self::MIN_SECRET_LENGTH
            || in_array($secret, self::PLACEHOLDER_SECRETS, true)
        ) {
            throw new RuntimeException(
                'JWT signing secret is not configured. Set a strong, random Jwt.secret '
                . '(at least ' . self::MIN_SECRET_LENGTH . ' characters, e.g. via the '
                . 'JWT_SECRET env var) in config/app_local.php.',
            );
        }

        return $secret;
    }

    /**
     * @param string $data Raw data.
     * @return string URL-safe base64.
     */
    private static function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * @param string $data URL-safe base64.
     * @return string Decoded raw data.
     */
    private static function base64UrlDecode(string $data): string
    {
        $remainder = strlen($data) % 4;
        if ($remainder) {
            $data .= str_repeat('=', 4 - $remainder);
        }

        return (string)base64_decode(strtr($data, '-_', '+/'), true);
    }
}
