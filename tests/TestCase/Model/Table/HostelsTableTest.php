<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\HostelsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\HostelsTable Test Case
 */
class HostelsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\HostelsTable
     */
    protected $Hostels;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'app.Hostels',
        'app.Hostelrooms',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Hostels') ? [] : ['className' => HostelsTable::class];
        $this->Hostels = TableRegistry::getTableLocator()->get('Hostels', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Hostels);

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
