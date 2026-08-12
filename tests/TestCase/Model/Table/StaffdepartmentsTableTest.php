<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\StaffdepartmentsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\StaffdepartmentsTable Test Case
 */
class StaffdepartmentsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\StaffdepartmentsTable
     */
    protected $Staffdepartments;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'app.Staffdepartments',
        'app.Teachers',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Staffdepartments') ? [] : ['className' => StaffdepartmentsTable::class];
        $this->Staffdepartments = TableRegistry::getTableLocator()->get('Staffdepartments', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Staffdepartments);

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
}
