<?php
declare(strict_types=1);

namespace App\Controller\Ems;

use Cake\Http\Response;

final class PaymentSubmissionsController extends AppController
{
    /**
     * List payment claims with their decision and safe evidence metadata.
     *
     * @return \Cake\Http\Response JSON response.
     */
    public function index(): Response
    {
        $rows = $this->tenant()->query('EmsPaymentSubmissions')->orderByDesc('created')->all()->toList();
        $ids = array_map(static fn($r) => (string)$r->id, $rows);
        $decisions = [];
        $evidence = [];
        if ($ids !== []) {
            $decisionRows = $this->tenant()->query('EmsFinanceDecisions')->where([
                'request_type' => 'payment_submission',
                'request_id IN' => $ids,
            ])->all();
            foreach ($decisionRows as $d) {
                $decisions[(string)$d->request_id] = $d;
            }
            $evidenceRows = $this->tenant()->query('EmsFinanceEvidence')->where([
                'owner_type' => 'payment_submission',
                'owner_id IN' => $ids,
            ])->all();
            foreach ($evidenceRows as $item) {
                $evidence[(string)$item->owner_id] = [
                    'id' => (string)$item->id,
                    'filename' => (string)$item->filename,
                    'mediaType' => (string)$item->media_type,
                    'sizeBytes' => (int)$item->size_bytes,
                    'scanStatus' => (string)$item->scan_status,
                    'contentHash' => (string)$item->content_hash,
                ];
            }
        }
        $items = array_map(fn($r) => $this->financeSecurity()->submissionWire(
            $r,
            $decisions[(string)$r->id] ?? null,
            $evidence[(string)$r->id] ?? null,
        ), $rows);

        return $this->json([
            'items' => $items,
            'total' => count($items),
            'page' => 1,
            'pageSize' => count($items),
        ]);
    }

    /**
     * Approve or reject one payment claim.
     *
     * @param string $id Payment submission identifier.
     * @return \Cake\Http\Response JSON response.
     */
    public function decide(string $id): Response
    {
        $body = $this->body();
        $key = trim((string)$this->request->getHeaderLine('Idempotency-Key'));
        $replay = $this->financeSecurity()->replay(
            $this->viewer,
            'payment_submission.decide',
            $key,
            $body + ['id' => $id],
        );
        if ($replay !== null) {
            return $this->json($replay['body'], $replay['status']);
        }
        $table = $this->fetchTable('EmsPaymentSubmissions');
        $result = $table->getConnection()->transactional(function () use ($id, $body, $key) {
            $row = $this->tenant()->query('EmsPaymentSubmissions')
                ->where(['id' => $id])
                ->epilog('FOR UPDATE')
                ->first();
            if (!$row) {
                $this->fail(404, 'Payment submission not found.');
            }
            $result = $this->financeSecurity()->decideSubmission(
                $row,
                $this->viewer,
                (string)($body['decision'] ?? ''),
                trim((string)($body['reason'] ?? '')),
            );
            $this->financeSecurity()->remember(
                $this->viewer,
                'payment_submission.decide',
                $key,
                $body + ['id' => $id],
                200,
                $result,
            );

            return $result;
        });

        return $this->json($result);
    }

    /**
     * Download clean evidence for an authorised finance officer.
     *
     * @param string $id Payment submission identifier.
     * @return \Cake\Http\Response Private evidence response.
     */
    public function evidence(string $id): Response
    {
        $submission = $this->tenant()->query('EmsPaymentSubmissions')->where(['id' => $id])->first();
        if (!$submission) {
            $this->fail(404, 'Payment submission not found.');
        }
        $evidence = $this->tenant()->query('EmsFinanceEvidence')->where([
            'owner_type' => 'payment_submission',
            'owner_id' => $id,
        ])->first();
        if (!$evidence) {
            $this->fail(404, 'Payment evidence not found.');
        }
        if ((string)$evidence->scan_status !== 'clean') {
            $this->fail(423, 'Payment evidence is unavailable until malware scanning passes.');
        }
        $object = $this->fetchTable('EmsDocumentObjects')->find()
            ->where(['storage_path' => (string)$evidence->storage_path])
            ->first();
        if (!$object) {
            $this->fail(404, 'Payment evidence file not found.');
        }
        $filename = str_replace(['"', "\r", "\n"], '', (string)$evidence->filename);

        return $this->response
            ->withHeader('Content-Type', (string)$evidence->media_type)
            ->withHeader('Content-Disposition', 'inline; filename="' . $filename . '"')
            ->withHeader('Cache-Control', 'private, no-store')
            // Never let the browser second-guess the stored (magic-byte-verified)
            // media type — no MIME sniffing on inline private evidence.
            ->withHeader('X-Content-Type-Options', 'nosniff')
            ->withStringBody((string)$object->body);
    }
}
