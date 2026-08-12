<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\SponsorsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\SponsorsTable Test Case
 */
class SponsorsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\SponsorsTable
     */
    protected $Sponsors;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'app.Sponsors',
        'app.Admins',
        'app.Sponsorships',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Sponsors') ? [] : ['className' => SponsorsTable::class];
        $this->Sponsors = TableRegistry::getTableLocator()->get('Sponsors', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Sponsors);

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
