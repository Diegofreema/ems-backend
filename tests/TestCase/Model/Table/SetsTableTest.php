<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\SetsTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\SetsTable Test Case
 */
class SetsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\SetsTable
     */
    protected $Sets;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected array $fixtures = [
        'app.Sets',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Sets') ? [] : ['className' => SetsTable::class];
        $this->Sets = $this->getTableLocator()->get('Sets', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Sets);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\SetsTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
