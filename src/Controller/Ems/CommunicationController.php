<?php
declare(strict_types=1);

namespace App\Controller\Ems;

use App\Ems\Messages;
use App\Ems\Serializer\CommsSerializer;
use Cake\Datasource\EntityInterface;
use Cake\Http\Response;
use Cake\I18n\FrozenDate;
use Cake\Utility\Text;

/**
 * Communication (document.md §3.20): the notice board, the portal feed, the
 * consent-aware delivery pipeline, contact preferences and derived alerts.
 *
 * Publishing an announcement puts it in the portal feed; pushing it down a
 * channel is a separate, consent-checked step. One send per announcement ever;
 * a `school_news` message needs a recorded yes (silence is never a yes); a
 * failure is retried at most three times, then left for a person to follow up.
 */
class CommunicationController extends AppController
{
    /** teacher/parent/student → the single audience they may read in the feed. */
    private const ROLE_AUDIENCE = ['teacher' => 'teachers', 'parent' => 'parents', 'student' => 'students'];
    private const AUDIENCES = ['everyone', 'teachers', 'parents', 'students'];
    private const CATEGORIES = ['general', 'academic', 'fees', 'event', 'urgent'];
    private const CHANNELS = ['email', 'sms'];
    private const PURPOSES = ['transactional', 'school_news'];

    // --- announcements (management list + detail) ----------------------------

    /** GET /announcements — staff management list. Newest (publishedOn??createdOn) first. */
    public function index(): Response
    {
        $params = $this->pageParams();
        $needle = strtolower(trim((string)$this->request->getQuery('query', '')));
        $audience = (string)$this->request->getQuery('audience', 'all');
        $status = (string)$this->request->getQuery('status', 'all');

        $rows = $this->announcementsForSort();
        $filtered = [];
        foreach ($rows as $a) {
            $audOk = $audience === 'all' || (string)$a->audience === $audience;
            $statusOk = $status === 'all' || (string)$a->status === $status;
            $matches = $needle === ''
                || str_contains(strtolower((string)$a->title), $needle)
                || str_contains(strtolower((string)$a->body), $needle);
            if ($audOk && $statusOk && $matches) {
                $filtered[] = $a;
            }
        }

        $total = count($filtered);
        $page = array_slice($filtered, ($params['page'] - 1) * $params['pageSize'], $params['pageSize']);

        return $this->paginated(
            array_map([CommsSerializer::class, 'announcement'], $page),
            $total,
            $params['page'],
            $params['pageSize']
        );
    }

    /** GET /announcements/feed — published only; role's audience + everyone; pinned first. */
    public function feed(): Response
    {
        $params = $this->pageParams();
        $audience = $this->comms()->feedAudienceForRole($this->viewer->role);

        $rows = [];
        foreach ($this->announcementsForSort() as $a) {
            if ((string)$a->status !== 'published') {
                continue;
            }
            if ($audience === 'all' || (string)$a->audience === 'everyone' || (string)$a->audience === $audience) {
                $rows[] = $a;
            }
        }
        // Pinned first, then most recently published — a stable sort over the
        // already newest-first list keeps ties in newest-first order.
        usort($rows, function ($a, $b) {
            $pa = (bool)$a->pinned;
            $pb = (bool)$b->pinned;
            if ($pa !== $pb) {
                return $pa ? -1 : 1;
            }

            return strcmp($this->publishedKey($b), $this->publishedKey($a));
        });

        $total = count($rows);
        $page = array_slice($rows, ($params['page'] - 1) * $params['pageSize'], $params['pageSize']);

        return $this->paginated(
            array_map([CommsSerializer::class, 'announcement'], $page),
            $total,
            $params['page'],
            $params['pageSize']
        );
    }

    /** GET /announcements/{id} — 404 first; non-staff may only open a published
     *  announcement addressed to them. */
    public function view(string $id): Response
    {
        $announcement = $this->findAnnouncement($id);
        $mine = self::ROLE_AUDIENCE[$this->viewer->role] ?? null;
        if ($mine !== null) {
            $addressed = (string)$announcement->status === 'published'
                && ((string)$announcement->audience === 'everyone' || (string)$announcement->audience === $mine);
            if (!$addressed) {
                $this->fail(403, Messages::ANNOUNCEMENT_FORBIDDEN);
            }
        }

        return $this->json(CommsSerializer::announcement($announcement));
    }

