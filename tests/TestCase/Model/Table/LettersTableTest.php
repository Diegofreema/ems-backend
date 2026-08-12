<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\LettersTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\LettersTable Test Case
 */
class LettersTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\LettersTable
     */
    protected $Letters;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'app.Letters',
        'app.Modes',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Letters') ? [] : ['className' => LettersTable::class];
        $this->Letters = $this->getTableLocator()->get('Letters', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Letters);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\LettersTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @uses \App\Model\Table\LettersTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
