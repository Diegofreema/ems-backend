<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\SubjectsStudentsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\SubjectsStudentsTable Test Case
 */
class SubjectsStudentsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\SubjectsStudentsTable
     */
    protected $SubjectsStudents;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'app.SubjectsStudents',
        'app.Subjects',
        'app.Students',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('SubjectsStudents') ? [] : ['className' => SubjectsStudentsTable::class];
        $this->SubjectsStudents = TableRegistry::getTableLocator()->get('SubjectsStudents', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->SubjectsStudents);

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
