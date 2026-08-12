<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\FeesSponsorshipsTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\FeesSponsorshipsTable Test Case
 */
class FeesSponsorshipsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\FeesSponsorshipsTable
     */
    protected $FeesSponsorships;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected array $fixtures = [
        'app.FeesSponsorships',
        'app.Fees',
        'app.Sponsorships',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('FeesSponsorships') ? [] : ['className' => FeesSponsorshipsTable::class];
        $this->FeesSponsorships = $this->getTableLocator()->get('FeesSponsorships', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->FeesSponsorships);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\FeesSponsorshipsTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @uses \App\Model\Table\FeesSponsorshipsTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
