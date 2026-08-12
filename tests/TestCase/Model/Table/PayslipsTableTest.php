<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\PayslipsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\PayslipsTable Test Case
 */
class PayslipsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\PayslipsTable
     */
    protected $Payslips;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'app.Payslips',
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
        $config = TableRegistry::getTableLocator()->exists('Payslips') ? [] : ['className' => PayslipsTable::class];
        $this->Payslips = TableRegistry::getTableLocator()->get('Payslips', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Payslips);

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
