<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\SemestersSubjectsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\SemestersSubjectsTable Test Case
 */
class SemestersSubjectsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\SemestersSubjectsTable
     */
    protected $SemestersSubjects;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'app.SemestersSubjects',
        'app.Semesters',
        'app.Subjects',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('SemestersSubjects') ? [] : ['className' => SemestersSubjectsTable::class];
        $this->SemestersSubjects = TableRegistry::getTableLocator()->get('SemestersSubjects', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->SemestersSubjects);

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
