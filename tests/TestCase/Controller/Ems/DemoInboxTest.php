<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller\Ems;

use Cake\Cache\Cache;
use Cake\Utility\Text;

/**
 * The platform demo-requests inbox (CRM-lite) — DemoInboxController.
 *
 * The surface is tenant-less and gated to the `platform_staff` role: staff list
 * and search the pipeline, read a request with its note trail, move its stage,
 * and append notes. These pin the contract plus the two authority invariants —
 * a school role is refused, and an anonymous caller is refused — so the inbox
 * can never leak cross-tenant leads to a tenant user.
 */
class DemoInboxTest extends EmsIntegrationTestCase
{
    protected const CLEANUP_TABLES = [
        'ems_demo_request_notes',
        'ems_demo_requests',
        'ems_users',
        'ems_schools',
    ];

    private string $platformId = '';

    protected function setUp(): void
    {
        parent::setUp();
        Cache::clear();
        $this->platformId = Text::uuid();
    }

    protected function tearDown(): void
    {
        Cache::clear();
        parent::tearDown();
    }

    /** Authenticate the next request as a platform-staff operator. */
    private function authAsPlatform(): void
    {
        $this->authAs('platform_staff', $this->platformId, 'Platform Op');
    }

    /** Insert one demo request row (non-tenant), returning its id. */
    private function seedRequest(array $overrides = []): string
    {
        $id = $overrides['id'] ?? Text::uuid();
        $this->insertRow('ems_demo_requests', $overrides + [
            'id' => $id,
            'institution_name' => 'Greenfield College',
            'contact_name' => 'Ada Okafor',
            'email' => 'ada@greenfield.edu',
            'phone' => '+2348012345678',
            'status' => 'new',
        ]);

        return $id;
    }

    public function testPlatformStaffListsRequestsNewestFirst(): void
    {
        $this->seedRequest([
            'institution_name' => 'Older School',
            'created' => '2026-08-01 09:00:00',
        ]);
        $this->seedRequest([
            'institution_name' => 'Newer School',
            'created' => '2026-08-17 09:00:00',
        ]);

        $this->authAsPlatform();
        $this->get('/api/ems/platform/demo-requests');

        $this->assertResponseOk();
        $body = $this->responseJson();
        $this->assertSame(2, $body['total']);
        $this->assertSame(1, $body['page']);
        $this->assertCount(2, $body['items']);
        // Newest first.
        $this->assertSame('Newer School', $body['items'][0]['institutionName']);
        $this->assertSame('Older School', $body['items'][1]['institutionName']);
        // The list row carries the pipeline stage.
        $this->assertSame('new', $body['items'][0]['status']);
    }

    public function testListFiltersByStatus(): void
    {
        $this->seedRequest(['institution_name' => 'Fresh Lead', 'status' => 'new']);
        $this->seedRequest(['institution_name' => 'Working Lead', 'status' => 'contacted']);

        $this->authAsPlatform();
        $this->get('/api/ems/platform/demo-requests?status=contacted');

        $this->assertResponseOk();
        $body = $this->responseJson();
        $this->assertSame(1, $body['total']);
        $this->assertSame('Working Lead', $body['items'][0]['institutionName']);
    }

    public function testListSearchesInstitutionContactAndEmail(): void
    {
        $this->seedRequest(['institution_name' => 'Sunrise Academy', 'email' => 'head@sunrise.edu']);
        $this->seedRequest(['institution_name' => 'Moonlight School', 'email' => 'admin@moonlight.edu']);

        $this->authAsPlatform();
        $this->get('/api/ems/platform/demo-requests?q=moonlight');

        $this->assertResponseOk();
        $body = $this->responseJson();
        $this->assertSame(1, $body['total']);
        $this->assertSame('Moonlight School', $body['items'][0]['institutionName']);
    }

    public function testListPaginates(): void
    {
        for ($i = 0; $i < 3; $i++) {
            $this->seedRequest(['institution_name' => 'School ' . $i]);
        }

        $this->authAsPlatform();
        $this->get('/api/ems/platform/demo-requests?page=1&pageSize=2');

        $this->assertResponseOk();
        $body = $this->responseJson();
        $this->assertSame(3, $body['total']);
        $this->assertSame(2, $body['pageSize']);
        $this->assertCount(2, $body['items']);
    }

