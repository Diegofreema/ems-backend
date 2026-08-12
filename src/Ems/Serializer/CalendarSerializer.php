<?php
declare(strict_types=1);

namespace App\Ems\Serializer;

use Cake\Datasource\EntityInterface;

/**
 * Wire shapes for the calendar module (document.md §3.13) and audit events
 * (§1.6).
 */
final class CalendarSerializer
{
    public static function session(EntityInterface $s): array
    {
        $out = [
            'id' => (string)$s->id,
            'schoolId' => (string)$s->school_id,
            'name' => (string)$s->name,
            'startsOn' => Wire::date($s->starts_on),
            'endsOn' => Wire::date($s->ends_on),
            'status' => (string)$s->status,
        ];
        if ($s->closed_on !== null) {
            $out['closedOn'] = Wire::date($s->closed_on);
        }

        return $out;
    }

    /**
     * SessionWithTerms — terms already sorted First/Second/Third by the caller.
     *
     * @param array<\Cake\Datasource\EntityInterface> $terms
     */
    public static function sessionWithTerms(EntityInterface $s, array $terms): array
    {
        return self::session($s) + [
            'terms' => array_map([self::class, 'term'], array_values($terms)),
        ];
    }

    public static function term(EntityInterface $t): array
    {
        $out = [
            'id' => (string)$t->id,
            'schoolId' => (string)$t->school_id,
            'sessionId' => (string)$t->session_id,
            'name' => (string)$t->name,
            'startsOn' => Wire::date($t->starts_on),
            'endsOn' => Wire::date($t->ends_on),
            'status' => (string)$t->status,
        ];
        if ($t->closed_on !== null) {
            $out['closedOn'] = Wire::date($t->closed_on);
        }

        return $out;
    }

    public static function campus(EntityInterface $c): array
    {
        return [
            'id' => (string)$c->id,
            'schoolId' => (string)$c->school_id,
            'name' => (string)$c->name,
            'address' => (string)$c->address,
            'active' => (bool)$c->active,
        ];
    }

    public static function auditEvent(EntityInterface $e): array
    {
        $out = [
            'id' => (string)$e->id,
            'schoolId' => (string)$e->school_id,
            'actor' => (string)$e->actor,
            'actorUserId' => (string)$e->actor_user_id,
            'actorRole' => (string)$e->actor_role,
            'requestId' => (string)$e->request_id,
            'ipAddress' => (string)$e->ip_address,
            'action' => (string)$e->action,
            'outcome' => (string)$e->outcome,
            'entityType' => (string)$e->entity_type,
            'entityId' => (string)$e->entity_id,
            'summary' => (string)$e->summary,
            'at' => Wire::datetime($e->at),
            'keyId' => (string)$e->key_id,
            'previousHash' => (string)$e->previous_hash,
            'eventHash' => (string)$e->event_hash,
        ];
        if ($e->reason !== null && $e->reason !== '') {
            $out['reason'] = (string)$e->reason;
        }

        return $out;
    }

    private function __construct()
    {
    }
}