    /** POST /announcements — { data: AnnouncementInput, publish: bool }. */
    public function add(): Response
    {
        $body = $this->body();
        $data = is_array($body['data'] ?? null) ? $body['data'] : $body;
        $publish = (bool)($body['publish'] ?? false);
        $today = FrozenDate::today();
        $this->assertAnnouncementData($data);

        $announcements = $this->fetchTable('EmsAnnouncements');
        $announcement = $announcements->newEntity([
            'school_id' => $this->viewer->schoolId,
            'title' => trim((string)($data['title'] ?? '')),
            'body' => (string)($data['body'] ?? ''),
            'audience' => (string)($data['audience'] ?? 'everyone'),
            'category' => (string)($data['category'] ?? 'general'),
            'status' => $publish ? 'published' : 'draft',
            'author_name' => $this->viewer->name,
            'created_on' => $today,
            'published_on' => $publish ? $today : null,
            'pinned' => (bool)($data['pinned'] ?? false),
        ], ['validate' => false]);
        $announcements->saveOrFail($announcement);

        return $this->json(CommsSerializer::announcement($announcement), 201);
    }

    /** POST /announcements/{id}/publish */
    /** PUT /announcements/{id} — a DRAFT can be corrected; sent mail cannot. */
    public function edit(string $id): Response
    {
        $announcements = $this->fetchTable('EmsAnnouncements');
        $row = $this->tenant()->query('EmsAnnouncements')
            ->where(['id' => $id])
            ->first();
        if ($row === null) {
            $this->fail(404, Messages::ANNOUNCEMENT_NOT_FOUND);
        }
        if ((string)$row->status !== 'draft') {
            $this->fail(422, Messages::ANNOUNCEMENT_SENT_LOCKED);
        }
        $body = $this->body();
        $this->assertAnnouncementData($body, true);
        foreach (['title', 'body', 'audience', 'category'] as $field) {
            if (array_key_exists($field, $body)) {
                $row->{$field} = (string)$body[$field];
            }
        }
        $announcements->saveOrFail($row);

        return $this->json(CommsSerializer::announcement($row));
    }

    /** DELETE /announcements/{id} — drafts only. */
    public function delete(string $id): Response
    {
        $announcements = $this->fetchTable('EmsAnnouncements');
        $row = $this->tenant()->query('EmsAnnouncements')
            ->where(['id' => $id])
            ->first();
        if ($row !== null) {
            if ((string)$row->status !== 'draft') {
                $this->fail(422, Messages::ANNOUNCEMENT_SENT_LOCKED);
            }
            $announcements->deleteOrFail($row);
        }

        return $this->response->withStatus(204);
    }

    public function publish(string $id): Response
    {
        $announcement = $this->findAnnouncement($id);
        if ((string)$announcement->status === 'published') {
            $this->fail(422, Messages::ANNOUNCEMENT_ALREADY_PUBLISHED);
        }
        $announcement->status = 'published';
        $announcement->published_on = FrozenDate::today();
        $this->fetchTable('EmsAnnouncements')->saveOrFail($announcement);

        return $this->json(CommsSerializer::announcement($announcement));
    }

    // --- the sent-notification log -------------------------------------------

    /** GET /notifications — staff. sentOn desc. */
    public function notifications(): Response
    {
        $params = $this->pageParams();
        $channel = (string)$this->request->getQuery('channel', 'all');
        $kind = (string)$this->request->getQuery('kind', 'all');

        $rows = $this->tenant()->query('EmsNotifications')
            ->orderByDesc('sent_on')->orderByDesc('created')->all()->toList();
        $filtered = [];
        foreach ($rows as $n) {
            if (($channel === 'all' || (string)$n->channel === $channel)
                && ($kind === 'all' || (string)$n->kind === $kind)) {
                $filtered[] = $n;
            }
        }

        $total = count($filtered);
        $page = array_slice($filtered, ($params['page'] - 1) * $params['pageSize'], $params['pageSize']);

        return $this->paginated(
            array_map([CommsSerializer::class, 'notification'], $page),
            $total,
            $params['page'],
            $params['pageSize']
        );
    }

    // --- delivery ------------------------------------------------------------

