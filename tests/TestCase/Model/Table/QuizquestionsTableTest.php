<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\QuizquestionsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\QuizquestionsTable Test Case
 */
class QuizquestionsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\QuizquestionsTable
     */
    protected $Quizquestions;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'app.Quizquestions',
        'app.Quizzes',
        'app.Attemptedquizzes',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Quizquestions') ? [] : ['className' => QuizquestionsTable::class];
        $this->Quizquestions = TableRegistry::getTableLocator()->get('Quizquestions', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Quizquestions);

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
