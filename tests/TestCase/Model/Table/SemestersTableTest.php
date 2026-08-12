<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\SemestersTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\SemestersTable Test Case
 */
class SemestersTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\SemestersTable
     */
    protected $Semesters;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'app.Semesters',
        'app.Courseassignments',
        'app.Courseregistrations',
        'app.Results',
        'app.Settings',
        'app.Subjects',
        'app.Departments',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Semesters') ? [] : ['className' => SemestersTable::class];
        $this->Semesters = TableRegistry::getTableLocator()->get('Semesters', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Semesters);

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
}