    /** GET /announcements/audience-preview — counts + 5-sample, masked. Previews
     *  a chosen audience/purpose/channel; not tied to a specific announcement. */
    public function audiencePreview(): Response
    {
        $audience = (string)$this->request->getQuery('audience', 'everyone');
        $purpose = (string)$this->request->getQuery('purpose', 'transactional');
        $channel = (string)$this->request->getQuery('channel', 'email');
        $this->assertAudience($audience);
        $this->assertChannelAndPurpose($channel, $purpose);
        $comms = $this->comms();

        $members = $comms->resolveAudience($audience, $channel);
        $reachable = 0;
        $suppressed = 0;
        $unreachable = 0;
        $sample = [];
        foreach ($members as $m) {
            if ($m['address'] === '') {
                $unreachable++;
                continue;
            }
            if (!$comms->consentFor($m['personId'], $channel, $purpose)['allowed']) {
                $suppressed++;
                continue;
            }
            $reachable++;
            if (count($sample) < 5) {
                $row = ['personName' => $m['personName'], 'address' => $comms->maskAddress($m['address'])];
                if ($m['aboutStudentName'] !== null) {
                    $row['aboutStudentName'] = $m['aboutStudentName'];
                }
                $sample[] = $row;
            }
        }

        return $this->json([
            'audience' => $audience,
            'purpose' => $purpose,
            'channel' => $channel,
            'total' => count($members),
            'reachable' => $reachable,
            'suppressed' => $suppressed,
            'unreachable' => $unreachable,
            'sample' => $sample,
        ]);
    }

    /** POST /announcements/{id}/deliver — { channel, purpose }. One send ever. */
    public function deliver(string $id): Response
    {
        $announcement = $this->findAnnouncement($id);
        if ((string)$announcement->status !== 'published') {
            $this->fail(422, Messages::DELIVER_NOT_PUBLISHED);
        }
        $recipients = $this->fetchTable('EmsMessageRecipients');
        $notifications = $this->fetchTable('EmsNotifications');
        $already = $this->tenant()->query('EmsMessageRecipients')->where(['announcement_id' => $id])->count()
            + $this->tenant()->query('EmsNotifications')->where(['announcement_id' => $id])->count();
        if ($already > 0) {
            $this->fail(409, Messages::DELIVER_ALREADY_SENT);
        }

        $body = $this->body();
        $channel = (string)($body['channel'] ?? 'email');
        $purpose = (string)($body['purpose'] ?? 'transactional');
        $this->assertChannelAndPurpose($channel, $purpose);
        $comms = $this->comms();
        $today = FrozenDate::today()->format('Y-m-d');
        try {
            $report = $notifications->getConnection()->transactional(
                function () use ($notifications, $recipients, $announcement, $id, $channel, $purpose, $comms, $today) {
                    // The unique announcement log row is created first. It is
                    // the one-send marker even when the audience is empty.
                    $paragraphs = explode("\n\n", (string)$announcement->body);
                    $notification = $notifications->newEntity([
                        'school_id' => $this->viewer->schoolId,
                        'announcement_id' => $id,
                        'channel' => $channel,
                        'kind' => 'announcement',
                        'subject' => (string)$announcement->title,
                        'body' => $paragraphs[0] ?? '',
                        'audience_label' => $comms->audienceLabel((string)$announcement->audience),
                        'recipient_count' => 0,
                        'sent_on' => $today,
                        'sent_by' => $this->viewer->name,
                    ], ['validate' => false]);
                    $notifications->saveOrFail($notification);

                    foreach ($comms->resolveAudience((string)$announcement->audience, $channel) as $member) {
                        // The row id is the stable per-recipient delivery key.
                        $recipientId = Text::uuid();
                        $row = [
                            'school_id' => $this->viewer->schoolId,
                            'announcement_id' => $id,
                            'notification_id' => (string)$notification->id,
                            'person_id' => $member['personId'],
                            'person_name' => $member['personName'],
                            'person_kind' => $member['personKind'],
                            'about_student_name' => $member['aboutStudentName'],
                            'channel' => $channel,
                            'address' => $member['address'] === '' ? '' : $comms->maskAddress($member['address']),
                            'status' => 'queued',
                            'attempts' => 0,
                            'updated_on' => $today,
                        ];
                        if ($member['address'] === '') {
                            $row['status'] = 'suppressed';
                            $row['suppressed_reason'] = 'No ' . ($channel === 'email' ? 'email address' : 'phone number') . ' on file';
                        } else {
                            $consent = $comms->consentFor($member['personId'], $channel, $purpose);
                            if (!$consent['allowed']) {
                                $row['status'] = 'suppressed';
                                $row['suppressed_reason'] = $consent['reason'];
                            } else {
                                $outcome = $comms->attemptDelivery(
                                    $recipientId,
                                    1,
                                    $channel,
                                    $member['address'],
                                    (string)$announcement->title,
                                    (string)$announcement->body,
                                    $member['personName'],
                                    $member['aboutStudentName'],
                                );
                                $row['status'] = $outcome['ok'] ? 'sent' : 'failed';
                                $row['attempts'] = 1;
                                $row['provider_ref'] = $outcome['ref'];
                                $row['failure_reason'] = $outcome['reason'];
                            }
                        }
                        $recipient = $recipients->newEntity($row, ['validate' => false]);
                        $recipient->id = $recipientId;
                        $recipients->saveOrFail($recipient);
                    }

                    $report = $this->buildDeliveryReport($id);
                    $notification->recipient_count = $report['sent'];
                    $notifications->saveOrFail($notification);

                    return $report;
                }
            );
        } catch (\Throwable $error) {
            // A simultaneous request can only win the unique send marker once.
            if ($this->tenant()->query('EmsNotifications')->where(['announcement_id' => $id])->count() > 0) {
                $this->fail(409, Messages::DELIVER_ALREADY_SENT);
            }
            throw $error;
        }

        return $this->json($report);
    }

