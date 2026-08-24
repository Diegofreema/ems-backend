<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller\Ems;

use Cake\Core\Configure;
use Cake\Http\TestSuite\HttpClientTrait;

/**
 * Onboarding must reach every role even when e-mail cannot be delivered
 * (document.md §3.14). A staff invitation used to be all-or-nothing: any
 * delivery failure deleted the account and returned 503, so a school whose
 * Resend sender was misconfigured could not onboard anyone and had no clue
 * why. This proves the resilient behaviour:
 *
 *   - a failed send KEEPS the invited account and returns the one-time code,
 *     exactly as a family invite does, so the admin can hand access over;
 *   - a successful send reports `sent` and exposes no code;
 *   - the code from a failed send actually activates the account;
 *   - Resend behaves the same way.
 *
 * The test environment has no Resend key, so an un-mocked delivery attempt
 * throws at the configuration guard (no network) — that is the failure path.
 * The `sent` case sets a key and mocks the Resend endpoint.
 */
class InviteDeliveryTest extends EmsIntegrationTestCase
{
    use HttpClientTrait;

    protected function setUp(): void
    {
        parent::setUp();
        Configure::write('Ems.resendApiKey', '');
    }

    /** Set a key and stub the Resend HTTP endpoint so a send succeeds. */
    private function mockSuccessfulDelivery(): void
    {
        Configure::write('Ems.resendApiKey', 'test-resend-key');
        Configure::write('Ems.emailFrom', 'EMS <noreply@test.school>');
        $this->mockClientPost(
            'https://api.resend.com/emails',
            $this->newClientResponse(200, ['Content-Type: application/json'], '{"id":"message-1"}'),
        );
    }

    public function testInviteKeepsTheAccountAndReturnsACodeWhenDeliveryFails(): void
    {
        $this->authAsAdmin();
        $this->post($this->schoolPath('/users/invite'), [
            'name' => 'Tunde Registrar',
            'email' => 'tunde.registrar@test.school',
            'role' => 'registrar',
        ]);

        // The account is created (201), not rolled back.
        $this->assertResponseCode(201);
        $body = $this->responseJson();
        $this->assertSame('failed', $body['delivery']['status']);
        $this->assertNotEmpty($body['delivery']['code']);
        // The persisted user object still comes back intact.
        $this->assertSame('tunde.registrar@test.school', $body['email']);
        $this->assertSame('invited', $body['status']);
        $this->assertSame(1, $this->rowCount('ems_users', [
            'email' => 'tunde.registrar@test.school',
            'status' => 'invited',
        ]));
    }

    public function testInviteReportsSentAndHidesTheCodeWhenDeliverySucceeds(): void
    {
        $this->mockSuccessfulDelivery();
        $this->authAsAdmin();
        $this->post($this->schoolPath('/users/invite'), [
            'name' => 'Bisi Bursar',
            'email' => 'bisi.bursar@test.school',
            'role' => 'bursar',
        ]);

        $this->assertResponseCode(201);
        $body = $this->responseJson();
        $this->assertSame('sent', $body['delivery']['status']);
        $this->assertArrayNotHasKey('code', $body['delivery']);
        $this->assertSame(1, $this->rowCount('ems_users', [
            'email' => 'bisi.bursar@test.school',
            'status' => 'invited',
        ]));
    }

    public function testTheCodeFromAFailedInviteActivatesTheAccount(): void
    {
        $this->authAsAdmin();
        $this->post($this->schoolPath('/users/invite'), [
            'name' => 'Remi Registrar',
            'email' => 'remi.registrar@test.school',
            'role' => 'registrar',
        ]);
        $code = (string)$this->responseJson()['delivery']['code'];
        $this->assertNotSame('', $code);

        // The public redemption route turns the invited account active.
        $this->post('/api/ems/auth/invite/accept', ['code' => $code, 'password' => 'StaffPass1']);
        $this->assertResponseOk();
        $this->assertSame(1, $this->rowCount('ems_users', [
            'email' => 'remi.registrar@test.school',
            'status' => 'active',
        ]));
    }

    public function testResendReturnsTheCodeWhenDeliveryFails(): void
    {
        // Seed an invited staff account with a mailbox, delivered earlier.
        $this->mockSuccessfulDelivery();
        $this->authAsAdmin();
        $this->post($this->schoolPath('/users/invite'), [
            'name' => 'Kemi Registrar',
            'email' => 'kemi.registrar@test.school',
            'role' => 'registrar',
        ]);
        $userId = (string)$this->responseJson()['id'];

        // Now e-mail is down again: resend cannot deliver, but the fresh code
        // still comes back rather than a bare 503. (The harness authenticates
        // per request, so re-attach the admin token for this second call.)
        Configure::write('Ems.resendApiKey', '');
        $this->authAsAdmin();
        $this->post($this->schoolPath('/users/' . $userId . '/invite/resend'));

        $this->assertResponseOk();
        $resent = $this->responseJson();
        $this->assertSame('failed', $resent['status']);
        $this->assertNotEmpty($resent['code']);

        // And that code activates the account.
        $this->post('/api/ems/auth/invite/accept', [
            'code' => (string)$resent['code'],
            'password' => 'StaffPass2',
        ]);
        $this->assertResponseOk();
        $this->assertSame(1, $this->rowCount('ems_users', [
            'id' => $userId,
            'status' => 'active',
        ]));
    }
}
