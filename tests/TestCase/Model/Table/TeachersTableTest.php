<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\TeachersTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\TeachersTable Test Case
 */
class TeachersTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\TeachersTable
     */
    protected $Teachers;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'app.Teachers',
        'app.Users',
        'app.Countries',
        'app.States',
        'app.Departments',
        'app.Staffgrades',
        'app.Staffdepartments',
        'app.Coursematerials',
        'app.Payslips',
        'app.Staffmessages',
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
        $config = TableRegistry::getTableLocator()->exists('Teachers') ? [] : ['className' => TeachersTable::class];
        $this->Teachers = TableRegistry::getTableLocator()->get('Teachers', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Teachers);

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
