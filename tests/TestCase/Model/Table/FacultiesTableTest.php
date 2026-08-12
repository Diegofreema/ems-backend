<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\FacultiesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\FacultiesTable Test Case
 */
class FacultiesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\FacultiesTable
     */
    protected $Faculties;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'app.Faculties',
        'app.Departments',
        'app.Results',
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
        $config = TableRegistry::getTableLocator()->exists('Faculties') ? [] : ['className' => FacultiesTable::class];
        $this->Faculties = TableRegistry::getTableLocator()->get('Faculties', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Faculties);

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
