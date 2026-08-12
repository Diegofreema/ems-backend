<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ExamsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ExamsTable Test Case
 */
class ExamsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ExamsTable
     */
    protected $Exams;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'app.Exams',
        'app.Departments',
        'app.Faculties',
        'app.Semesters',
        'app.Sessions',
        'app.Admins',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Exams') ? [] : ['className' => ExamsTable::class];
        $this->Exams = TableRegistry::getTableLocator()->get('Exams', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Exams);

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
