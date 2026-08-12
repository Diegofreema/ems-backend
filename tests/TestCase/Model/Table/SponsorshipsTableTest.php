<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\SponsorshipsTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\SponsorshipsTable Test Case
 */
class SponsorshipsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\SponsorshipsTable
     */
    protected $Sponsorships;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected array $fixtures = [
        'app.Sponsorships',
        'app.Sponsors',
        'app.Sessions',
        'app.Students',
        'app.Admins',
        'app.Sponsorshippayments',
        'app.Fees',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Sponsorships') ? [] : ['className' => SponsorshipsTable::class];
        $this->Sponsorships = $this->getTableLocator()->get('Sponsorships', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Sponsorships);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\SponsorshipsTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @uses \App\Model\Table\SponsorshipsTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
