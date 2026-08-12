<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\TrequestsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\TrequestsTable Test Case
 */
class TrequestsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\TrequestsTable
     */
    protected $Trequests;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'app.Trequests',
        'app.Students',
        'app.Continents',
        'app.Countries',
        'app.States',
        'app.Couriers',
        'app.Fees',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Trequests') ? [] : ['className' => TrequestsTable::class];
        $this->Trequests = TableRegistry::getTableLocator()->get('Trequests', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Trequests);

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
