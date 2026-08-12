<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\DepartmentsSemestersTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\DepartmentsSemestersTable Test Case
 */
class DepartmentsSemestersTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\DepartmentsSemestersTable
     */
    protected $DepartmentsSemesters;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'app.DepartmentsSemesters',
        'app.Departments',
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
        $config = TableRegistry::getTableLocator()->exists('DepartmentsSemesters') ? [] : ['className' => DepartmentsSemestersTable::class];
        $this->DepartmentsSemesters = TableRegistry::getTableLocator()->get('DepartmentsSemesters', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->DepartmentsSemesters);

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
