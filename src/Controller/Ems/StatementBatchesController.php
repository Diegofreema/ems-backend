<?php
declare(strict_types=1);

namespace App\Controller\Ems;

use Cake\Http\Response;
use Cake\I18n\FrozenTime;
use Cake\Utility\Text;

final class StatementBatchesController extends AppController
{
    public function add(): Response
    {
        if ($this->viewer->role !== 'bursar') {
            $this->fail(403, 'Only a bursar can import a bank statement.');
        }
        $body = $this->body();
        $key = trim((string)$this->request->getHeaderLine('Idempotency-Key'));
        $replay = $this->financeSecurity()->replay($this->viewer, 'statement.import', $key, $body);
        if ($replay) {
            return $this->json($replay['body'], $replay['status']);
        }
        $csv = (string)($body['csv'] ?? '');
        $source = trim((string)($body['sourceName'] ?? ''));
        if ($csv === '' || $source === '') {
            $this->fail(422, 'Source name and CSV data are required.');
        }
        $lines = preg_split('/\r\n|\r|\n/', trim($csv));
        $header = array_map('trim', str_getcsv((string)array_shift($lines)));
        $required = ['date','reference','description','amount','direction'];
        foreach ($required as $column) {
            if (!in_array($column, $header, true)) {
                $this->fail(422, 'Statement CSV requires date, reference, description, amount and direction columns.');
            }
        }
        $batchTable = $this->fetchTable('EmsBankStatementBatches');
        $result = $batchTable->getConnection()->transactional(function () use ($body, $key, $source, $csv, $lines, $header, $batchTable) {
            $this->financeSecurity()->assertWritable();
            $id = Text::uuid();
            $batch = $batchTable->newEntity(['id' => $id,'school_id' => $this->viewer->schoolId,'source_name' => $source,'file_hash' => hash('sha256', $csv),'imported_by_user_id' => $this->viewer->userId,'created' => FrozenTime::now('UTC')], ['validate' => false]);
            $batchTable->saveOrFail($batch);
            $rows = $this->fetchTable('EmsBankStatementRows');
            $count = 0;
            foreach ($lines as $line) {
                if (trim((string)$line) === '') {
                    continue;
                }
                $values = str_getcsv((string)$line);
                $r = array_combine($header, $values);
                if (!$r) {
                    $this->fail(422, 'A statement row has the wrong number of columns.');
                }
                $amount = (int)$r['amount'];
                $direction = strtolower(trim((string)$r['direction']));
                if ($amount <= 0 || !in_array($direction, ['credit','debit'], true)) {
                    $this->fail(422, 'Statement amounts must be positive and direction must be credit or debit.');
                }
                $canonical = implode('|', [$r['date'],strtoupper(trim((string)$r['reference'])),$r['description'],$amount,$direction]);
                $rows->saveOrFail($rows->newEntity(['id' => Text::uuid(),'school_id' => $this->viewer->schoolId,'batch_id' => $id,'occurred_on' => $r['date'],'reference' => strtoupper(trim((string)$r['reference'])),'description' => trim((string)$r['description']),'amount' => $amount,'direction' => $direction,'row_hash' => hash('sha256', $canonical)], ['validate' => false]));
                $count++;
            }
            $result = ['id' => $id,'sourceName' => $source,'rowCount' => $count,'fileHash' => hash('sha256', $csv)];
            $this->audit()->log($this->viewer, 'statement.imported', 'statement_batch', $id, 'A private bank statement batch was imported.');
            $this->financeSecurity()->remember($this->viewer, 'statement.import', $key, $body, 201, $result);

            return $result;
        });

        return $this->json($result, 201);
    }

    public function rows(string $id): Response
    {
        $batch = $this->tenant()->query('EmsBankStatementBatches')->where(['id' => $id])->first();
        if (!$batch) {
            $this->fail(404, 'Statement batch not found.');
        }
        $items = [];
        foreach ($this->tenant()->query('EmsBankStatementRows')->where(['batch_id' => $id])->all() as $r) {
            $items[] = ['id' => (string)$r->id,'date' => (string)$r->occurred_on,'reference' => (string)$r->reference,'description' => (string)$r->description,'amount' => (int)$r->amount,'direction' => (string)$r->direction];
        }

        return $this->json(['items' => $items,'total' => count($items),'page' => 1,'pageSize' => count($items)]);
    }
}
