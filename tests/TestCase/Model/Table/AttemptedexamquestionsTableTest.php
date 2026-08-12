<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\AttemptedexamquestionsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\AttemptedexamquestionsTable Test Case
 */
class AttemptedexamquestionsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\AttemptedexamquestionsTable
     */
    protected $Attemptedexamquestions;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'app.Attemptedexamquestions',
        'app.Examquestions',
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
        $config = TableRegistry::getTableLocator()->exists('Attemptedexamquestions') ? [] : ['className' => AttemptedexamquestionsTable::class];
        $this->Attemptedexamquestions = TableRegistry::getTableLocator()->get('Attemptedexamquestions', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Attemptedexamquestions);

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
