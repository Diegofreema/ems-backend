<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CouriersTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CouriersTable Test Case
 */
class CouriersTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\CouriersTable
     */
    protected $Couriers;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'app.Couriers',
        'app.Trequests',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Couriers') ? [] : ['className' => CouriersTable::class];
        $this->Couriers = TableRegistry::getTableLocator()->get('Couriers', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Couriers);

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
