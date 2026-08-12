<?php
declare(strict_types=1);

namespace App\Ems\Serializer;

use Cake\Datasource\EntityInterface;

/**
 * Wire shape for report jobs (document.md §3.21). Optional fields are omitted
 * when unset. `storagePath` is a private-bucket path (never a public URL); the
 * file itself is only handed over through the audited download endpoint.
 */
final class ReportSerializer
{
    public static function job(EntityInterface $j): array
    {
        $out = [
            'id' => (string)$j->id,
            'schoolId' => (string)$j->school_id,
            'reportType' => (string)$j->report_type,
            'requestedBy' => (string)$j->requested_by,
            'requestedOn' => Wire::date($j->requested_on),
            'filters' => (object)self::decode($j->filters),
            'status' => (string)$j->status,
        ];
        if ($j->storage_path !== null && (string)$j->storage_path !== '') {
            $out['storagePath'] = (string)$j->storage_path;
        }
        if ($j->row_count !== null) {
            $out['rowCount'] = (int)$j->row_count;
        }
        if ($j->ready_on !== null) {
            $out['readyOn'] = Wire::date($j->ready_on);
        }
        if ($j->expires_on !== null) {
            $out['expiresOn'] = Wire::date($j->expires_on);
        }
        if ($j->error !== null && (string)$j->error !== '') {
            $out['error'] = (string)$j->error;
        }
        $out['downloads'] = array_values(self::decode($j->downloads));

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
