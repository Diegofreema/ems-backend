<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ProgrametypesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ProgrametypesTable Test Case
 */
class ProgrametypesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ProgrametypesTable
     */
    protected $Programetypes;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'app.Programetypes',
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
        $config = TableRegistry::getTableLocator()->exists('Programetypes') ? [] : ['className' => ProgrametypesTable::class];
        $this->Programetypes = TableRegistry::getTableLocator()->get('Programetypes', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Programetypes);

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
