<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller\Ems;

use App\Ems\Messages;
use Cake\Cache\Cache;

/**
 * The public book-a-demo lead-capture endpoint. Unauthenticated and tenant-less
 * (a prospect has no school), so these tests post straight to the public path.
 * Mail is best-effort with no key configured in test, so a successful capture
 * still returns 201 even though delivery is skipped.
 */
final class DemoRequestTest extends EmsIntegrationTestCase
{
    protected const CLEANUP_TABLES = [
        'ems_demo_requests',
        'ems_users',
        'ems_schools',
    ];

    private const PATH = '/api/ems/public/demo-requests';

    protected function setUp(): void
    {
        parent::setUp();
        // Reset the IP rate-limit counter so each test starts from a clean bucket.
        Cache::clear();
    }

    protected function tearDown(): void
    {
        Cache::clear();
        parent::tearDown();
    }

    public function testCapturesAValidRequestAndReturnsTheSerializedRow(): void
    {
        $this->post(self::PATH, [
            'institutionName' => 'Greenfield College',
            'contactName' => 'Ada Okafor',
            'email' => 'ada@greenfield.edu',
            'phone' => '+2348012345678',
            'roleTitle' => 'Head of ICT',
            'location' => 'Lagos, Nigeria',
            'sizeBand' => '500_1000',
            'message' => 'Keen to see the fees module.',
        ]);

        $this->assertResponseCode(201);
        $body = $this->responseJson();
        $this->assertSame('Greenfield College', $body['institutionName']);
        $this->assertSame('Ada Okafor', $body['contactName']);
        $this->assertSame('new', $body['status']);
        $this->assertSame('Head of ICT', $body['roleTitle']);
        $this->assertSame('500_1000', $body['sizeBand']);
        $this->assertArrayHasKey('id', $body);
        $this->assertArrayHasKey('createdAt', $body);
        $this->assertSame(1, $this->rowCount('ems_demo_requests', ['email' => 'ada@greenfield.edu']));
    }

    public function testOmitsEmptyOptionalFieldsFromTheResponse(): void
    {
        $this->post(self::PATH, [
            'institutionName' => 'Sunrise Academy',
            'contactName' => 'Bola Smith',
            'email' => 'bola@sunrise.edu',
            'phone' => '08099998888',
        ]);

        $this->assertResponseCode(201);
        $body = $this->responseJson();
        $this->assertArrayNotHasKey('roleTitle', $body);
        $this->assertArrayNotHasKey('location', $body);
        $this->assertArrayNotHasKey('sizeBand', $body);
        $this->assertArrayNotHasKey('message', $body);
    }

    public function testRequiresAnInstitutionName(): void
    {
        $this->post(self::PATH, [
            'institutionName' => '   ',
            'contactName' => 'Ada Okafor',
            'email' => 'ada@greenfield.edu',
            'phone' => '08012345678',
        ]);

        $this->assertResponseCode(422);
        $this->assertSame(Messages::DEMO_INSTITUTION_REQUIRED, $this->responseJson()['message']);
        $this->assertSame(0, $this->rowCount('ems_demo_requests', []));
    }

    public function testRequiresAContactName(): void
    {
        $this->post(self::PATH, [
            'institutionName' => 'Greenfield College',
            'contactName' => '',
            'email' => 'ada@greenfield.edu',
            'phone' => '08012345678',
        ]);

        $this->assertResponseCode(422);
        $this->assertSame(Messages::DEMO_CONTACT_REQUIRED, $this->responseJson()['message']);
        $this->assertSame(0, $this->rowCount('ems_demo_requests', []));
    }

    public function testRejectsAMalformedEmail(): void
    {
        $this->post(self::PATH, [
            'institutionName' => 'Greenfield College',
            'contactName' => 'Ada Okafor',
            'email' => 'not-an-email',
            'phone' => '08012345678',
        ]);

        $this->assertResponseCode(422);
        $this->assertSame(Messages::DEMO_EMAIL_INVALID, $this->responseJson()['message']);
        $this->assertSame(0, $this->rowCount('ems_demo_requests', []));
    }

    public function testRequiresAPhoneNumber(): void
    {
        $this->post(self::PATH, [
            'institutionName' => 'Greenfield College',
            'contactName' => 'Ada Okafor',
            'email' => 'ada@greenfield.edu',
            'phone' => '',
        ]);

        $this->assertResponseCode(422);
        $this->assertSame(Messages::DEMO_PHONE_REQUIRED, $this->responseJson()['message']);
        $this->assertSame(0, $this->rowCount('ems_demo_requests', []));
    }

    public function testHoneypotSilentlyAcceptsWithoutSaving(): void
    {
        // A bot fills the hidden `website` field: the response looks like success
        // so the bot learns nothing, but no row is written.
        $this->post(self::PATH, [
            'institutionName' => 'Spam Co',
            'contactName' => 'Bot',
            'email' => 'bot@spam.example',
            'phone' => '00000000000',
            'website' => 'http://spam.example',
        ]);

        $this->assertResponseCode(201);
        $this->assertSame(0, $this->rowCount('ems_demo_requests', []));
    }

    public function testDropsAnUnknownSizeBand(): void
    {
        $this->post(self::PATH, [
            'institutionName' => 'Greenfield College',
            'contactName' => 'Ada Okafor',
            'email' => 'ada@greenfield.edu',
            'phone' => '08012345678',
            'sizeBand' => 'gigantic',
        ]);

        $this->assertResponseCode(201);
        $this->assertArrayNotHasKey('sizeBand', $this->responseJson());
        $this->assertSame(1, $this->rowCount('ems_demo_requests', ['size_band IS' => null]));
    }

    public function testRateLimitsAfterFiveRequests(): void
    {
        $payload = [
            'institutionName' => 'Greenfield College',
            'contactName' => 'Ada Okafor',
            'email' => 'ada@greenfield.edu',
            'phone' => '08012345678',
        ];

        for ($i = 0; $i < 5; $i++) {
            $this->post(self::PATH, $payload);
            $this->assertResponseCode(201);
        }

        $this->post(self::PATH, $payload);
        $this->assertResponseCode(429);
    }
}
