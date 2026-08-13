<?php
declare(strict_types=1);

namespace App\Ems;

use Cake\ORM\Locator\LocatorInterface;
use Cake\Utility\Text;

/**
 * Per-school monotonic sequences (document.md §3.9 #3).
 *
 * The mock derives applicationNumber / admissionNumber from table LENGTH, which
 * collides after a deletion. This uses a real counter row per (school, name)
 * with MySQL's connection-scoped LAST_INSERT_ID() trick so concurrent callers
 * never receive the same value, and a deleted number is never reissued.
 *
 * The counter is seeded once from an optional floor (e.g. the current max) so
 * numbers pick up above any records that predate the sequence.
 */
class Sequences
{
    /**
     * @var \Cake\ORM\Locator\LocatorInterface
     */
    private LocatorInterface $locator;

    public function __construct(LocatorInterface $locator)
    {
        $this->locator = $locator;
    }

    /**
     * The next value in the (school, name) sequence. If the row does not exist
     * yet it is created at `floor`, so the first returned value is floor + 1.
     */
    public function next(string $schoolId, string $name, int $floor = 0): int
    {
        $conn = $this->locator->get('EmsSequences')->getConnection();

        // Create the counter at `floor` on first use (ignored if it exists).
        $conn->execute(
            'INSERT IGNORE INTO ems_sequences (id, school_id, name, value, created, modified) '
            . 'VALUES (?, ?, ?, ?, NOW(), NOW())',
            [Text::uuid(), $schoolId, $name, $floor],
        );
        // Atomic increment: LAST_INSERT_ID(expr) stashes the new value on this
        // connection, so the follow-up SELECT cannot race another writer.
        $conn->execute(
            'UPDATE ems_sequences SET value = LAST_INSERT_ID(value + 1), modified = NOW() '
            . 'WHERE school_id = ? AND name = ?',
            [$schoolId, $name],
        );
        $row = $conn->execute('SELECT LAST_INSERT_ID() AS v')->fetch('assoc');

        return (int)$row['v'];
    }
}
