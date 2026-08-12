<?php
declare(strict_types=1);

namespace App\Test\TestCase\Api;

use App\Api\Jwt;
use Cake\Core\Configure;
use Cake\TestSuite\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use RuntimeException;

/**
 * The test surface of the App\Api\Jwt signing seam. The security property under
 * test is fail-closed: a server whose Jwt.secret is unset, still a shipped
 * placeholder, or too short must be unable to EITHER mint a token or accept one
 * — there is no weak-default mode where a guessable key silently signs auth.
 *
 * The secret resolves from Configure at call time, so each test swaps
 * Jwt.secret in and asserts the behaviour; setUp/tearDown restore the real
 * configured value so no test leaks its key into the next.
 */
class JwtTest extends TestCase
{
    private const NOW = 1_700_000_000;

    /** A strong, non-placeholder key for the happy-path cases. */
    private const GOOD_SECRET = '0123456789abcdef0123456789abcdef0123456789abcdef';

    private mixed $originalSecret = null;

    private mixed $originalSalt = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->originalSecret = Configure::read('Jwt.secret');
        $this->originalSalt = Configure::read('Security.salt');
    }

    protected function tearDown(): void
    {
        Configure::write('Jwt.secret', $this->originalSecret);
        Configure::write('Security.salt', $this->originalSalt);
        parent::tearDown();
    }

    public function testRoundTripWithAStrongSecret(): void
    {
        Configure::write('Jwt.secret', self::GOOD_SECRET);

        $token = Jwt::encode(['sub' => 'user-1', 'type' => 'ems'], 3600, self::NOW);
        $claims = Jwt::decode($token, self::NOW);

        $this->assertSame('user-1', $claims['sub']);
        $this->assertSame('ems', $claims['type']);
        $this->assertSame(self::NOW + 3600, $claims['exp']);
    }

    /**
     * A token minted under one secret must not verify under a different one —
     * the whole point of the signature. Also guards against the resolver being
     * accidentally constant.
     */
    public function testTokenSignedUnderOneSecretIsRejectedUnderAnother(): void
    {
        Configure::write('Jwt.secret', self::GOOD_SECRET);
        $token = Jwt::encode(['sub' => 'user-1'], 3600, self::NOW);

        Configure::write('Jwt.secret', 'fedcba9876543210fedcba9876543210fedcba98');
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Invalid token signature.');
        Jwt::decode($token, self::NOW);
    }

    /**
     * @return array<string, array{0: mixed}>
     */
    public static function invalidSecretProvider(): array
    {
        return [
            'empty' => [''],
            'null / unset' => [null],
            'shipped Jwt placeholder' => ['a3f1c9e7b2d84605f7a1c3e5d9b8074628f1a2b3c4d5e6f70819cd'],
            'shipped salt placeholder' => ['e84a42a75300082ce06301f4daf9fd64b8c7e65e1fcc97f6799f648787bde5df'],
            'example __JWT_SECRET__ placeholder' => ['__JWT_SECRET__'],
            'too short (31 chars)' => [str_repeat('a', 31)],
        ];
    }

    #[DataProvider('invalidSecretProvider')]
    public function testEncodeRefusesAnInvalidSecret(mixed $secret): void
    {
        Configure::write('Jwt.secret', $secret);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('JWT signing secret is not configured');
        Jwt::encode(['sub' => 'user-1'], 3600, self::NOW);
    }

    #[DataProvider('invalidSecretProvider')]
    public function testDecodeRefusesAnInvalidSecret(mixed $secret): void
    {
        // Mint a real token first, under a good secret...
        Configure::write('Jwt.secret', self::GOOD_SECRET);
        $token = Jwt::encode(['sub' => 'user-1'], 3600, self::NOW);

        // ...then degrade the secret: verification must fail closed, not fall
        // back to some other key.
        Configure::write('Jwt.secret', $secret);
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('JWT signing secret is not configured');
        Jwt::decode($token, self::NOW);
    }

    /**
     * The dropped fallback: even with a perfectly good Security.salt present,
     * an unset Jwt.secret must NOT borrow it — JWT signing has exactly one
     * source now.
     */
    public function testDoesNotFallBackToSecuritySalt(): void
    {
        Configure::write('Jwt.secret', '');
        Configure::write('Security.salt', self::GOOD_SECRET);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('JWT signing secret is not configured');
        Jwt::encode(['sub' => 'user-1'], 3600, self::NOW);
    }

    /** The real configured secret (from app_local.php) must itself be valid. */
    public function testConfiguredSecretIsStrongEnoughToSign(): void
    {
        // No override — uses whatever app_local.php provisioned.
        $token = Jwt::encode(['sub' => 'user-1', 'type' => 'ems'], 3600, self::NOW);
        $claims = Jwt::decode($token, self::NOW);

        $this->assertSame('user-1', $claims['sub']);
    }
}
