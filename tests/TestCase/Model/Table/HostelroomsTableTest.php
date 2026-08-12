<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\HostelroomsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\HostelroomsTable Test Case
 */
class HostelroomsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\HostelroomsTable
     */
    protected $Hostelrooms;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'app.Hostelrooms',
        'app.Hostels',
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
        $config = TableRegistry::getTableLocator()->exists('Hostelrooms') ? [] : ['className' => HostelroomsTable::class];
        $this->Hostelrooms = TableRegistry::getTableLocator()->get('Hostelrooms', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Hostelrooms);

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
