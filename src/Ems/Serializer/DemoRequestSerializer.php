<?php
declare(strict_types=1);

namespace App\Ems\Serializer;

use Cake\Datasource\EntityInterface;

/**
 * Wire shape for a book-a-demo request. Optional fields are OMITTED when empty
 * so the bytes match a JS `JSON.stringify` (which drops `undefined`); the shape
 * is total — a freshly-saved entity and a queried one serialise identically.
 */
final class DemoRequestSerializer
{
    public static function request(EntityInterface $r): array
    {
        $out = [
            'id' => (string)$r->id,
            'institutionName' => (string)$r->institution_name,
            'contactName' => (string)$r->contact_name,
            'email' => (string)$r->email,
            'phone' => (string)$r->phone,
            'status' => (string)$r->status,
            'createdAt' => Wire::datetime($r->created),
        ];
        self::put($out, 'roleTitle', $r->role_title);
        self::put($out, 'location', $r->location);
        self::put($out, 'sizeBand', $r->size_band);
        self::put($out, 'message', $r->message);

        return $out;
    }

    /** Add a key only when the value is a non-empty string. */
    private static function put(array &$out, string $key, mixed $value): void
    {
        $value = $value === null ? '' : trim((string)$value);
        if ($value !== '') {
            $out[$key] = $value;
        }
    }

    private function __construct()
    {
    }
}