    /** GET /announcements/{id}/delivery — never-sent → not_sent, empty counters. */
    public function delivery(string $id): Response
    {
        $this->findAnnouncement($id);

        return $this->json($this->buildDeliveryReport($id));
    }

    /** POST /announcements/{id}/delivery/retry — failures with attempts < 3. */
    public function retry(string $id): Response
    {
        $announcement = $this->findAnnouncement($id);
        $recipients = $this->fetchTable('EmsMessageRecipients');
        $rows = $this->tenant()->query('EmsMessageRecipients')
            ->where(['announcement_id' => $id])->all()->toList();
        $retriable = array_values(array_filter(
            $rows,
            fn ($r) => (string)$r->status === 'failed' && (int)$r->attempts < \App\Ems\Comms::MAX_DELIVERY_ATTEMPTS
        ));
        if ($retriable === []) {
            $this->fail(422, Messages::DELIVER_NOTHING_TO_RETRY);
        }
        $comms = $this->comms();
        $audience = $comms->resolveAudience((string)$announcement->audience, (string)$rows[0]->channel);
        $addresses = [];
        foreach ($audience as $member) {
            $addresses[$member['personId']] = $member['address'];
        }
        $today = FrozenDate::today()->format('Y-m-d');
        foreach ($retriable as $row) {
            $attempt = (int)$row->attempts + 1;
            $outcome = $comms->attemptDelivery(
                (string)$row->id,
                $attempt,
                (string)$row->channel,
                (string)($addresses[(string)$row->person_id] ?? ''),
                (string)$announcement->title,
                (string)$announcement->body,
                (string)$row->person_name,
                $row->about_student_name === null ? null : (string)$row->about_student_name,
            );
            $row->attempts = $attempt;
            $row->updated_on = $today;
            if ($outcome['ok']) {
                $row->status = 'sent';
                $row->provider_ref = $outcome['ref'];
                $row->failure_reason = null;
            } else {
                $row->failure_reason = $outcome['reason'];
            }
            $recipients->saveOrFail($row);
        }

        return $this->json($this->buildDeliveryReport($id));
    }

    // --- contact preferences + consent ---------------------------------------

    /** GET /me/preferences — null for staff-office accounts with no contact record. */
    public function myPreferences(): Response
    {
        $identity = $this->comms()->contactIdentityFor($this->viewer->userId, $this->viewer->role, $this->viewer->name);
        if ($identity === null) {
            return $this->json(null);
        }
        $prefs = $this->tenant()->query('EmsContactPreferences')
            ->where(['person_id' => $identity['personId']])
            ->orderByAsc('created')->all()->toList();

        return $this->json([
            'personName' => $identity['personName'],
            'preferences' => array_map([CommsSerializer::class, 'contactPreference'], $prefs),
        ]);
    }

