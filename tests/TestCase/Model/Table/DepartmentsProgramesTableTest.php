<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\DepartmentsProgramesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\DepartmentsProgramesTable Test Case
 */
class DepartmentsProgramesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\DepartmentsProgramesTable
     */
    protected $DepartmentsProgrames;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'app.DepartmentsProgrames',
        'app.Departments',
        'app.Programes',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('DepartmentsProgrames') ? [] : ['className' => DepartmentsProgramesTable::class];
        $this->DepartmentsProgrames = TableRegistry::getTableLocator()->get('DepartmentsProgrames', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->DepartmentsProgrames);

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
