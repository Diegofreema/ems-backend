<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\FeeallocationsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\FeeallocationsTable Test Case
 */
class FeeallocationsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\FeeallocationsTable
     */
    protected $Feeallocations;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'app.Feeallocations',
        'app.Fees',
        'app.Departments',
        'app.Users',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Feeallocations') ? [] : ['className' => FeeallocationsTable::class];
        $this->Feeallocations = TableRegistry::getTableLocator()->get('Feeallocations', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Feeallocations);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
