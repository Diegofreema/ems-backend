<?php
declare(strict_types=1);

namespace App\Controller\Ems;

use App\Ems\Imports;
use App\Ems\Messages;
use App\Ems\Serializer\ImportSerializer;
use Cake\Datasource\EntityInterface;
use Cake\Http\Response;
use Cake\I18n\FrozenDate;

/**
 * Staged CSV imports (document.md §3.17). Administrator/registrar only. A file
 * lands in a review batch; nothing touches the register until a person commits.
 * A row that looks like an existing person is held for a decision (new / same /
 * skip). Committing accounts for every source row and writes an audit event;
 * discarding deletes the staged rows (they hold real names).
 */
class ImportsController extends AppController
{
    /** GET /imports/template?kind= */
    public function template(): Response
    {
        $kind = (string)$this->request->getQuery('kind', 'students');
        if (!Imports::isKnownKind($kind)) {
            $kind = 'students';
        }

        return $this->json($this->importsEngine()->template($kind));
    }

    /** GET /imports — uploadedOn desc, then id desc. */
    public function index(): Response
    {
        $params = $this->pageParams();
        $rows = $this->tenant()->query('EmsImportBatches')->all()->toList();
        usort($rows, fn ($a, $b) =>
            strcmp((string)$b->uploaded_on, (string)$a->uploaded_on)
            ?: strcmp((string)$b->id, (string)$a->id));

        $total = count($rows);
        $page = array_slice($rows, ($params['page'] - 1) * $params['pageSize'], $params['pageSize']);

        return $this->paginated(
            array_map([ImportSerializer::class, 'batch'], $page),
            $total,
            $params['page'],
            $params['pageSize']
        );
    }

    /** GET /imports/{batchId} — the batch + its rows by lineNumber. */
    public function view(string $batchId): Response
    {
        $batch = $this->findBatch($batchId);

        return $this->json($this->preview($batch));
    }

    /** POST /imports — { kind, filename, text }. Parses + checks; register untouched. */
    public function add(): Response
    {
        $body = $this->body();
        $kind = (string)($body['kind'] ?? 'students');
        if (!Imports::isKnownKind($kind)) {
            $kind = 'students';
        }
        $filename = (string)($body['filename'] ?? '');
        $text = (string)($body['text'] ?? '');
        $engine = $this->importsEngine();

        $parsed = $engine->parseFile($text);
        if ($parsed['header'] === []) {
            $this->fail(422, Messages::IMPORT_NO_HEADING);
        }
        $definition = Imports::DEFINITIONS[$kind];
        $expected = array_map(fn ($c) => $c['key'], $definition['columns']);
        $missing = [];
        foreach ($definition['columns'] as $c) {
            if ($c['required'] && !in_array($c['key'], $parsed['header'], true)) {
                $missing[] = $c['key'];
            }
        }
        if ($missing !== []) {
            $this->fail(422, sprintf('The file is missing these columns: %s. Start from the template.', implode(', ', $missing)));
        }
        if ($parsed['rows'] === []) {
            $this->fail(422, Messages::IMPORT_NO_RECORDS);
        }

        $ignored = array_values(array_filter($parsed['header'], fn ($h) => $h !== '' && !in_array($h, $expected, true)));
        $batches = $this->fetchTable('EmsImportBatches');
        $batch = $batches->newEntity([
            'school_id' => $this->viewer->schoolId,
            'kind' => $kind,
            'filename' => $filename,
            'uploaded_by' => $this->viewer->name,
            'uploaded_on' => FrozenDate::today(),
            'source_row_count' => count($parsed['rows']),
            'ignored_columns' => $ignored,
            'status' => 'review',
        ], ['validate' => false]);
        $batches->saveOrFail($batch);

        $rowsTable = $this->fetchTable('EmsImportRows');
        $earlier = [];
        foreach ($parsed['rows'] as $raw) {
            $values = [];
            foreach ($definition['columns'] as $column) {
                $at = array_search($column['key'], $parsed['header'], true);
                $values[$column['key']] = $at === false ? '' : trim((string)($raw['cells'][$at] ?? ''));
            }
            $issues = $kind === 'students' ? $engine->checkStudentRow($values) : $engine->checkGuardianRow($values);
            $matches = $issues !== [] ? []
                : ($kind === 'students' ? $engine->studentMatches($values, $earlier) : $engine->guardianMatches($values, $earlier));
            $check = $issues !== [] ? 'invalid' : ($matches !== [] ? 'duplicate' : 'valid');
            $decision = $check === 'valid' ? 'import' : ($check === 'invalid' ? 'skip' : 'undecided');

            $rowsTable->saveOrFail($rowsTable->newEntity([
                'school_id' => $this->viewer->schoolId,
                'batch_id' => (string)$batch->id,
                'line_number' => (int)$raw['lineNumber'],
                'row_values' => $values,
                'row_check' => $check,
                'issues' => $issues,
                'matches' => $matches,
                'decision' => $decision,
            ], ['validate' => false]));
            $earlier[] = ['values' => $values, 'lineNumber' => (int)$raw['lineNumber']];
        }

        return $this->json($this->preview($batch), 201);
    }

