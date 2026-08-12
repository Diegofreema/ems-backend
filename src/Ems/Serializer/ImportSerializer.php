<?php
declare(strict_types=1);

namespace App\Ems\Serializer;

use Cake\Datasource\EntityInterface;

/**
 * Wire shapes for staged CSV imports (document.md §3.17). `values`/`check` come
 * back from the `row_values`/`row_check` columns (renamed off MySQL reserved
 * words). Optional fields are omitted when unset.
 */
final class ImportSerializer
{
    public static function batch(EntityInterface $b): array
    {
        $out = [
            'id' => (string)$b->id,
            'schoolId' => (string)$b->school_id,
            'kind' => (string)$b->kind,
            'filename' => (string)$b->filename,
            'uploadedBy' => (string)$b->uploaded_by,
            'uploadedOn' => Wire::date($b->uploaded_on),
            'sourceRowCount' => (int)$b->source_row_count,
            'ignoredColumns' => array_values(self::decode($b->ignored_columns)),
            'status' => (string)$b->status,
        ];
        if ($b->committed_on !== null) {
            $out['committedOn'] = Wire::date($b->committed_on);
        }
        $result = self::decode($b->result);
        if ($result !== []) {
            $out['result'] = [
                'created' => (int)($result['created'] ?? 0),
                'merged' => (int)($result['merged'] ?? 0),
                'skipped' => (int)($result['skipped'] ?? 0),
                'rejected' => (int)($result['rejected'] ?? 0),
            ];
        }

        return $out;
    }

    public static function row(EntityInterface $r): array
    {
        $out = [
            'id' => (string)$r->id,
            'batchId' => (string)$r->batch_id,
            'schoolId' => (string)$r->school_id,
            'lineNumber' => (int)$r->line_number,
            'values' => (object)self::decode($r->row_values),
            'check' => (string)$r->row_check,
            'issues' => array_values(self::decode($r->issues)),
            'matches' => array_values(self::decode($r->matches)),
            'decision' => (string)$r->decision,
        ];
        if ($r->merge_target_id !== null && (string)$r->merge_target_id !== '') {
            $out['mergeTargetId'] = (string)$r->merge_target_id;
        }
        if ($r->outcome !== null && (string)$r->outcome !== '') {
            $out['outcome'] = (string)$r->outcome;
        }
        if ($r->result_id !== null && (string)$r->result_id !== '') {
            $out['resultId'] = (string)$r->result_id;
        }
        if ($r->outcome_note !== null && (string)$r->outcome_note !== '') {
            $out['outcomeNote'] = (string)$r->outcome_note;
        }

        return $out;
    }

    /** @param mixed $value */
    private static function decode($value): array
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);

            return is_array($decoded) ? $decoded : [];
        }

        return is_array($value) ? $value : [];
    }

    private function __construct()
    {
    }
}
