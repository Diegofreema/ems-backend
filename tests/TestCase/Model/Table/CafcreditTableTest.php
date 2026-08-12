<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CafcreditTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CafcreditTable Test Case
 */
class CafcreditTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\CafcreditTable
     */
    protected $Cafcredit;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected array $fixtures = [
        'app.Cafcredit',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Cafcredit') ? [] : ['className' => CafcreditTable::class];
        $this->Cafcredit = $this->getTableLocator()->get('Cafcredit', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Cafcredit);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\CafcreditTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
