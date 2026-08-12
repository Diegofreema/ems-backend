<?php
declare(strict_types=1);

namespace App\Controller\Ems;

use App\Ems\Messages;
use App\Ems\Serializer\GovernanceSerializer;
use Cake\Datasource\EntityInterface;
use Cake\Http\Response;
use Cake\I18n\FrozenDate;

/**
 * Privacy (data-subject) requests (document.md §3.23). Administrator-only. A
 * request walks received → verified → approved|refused, and approved → fulfilled.
 * Deciding never deletes or exports anything itself — it records the decision;
 * fulfilment records what was actually done. Every move writes its own audit
 * event, so the record of "who decided what about a child's data" is auditable.
 */
class PrivacyRequestsController extends AppController
{
    /** kind → human label (lower-cased in the audit summary). */
    private const KIND_LABELS = [
        'access' => 'See what is held',
        'correction' => 'Correct a record',
        'export' => 'Copy of the record',
        'deletion' => 'Delete a record',
    ];

    /** GET /privacy-requests — requestedOn desc, then reference desc. */
    public function index(): Response
    {
        $params = $this->pageParams();
        $rows = $this->tenant()->query('EmsPrivacyRequests')
            ->all()->toList();
        usort($rows, fn ($a, $b) =>
            strcmp((string)$b->requested_on, (string)$a->requested_on)
            ?: strcmp((string)$b->reference, (string)$a->reference));

        $total = count($rows);
        $page = array_slice($rows, ($params['page'] - 1) * $params['pageSize'], $params['pageSize']);

        return $this->paginated(
            array_map([GovernanceSerializer::class, 'privacyRequest'], $page),
            $total,
            $params['page'],
            $params['pageSize']
        );
    }

    /** POST /privacy-requests */
    public function add(): Response
    {
        $body = $this->body();
        $kind = (string)($body['kind'] ?? 'access');
        $subjectName = trim((string)($body['subjectName'] ?? ''));
        $seq = $this->sequences()->next($this->viewer->schoolId, 'privacy_request');
        $reference = 'PRV-' . str_pad((string)$seq, 4, '0', STR_PAD_LEFT);

        $requests = $this->fetchTable('EmsPrivacyRequests');
        $request = $requests->newEntity([
            'school_id' => $this->viewer->schoolId,
            'reference' => $reference,
            'kind' => $kind,
            'subject_name' => $subjectName,
            'subject_student_id' => trim((string)($body['subjectStudentId'] ?? '')) ?: null,
            'requested_by' => trim((string)($body['requestedBy'] ?? '')),
            'contact' => trim((string)($body['contact'] ?? '')),
            'requested_on' => FrozenDate::today(),
            'detail' => trim((string)($body['detail'] ?? '')),
            'status' => 'received',
        ], ['validate' => false]);
        $requests->saveOrFail($request);

        $label = strtolower(self::KIND_LABELS[$kind] ?? $kind);
        $this->audit()->log(
            $this->viewer,
            'privacy_request.logged',
            'privacy_request',
            (string)$request->id,
            sprintf('Logged a %s request for %s', $label, $subjectName)
        );

        return $this->json(GovernanceSerializer::privacyRequest($request), 201);
    }

    /** POST /privacy-requests/{id}/verify */
    public function verify(string $id): Response
    {
        $request = $this->findRequest($id);
        if ((string)$request->status !== 'received') {
            $this->fail(409, Messages::PRIVACY_PAST_IDENTITY);
        }
        $evidence = trim((string)($this->body()['evidence'] ?? ''));
        if ($evidence === '') {
            $this->fail(422, Messages::PRIVACY_EVIDENCE_REQUIRED);
        }
        $requests = $this->fetchTable('EmsPrivacyRequests');
        $request->status = 'verified';
        $request->identity_evidence = $evidence;
        $request->identity_verified_by = $this->viewer->name;
        $request->identity_verified_on = FrozenDate::today();
        $requests->saveOrFail($request);

        $this->audit()->log(
            $this->viewer,
            'privacy_request.identity_verified',
            'privacy_request',
            (string)$request->id,
            sprintf('Verified the identity of the requester on %s', (string)$request->reference),
            $evidence
        );

        return $this->json(GovernanceSerializer::privacyRequest($request));
    }

    /** POST /privacy-requests/{id}/decide */
    public function decide(string $id): Response
    {
        $request = $this->findRequest($id);
        if ((string)$request->status !== 'verified') {
            $this->fail(409, Messages::PRIVACY_VERIFY_FIRST);
        }
        $body = $this->body();
        $approve = (bool)($body['approve'] ?? false);
        $note = trim((string)($body['note'] ?? ''));
        if ($note === '') {
            $this->fail(422, Messages::PRIVACY_DECISION_REASON);
        }
        $retentionNote = trim((string)($body['retentionNote'] ?? ''));
        if ($approve && (string)$request->kind === 'deletion' && $retentionNote === '') {
            $this->fail(422, Messages::PRIVACY_RETENTION_REQUIRED);
        }

        $requests = $this->fetchTable('EmsPrivacyRequests');
        $request->status = $approve ? 'approved' : 'refused';
        $request->decision_note = $note;
        $request->decided_by = $this->viewer->name;
        $request->decided_on = FrozenDate::today();
        if ($approve && (string)$request->kind === 'deletion') {
            $request->retention_note = $retentionNote;
        }
        $requests->saveOrFail($request);

        $this->audit()->log(
            $this->viewer,
            $approve ? 'privacy_request.approved' : 'privacy_request.refused',
            'privacy_request',
            (string)$request->id,
            sprintf('%s %s for %s', $approve ? 'Approved' : 'Refused', (string)$request->reference, (string)$request->subject_name),
            $note
        );

        return $this->json(GovernanceSerializer::privacyRequest($request));
    }

    /** POST /privacy-requests/{id}/fulfil */
    public function fulfil(string $id): Response
    {
        $request = $this->findRequest($id);
        if ((string)$request->status !== 'approved') {
            $this->fail(409, Messages::PRIVACY_ONLY_APPROVED_FULFIL);
        }
        $note = trim((string)($this->body()['note'] ?? ''));
        if ($note === '') {
            $this->fail(422, Messages::PRIVACY_FULFIL_NOTE);
        }
        $requests = $this->fetchTable('EmsPrivacyRequests');
        $request->status = 'fulfilled';
        $request->fulfilment_note = $note;
        $request->fulfilled_on = FrozenDate::today();
        $requests->saveOrFail($request);

        $this->audit()->log(
            $this->viewer,
            'privacy_request.fulfilled',
            'privacy_request',
            (string)$request->id,
            sprintf('Fulfilled %s for %s', (string)$request->reference, (string)$request->subject_name),
            $note
        );

        return $this->json(GovernanceSerializer::privacyRequest($request));
    }

    private function findRequest(string $id): EntityInterface
    {
        return $this->findOr404('EmsPrivacyRequests', $id, Messages::PRIVACY_NOT_FOUND);
    }
}
