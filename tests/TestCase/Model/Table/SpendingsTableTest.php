<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\SpendingsTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\SpendingsTable Test Case
 */
class SpendingsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\SpendingsTable
     */
    protected $Spendings;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected array $fixtures = [
        'app.Spendings',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Spendings') ? [] : ['className' => SpendingsTable::class];
        $this->Spendings = $this->getTableLocator()->get('Spendings', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Spendings);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\SpendingsTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
