<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\PrivilegesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\PrivilegesTable Test Case
 */
class PrivilegesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\PrivilegesTable
     */
    protected $Privileges;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'app.Privileges',
        'app.Admins',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Privileges') ? [] : ['className' => PrivilegesTable::class];
        $this->Privileges = TableRegistry::getTableLocator()->get('Privileges', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Privileges);

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
}
