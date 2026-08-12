<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\PostcategoriesTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\PostcategoriesTable Test Case
 */
class PostcategoriesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\PostcategoriesTable
     */
    protected $Postcategories;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'app.Postcategories',
        'app.Posts',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Postcategories') ? [] : ['className' => PostcategoriesTable::class];
        $this->Postcategories = $this->getTableLocator()->get('Postcategories', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Postcategories);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\PostcategoriesTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