    /** PUT /me/preferences — { channel, purpose, enabled }. */
    public function setPreference(): Response
    {
        $identity = $this->comms()->contactIdentityFor($this->viewer->userId, $this->viewer->role, $this->viewer->name);
        if ($identity === null) {
            $this->fail(404, Messages::PREF_NO_CONTACT);
        }
        $body = $this->body();
        $channel = (string)($body['channel'] ?? 'email');
        $purpose = (string)($body['purpose'] ?? 'school_news');
        $this->assertChannelAndPurpose($channel, $purpose);
        $enabled = (bool)($body['enabled'] ?? false);
        if ($purpose === 'transactional' && !$enabled) {
            $this->fail(422, Messages::PREF_TRANSACTIONAL_LOCKED);
        }
        $today = FrozenDate::today();
        $prefs = $this->fetchTable('EmsContactPreferences');
        $existing = $this->tenant()->query('EmsContactPreferences')->where([
            'person_id' => $identity['personId'],
            'channel' => $channel,
            'purpose' => $purpose,
        ])->first();
        if ($existing !== null) {
            $existing->enabled = $enabled;
            $existing->source = 'Portal';
            $existing->recorded_on = $today;
            $existing->withdrawn_on = $enabled ? null : $today;
            $prefs->saveOrFail($existing);

            return $this->json(CommsSerializer::contactPreference($existing));
        }
        $created = $prefs->newEntity([
            'school_id' => $this->viewer->schoolId,
            'person_id' => $identity['personId'],
            'person_name' => $identity['personName'],
            'channel' => $channel,
            'purpose' => $purpose,
            'enabled' => $enabled,
            'source' => 'Portal',
            'recorded_on' => $today,
            'withdrawn_on' => $enabled ? null : $today,
        ], ['validate' => false]);
        $prefs->saveOrFail($created);

        return $this->json(CommsSerializer::contactPreference($created));
    }

    // --- alerts + reminder blasts --------------------------------------------

    /** GET /alerts — staff. Derived fresh on every read, never stored. */
    public function alerts(): Response
    {

        return $this->json($this->comms()->computeAlerts());
    }

    /** POST /alerts/send — { kind, channel }. Recomputes server-side; stale → 422. */
    public function sendAlert(): Response
    {
        $body = $this->body();
        $kind = (string)($body['kind'] ?? '');
        $channel = (string)($body['channel'] ?? 'email');
        $this->assertOneOf($channel, self::CHANNELS, Messages::COMMS_CHANNEL_INVALID);
        $comms = $this->comms();

        $alert = null;
        foreach ($comms->computeAlerts() as $a) {
            if ($a['kind'] === $kind) {
                $alert = $a;
                break;
            }
        }
        if ($alert === null) {
            $this->fail(422, Messages::ALERT_STALE);
        }
        $message = \App\Ems\Comms::ALERT_MESSAGES[$kind];
        $notifications = $this->fetchTable('EmsNotifications');
        $recipients = $this->fetchTable('EmsMessageRecipients');
        $today = FrozenDate::today()->format('Y-m-d');
        $notification = $notifications->getConnection()->transactional(
            function () use ($notifications, $recipients, $comms, $kind, $channel, $message, $alert, $today) {
                $notification = $notifications->newEntity([
                    'school_id' => $this->viewer->schoolId,
                    'channel' => $channel,
                    'kind' => $message['kind'],
                    'subject' => $message['subject'],
                    'body' => $message['body'],
                    'audience_label' => $alert['audienceLabel'],
                    'recipient_count' => 0,
                    'sent_on' => $today,
                    'sent_by' => $this->viewer->name,
                ], ['validate' => false]);
                $notifications->saveOrFail($notification);

                $sent = 0;
                foreach ($comms->resolveAlertAudience($kind, $channel) as $member) {
                    $recipientId = Text::uuid();
                    $row = [
                        'school_id' => $this->viewer->schoolId,
                        'announcement_id' => null,
                        'notification_id' => (string)$notification->id,
                        'person_id' => $member['personId'],
                        'person_name' => $member['personName'],
                        'person_kind' => $member['personKind'],
                        'about_student_name' => $member['aboutStudentName'],
                        'channel' => $channel,
                        'address' => $member['address'] === '' ? '' : $comms->maskAddress($member['address']),
                        'status' => 'suppressed',
                        'attempts' => 0,
                        'updated_on' => $today,
                    ];
                    if ($member['address'] === '') {
                        $row['suppressed_reason'] = 'No ' . ($channel === 'email' ? 'email address' : 'phone number') . ' on file';
                    } else {
                        $outcome = $comms->attemptDelivery(
                            $recipientId,
                            1,
                            $channel,
                            $member['address'],
                            $message['subject'],
                            $message['body'],
                            $member['personName'],
                            $member['aboutStudentName'],
                        );
                        $row['status'] = $outcome['ok'] ? 'sent' : 'failed';
                        $row['attempts'] = 1;
                        $row['provider_ref'] = $outcome['ref'];
                        $row['failure_reason'] = $outcome['reason'];
                        $sent += $outcome['ok'] ? 1 : 0;
                    }
                    $recipient = $recipients->newEntity($row, ['validate' => false]);
                    $recipient->id = $recipientId;
                    $recipients->saveOrFail($recipient);
                }

                $notification->recipient_count = $sent;
                $notifications->saveOrFail($notification);

                return $notification;
            }
        );

        return $this->json(CommsSerializer::notification($notification));
    }

