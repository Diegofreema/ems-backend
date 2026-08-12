<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\FeesStudentsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\FeesStudentsTable Test Case
 */
class FeesStudentsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\FeesStudentsTable
     */
    protected $FeesStudents;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'app.FeesStudents',
        'app.Fees',
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
        $config = TableRegistry::getTableLocator()->exists('FeesStudents') ? [] : ['className' => FeesStudentsTable::class];
        $this->FeesStudents = TableRegistry::getTableLocator()->get('FeesStudents', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->FeesStudents);

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
