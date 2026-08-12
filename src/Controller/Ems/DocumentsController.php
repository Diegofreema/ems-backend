<?php
declare(strict_types=1);

namespace App\Controller\Ems;

use App\Ems\DocumentPolicy;
use App\Ems\DocumentWriter;
use App\Ems\Messages;
use App\Ems\Serializer\DocumentSerializer;
use Cake\Datasource\EntityInterface;
use Cake\Http\Response;
use Cake\I18n\FrozenDate;

/**
 * Documents & signed links (document.md §3.8).
 *
 * Reading a document takes TWO permissions, never one: the decision that issues
 * a link (`link`) and the live session that redeems it (Files::download).
 * Holding a link is never enough. Access is gated per owner kind by
 * DocumentPolicy; verification is admissions-office only.
 */
class DocumentsController extends AppController
{
    /**
     * GET /documents?owner=&ownerId= — a record's documents, uploadedOn desc.
     */
    public function index(): Response
    {
        $owner = (string)$this->request->getQuery('owner', '');
        $ownerId = (string)$this->request->getQuery('ownerId', '');
        DocumentPolicy::assertOwnerAccess($this->scope(), $this->viewer, $owner, $ownerId);

        $rows = $this->tenant()->query('EmsDocuments')
            ->where(['owner' => $owner, 'owner_id' => $ownerId])
            ->orderByDesc('uploaded_on')
            ->orderByDesc('id')
            ->all();

        return $this->json(array_map([DocumentSerializer::class, 'one'], $rows->toList()));
    }

    /**
     * POST /documents { owner, ownerId, name, type, contentType, sizeBytes, body }.
     */
    public function add(): Response
    {
        $body = $this->body();
        $owner = (string)($body['owner'] ?? '');
        $ownerId = (string)($body['ownerId'] ?? '');
        DocumentPolicy::assertOwnerAccess($this->scope(), $this->viewer, $owner, $ownerId);

        $writer = new DocumentWriter($this->getTableLocator(), $this->storage());
        $document = $writer->store($this->viewer->schoolId, $owner, $ownerId, [
            'name' => (string)($body['name'] ?? ''),
            'type' => (string)($body['type'] ?? 'other'),
            'contentType' => (string)($body['contentType'] ?? ''),
            'sizeBytes' => (int)($body['sizeBytes'] ?? 0),
            'body' => (string)($body['body'] ?? ''),
        ], $this->viewer->name);

        $this->audit()->log(
            $this->viewer,
            'document.uploaded',
            'document',
            (string)$document->id,
            sprintf('Added "%s" to the %s record.', (string)$document->name, $owner === 'student' ? 'student' : 'application')
        );

        return $this->json(DocumentSerializer::one($document), 201);
    }

    /**
     * POST /documents/{id}/verify { decision, note } — admissions office only.
     */
    public function verify(string $id): Response
    {
        DocumentPolicy::assertCanVerify($this->viewer);
        $documents = $this->fetchTable('EmsDocuments');
        $document = $this->findDocument($id);

        $body = $this->body();
        $decision = (string)($body['decision'] ?? '');
        $note = trim((string)($body['note'] ?? ''));
        if ($decision === 'rejected' && $note === '') {
            $this->fail(422, Messages::DOCUMENT_REJECT_NOTE);
        }

        $document->verification = $decision;
        $document->verified_by = $this->viewer->name;
        $document->verified_on = FrozenDate::today();
        $document->verification_note = $note;
        $documents->saveOrFail($document);

        $this->audit()->log(
            $this->viewer,
            $decision === 'verified' ? 'document.verified' : 'document.rejected',
            'document',
            $id,
            sprintf('%s "%s".', $decision === 'verified' ? 'Accepted' : 'Rejected', (string)$document->name),
            $note !== '' ? $note : null
        );

        return $this->json(DocumentSerializer::one($document));
    }

    /**
     * POST /documents/{id}/link — the permission decision point. A mutation,
     * never cached; the response never carries bytes or the storage path.
     */
    public function link(string $id): Response
    {
        $document = $this->findDocument($id);
        DocumentPolicy::assertOwnerAccess($this->scope(), $this->viewer, (string)$document->owner, (string)$document->owner_id);

        $grant = $this->storage()->signGrant(
            $this->viewer->schoolId,
            (string)$document->id,
            (string)$document->storage_path,
            $this->storage()->filenameFor((string)$document->name, (string)$document->content_type),
            $this->viewer
        );

        return $this->json(DocumentSerializer::signedLink($grant, (string)$document->id));
    }

    /**
     * DELETE /documents/{id} — removes the object AND revokes its grants.
     */
    public function remove(string $id): Response
    {
        $documents = $this->fetchTable('EmsDocuments');
        $document = $this->findDocument($id);
        DocumentPolicy::assertOwnerAccess($this->scope(), $this->viewer, (string)$document->owner, (string)$document->owner_id);
        // A verified document has become part of the student's regulated
        // record; only an unverified upload (a correctable mistake) may go.
        if ((string)$document->verification === 'verified') {
            $this->fail(409, Messages::DOCUMENT_VERIFIED_LOCKED);
        }

        $documents->getConnection()->transactional(function () use ($documents, $document) {
            $this->storage()->removeObject((string)$document->storage_path);
            $this->storage()->revokeGrantsFor((string)$document->storage_path);
            $documents->deleteOrFail($document);
        });

        $this->audit()->log(
            $this->viewer,
            'document.removed',
            'document',
            $id,
            sprintf('Deleted "%s" and the file behind it.', (string)$document->name)
        );

        return $this->json(null, 204);
    }

    private function findDocument(string $id): EntityInterface
    {
        return $this->findOr404('EmsDocuments', $id, Messages::DOCUMENT_NOT_FOUND);
    }
}