    // --- helpers -------------------------------------------------------------

    /** All announcements, newest-created first (the mock's unshift order). */
    private function announcementsForSort(): array
    {
        return $this->tenant()->query('EmsAnnouncements')
            ->orderByDesc('created')->orderByDesc('id')->all()->toList();
    }

    private function publishedKey(EntityInterface $a): string
    {
        return $a->published_on !== null
            ? (string)\App\Ems\Serializer\Wire::date($a->published_on)
            : (string)\App\Ems\Serializer\Wire::date($a->created_on);
    }

    private function findAnnouncement(string $id): EntityInterface
    {
        return $this->findOr404('EmsAnnouncements', $id, Messages::ANNOUNCEMENT_NOT_FOUND);
    }

    /** Validate the public announcement input before it reaches the database. */
    private function assertAnnouncementData(array $data, bool $partial = false): void
    {
        if (!$partial || array_key_exists('title', $data)) {
            $title = trim((string)($data['title'] ?? ''));
            if ($title === '') {
                $this->fail(422, Messages::ANNOUNCEMENT_TITLE_REQUIRED);
            }
            if (mb_strlen($title) > 190) {
                $this->fail(422, Messages::ANNOUNCEMENT_TITLE_TOO_LONG);
            }
        }
        if (!$partial || array_key_exists('body', $data)) {
            if (trim((string)($data['body'] ?? '')) === '') {
                $this->fail(422, Messages::ANNOUNCEMENT_BODY_REQUIRED);
            }
        }
        if (!$partial || array_key_exists('audience', $data)) {
            $this->assertAudience((string)($data['audience'] ?? ''));
        }
        if (!$partial || array_key_exists('category', $data)) {
            $this->assertOneOf(
                (string)($data['category'] ?? ''),
                self::CATEGORIES,
                Messages::ANNOUNCEMENT_CATEGORY_INVALID
            );
        }
    }

    private function assertAudience(string $audience): void
    {
        $this->assertOneOf($audience, self::AUDIENCES, Messages::ANNOUNCEMENT_AUDIENCE_INVALID);
    }

    private function assertChannelAndPurpose(string $channel, string $purpose): void
    {
        $this->assertOneOf($channel, self::CHANNELS, Messages::COMMS_CHANNEL_INVALID);
        $this->assertOneOf($purpose, self::PURPOSES, Messages::COMMS_PURPOSE_INVALID);
    }

    private function assertOneOf(string $value, array $allowed, string $message): void
    {
        if (!in_array($value, $allowed, true)) {
            $this->fail(422, $message);
        }
    }

    /** The delivery report over one announcement's recipient rows. */
    private function buildDeliveryReport(string $announcementId): array
    {
        $rows = $this->tenant()->query('EmsMessageRecipients')
            ->where(['announcement_id' => $announcementId])
            ->all()->toList();
        $order = \App\Ems\Comms::STATUS_ORDER;
        usort($rows, fn ($a, $b) =>
            (($order[(string)$a->status] ?? 9) <=> ($order[(string)$b->status] ?? 9))
            ?: strcmp((string)$a->person_name, (string)$b->person_name));

        $count = fn (string $status) => count(array_filter($rows, fn ($r) => (string)$r->status === $status));
        $needsFollowUp = count(array_filter(
            $rows,
            fn ($r) => (string)$r->status === 'failed' && (int)$r->attempts >= \App\Ems\Comms::MAX_DELIVERY_ATTEMPTS
        ));

        return [
            'announcementId' => $announcementId,
            'status' => $this->comms()->deliveryStatusOf($rows),
            'channel' => $rows === [] ? null : (string)$rows[0]->channel,
            'total' => count($rows),
            'sent' => $count('sent'),
            'failed' => $count('failed'),
            'queued' => $count('queued'),
            'suppressed' => $count('suppressed'),
            'needsFollowUp' => $needsFollowUp,
            'recipients' => array_map([CommsSerializer::class, 'messageRecipient'], $rows),
        ];
    }
}
