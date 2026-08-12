<?php
declare(strict_types=1);

namespace App\Test\TestCase\Ems;

use App\Ems\ClamAv;
use Cake\TestSuite\TestCase;

final class ClamAvTest extends TestCase
{
    /**
     * @var string|false
     */
    private $host;

    protected function setUp(): void
    {
        parent::setUp();
        $this->host = getenv('EMS_CLAMAV_HOST');
        putenv('EMS_CLAMAV_HOST');
    }

    protected function tearDown(): void
    {
        putenv($this->host === false ? 'EMS_CLAMAV_HOST' : 'EMS_CLAMAV_HOST=' . $this->host);
        parent::tearDown();
    }

    public function testUnavailableScannerFailsClosed(): void
    {
        $this->assertSame('quarantined', (new ClamAv())->scan('%PDF-1.4'));
    }
}
