<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\AttemptedquizzesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\AttemptedquizzesTable Test Case
 */
class AttemptedquizzesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\AttemptedquizzesTable
     */
    protected $Attemptedquizzes;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'app.Attemptedquizzes',
        'app.Quizquestions',
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
        $config = TableRegistry::getTableLocator()->exists('Attemptedquizzes') ? [] : ['className' => AttemptedquizzesTable::class];
        $this->Attemptedquizzes = TableRegistry::getTableLocator()->get('Attemptedquizzes', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Attemptedquizzes);

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
