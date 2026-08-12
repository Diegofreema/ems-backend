<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\SubjectsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\SubjectsTable Test Case
 */
class SubjectsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\SubjectsTable
     */
    protected $Subjects;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'app.Subjects',
        'app.Departments',
        'app.Users',
        'app.Semesters',
        'app.Levels',
        'app.Coursematerials',
        'app.Results',
        'app.Topics',
        'app.Courseassignments',
        'app.Courseregistrations',
        'app.Students',
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
        $config = TableRegistry::getTableLocator()->exists('Subjects') ? [] : ['className' => SubjectsTable::class];
        $this->Subjects = TableRegistry::getTableLocator()->get('Subjects', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Subjects);

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