    /** PUT /imports/{batchId}/rows/{rowId}/decision — { decision, mergeTargetId? }. */
    public function setDecision(string $batchId, string $rowId): Response
    {
        $batch = $this->findBatch($batchId);
        if ((string)$batch->status !== 'review') {
            $this->fail(409, Messages::IMPORT_ALREADY_COMMITTED);
        }
        $row = $this->tenant()->query('EmsImportRows')
            ->where(['batch_id' => $batchId, 'id' => $rowId])->first();
        if ($row === null) {
            $this->fail(404, Messages::IMPORT_ROW_NOT_FOUND);
        }
        $body = $this->body();
        $decision = (string)($body['decision'] ?? '');
        $mergeTargetId = (string)($body['mergeTargetId'] ?? '');
        if ((string)$row->row_check === 'invalid' && $decision !== 'skip') {
            $this->fail(422, Messages::IMPORT_INVALID_NOT_IMPORTED);
        }
        if ($decision === 'merge') {
            $target = null;
            foreach ($this->decode($row->matches) as $m) {
                if ((string)($m['targetId'] ?? '') === $mergeTargetId) {
                    $target = $m;
                    break;
                }
            }
            if ($target === null) {
                $this->fail(422, Messages::IMPORT_CHOOSE_MATCH);
            }
            if (!empty($target['withinFile'])) {
                $this->fail(422, Messages::IMPORT_MATCH_WITHIN_FILE);
            }
        }
        $row->decision = $decision;
        $row->merge_target_id = $decision === 'merge' ? $mergeTargetId : null;
        $this->fetchTable('EmsImportRows')->saveOrFail($row);

        return $this->json(ImportSerializer::row($row));
    }

    /** POST /imports/{batchId}/skip-flagged — skip every undecided duplicate. */
    public function skipFlagged(string $batchId): Response
    {
        $batch = $this->findBatch($batchId);
        if ((string)$batch->status !== 'review') {
            $this->fail(409, Messages::IMPORT_ALREADY_COMMITTED);
        }
        $rowsTable = $this->fetchTable('EmsImportRows');
        $count = 0;
        foreach ($this->rowsOf($batchId) as $row) {
            if ((string)$row->row_check === 'duplicate' && (string)$row->decision === 'undecided') {
                $row->decision = 'skip';
                $row->merge_target_id = null;
                $rowsTable->saveOrFail($row);
                $count++;
            }
        }

        return $this->json($count);
    }

