<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CourseassignmentsSubjectsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CourseassignmentsSubjectsTable Test Case
 */
class CourseassignmentsSubjectsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\CourseassignmentsSubjectsTable
     */
    protected $CourseassignmentsSubjects;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'app.CourseassignmentsSubjects',
        'app.Courseassignments',
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
        $config = TableRegistry::getTableLocator()->exists('CourseassignmentsSubjects') ? [] : ['className' => CourseassignmentsSubjectsTable::class];
        $this->CourseassignmentsSubjects = TableRegistry::getTableLocator()->get('CourseassignmentsSubjects', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->CourseassignmentsSubjects);

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
