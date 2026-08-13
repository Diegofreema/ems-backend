<?php
declare(strict_types=1);

namespace App\Controller\Ems;

use App\Ems\Messages;
use App\Ems\Serializer\CalendarSerializer;
use Cake\Chronos\ChronosInterface;
use Cake\Datasource\EntityInterface;
use Cake\Http\Response;
use Cake\I18n\FrozenDate;

/**
 * Academic calendar (document.md §3.13). Closed sessions/terms are immutable
 * reference points — every write path checks openness before touching dates.
 * Close/reopen actions write the §4 audit events.
 */
class CalendarController extends AppController
{
    private const TERM_ORDER = ['First' => 1, 'Second' => 2, 'Third' => 3];

    /**
     * GET /calendar/sessions — SessionWithTerms[], newest session first
     * (name desc), terms in First/Second/Third order.
     */
    public function sessions(): Response
    {
        $sessions = $this->tenant()->query('EmsAcademicSessions')
            ->orderByDesc('name')
            ->all()
            ->toList();

        $termsBySession = [];
        foreach (
            $this->tenant()->query('EmsAcademicTerms') as $term
        ) {
            $termsBySession[(string)$term->session_id][] = $term;
        }

        $out = [];
        foreach ($sessions as $session) {
            $terms = $termsBySession[(string)$session->id] ?? [];
            usort($terms, function ($a, $b) {
                return (self::TERM_ORDER[$a->name] ?? 9) <=> (self::TERM_ORDER[$b->name] ?? 9);
            });
            $out[] = CalendarSerializer::sessionWithTerms($session, $terms);
        }

        return $this->json($out);
    }

    /**
     * POST /calendar/sessions { name, startsOn, endsOn }
     */
    public function createSession(): Response
    {
        $body = $this->body();
        $name = trim((string)($body['name'] ?? ''));
        $startsOn = (string)($body['startsOn'] ?? '');
        $endsOn = (string)($body['endsOn'] ?? '');

        if (!preg_match('#^\d{4}/\d{4}$#', $name)) {
            $this->fail(422, Messages::SESSION_NAME_FORMAT);
        }
        if ($startsOn === '' || $endsOn === '') {
            $this->fail(422, Messages::SESSION_DATES_REQUIRED);
        }
        if ($startsOn >= $endsOn) {
            $this->fail(422, Messages::SESSION_DATE_ORDER);
        }

        $sessions = $this->fetchTable('EmsAcademicSessions');
        if ($this->tenant()->exists('EmsAcademicSessions', ['name' => $name])) {
            $this->fail(422, sprintf(Messages::SESSION_EXISTS, $name));
        }
        $overlap = $this->tenant()->query('EmsAcademicSessions')
            ->where([
                'starts_on <=' => $endsOn,
                'ends_on >=' => $startsOn,
            ])
            ->first();
        if ($overlap !== null) {
            $this->fail(422, sprintf(Messages::SESSION_OVERLAP, (string)$overlap->name));
        }

        $session = $sessions->saveOrFail($sessions->newEntity([
            'school_id' => $this->viewer->schoolId,
            'name' => $name,
            'starts_on' => $startsOn,
            'ends_on' => $endsOn,
            'status' => 'open',
        ]));

        return $this->json(CalendarSerializer::session($session), 201);
    }

    /**
     * POST /calendar/sessions/{id}/terms { name, startsOn, endsOn }
     */

    /** PUT /calendar/sessions/{id} — correct an open session's name/dates. */
    public function updateSession(string $id): Response
    {
        $sessions = $this->fetchTable('EmsAcademicSessions');
        $session = $this->tenant()->query('EmsAcademicSessions')
            ->where(['id' => $id])
            ->first();
        if ($session === null) {
            $this->fail(404, Messages::CALENDAR_SESSION_NOT_FOUND);
        }
        if ((string)$session->status === 'closed') {
            $this->fail(422, Messages::SESSION_CLOSED_EDIT);
        }
        $body = $this->body();
        if (array_key_exists('name', $body) && trim((string)$body['name']) !== '') {
            $session->name = trim((string)$body['name']);
        }
        if (array_key_exists('startsOn', $body)) {
            $session->starts_on = (string)$body['startsOn'];
        }
        if (array_key_exists('endsOn', $body)) {
            $session->ends_on = (string)$body['endsOn'];
        }
        if ((string)$session->starts_on >= (string)$session->ends_on) {
            $this->fail(422, Messages::SESSION_DATE_ORDER);
        }
        $sessions->saveOrFail($session);

        return $this->json(CalendarSerializer::session($session));
    }

