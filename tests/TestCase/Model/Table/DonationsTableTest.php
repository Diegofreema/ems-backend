<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\DonationsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\DonationsTable Test Case
 */
class DonationsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\DonationsTable
     */
    protected $Donations;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'app.Donations',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Donations') ? [] : ['className' => DonationsTable::class];
        $this->Donations = TableRegistry::getTableLocator()->get('Donations', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Donations);

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
