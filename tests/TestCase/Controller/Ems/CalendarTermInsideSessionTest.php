<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller\Ems;

use Cake\Utility\Text;

/**
 * POST /calendar/sessions/{id}/terms — the term-inside-session boundary check.
 *
 * Regression guard for assertInsideSession(): the session's date columns come
 * back as Cake\I18n\Date, whose (string) cast is the locale SHORT format
 * ("9/1/26"), not "2026-09-01". Comparing an incoming ISO date against that
 * locale string rejected every in-range term. The check must format the
 * boundaries as Y-m-d before comparing, so this exercises the real controller
 * and ORM rather than a string stub.
 */
class CalendarTermInsideSessionTest extends EmsIntegrationTestCase
{
    protected const CLEANUP_TABLES = [
        'ems_audit_events',
        'ems_academic_terms',
        'ems_academic_sessions',
        'ems_refresh_tokens',
        'ems_password_resets',
        'ems_users',
        'ems_schools',
    ];

    private string $sessionId = '';

    protected function setUp(): void
    {
        parent::setUp();
        $this->sessionId = Text::uuid();
        $this->insertRow('ems_academic_sessions', [
            'id' => $this->sessionId,
            'school_id' => $this->schoolId,
            'name' => '2026/2027',
            'starts_on' => '2026-09-01',
            'ends_on' => '2027-07-31',
            'status' => 'open',
        ]);
    }

    public function testInRangeTermIsCreated(): void
    {
        $this->authAsAdmin();
        $this->post($this->schoolPath('/calendar/sessions/' . $this->sessionId . '/terms'), [
            'name' => 'First', 'startsOn' => '2026-09-01', 'endsOn' => '2026-12-15',
        ]);
        $this->assertResponseCode(201);
        $this->assertSame('First', $this->responseJson()['name'] ?? null);
    }

    public function testTermSpanningTheFullSessionIsCreated(): void
    {
        $this->authAsAdmin();
        $this->post($this->schoolPath('/calendar/sessions/' . $this->sessionId . '/terms'), [
            'name' => 'First', 'startsOn' => '2026-09-01', 'endsOn' => '2027-07-31',
        ]);
        $this->assertResponseCode(201);
    }

    public function testTermStartingBeforeSessionIsRejected(): void
    {
        $this->authAsAdmin();
        $this->post($this->schoolPath('/calendar/sessions/' . $this->sessionId . '/terms'), [
            'name' => 'First', 'startsOn' => '2026-08-15', 'endsOn' => '2026-12-15',
        ]);
        $this->assertResponseCode(422);
        $this->assertSame(
            'Term dates must fall inside the 2026/2027 session.',
            $this->responseJson()['message'] ?? null,
        );
    }

    public function testTermEndingAfterSessionIsRejected(): void
    {
        $this->authAsAdmin();
        $this->post($this->schoolPath('/calendar/sessions/' . $this->sessionId . '/terms'), [
            'name' => 'Third', 'startsOn' => '2027-05-01', 'endsOn' => '2027-09-30',
        ]);
        $this->assertResponseCode(422);
        $this->assertSame(
            'Term dates must fall inside the 2026/2027 session.',
            $this->responseJson()['message'] ?? null,
        );
    }
}
