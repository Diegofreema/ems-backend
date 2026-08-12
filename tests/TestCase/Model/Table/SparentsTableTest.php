<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\SparentsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\SparentsTable Test Case
 */
class SparentsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\SparentsTable
     */
    protected $Sparents;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'app.Sparents',
        'app.Users',
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
        $config = TableRegistry::getTableLocator()->exists('Sparents') ? [] : ['className' => SparentsTable::class];
        $this->Sparents = TableRegistry::getTableLocator()->get('Sparents', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Sparents);

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
