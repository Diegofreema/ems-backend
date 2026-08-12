<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\StaffgradesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\StaffgradesTable Test Case
 */
class StaffgradesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\StaffgradesTable
     */
    protected $Staffgrades;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'app.Staffgrades',
        'app.Teachers',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Staffgrades') ? [] : ['className' => StaffgradesTable::class];
        $this->Staffgrades = TableRegistry::getTableLocator()->get('Staffgrades', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Staffgrades);

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
