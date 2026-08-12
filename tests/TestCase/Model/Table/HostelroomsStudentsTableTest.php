<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\HostelroomsStudentsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\HostelroomsStudentsTable Test Case
 */
class HostelroomsStudentsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\HostelroomsStudentsTable
     */
    protected $HostelroomsStudents;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'app.HostelroomsStudents',
        'app.Hostelrooms',
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
        $config = TableRegistry::getTableLocator()->exists('HostelroomsStudents') ? [] : ['className' => HostelroomsStudentsTable::class];
        $this->HostelroomsStudents = TableRegistry::getTableLocator()->get('HostelroomsStudents', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->HostelroomsStudents);

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
