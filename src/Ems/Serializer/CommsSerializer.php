<?php
declare(strict_types=1);

namespace App\Ems\Serializer;

use Cake\Datasource\EntityInterface;

/**
 * Wire shapes for the communication module (document.md §3.20). Optional fields
 * are omitted when unset to match JS JSON.stringify. Stored addresses are
 * already masked (§3.20) — the serializer never sees a full address.
 */
final class CommsSerializer
{
    public static function announcement(EntityInterface $a): array
    {
        $out = [
            'id' => (string)$a->id,
            'schoolId' => (string)$a->school_id,
            'title' => (string)$a->title,
            'body' => (string)$a->body,
            'audience' => (string)$a->audience,
            'category' => (string)$a->category,
            'status' => (string)$a->status,
            'authorName' => (string)$a->author_name,
            'createdOn' => Wire::date($a->created_on),
        ];
        if ($a->published_on !== null) {
            $out['publishedOn'] = Wire::date($a->published_on);
        }
        $out['pinned'] = (bool)$a->pinned;

        return $out;
    }

    /** One portal inbox row (§3.19) — `readAt` null until the panel is opened. */
    public static function portalNotification(EntityInterface $n): array
    {
        return [
            'id' => (string)$n->id,
            'kind' => (string)$n->kind,
            'title' => (string)$n->title,
            'body' => (string)$n->body,
            'studentId' => $n->student_id === null ? null : (string)$n->student_id,
            'date' => Wire::date($n->date),
            'readAt' => Wire::datetime($n->read_at),
            'createdOn' => Wire::datetime($n->created),
        ];
    }

    public static function notification(EntityInterface $n): array
    {
        return [
            'id' => (string)$n->id,
            'schoolId' => (string)$n->school_id,
            'channel' => (string)$n->channel,
            'kind' => (string)$n->kind,
            'subject' => (string)$n->subject,
            'body' => (string)$n->body,
            'audienceLabel' => (string)$n->audience_label,
            'recipientCount' => (int)$n->recipient_count,
            'sentOn' => Wire::date($n->sent_on),
            'sentBy' => (string)$n->sent_by,
        ];
    }

    public static function messageRecipient(EntityInterface $r): array
    {
        $out = [
            'id' => (string)$r->id,
            'schoolId' => (string)$r->school_id,
            'announcementId' => (string)$r->announcement_id,
            'personId' => (string)$r->person_id,
            'personName' => (string)$r->person_name,
            'personKind' => (string)$r->person_kind,
        ];
        if ($r->about_student_name !== null && (string)$r->about_student_name !== '') {
            $out['aboutStudentName'] = (string)$r->about_student_name;
        }
        $out['channel'] = (string)$r->channel;
        $out['address'] = (string)$r->address;
        $out['status'] = (string)$r->status;
        $out['attempts'] = (int)$r->attempts;
        if ($r->provider_ref !== null && (string)$r->provider_ref !== '') {
            $out['providerRef'] = (string)$r->provider_ref;
        }
        if ($r->failure_reason !== null && (string)$r->failure_reason !== '') {
            $out['failureReason'] = (string)$r->failure_reason;
        }
        if ($r->suppressed_reason !== null && (string)$r->suppressed_reason !== '') {
            $out['suppressedReason'] = (string)$r->suppressed_reason;
        }
        $out['updatedOn'] = Wire::date($r->updated_on);

        return $out;
    }

    public static function contactPreference(EntityInterface $p): array
    {
        $out = [
            'id' => (string)$p->id,
            'schoolId' => (string)$p->school_id,
            'personId' => (string)$p->person_id,
            'personName' => (string)$p->person_name,
            'channel' => (string)$p->channel,
            'purpose' => (string)$p->purpose,
            'enabled' => (bool)$p->enabled,
            'source' => (string)$p->source,
            'recordedOn' => Wire::date($p->recorded_on),
        ];
        if ($p->withdrawn_on !== null) {
            $out['withdrawnOn'] = Wire::date($p->withdrawn_on);
        }

        return $out;
    }

    private function __construct()
    {
    }
}
