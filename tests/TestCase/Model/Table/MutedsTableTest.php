<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\MutedsTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\MutedsTable Test Case
 */
class MutedsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\MutedsTable
     */
    protected $Muteds;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'app.Muteds',
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
        $config = $this->getTableLocator()->exists('Muteds') ? [] : ['className' => MutedsTable::class];
        $this->Muteds = $this->getTableLocator()->get('Muteds', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Muteds);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\MutedsTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @uses \App\Model\Table\MutedsTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
