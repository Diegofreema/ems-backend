<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\AdmisionconditionsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\AdmisionconditionsTable Test Case
 */
class AdmisionconditionsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\AdmisionconditionsTable
     */
    protected $Admisionconditions;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'app.Admisionconditions',
        'app.Users',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Admisionconditions') ? [] : ['className' => AdmisionconditionsTable::class];
        $this->Admisionconditions = TableRegistry::getTableLocator()->get('Admisionconditions', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Admisionconditions);

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
