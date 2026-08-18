<?php
declare(strict_types=1);

namespace App\Controller\Ems;

use App\Ems\Email;
use App\Ems\Messages;
use App\Ems\Resend;
use App\Ems\Serializer\DemoRequestSerializer;
use Cake\Core\Configure;
use Cake\Datasource\EntityInterface;
use Cake\Http\Response;
use Cake\Log\Log;
use Throwable;

/**
 * Public book-a-demo lead capture (unauthenticated).
 *
 * `create` is a public action: a prospect has no account and no school, so there
 * is no viewer and no tenant. The row is written directly (outside `Tenant`),
 * then two e-mails are sent best-effort — a team notification (Reply-To the
 * prospect) and an acknowledgement to the prospect. A mail outage never fails
 * the save; the row is the durable record. Rate-limited and honeypot-guarded
 * like the public admissions endpoint.
 */
class DemoRequestsController extends AppController
{
    protected array $publicActions = ['create'];

    /** Size bands offered by the form → the label shown in the team e-mail. */
    private const SIZE_BANDS = [
        'under_100' => 'Under 100 students',
        '100_500' => '100–500 students',
        '500_1000' => '500–1,000 students',
        '1000_plus' => 'Over 1,000 students',
    ];

    /**
     * POST /public/demo-requests — capture one demo request.
     */
    public function create(): Response
    {
        $this->rateLimit('demo', 5, 900);

        $body = $this->body();

        // Honeypot: a real person never fills the hidden `website` field. If it
        // carries anything, silently accept without writing — the bot gets a 201
        // and learns nothing.
        if (trim((string)($body['website'] ?? '')) !== '') {
            return $this->json(['status' => 'new'], 201);
        }

        $institution = $this->clip((string)($body['institutionName'] ?? ''), 190);
        $contact = $this->clip((string)($body['contactName'] ?? ''), 190);
        $email = $this->clip((string)($body['email'] ?? ''), 190);
        $phone = $this->clip((string)($body['phone'] ?? ''), 40);

        if ($institution === '') {
            $this->fail(422, Messages::DEMO_INSTITUTION_REQUIRED);
        }
        if ($contact === '') {
            $this->fail(422, Messages::DEMO_CONTACT_REQUIRED);
        }
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $this->fail(422, Messages::DEMO_EMAIL_INVALID);
        }
        if ($phone === '') {
            $this->fail(422, Messages::DEMO_PHONE_REQUIRED);
        }

        $sizeBand = (string)($body['sizeBand'] ?? '');
        $sizeBand = isset(self::SIZE_BANDS[$sizeBand]) ? $sizeBand : null;

        $requests = $this->fetchTable('EmsDemoRequests');
        $request = $requests->saveOrFail($requests->newEntity([
            'institution_name' => $institution,
            'contact_name' => $contact,
            'email' => $email,
            'phone' => $phone,
            'role_title' => $this->clipOrNull((string)($body['roleTitle'] ?? ''), 120),
            'location' => $this->clipOrNull((string)($body['location'] ?? ''), 190),
            'size_band' => $sizeBand,
            'message' => $this->clipOrNull((string)($body['message'] ?? ''), 2000),
            'status' => 'new',
        ], ['validate' => false]));

        $this->notify($request, $sizeBand);

        return $this->json(DemoRequestSerializer::request($request), 201);
    }

    /** Send both e-mails best-effort — never let a mail failure bounce the save. */
    private function notify(EntityInterface $request, ?string $sizeBand): void
    {
        $to = trim((string)Configure::read('Ems.demoNotifyEmail', ''));
        $email = (string)$request->email;

        try {
            if ($to !== '') {
                $notify = Email::demoRequestNotify((string)$request->institution_name, [
                    'Institution' => (string)$request->institution_name,
                    'Contact' => (string)$request->contact_name,
                    'Email' => $email,
                    'Phone' => (string)$request->phone,
                    'Role' => (string)($request->role_title ?? ''),
                    'Location' => (string)($request->location ?? ''),
                    'School size' => $sizeBand === null ? '' : self::SIZE_BANDS[$sizeBand],
                    'Message' => (string)($request->message ?? ''),
                ]);
                Resend::deliver(
                    $to,
                    sprintf('New demo request — %s', (string)$request->institution_name),
                    $notify['text'],
                    $notify['html'],
                    $email,
                );
            }
        } catch (Throwable $e) {
            Log::error(sprintf('EMS demo-request team notification failed for %s: %s', $email, $e->getMessage()));
        }

        try {
            $ack = Email::demoRequestAck((string)$request->contact_name, (string)$request->institution_name);
            Resend::deliver($email, 'We have received your demo request', $ack['text'], $ack['html']);
        } catch (Throwable $e) {
            Log::error(sprintf('EMS demo-request acknowledgement failed for %s: %s', $email, $e->getMessage()));
        }
    }

    /** Trim and hard-cap a string to a column width. */
    private function clip(string $value, int $max): string
    {
        return mb_substr(trim($value), 0, $max);
    }

    /** Like clip(), but an empty result becomes null for a nullable column. */
    private function clipOrNull(string $value, int $max): ?string
    {
        $value = $this->clip($value, $max);

        return $value === '' ? null : $value;
    }
}