    /** POST /imports/{batchId}/commit — write the reviewed batch to the register. */
    public function commit(string $batchId): Response
    {
        $batch = $this->findBatch($batchId);
        if ((string)$batch->status !== 'review') {
            $this->fail(409, Messages::IMPORT_ALREADY_COMMITTED);
        }
        $rows = $this->rowsOf($batchId);
        $undecided = 0;
        foreach ($rows as $r) {
            if ((string)$r->row_check === 'duplicate' && (string)$r->decision === 'undecided') {
                $undecided++;
            }
        }
        if ($undecided > 0) {
            $this->fail(422, sprintf(
                '%d %s a decision before this file can be committed.',
                $undecided,
                $undecided === 1 ? 'row still needs' : 'rows still need'
            ));
        }

        $engine = $this->importsEngine();
        $rowsTable = $this->fetchTable('EmsImportRows');
        $kind = (string)$batch->kind;
        $result = ['created' => 0, 'merged' => 0, 'skipped' => 0, 'rejected' => 0];

        // One transaction for the whole file — a mid-commit failure must not
        // leave a half-imported batch, with some rows written to the register
        // but the batch still stuck in review.
        $this->fetchTable('EmsImportBatches')->getConnection()->transactional(
            function () use ($rows, $rowsTable, $kind, $engine, $batch, &$result) {
        foreach ($rows as $row) {
            $values = $this->decode($row->row_values);
            if ((string)$row->row_check === 'invalid') {
                $this->stampRow($rowsTable, $row, 'rejected', 'Left out because of the errors above.');
                $result['rejected']++;
                continue;
            }
            if ((string)$row->decision === 'skip') {
                $this->stampRow($rowsTable, $row, 'skipped', 'Left out on review.');
                $result['skipped']++;
                continue;
            }
            if ((string)$row->decision === 'merge' && (string)$row->merge_target_id !== '') {
                $targetId = (string)$row->merge_target_id;
                if ($kind === 'students') {
                    $existing = $this->tenant()->query('EmsStudents')
                        ->where(['id' => $targetId])->first();
                    if ($existing === null) {
                        $this->stampRow($rowsTable, $row, 'rejected', 'The record it was to be merged into no longer exists.');
                        $result['rejected']++;
                        continue;
                    }
                    $changed = $engine->mergeStudent($existing, $values);
                    $note = $changed === []
                        ? sprintf('Matched %s %s; nothing needed changing.', (string)$existing->first_name, (string)$existing->last_name)
                        : sprintf('Updated %s %s: changed %s.', (string)$existing->first_name, (string)$existing->last_name, implode('; ', $changed));
                    $this->stampRow($rowsTable, $row, 'merged', $note, (string)$existing->id);
                    $this->audit()->log(
                        $this->viewer, 'import.merged', 'student', (string)$existing->id,
                        sprintf('Merged row %d of %s into %s %s (%s). The existing record was kept and its identifier is unchanged.',
                            (int)$row->line_number, (string)$batch->filename, (string)$existing->first_name, (string)$existing->last_name, (string)$existing->admission_number),
                        $this->firstReason($row)
                    );
                } else {
                    $existing = $this->tenant()->query('EmsGuardians')
                        ->where(['id' => $targetId])->first();
                    if ($existing === null) {
                        $this->stampRow($rowsTable, $row, 'rejected', 'The record it was to be merged into no longer exists.');
                        $result['rejected']++;
                        continue;
                    }
                    $changed = $engine->mergeGuardian($existing, $values);
                    $note = $changed === []
                        ? sprintf('Matched %s %s; nothing needed changing.', (string)$existing->first_name, (string)$existing->last_name)
                        : sprintf('Updated %s %s: changed %s.', (string)$existing->first_name, (string)$existing->last_name, implode('; ', $changed));
                    $this->stampRow($rowsTable, $row, 'merged', $note, (string)$existing->id);
                    $this->audit()->log(
                        $this->viewer, 'import.merged', 'guardian', (string)$existing->id,
                        sprintf('Merged row %d of %s into the guardian record for %s %s. The existing record was kept and its identifier is unchanged.',
                            (int)$row->line_number, (string)$batch->filename, (string)$existing->first_name, (string)$existing->last_name),
                        $this->firstReason($row)
                    );
                }
                $result['merged']++;
                continue;
            }

            // Create.
            if ($kind === 'students') {
                $student = $engine->createStudent($values);
                $this->stampRow($rowsTable, $row, 'created', sprintf('Added as %s.', (string)$student->admission_number), (string)$student->id);
            } else {
                $guardian = $engine->createGuardian($values);
                if ($guardian === null) {
                    $this->stampRow($rowsTable, $row, 'rejected', 'The student on this row is no longer on the register.');
                    $result['rejected']++;
                    continue;
                }
                $note = (bool)$guardian->is_primary ? 'Added as the first contact for this student.' : 'Added as an additional contact.';
                $this->stampRow($rowsTable, $row, 'created', $note, (string)$guardian->id);
            }
            $result['created']++;
        }

        $batch->status = 'committed';
        $batch->committed_on = FrozenDate::today();
        $batch->result = $result;
        $this->fetchTable('EmsImportBatches')->saveOrFail($batch);

        $this->audit()->log(
            $this->viewer, 'import.committed', 'import', (string)$batch->id,
            sprintf('Imported %s (%d %s of %s): %d created, %d merged into existing records, %d skipped on review, %d rejected for errors.',
                (string)$batch->filename, (int)$batch->source_row_count, (int)$batch->source_row_count === 1 ? 'row' : 'rows',
                (string)$batch->kind, $result['created'], $result['merged'], $result['skipped'], $result['rejected'])
        );
            }
        );

        return $this->json($this->preview($batch));
    }

