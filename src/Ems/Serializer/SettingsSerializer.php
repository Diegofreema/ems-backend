<?php
declare(strict_types=1);

namespace App\Ems\Serializer;

use Cake\Datasource\EntityInterface;

/**
 * Wire shapes for the tenant registry (School, §1.1) and accounts
 * (SchoolUser, §3.14). The user projection is a strict whitelist — the
 * contract forbids ever returning password/inviteCode/reset material.
 */
final class SettingsSerializer
{
    public static function school(EntityInterface $s): array
    {
        $out = [
            'id' => (string)$s->id,
            'slug' => (string)$s->slug,
            'name' => (string)$s->name,
            'shortName' => (string)$s->short_name,
            'motto' => (string)$s->motto,
            'address' => (string)$s->address,
        ];
        if ($s->logo !== null && $s->logo !== '') {
            $out['logo'] = (string)$s->logo;
        }

        return $out;
    }

    public static function user(EntityInterface $u): array
    {
        $out = [
            'id' => (string)$u->id,
            'schoolId' => (string)$u->school_id,
            'name' => (string)$u->name,
            'email' => (string)$u->email,
            'role' => (string)$u->role,
            'status' => (string)$u->status,
            'addedOn' => Wire::date($u->added_on),
            // Self-service account fields (Account & security). twoFactorEnabled
            // is always present; phone/avatar are omitted when unset, matching how
            // the school logo is treated.
            'twoFactorEnabled' => (bool)$u->two_factor_enabled,
        ];
        if ($u->phone !== null && (string)$u->phone !== '') {
            $out['phone'] = (string)$u->phone;
        }
        if ($u->avatar !== null && (string)$u->avatar !== '') {
            $out['avatar'] = (string)$u->avatar;
        }
        $link = self::link($u);
        if ($link !== null) {
            $out['link'] = $link;
        }

        return $out;
    }

    /**
     * Rebuild the PersonLink union from its three storage columns.
     */
    public static function link(EntityInterface $u): ?array
    {
        switch ($u->link_kind) {
            case 'teacher':
                return $u->link_teacher_id === null ? null : [
                    'kind' => 'teacher',
                    'teacherId' => (string)$u->link_teacher_id,
                ];
            case 'student':
                return $u->link_student_id === null ? null : [
                    'kind' => 'student',
                    'studentId' => (string)$u->link_student_id,
                ];
            case 'parent':
                $ids = $u->link_student_ids;
                if (is_string($ids)) {
                    $ids = json_decode($ids, true);
                }

                return [
                    'kind' => 'parent',
                    'studentIds' => is_array($ids) ? array_values(array_map('strval', $ids)) : [],
                ];
            default:
                return null;
        }
    }

    private function __construct()
    {
    }
}