    public function testSummaryCountsEachStage(): void
    {
        $this->seedRequest(['status' => 'new']);
        $this->seedRequest(['status' => 'new']);
        $this->seedRequest(['status' => 'won']);

        $this->authAsPlatform();
        $this->get('/api/ems/platform/demo-requests/summary');

        $this->assertResponseOk();
        $body = $this->responseJson();
        $this->assertSame(3, $body['total']);
        $this->assertSame(2, $body['byStatus']['new']);
        $this->assertSame(1, $body['byStatus']['won']);
        $this->assertSame(0, $body['byStatus']['lost']);
    }

    public function testViewReturnsDetailWithEmptyNotes(): void
    {
        $id = $this->seedRequest(['role_title' => 'Head of ICT', 'message' => 'Keen to see fees.']);

        $this->authAsPlatform();
        $this->get('/api/ems/platform/demo-requests/' . $id);

        $this->assertResponseOk();
        $body = $this->responseJson();
        $this->assertSame($id, $body['id']);
        $this->assertSame('Head of ICT', $body['roleTitle']);
        $this->assertSame('Keen to see fees.', $body['message']);
        // notes is always present — an empty array, never omitted.
        $this->assertSame([], $body['notes']);
        $this->assertArrayHasKey('updatedAt', $body);
    }

    public function testUpdateStatusMovesTheStage(): void
    {
        $id = $this->seedRequest();

        $this->authAsPlatform();
        $this->patch('/api/ems/platform/demo-requests/' . $id . '/status', ['status' => 'qualified']);

        $this->assertResponseOk();
        $body = $this->responseJson();
        $this->assertSame('qualified', $body['status']);
        $this->assertTrue($this->rowExists('ems_demo_requests', ['id' => $id, 'status' => 'qualified']));
    }

    public function testUpdateStatusRejectsUnknownStage(): void
    {
        $id = $this->seedRequest();

        $this->authAsPlatform();
        $this->patch('/api/ems/platform/demo-requests/' . $id . '/status', ['status' => 'banana']);

        $this->assertResponseCode(422);
        $this->assertTrue($this->rowExists('ems_demo_requests', ['id' => $id, 'status' => 'new']));
    }

    public function testUpdateStatusUnknownRequestIs404(): void
    {
        $this->authAsPlatform();
        $this->patch('/api/ems/platform/demo-requests/' . Text::uuid() . '/status', ['status' => 'won']);

        $this->assertResponseCode(404);
    }

    public function testAddNoteAppendsAuthoredNote(): void
    {
        $id = $this->seedRequest();

        $this->authAsPlatform();
        $this->post('/api/ems/platform/demo-requests/' . $id . '/notes', ['body' => 'Called, left a voicemail.']);

        $this->assertResponseCode(201);
        $body = $this->responseJson();
        $this->assertCount(1, $body['notes']);
        $this->assertSame('Called, left a voicemail.', $body['notes'][0]['body']);
        $this->assertSame('Platform Op', $body['notes'][0]['authorName']);
        $this->assertTrue($this->rowExists('ems_demo_request_notes', [
            'demo_request_id' => $id,
            'author_user_id' => $this->platformId,
        ]));
    }

    public function testAddNoteRejectsEmptyBody(): void
    {
        $id = $this->seedRequest();

        $this->authAsPlatform();
        $this->post('/api/ems/platform/demo-requests/' . $id . '/notes', ['body' => '   ']);

        $this->assertResponseCode(422);
        $this->assertSame(0, $this->rowCount('ems_demo_request_notes', ['demo_request_id' => $id]));
    }

    public function testSchoolRoleIsForbidden(): void
    {
        $id = $this->seedRequest();

        // A school administrator must never reach the cross-tenant inbox.
        $this->authAsAdmin();
        $this->get('/api/ems/platform/demo-requests');
        $this->assertResponseCode(403);

        $this->authAsAdmin();
        $this->get('/api/ems/platform/demo-requests/' . $id);
        $this->assertResponseCode(403);

        $this->authAsAdmin();
        $this->patch('/api/ems/platform/demo-requests/' . $id . '/status', ['status' => 'won']);
        $this->assertResponseCode(403);
    }

    public function testAnonymousIsUnauthorized(): void
    {
        $this->seedRequest();

        $this->get('/api/ems/platform/demo-requests');

        $this->assertResponseCode(401);
    }
}