    /** POST /imports/{batchId}/discard — throw the staged file away. */
    public function discard(string $batchId): Response
    {
        $batch = $this->findBatch($batchId);
        if ((string)$batch->status !== 'review') {
            $this->fail(409, Messages::IMPORT_COMMITTED_DISCARD);
        }
        $batch->status = 'discarded';
        $this->fetchTable('EmsImportBatches')->saveOrFail($batch);
        $this->fetchTable('EmsImportRows')->deleteAll(['school_id' => $this->viewer->schoolId, 'batch_id' => $batchId]);

        $this->audit()->log(
            $this->viewer, 'import.discarded', 'import', (string)$batch->id,
            sprintf('Discarded %s without importing any of its %d rows.', (string)$batch->filename, (int)$batch->source_row_count)
        );

        return $this->response->withStatus(204);
    }

    // --- helpers -------------------------------------------------------------

    private function findBatch(string $id): EntityInterface
    {
        return $this->findOr404('EmsImportBatches', $id, Messages::IMPORT_NOT_FOUND);
    }

    /** @return array<int, \Cake\Datasource\EntityInterface> */
    private function rowsOf(string $batchId): array
    {
        return $this->tenant()->query('EmsImportRows')
            ->where(['batch_id' => $batchId])
            ->orderByAsc('line_number')->all()->toList();
    }

    private function preview(EntityInterface $batch): array
    {
        return [
            'batch' => ImportSerializer::batch($batch),
            'rows' => array_map([ImportSerializer::class, 'row'], $this->rowsOf((string)$batch->id)),
        ];
    }

    private function stampRow($table, EntityInterface $row, string $outcome, string $note, ?string $resultId = null): void
    {
        $row->outcome = $outcome;
        $row->outcome_note = $note;
        if ($resultId !== null) {
            $row->result_id = $resultId;
        }
        $table->saveOrFail($row);
    }

    private function firstReason(EntityInterface $row): ?string
    {
        $matches = $this->decode($row->matches);

        return $matches[0]['reasons'][0] ?? null;
    }

    /** @param mixed $value */
    private function decode($value): array
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);

            return is_array($decoded) ? $decoded : [];
        }

        return is_array($value) ? $value : [];
    }
}
