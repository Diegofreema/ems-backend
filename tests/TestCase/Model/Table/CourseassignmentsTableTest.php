<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CourseassignmentsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CourseassignmentsTable Test Case
 */
class CourseassignmentsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\CourseassignmentsTable
     */
    protected $Courseassignments;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'app.Courseassignments',
        'app.Departments',
        'app.Semesters',
        'app.Levels',
        'app.Users',
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
        $config = TableRegistry::getTableLocator()->exists('Courseassignments') ? [] : ['className' => CourseassignmentsTable::class];
        $this->Courseassignments = TableRegistry::getTableLocator()->get('Courseassignments', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Courseassignments);

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
