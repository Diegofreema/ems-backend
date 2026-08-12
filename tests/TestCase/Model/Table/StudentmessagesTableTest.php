<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\StudentmessagesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\StudentmessagesTable Test Case
 */
class StudentmessagesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\StudentmessagesTable
     */
    protected $Studentmessages;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'app.Studentmessages',
        'app.Students',
        'app.Users',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Studentmessages') ? [] : ['className' => StudentmessagesTable::class];
        $this->Studentmessages = TableRegistry::getTableLocator()->get('Studentmessages', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Studentmessages);

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
