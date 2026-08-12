<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\LgasTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\LgasTable Test Case
 */
class LgasTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\LgasTable
     */
    protected $Lgas;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'app.Lgas',
        'app.States',
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
        $config = TableRegistry::getTableLocator()->exists('Lgas') ? [] : ['className' => LgasTable::class];
        $this->Lgas = TableRegistry::getTableLocator()->get('Lgas', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Lgas);

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
