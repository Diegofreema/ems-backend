<?php
declare(strict_types=1);

namespace App\Controller\Ems;

use App\Ems\Messages;
use App\Ems\Serializer\GovernanceSerializer;
use Cake\Datasource\EntityInterface;
use Cake\Http\Response;
use Cake\I18n\FrozenDate;
use Cake\Utility\Text;

/**
 * The breach register (document.md §3.24). Administrator-only at the door, but
 * with a tighter seal inside: the register list shows that cases exist and who
 * is handling them, yet the DETAIL of a case is readable only by the responders
 * named on it — an administrator who is not named is refused. The recorder
 * becomes the lead responder at creation, so every case has an owner from the
 * first instant. Cases step strictly forward: recorded → contained →
 * investigating → reported → closed; no skips, no reopening.
 */
class IncidentsController extends AppController
{
    private const NEXT = [
        'recorded' => 'contained',
        'contained' => 'investigating',
        'investigating' => 'reported',
        'reported' => 'closed',
        'closed' => null,
    ];

    private const STATUS_LABELS = [
        'recorded' => 'Recorded',
        'contained' => 'Contained',
        'investigating' => 'Investigating',
        'reported' => 'Reported',
        'closed' => 'Closed',
    ];

    private const SEVERITY_LABELS = [
        'low' => 'Low',
        'medium' => 'Medium',
        'high' => 'High',
        'critical' => 'Critical',
    ];

    /** GET /incidents — register rows, newest first. No detail leaks here. */
    public function index(): Response
    {
        $params = $this->pageParams();
        $rows = $this->tenant()->query('EmsIncidents')
            ->all()->toList();
        usort($rows, fn ($a, $b) =>
            strcmp((string)$b->recorded_on, (string)$a->recorded_on)
            ?: strcmp((string)$b->reference, (string)$a->reference));

        $total = count($rows);
        $page = array_slice($rows, ($params['page'] - 1) * $params['pageSize'], $params['pageSize']);
        $items = array_map(
            fn ($i) => GovernanceSerializer::incidentSummary($i, $this->isResponder($i)),
            $page
        );

        return $this->paginated($items, $total, $params['page'], $params['pageSize']);
    }

    /** GET /incidents/{id} — 404 before 403; then the responder seal. */
    public function view(string $id): Response
    {
        $incident = $this->findIncident($id);
        $this->assertResponder($incident);
        $named = [];
        foreach (GovernanceSerializer::incident($incident)['responders'] as $r) {
            $named[$r['userId']] = true;
        }
        $candidates = [];
        foreach ($this->activeAdmins() as $c) {
            if (!isset($named[$c['userId']])) {
                $candidates[] = $c;
            }
        }

        return $this->json(GovernanceSerializer::incident($incident, $candidates));
    }

    /** POST /incidents — recorder becomes the lead responder. */
    public function add(): Response
    {
        $body = $this->body();
        $title = trim((string)($body['title'] ?? ''));
        $description = trim((string)($body['description'] ?? ''));
        $categories = is_array($body['dataCategories'] ?? null) ? array_values($body['dataCategories']) : [];
        $discoveredOn = trim((string)($body['discoveredOn'] ?? ''));
        if ($title === '') {
            $this->fail(422, Messages::INCIDENT_TITLE);
        }
        if ($description === '') {
            $this->fail(422, Messages::INCIDENT_DESCRIPTION);
        }
        if ($categories === []) {
            $this->fail(422, Messages::INCIDENT_CATEGORY);
        }
        if ($discoveredOn === '') {
            $this->fail(422, Messages::INCIDENT_DISCOVERED);
        }
        if ($this->viewer->userId === '') {
            $this->fail(403, Messages::INCIDENT_RECORD_RESPONDER);
        }

        $today = FrozenDate::today()->format('Y-m-d');
        $seq = $this->sequences()->next($this->viewer->schoolId, 'incident');
        $reference = 'INC-' . str_pad((string)$seq, 4, '0', STR_PAD_LEFT);
        // Generate the id up front: entry ids are keyed off the incident id.
        $id = Text::uuid();
        $incidents = $this->fetchTable('EmsIncidents');
        $incident = $incidents->newEntity([
            'id' => $id,
            'school_id' => $this->viewer->schoolId,
            'reference' => $reference,
            'title' => $title,
            'description' => $description,
            'severity' => (string)($body['severity'] ?? 'low'),
            'data_categories' => $categories,
            'status' => 'recorded',
            'discovered_on' => $discoveredOn,
            'recorded_on' => $today,
            'recorded_by' => $this->viewer->name,
            'responders' => [[
                'userId' => $this->viewer->userId,
                'name' => $this->viewer->name,
                'addedOn' => $today,
                'addedBy' => $this->viewer->name,
                'lead' => true,
            ]],
            'entries' => [$this->entry($id, 1, $today, 'recorded', $description)],
        ], ['validate' => false]);
        $incidents->saveOrFail($incident);

        $severityLabel = strtolower(self::SEVERITY_LABELS[(string)$incident->severity] ?? (string)$incident->severity);
        $this->audit()->log(
            $this->viewer,
            'incident.recorded',
            'incident',
            (string)$incident->id,
            sprintf('Recorded %s (%s severity)', $reference, $severityLabel)
        );

        return $this->json(GovernanceSerializer::incident($incident), 201);
    }

