<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\LevelsTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\LevelsTable Test Case
 */
class LevelsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\LevelsTable
     */
    protected $Levels;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected array $fixtures = [
        'app.Levels',
        'app.Courseassignments',
        'app.Courseregistrations',
        'app.Results',
        'app.Students',
        'app.Subjects',
        'app.Timetables',
        'app.Departments',
        'app.Fees',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Levels') ? [] : ['className' => LevelsTable::class];
        $this->Levels = $this->getTableLocator()->get('Levels', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Levels);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\LevelsTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
