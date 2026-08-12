<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\QuizzesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\QuizzesTable Test Case
 */
class QuizzesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\QuizzesTable
     */
    protected $Quizzes;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'app.Quizzes',
        'app.Faculties',
        'app.Departments',
        'app.Semesters',
        'app.Sessions',
        'app.Subjects',
        'app.Quizquestions',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Quizzes') ? [] : ['className' => QuizzesTable::class];
        $this->Quizzes = TableRegistry::getTableLocator()->get('Quizzes', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Quizzes);

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
