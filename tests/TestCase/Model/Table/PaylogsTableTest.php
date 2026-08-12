<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\PaylogsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\PaylogsTable Test Case
 */
class PaylogsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\PaylogsTable
     */
    protected $Paylogs;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'app.Paylogs',
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
        $config = TableRegistry::getTableLocator()->exists('Paylogs') ? [] : ['className' => PaylogsTable::class];
        $this->Paylogs = TableRegistry::getTableLocator()->get('Paylogs', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Paylogs);

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
