<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ExamquestionsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ExamquestionsTable Test Case
 */
class ExamquestionsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ExamquestionsTable
     */
    protected $Examquestions;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'app.Examquestions',
        'app.Subjects',
        'app.Admins',
        'app.Exams',
        'app.Departments',
        'app.Levels',
        'app.Attemptedexamquestions',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Examquestions') ? [] : ['className' => ExamquestionsTable::class];
        $this->Examquestions = TableRegistry::getTableLocator()->get('Examquestions', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Examquestions);

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
