<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\StudentsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\StudentsTable Test Case
 */
class StudentsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\StudentsTable
     */
    protected $Students;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'app.Students',
        'app.Departments',
        'app.States',
        'app.Countries',
        'app.Lgas',
        'app.Users',
        'app.Levels',
        'app.Faculties',
        'app.Programes',
        'app.Borrowedbooks',
        'app.Courseregistrations',
        'app.Invoices',
        'app.Results',
        'app.Studentmessages',
        'app.Transactions',
        'app.Trequests',
        'app.Fees',
        'app.Hostelrooms',
        'app.Sparents',
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
        $config = TableRegistry::getTableLocator()->exists('Students') ? [] : ['className' => StudentsTable::class];
        $this->Students = TableRegistry::getTableLocator()->get('Students', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Students);

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
