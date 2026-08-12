<?php
declare(strict_types=1);

namespace App\Test\TestCase\Ems;

use App\Ems\FinanceChain;
use Cake\TestSuite\TestCase;
use RuntimeException;

final class FinanceChainTest extends TestCase
{
    public function testOrdersEventsByHashLinksWhenTimestampsWouldTie(): void
    {
        $first = str_repeat('a', 64);
        $second = str_repeat('b', 64);
        $third = str_repeat('c', 64);
        $rows = [
            ['id' => 'reversal', 'previous_hash' => $second, 'event_hash' => $third],
            ['id' => 'second-payment', 'previous_hash' => $first, 'event_hash' => $second],
            ['id' => 'first-payment', 'previous_hash' => FinanceChain::GENESIS_HASH, 'event_hash' => $first],
        ];

        $ordered = FinanceChain::order($rows);

        $this->assertSame(['first-payment', 'second-payment', 'reversal'], array_column($ordered, 'id'));
        $this->assertSame($third, FinanceChain::tipHash($rows));
    }

    public function testRejectsForkedChain(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('forks');

        FinanceChain::order([
            ['id' => 'one', 'previous_hash' => FinanceChain::GENESIS_HASH, 'event_hash' => str_repeat('a', 64)],
            ['id' => 'two', 'previous_hash' => FinanceChain::GENESIS_HASH, 'event_hash' => str_repeat('b', 64)],
        ]);
    }

    public function testRejectsOrphanedChain(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('orphaned');

        FinanceChain::order([
            ['id' => 'one', 'previous_hash' => str_repeat('d', 64), 'event_hash' => str_repeat('a', 64)],
        ]);
    }
}