    /** POST /incidents/{id}/advance — one legal step forward, with evidence. */
    public function advance(string $id): Response
    {
        $incident = $this->findIncident($id);
        $this->assertResponder($incident);
        $body = $this->body();
        $to = (string)($body['to'] ?? '');
        $current = (string)$incident->status;
        if ((self::NEXT[$current] ?? null) !== $to) {
            $this->fail(409, sprintf(
                'A %s incident cannot move to %s.',
                strtolower(self::STATUS_LABELS[$current] ?? $current),
                strtolower(self::STATUS_LABELS[$to] ?? $to)
            ));
        }
        $note = trim((string)($body['note'] ?? ''));
        if ($note === '') {
            $this->fail(422, Messages::INCIDENT_STEP_NOTE);
        }

        $incident->status = $to;
        if ($to === 'contained') {
            $incident->containment_note = $note;
        } elseif ($to === 'reported') {
            $incident->report_evidence = $note;
        } elseif ($to === 'closed') {
            $incident->close_summary = $note;
        }
        $this->appendEntry($incident, $to, $note);
        $this->fetchTable('EmsIncidents')->saveOrFail($incident);

        $this->audit()->log(
            $this->viewer,
            'incident.' . $to,
            'incident',
            (string)$incident->id,
            sprintf('Moved %s to %s', (string)$incident->reference, strtolower(self::STATUS_LABELS[$to] ?? $to)),
            $note
        );

        return $this->json(GovernanceSerializer::incident($incident));
    }

    /** POST /incidents/{id}/notes — log onto the trail without moving the case. */
    public function addNote(string $id): Response
    {
        $incident = $this->findIncident($id);
        $this->assertResponder($incident);
        if ((string)$incident->status === 'closed') {
            $this->fail(409, Messages::INCIDENT_CLOSED);
        }
        $note = trim((string)($this->body()['note'] ?? ''));
        if ($note === '') {
            $this->fail(422, Messages::INCIDENT_NOTE_REQUIRED);
        }
        $this->appendEntry($incident, 'note', $note);
        $this->fetchTable('EmsIncidents')->saveOrFail($incident);

        return $this->json(GovernanceSerializer::incident($incident));
    }

    /** POST /incidents/{id}/responders — widen the circle (responder-only). */
    public function addResponder(string $id): Response
    {
        $incident = $this->findIncident($id);
        $this->assertResponder($incident);
        $userId = (string)($this->body()['userId'] ?? '');
        $responders = GovernanceSerializer::incident($incident)['responders'];
        foreach ($responders as $r) {
            if ($r['userId'] === $userId) {
                $this->fail(409, Messages::INCIDENT_ALREADY_RESPONDER);
            }
        }
        $candidate = null;
        foreach ($this->activeAdmins() as $c) {
            if ($c['userId'] === $userId) {
                $candidate = $c;
                break;
            }
        }
        if ($candidate === null) {
            $this->fail(422, Messages::INCIDENT_RESPONDER_MUST_ADMIN);
        }

        $today = FrozenDate::today()->format('Y-m-d');
        $responders[] = [
            'userId' => $candidate['userId'],
            'name' => $candidate['name'],
            'addedOn' => $today,
            'addedBy' => $this->viewer->name,
        ];
        $incident->responders = $responders;
        $this->appendEntry($incident, 'responder_added', sprintf('Named %s as a responder', $candidate['name']));
        $this->fetchTable('EmsIncidents')->saveOrFail($incident);

        $this->audit()->log(
            $this->viewer,
            'incident.responder_added',
            'incident',
            (string)$incident->id,
            sprintf('Named %s as a responder on %s', $candidate['name'], (string)$incident->reference)
        );

        return $this->json(GovernanceSerializer::incident($incident));
    }

    // --- helpers -------------------------------------------------------------

    private function findIncident(string $id): EntityInterface
    {
        return $this->findOr404('EmsIncidents', $id, Messages::INCIDENT_NOT_FOUND);
    }

    private function isResponder(EntityInterface $incident): bool
    {
        foreach (GovernanceSerializer::incident($incident)['responders'] as $r) {
            if ($r['userId'] === $this->viewer->userId && $this->viewer->userId !== '') {
                return true;
            }
        }

        return false;
    }

    private function assertResponder(EntityInterface $incident): void
    {
        if (!$this->isResponder($incident)) {
            $this->fail(403, Messages::INCIDENT_SEALED);
        }
    }

    /** @return array<int, array{userId:string,name:string}> */
    private function activeAdmins(): array
    {
        $out = [];
        foreach ($this->tenant()->query('EmsUsers')
            ->where(['role' => 'administrator', 'status' => 'active'])
            ->all() as $u) {
            $out[] = ['userId' => (string)$u->id, 'name' => (string)$u->name];
        }

        return $out;
    }

    /** @return array{id:string,at:string,actor:string,kind:string,note:string} */
    private function entry(string $incidentId, int $n, string $at, string $kind, string $note): array
    {
        return [
            'id' => $incidentId . '_e' . $n,
            'at' => $at,
            'actor' => $this->viewer->name,
            'kind' => $kind,
            'note' => $note,
        ];
    }

    private function appendEntry(EntityInterface $incident, string $kind, string $note): void
    {
        $entries = GovernanceSerializer::incident($incident)['entries'];
        $entries[] = $this->entry(
            (string)$incident->id,
            count($entries) + 1,
            FrozenDate::today()->format('Y-m-d'),
            $kind,
            $note
        );
        $incident->entries = $entries;
    }
}
