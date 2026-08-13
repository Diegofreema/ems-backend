<?php
declare(strict_types=1);

namespace App\Ems\Serializer;

use DateTimeInterface;

/**
 * Shared wire-format conversions (document.md §1.5): dates as YYYY-MM-DD,
 * datetimes as ISO-8601 UTC, ids as strings.
 */
final class Wire
{
    /**
     * @param mixed $value Date-ish value from the ORM.
     */
    public static function date(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }
        if (is_object($value) && method_exists($value, 'format')) {
            return $value->format('Y-m-d');
        }

        return substr((string)$value, 0, 10);
    }

    /**
     * @param mixed $value Datetime-ish value from the ORM.
     */
    public static function datetime(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }
        if ($value instanceof DateTimeInterface) {
            return $value->setTimezone('UTC')->format('Y-m-d\TH:i:s\Z');
        }

        return (string)$value;
    }

    /**
     * MySQL DECIMAL comes back as a string — the wire wants a number.
     *
     * @param mixed $value
     */
    public static function decimal(mixed $value): ?float
    {
        return $value === null ? null : (float)$value;
    }

    /**
     * Normalise a computed number to match JS JSON.stringify: a whole value is
     * emitted as an integer (70, not 70.0), a fractional value as a float
     * (72.5). Both parse to the identical JS number, but this keeps the wire
     * bytes identical to the mock the frontend was written against.
     *
     * @param mixed $value
     * @return float|int|null
     */
    public static function num(mixed $value): int|float|null
    {
        if ($value === null) {
            return null;
        }
        $f = (float)$value;

        return $f == floor($f) && is_finite($f) ? (int)$f : $f;
    }

    private function __construct()
    {
    }
}
