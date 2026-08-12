<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\UserloginsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\UserloginsTable Test Case
 */
class UserloginsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\UserloginsTable
     */
    protected $Userlogins;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'app.Userlogins',
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
        $config = TableRegistry::getTableLocator()->exists('Userlogins') ? [] : ['className' => UserloginsTable::class];
        $this->Userlogins = TableRegistry::getTableLocator()->get('Userlogins', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Userlogins);

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
