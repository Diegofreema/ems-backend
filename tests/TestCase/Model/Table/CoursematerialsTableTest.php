<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CoursematerialsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CoursematerialsTable Test Case
 */
class CoursematerialsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\CoursematerialsTable
     */
    protected $Coursematerials;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'app.Coursematerials',
        'app.Subjects',
        'app.Teachers',
        'app.Departments',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Coursematerials') ? [] : ['className' => CoursematerialsTable::class];
        $this->Coursematerials = TableRegistry::getTableLocator()->get('Coursematerials', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Coursematerials);

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
