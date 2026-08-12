<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ResultsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ResultsTable Test Case
 */
class ResultsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ResultsTable
     */
    protected $Results;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'app.Results',
        'app.Students',
        'app.Faculties',
        'app.Departments',
        'app.Subjects',
        'app.Semesters',
        'app.Sessions',
        'app.Users',
        'app.Levels',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Results') ? [] : ['className' => ResultsTable::class];
        $this->Results = TableRegistry::getTableLocator()->get('Results', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Results);

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
