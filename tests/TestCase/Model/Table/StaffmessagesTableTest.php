<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\StaffmessagesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\StaffmessagesTable Test Case
 */
class StaffmessagesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\StaffmessagesTable
     */
    protected $Staffmessages;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'app.Staffmessages',
        'app.Teachers',
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
        $config = TableRegistry::getTableLocator()->exists('Staffmessages') ? [] : ['className' => StaffmessagesTable::class];
        $this->Staffmessages = TableRegistry::getTableLocator()->get('Staffmessages', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Staffmessages);

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
