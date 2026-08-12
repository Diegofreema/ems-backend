<?php
declare(strict_types=1);

namespace App\Test\TestCase\Ems;

use App\Ems\FinanceKeys;
use Cake\TestSuite\TestCase;
use RuntimeException;

final class FinanceKeysTest extends TestCase
{
    /**
     * @var array<string, string|false>
     */
    private array $environment = [];

    protected function setUp(): void
    {
        parent::setUp();
        foreach ($this->names() as $name) {
            $this->environment[$name] = getenv($name);
            putenv($name);
        }
    }

    protected function tearDown(): void
    {
        foreach ($this->environment as $name => $value) {
            putenv($value === false ? $name : $name . '=' . $value);
        }
        parent::tearDown();
    }

    public function testLoadsCanonicalActiveAndRetainedKeys(): void
    {
        putenv('EMS_FINANCE_AUDIT_HMAC_ACTIVE_KEY_ID=active-2');
        putenv('EMS_FINANCE_AUDIT_HMAC_KEYS_JSON={"active-2":"12345678901234567890123456789012","old-1":"retained"}');

        $this->assertSame(
            ['active-2', '12345678901234567890123456789012'],
            FinanceKeys::active()
        );
        $this->assertSame('retained', FinanceKeys::verificationKeys()['old-1']);
    }

    public function testSupportsLegacyEnvironmentDuringTransition(): void
    {
        putenv('EMS_FINANCE_HMAC_KEY_ID=legacy-active');
        putenv('EMS_FINANCE_HMAC_KEY=12345678901234567890123456789012');

        $this->assertSame(
            ['legacy-active', '12345678901234567890123456789012'],
            FinanceKeys::active()
        );
    }

    public function testRejectsShortActiveKey(): void
    {
        putenv('EMS_FINANCE_AUDIT_HMAC_ACTIVE_KEY_ID=too-short');
        putenv('EMS_FINANCE_AUDIT_HMAC_KEYS_JSON={"too-short":"unsafe"}');
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('at least 32 bytes');

        FinanceKeys::active();
    }

    /**
     * @return array<int, string>
     */
    private function names(): array
    {
        return [
            'EMS_FINANCE_AUDIT_HMAC_ACTIVE_KEY_ID',
            'EMS_FINANCE_AUDIT_HMAC_KEYS_JSON',
            'EMS_FINANCE_HMAC_KEY_ID',
            'EMS_FINANCE_HMAC_KEY',
            'EMS_FINANCE_HMAC_KEYS',
        ];
    }
}