    public function createTerm(string $id): Response
    {
        $session = $this->findSession($id);
        if ($session->status !== 'open') {
            $this->fail(422, Messages::SESSION_CLOSED_EDIT);
        }

        $body = $this->body();
        $name = trim((string)($body['name'] ?? ''));
        $startsOn = (string)($body['startsOn'] ?? '');
        $endsOn = (string)($body['endsOn'] ?? '');

        if ($startsOn === '' || $endsOn === '') {
            $this->fail(422, Messages::SESSION_DATES_REQUIRED);
        }
        if ($startsOn >= $endsOn) {
            $this->fail(422, Messages::SESSION_DATE_ORDER);
        }
        $this->assertInsideSession($session, $startsOn, $endsOn);

        $terms = $this->fetchTable('EmsAcademicTerms');
        if ($terms->exists(['session_id' => $session->id, 'name' => $name])) {
            $this->fail(422, sprintf(Messages::TERM_EXISTS, $name, (string)$session->name));
        }

        $term = $terms->saveOrFail($terms->newEntity([
            'school_id' => $this->viewer->schoolId,
            'session_id' => (string)$session->id,
            'name' => $name,
            'starts_on' => $startsOn,
            'ends_on' => $endsOn,
            'status' => 'open',
        ]));

        return $this->json(CalendarSerializer::term($term), 201);
    }

    /**
     * PUT /calendar/terms/{id}/dates { startsOn, endsOn } — session AND term
     * must be open.
     */
    public function updateTermDates(string $id): Response
    {
        $term = $this->findTerm($id);
        $session = $this->findSession((string)$term->session_id);

        if ($session->status !== 'open') {
            $this->fail(422, Messages::SESSION_CLOSED_EDIT);
        }
        if ($term->status !== 'open') {
            $this->fail(422, Messages::TERM_CLOSED_EDIT);
        }

        $body = $this->body();
        $startsOn = (string)($body['startsOn'] ?? '');
        $endsOn = (string)($body['endsOn'] ?? '');
        if ($startsOn === '' || $endsOn === '') {
            $this->fail(422, Messages::SESSION_DATES_REQUIRED);
        }
        if ($startsOn >= $endsOn) {
            $this->fail(422, Messages::SESSION_DATE_ORDER);
        }
        $this->assertInsideSession($session, $startsOn, $endsOn);

        $terms = $this->fetchTable('EmsAcademicTerms');
        $term->starts_on = $startsOn;
        $term->ends_on = $endsOn;
        $terms->saveOrFail($term);

        return $this->json(CalendarSerializer::term($term));
    }

    /**
     * POST /calendar/terms/{id}/close — audit `term.closed`.
     */
    public function closeTerm(string $id): Response
    {
        $term = $this->findTerm($id);
        $session = $this->findSession((string)$term->session_id);
        if ($term->status !== 'open') {
            $this->fail(422, Messages::TERM_CLOSED_EDIT);
        }

        $terms = $this->fetchTable('EmsAcademicTerms');
        $term->status = 'closed';
        $term->closed_on = FrozenDate::today();
        $terms->saveOrFail($term);

        $this->audit()->log(
            $this->viewer,
            'term.closed',
            'term',
            (string)$term->id,
            sprintf('Closed the %s term of %s', (string)$term->name, (string)$session->name),
        );

        return $this->json(CalendarSerializer::term($term));
    }

    /**
     * POST /calendar/terms/{id}/reopen { reason } — only a closed term, in an
     * open session, with a reason. Audit `term.reopened`.
     */
    public function reopenTerm(string $id): Response
    {
        $term = $this->findTerm($id);
        $session = $this->findSession((string)$term->session_id);
        $reason = trim((string)($this->body()['reason'] ?? ''));

        if ($term->status !== 'closed') {
            $this->fail(422, Messages::TERM_REOPEN_ONLY_CLOSED);
        }
        if ($reason === '') {
            $this->fail(422, Messages::TERM_REOPEN_REASON);
        }
        if ($session->status !== 'open') {
            $this->fail(422, Messages::SESSION_CLOSED_EDIT);
        }

        $terms = $this->fetchTable('EmsAcademicTerms');
        $term->status = 'open';
        $term->closed_on = null;
        $terms->saveOrFail($term);

        $this->audit()->log(
            $this->viewer,
            'term.reopened',
            'term',
            (string)$term->id,
            sprintf('Reopened the %s term of %s', (string)$term->name, (string)$session->name),
            $reason,
        );

        return $this->json(CalendarSerializer::term($term));
    }

