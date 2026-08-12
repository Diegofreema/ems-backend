<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\TimetablesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\TimetablesTable Test Case
 */
class TimetablesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\TimetablesTable
     */
    protected $Timetables;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'app.Timetables',
        'app.Sessions',
        'app.Departments',
        'app.Levels',
        'app.Semesters',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Timetables') ? [] : ['className' => TimetablesTable::class];
        $this->Timetables = TableRegistry::getTableLocator()->get('Timetables', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Timetables);

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
