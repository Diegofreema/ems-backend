<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\LevelsSubjectsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\LevelsSubjectsTable Test Case
 */
class LevelsSubjectsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\LevelsSubjectsTable
     */
    protected $LevelsSubjects;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'app.LevelsSubjects',
        'app.Subjects',
        'app.Levels',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('LevelsSubjects') ? [] : ['className' => LevelsSubjectsTable::class];
        $this->LevelsSubjects = TableRegistry::getTableLocator()->get('LevelsSubjects', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->LevelsSubjects);

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