    /**
     * POST /calendar/sessions/{id}/close — all terms must be closed first.
     * Audit `session.closed`.
     */
    public function closeSession(string $id): Response
    {
        $session = $this->findSession($id);
        if ($session->status !== 'open') {
            $this->fail(422, Messages::SESSION_CLOSED_EDIT);
        }

        $openTerms = $this->fetchTable('EmsAcademicTerms')->find()
            ->where(['session_id' => $session->id, 'status' => 'open'])
            ->all()
            ->toList();
        if ($openTerms !== []) {
            usort($openTerms, function ($a, $b) {
                return (self::TERM_ORDER[$a->name] ?? 9) <=> (self::TERM_ORDER[$b->name] ?? 9);
            });
            $names = array_map(function ($t) {
                return (string)$t->name;
            }, $openTerms);
            // Mock parity: names joined with ' and ', plural without parens.
            $this->fail(422, sprintf(
                Messages::SESSION_CLOSE_TERMS_FIRST,
                implode(' and ', $names),
                count($names) > 1 ? 's' : '',
            ));
        }

        $sessions = $this->fetchTable('EmsAcademicSessions');
        $session->status = 'closed';
        $session->closed_on = FrozenDate::today();
        $sessions->saveOrFail($session);

        $this->audit()->log(
            $this->viewer,
            'session.closed',
            'session',
            (string)$session->id,
            sprintf('Closed the %s session', (string)$session->name),
        );

        return $this->json(CalendarSerializer::session($session));
    }

    /**
     * POST /calendar/sessions/{id}/reopen { reason } — terms stay closed.
     * Audit `session.reopened`.
     */
    public function reopenSession(string $id): Response
    {
        $session = $this->findSession($id);
        $reason = trim((string)($this->body()['reason'] ?? ''));

        if ($session->status !== 'closed') {
            $this->fail(422, Messages::SESSION_REOPEN_ONLY_CLOSED);
        }
        if ($reason === '') {
            $this->fail(422, Messages::SESSION_REOPEN_REASON);
        }

        $sessions = $this->fetchTable('EmsAcademicSessions');
        $session->status = 'open';
        $session->closed_on = null;
        $sessions->saveOrFail($session);

        $this->audit()->log(
            $this->viewer,
            'session.reopened',
            'session',
            (string)$session->id,
            sprintf('Reopened the %s session', (string)$session->name),
            $reason,
        );

        return $this->json(CalendarSerializer::session($session));
    }

    /**
     * GET /calendar/audit — Paginated<AuditEvent>, session/term events only,
     * newest first. (Action is named calendarAudit because audit() is the
     * base controller's writer accessor.)
     */
    public function calendarAudit(): Response
    {
        $params = $this->pageParams();

        $query = $this->tenant()->query('EmsAuditEvents')
            ->where([
                'entity_type IN' => ['session', 'term'],
            ]);
        $total = $query->count();
        $rows = $query
            ->orderByDesc('seq')
            ->limit($params['pageSize'])
            ->offset(($params['page'] - 1) * $params['pageSize'])
            ->all();

        return $this->paginated(
            array_map([CalendarSerializer::class, 'auditEvent'], $rows->toList()),
            $total,
            $params['page'],
            $params['pageSize'],
        );
    }

    private function findSession(string $id): EntityInterface
    {
        return $this->findOr404('EmsAcademicSessions', $id, Messages::CALENDAR_SESSION_NOT_FOUND);
    }

    private function findTerm(string $id): EntityInterface
    {
        return $this->findOr404('EmsAcademicTerms', $id, Messages::TERM_NOT_FOUND);
    }

    private function assertInsideSession(EntityInterface $session, string $startsOn, string $endsOn): void
    {
        $sessionStart = $session->starts_on instanceof ChronosInterface
            ? $session->starts_on->format('Y-m-d')
            : (string)$session->starts_on;
        $sessionEnd = $session->ends_on instanceof ChronosInterface
            ? $session->ends_on->format('Y-m-d')
            : (string)$session->ends_on;

        if ($startsOn < $sessionStart || $endsOn > $sessionEnd) {
            $this->fail(422, sprintf(Messages::TERM_DATES_INSIDE, (string)$session->name));
        }
    }
}
