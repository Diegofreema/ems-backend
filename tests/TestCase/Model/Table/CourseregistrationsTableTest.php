<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CourseregistrationsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CourseregistrationsTable Test Case
 */
class CourseregistrationsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\CourseregistrationsTable
     */
    protected $Courseregistrations;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'app.Courseregistrations',
        'app.Students',
        'app.Sessions',
        'app.Semesters',
        'app.Levels',
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
        $config = TableRegistry::getTableLocator()->exists('Courseregistrations') ? [] : ['className' => CourseregistrationsTable::class];
        $this->Courseregistrations = TableRegistry::getTableLocator()->get('Courseregistrations', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Courseregistrations);

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
