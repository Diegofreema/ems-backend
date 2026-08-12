<?php
declare(strict_types=1);

namespace App\Ems;

use RuntimeException;

/** Orders append only finance events by their signed hash links. */
final class FinanceChain
{
    public const GENESIS_HASH = '0000000000000000000000000000000000000000000000000000000000000000';

    /**
     * @param array<int, array<string, mixed>> $rows Finance events.
     * @return array<int, array<string, mixed>> Events in signed chain order.
     */
    public static function order(array $rows): array
    {
        $next = [];
        $hashes = [];
        foreach ($rows as $row) {
            $previous = (string)($row['previous_hash'] ?? '');
            $hash = (string)($row['event_hash'] ?? '');
            if ($previous === '' || $hash === '') {
                throw new RuntimeException('Finance chain contains an event without both hashes.');
            }
            if (isset($next[$previous])) {
                throw new RuntimeException('Finance chain forks after hash ' . $previous . '.');
            }
            if (isset($hashes[$hash])) {
                throw new RuntimeException('Finance chain repeats event hash ' . $hash . '.');
            }
            $next[$previous] = $row;
            $hashes[$hash] = true;
        }

        $ordered = [];
        $expected = self::GENESIS_HASH;
        while (isset($next[$expected])) {
            $row = $next[$expected];
            $ordered[] = $row;
            $expected = (string)$row['event_hash'];
            if (count($ordered) > count($rows)) {
                throw new RuntimeException('Finance chain contains a cycle.');
            }
        }
        if (count($ordered) !== count($rows)) {
            throw new RuntimeException('Finance chain contains an orphaned or broken link.');
        }

        return $ordered;
    }

    /**
     * @param array<int, array<string, mixed>> $rows Finance events.
     * @return string Final event hash, or the genesis hash for an empty chain.
     */
    public static function tipHash(array $rows): string
    {
        $ordered = self::order($rows);
        if ($ordered === []) {
            return self::GENESIS_HASH;
        }

        return (string)$ordered[count($ordered) - 1]['event_hash'];
    }
}
