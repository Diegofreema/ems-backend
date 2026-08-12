<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\SparentsStudentsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\SparentsStudentsTable Test Case
 */
class SparentsStudentsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\SparentsStudentsTable
     */
    protected $SparentsStudents;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'app.SparentsStudents',
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
        $config = TableRegistry::getTableLocator()->exists('SparentsStudents') ? [] : ['className' => SparentsStudentsTable::class];
        $this->SparentsStudents = TableRegistry::getTableLocator()->get('SparentsStudents', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->SparentsStudents);

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
