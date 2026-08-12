<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\FeesLevelsTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\FeesLevelsTable Test Case
 */
class FeesLevelsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\FeesLevelsTable
     */
    protected $FeesLevels;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected array $fixtures = [
        'app.FeesLevels',
        'app.Fees',
        'app.Levels',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('FeesLevels') ? [] : ['className' => FeesLevelsTable::class];
        $this->FeesLevels = $this->getTableLocator()->get('FeesLevels', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->FeesLevels);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\FeesLevelsTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @uses \App\Model\Table\FeesLevelsTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
