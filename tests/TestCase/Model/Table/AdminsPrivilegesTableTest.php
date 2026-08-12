<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\AdminsPrivilegesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\AdminsPrivilegesTable Test Case
 */
class AdminsPrivilegesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\AdminsPrivilegesTable
     */
    protected $AdminsPrivileges;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'app.AdminsPrivileges',
        'app.Admins',
        'app.Privileges',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('AdminsPrivileges') ? [] : ['className' => AdminsPrivilegesTable::class];
        $this->AdminsPrivileges = TableRegistry::getTableLocator()->get('AdminsPrivileges', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->AdminsPrivileges);

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
