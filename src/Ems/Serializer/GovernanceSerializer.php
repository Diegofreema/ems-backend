<?php
declare(strict_types=1);

namespace App\Ems\Serializer;

use Cake\Datasource\EntityInterface;

/**
 * Wire shapes for the governance modules — privacy requests (§3.23) and
 * incidents (§3.24). Optional fields are OMITTED when unset so the bytes match
 * JS JSON.stringify (which drops `undefined`).
 */
final class GovernanceSerializer
{
    public static function privacyRequest(EntityInterface $r): array
    {
        $out = [
            'id' => (string)$r->id,
            'schoolId' => (string)$r->school_id,
            'reference' => (string)$r->reference,
            'kind' => (string)$r->kind,
            'subjectName' => (string)$r->subject_name,
        ];
        if ($r->subject_student_id !== null && (string)$r->subject_student_id !== '') {
            $out['subjectStudentId'] = (string)$r->subject_student_id;
        }
        $out['requestedBy'] = (string)$r->requested_by;
        $out['contact'] = (string)$r->contact;
        $out['requestedOn'] = Wire::date($r->requested_on);
        $out['detail'] = (string)$r->detail;
        $out['status'] = (string)$r->status;
        self::maybe($out, 'identityVerifiedBy', $r->identity_verified_by);
        self::maybeDate($out, 'identityVerifiedOn', $r->identity_verified_on);
        self::maybe($out, 'identityEvidence', $r->identity_evidence);
        self::maybe($out, 'decidedBy', $r->decided_by);
        self::maybeDate($out, 'decidedOn', $r->decided_on);
        self::maybe($out, 'decisionNote', $r->decision_note);
        self::maybeDate($out, 'fulfilledOn', $r->fulfilled_on);
        self::maybe($out, 'fulfilmentNote', $r->fulfilment_note);
        self::maybe($out, 'retentionNote', $r->retention_note);

        return $out;
    }

    /**
     * The register row (§3.24) — deliberately detail-free: no description, no
     * categories, no entries. `viewerIsResponder` is decided by the caller.
     */
    public static function incidentSummary(EntityInterface $i, bool $viewerIsResponder): array
    {
        $responders = [];
        foreach (self::decode($i->responders) ?? [] as $r) {
            $responders[] = [
                'userId' => (string)($r['userId'] ?? ''),
                'name' => (string)($r['name'] ?? ''),
                'lead' => (bool)($r['lead'] ?? false),
            ];
        }

        return [
            'id' => (string)$i->id,
            'reference' => (string)$i->reference,
            'title' => (string)$i->title,
            'severity' => (string)$i->severity,
            'status' => (string)$i->status,
            'dataCategoryCount' => count(self::decode($i->data_categories) ?? []),
            'responders' => $responders,
            'viewerIsResponder' => $viewerIsResponder,
            'discoveredOn' => Wire::date($i->discovered_on),
            'recordedOn' => Wire::date($i->recorded_on),
        ];
    }

    /**
     * The full case. `candidates` (addable active admins) is supplied by the
     * caller so the detail gate stays in the controller.
     *
     * @param array<int, array{userId:string,name:string}> $candidates
     */
    public static function incident(EntityInterface $i, ?array $candidates = null): array
    {
        $out = [
            'id' => (string)$i->id,
            'schoolId' => (string)$i->school_id,
            'reference' => (string)$i->reference,
            'title' => (string)$i->title,
            'description' => (string)$i->description,
            'severity' => (string)$i->severity,
            'dataCategories' => array_values(self::decode($i->data_categories) ?? []),
            'status' => (string)$i->status,
            'discoveredOn' => Wire::date($i->discovered_on),
            'recordedOn' => Wire::date($i->recorded_on),
            'recordedBy' => (string)$i->recorded_by,
            'responders' => self::responders($i),
        ];
        self::maybe($out, 'containmentNote', $i->containment_note);
        self::maybe($out, 'reportEvidence', $i->report_evidence);
        self::maybe($out, 'closeSummary', $i->close_summary);
        $out['entries'] = array_values(self::decode($i->entries) ?? []);
        if ($candidates !== null) {
            $out['candidates'] = array_values($candidates);
        }

        return $out;
    }

    /**
     * Stored responders in wire shape — `lead` present (true) only on the lead,
     * as JS stores it (the recorder), omitted for everyone added later.
     */
    private static function responders(EntityInterface $i): array
    {
        $out = [];
        foreach (self::decode($i->responders) ?? [] as $r) {
            $row = [
                'userId' => (string)($r['userId'] ?? ''),
                'name' => (string)($r['name'] ?? ''),
                'addedOn' => (string)($r['addedOn'] ?? ''),
                'addedBy' => (string)($r['addedBy'] ?? ''),
            ];
            if (!empty($r['lead'])) {
                $row['lead'] = true;
            }
            $out[] = $row;
        }

        return $out;
    }

    /** @param mixed $value */
    private static function decode($value): ?array
    {
        if ($value === null) {
            return null;
        }
        if (is_string($value)) {
            $decoded = json_decode($value, true);

            return is_array($decoded) ? $decoded : null;
        }

        return is_array($value) ? $value : null;
    }

    /** @param mixed $value */
    private static function maybe(array &$out, string $key, $value): void
    {
        if ($value !== null && (string)$value !== '') {
            $out[$key] = (string)$value;
        }
    }

    /** @param mixed $value */
    private static function maybeDate(array &$out, string $key, $value): void
    {
        if ($value !== null) {
            $out[$key] = Wire::date($value);
        }
    }

    private function __construct()
    {
    }
}
