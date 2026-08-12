<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\SetassignmentsTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\SetassignmentsTable Test Case
 */
class SetassignmentsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\SetassignmentsTable
     */
    protected $Setassignments;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected array $fixtures = [
        'app.Setassignments',
        'app.Subjects',
        'app.Teachers',
        'app.Semesters',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Setassignments') ? [] : ['className' => SetassignmentsTable::class];
        $this->Setassignments = $this->getTableLocator()->get('Setassignments', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Setassignments);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\SetassignmentsTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @uses \App\Model\Table\SetassignmentsTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
