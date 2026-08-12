<?php
declare(strict_types=1);

namespace App\Controller\Ems;

use Cake\Http\Response;
use Cake\I18n\FrozenTime;
use Cake\Utility\Text;

final class CashBatchesController extends AppController
{
    public function add(): Response
    {
        if ($this->viewer->role !== 'bursar') {
            $this->fail(403, 'Only a bursar can open a cash batch.');
        }
        $body = $this->body();
        $key = trim((string)$this->request->getHeaderLine('Idempotency-Key'));
        $replay = $this->financeSecurity()->replay($this->viewer, 'cash_batch.create', $key, $body);
        if ($replay) {
            return $this->json($replay['body'], $replay['status']);
        }
        $number = trim((string)($body['batchNumber'] ?? ''));
        $date = (string)($body['collectionDate'] ?? '');
        if ($number === '' || $date === '') {
            $this->fail(422, 'Batch number and collection date are required.');
        }
        $table = $this->fetchTable('EmsCashBatches');
        $result = $table->getConnection()->transactional(function () use ($body, $key, $number, $date, $table) {
            $this->financeSecurity()->assertWritable();
            $row = $table->newEntity(['id' => Text::uuid(),'school_id' => $this->viewer->schoolId,'batch_number' => $number,'collection_date' => $date,'recorder_user_id' => $this->viewer->userId,'expected_amount' => 0,'created' => FrozenTime::now('UTC')], ['validate' => false]);
            $table->saveOrFail($row);
            $result = ['id' => (string)$row->id,'batchNumber' => $number,'collectionDate' => $date,'status' => 'open'];
            $this->audit()->log($this->viewer, 'cash_batch.opened', 'cash_batch', (string)$row->id, 'A daily cash batch was opened.');
            $this->financeSecurity()->remember($this->viewer, 'cash_batch.create', $key, $body, 201, $result);

            return $result;
        });

        return $this->json($result, 201);
    }

    public function close(string $id): Response
    {
        if ($this->viewer->role !== 'administrator') {
            $this->fail(403, 'Only an administrator can close and approve a cash batch.');
        }
        $body = $this->body();
        $key = trim((string)$this->request->getHeaderLine('Idempotency-Key'));
        $request = $body + ['id' => $id];
        $replay = $this->financeSecurity()->replay($this->viewer, 'cash_batch.close', $key, $request);
        if ($replay) {
            return $this->json($replay['body'], $replay['status']);
        }
        $table = $this->fetchTable('EmsCashBatches');
        $result = $table->getConnection()->transactional(function () use ($id, $body, $key, $request, $table) {
            $this->financeSecurity()->assertWritable();
            $batch = $this->tenant()->query('EmsCashBatches')->where(['id' => $id])->epilog('FOR UPDATE')->first();
            if (!$batch) {
                $this->fail(404, 'Cash batch not found.');
            }
            if ($batch->closed_at) {
                $this->fail(409, 'This cash batch is already closed.');
            }
            $subs = $this->tenant()->query('EmsPaymentSubmissions')->where(['cash_batch_id' => $id,'method' => 'cash'])->all()->toList();
            $expected = array_sum(array_map(static fn($s)=>(int)$s->amount, $subs));
            $counted = (int)($body['countedAmount'] ?? -1);
            if ($counted !== $expected) {
                $this->fail(422, 'The counted cash must equal the individual acknowledgements in this batch.');
            }
            $batch->expected_amount = $expected;
            $batch->counted_amount = $counted;
            $batch->closed_by_user_id = $this->viewer->userId;
            $batch->closed_at = FrozenTime::now('UTC');
            $table->saveOrFail($batch);
            $payments = [];
            foreach ($subs as $submission) {
                $decided = $this->tenant()->query('EmsFinanceDecisions')->where(['request_type' => 'payment_submission','request_id' => (string)$submission->id])->count();
                if (!$decided) {
                    $payments[] = $this->financeSecurity()->decideSubmission($submission, $this->viewer, 'approved', 'Daily cash batch reconciled and closed.');
                }
            }$result = ['id' => $id,'batchNumber' => (string)$batch->batch_number,'expectedAmount' => $expected,'countedAmount' => $counted,'status' => 'closed','approvedCount' => count($payments)];
            $this->audit()->log($this->viewer, 'cash_batch.closed', 'cash_batch', $id, 'A reconciled cash batch was closed and its claims were approved.');
            $this->financeSecurity()->remember($this->viewer, 'cash_batch.close', $key, $request, 200, $result);

            return $result;
        });

        return $this->json($result);
    }
}
