<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\BorrowedbooksTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\BorrowedbooksTable Test Case
 */
class BorrowedbooksTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\BorrowedbooksTable
     */
    protected $Borrowedbooks;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'app.Borrowedbooks',
        'app.Students',
        'app.Books',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Borrowedbooks') ? [] : ['className' => BorrowedbooksTable::class];
        $this->Borrowedbooks = TableRegistry::getTableLocator()->get('Borrowedbooks', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Borrowedbooks);

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
