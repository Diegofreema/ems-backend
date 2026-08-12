<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\LiveclassesTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\LiveclassesTable Test Case
 */
class LiveclassesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\LiveclassesTable
     */
    protected $Liveclasses;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected array $fixtures = [
        'app.Liveclasses',
        'app.Teachers',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Liveclasses') ? [] : ['className' => LiveclassesTable::class];
        $this->Liveclasses = $this->getTableLocator()->get('Liveclasses', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Liveclasses);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\LiveclassesTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @uses \App\Model\Table\LiveclassesTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
