<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CourseregistrationsSubjectsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CourseregistrationsSubjectsTable Test Case
 */
class CourseregistrationsSubjectsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\CourseregistrationsSubjectsTable
     */
    protected $CourseregistrationsSubjects;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'app.CourseregistrationsSubjects',
        'app.Courseregistrations',
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
        $config = TableRegistry::getTableLocator()->exists('CourseregistrationsSubjects') ? [] : ['className' => CourseregistrationsSubjectsTable::class];
        $this->CourseregistrationsSubjects = TableRegistry::getTableLocator()->get('CourseregistrationsSubjects', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->CourseregistrationsSubjects);

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
