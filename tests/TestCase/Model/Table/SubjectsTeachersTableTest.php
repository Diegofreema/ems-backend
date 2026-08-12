<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\SubjectsTeachersTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\SubjectsTeachersTable Test Case
 */
class SubjectsTeachersTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\SubjectsTeachersTable
     */
    protected $SubjectsTeachers;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'app.SubjectsTeachers',
        'app.Teachers',
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
        $config = TableRegistry::getTableLocator()->exists('SubjectsTeachers') ? [] : ['className' => SubjectsTeachersTable::class];
        $this->SubjectsTeachers = TableRegistry::getTableLocator()->get('SubjectsTeachers', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->SubjectsTeachers);

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
