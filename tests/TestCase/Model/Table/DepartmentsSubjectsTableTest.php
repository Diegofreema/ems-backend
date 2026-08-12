<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\DepartmentsSubjectsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\DepartmentsSubjectsTable Test Case
 */
class DepartmentsSubjectsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\DepartmentsSubjectsTable
     */
    protected $DepartmentsSubjects;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'app.DepartmentsSubjects',
        'app.Departments',
        'app.Subjects',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('DepartmentsSubjects') ? [] : ['className' => DepartmentsSubjectsTable::class];
        $this->DepartmentsSubjects = TableRegistry::getTableLocator()->get('DepartmentsSubjects', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->DepartmentsSubjects);

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
