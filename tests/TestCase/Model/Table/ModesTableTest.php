<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ModesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ModesTable Test Case
 */
class ModesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ModesTable
     */
    protected $Modes;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'app.Modes',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Modes') ? [] : ['className' => ModesTable::class];
        $this->Modes = TableRegistry::getTableLocator()->get('Modes', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Modes);

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
