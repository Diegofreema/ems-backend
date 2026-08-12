<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\SponsorshipsStudentsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\SponsorshipsStudentsTable Test Case
 */
class SponsorshipsStudentsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\SponsorshipsStudentsTable
     */
    protected $SponsorshipsStudents;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'app.SponsorshipsStudents',
        'app.Students',
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
        $config = TableRegistry::getTableLocator()->exists('SponsorshipsStudents') ? [] : ['className' => SponsorshipsStudentsTable::class];
        $this->SponsorshipsStudents = TableRegistry::getTableLocator()->get('SponsorshipsStudents', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->SponsorshipsStudents);

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
