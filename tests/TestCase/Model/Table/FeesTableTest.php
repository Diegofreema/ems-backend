<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\FeesTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\FeesTable Test Case
 */
class FeesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\FeesTable
     */
    protected $Fees;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected array $fixtures = [
        'app.Fees',
        'app.Users',
        'app.Feeallocations',
        'app.Invoices',
        'app.Transactions',
        'app.Trequests',
        'app.Departments',
        'app.Levels',
        'app.Students',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Fees') ? [] : ['className' => FeesTable::class];
        $this->Fees = $this->getTableLocator()->get('Fees', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Fees);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\FeesTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @uses \App\Model\Table\FeesTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
