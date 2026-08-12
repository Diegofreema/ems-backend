<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use App\Controller\FeesController;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\FeesController Test Case
 *
 * @uses \App\Controller\FeesController
 */
class FeesControllerTest extends TestCase
{
    use IntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected array $fixtures = [
        'app.Fees',
        'app.Users',
        'app.Feeallocations',
        'app.Invoices',
        'app.Transactions',
        'app.Trequests',
        'app.Departments',
        'app.Levels',
        'app.Students',
        'app.DepartmentsFees',
        'app.FeesLevels',
        'app.FeesStudents',
    ];

    /**
     * Test index method
     *
     * @return void
     * @uses \App\Controller\FeesController::index()
     */
    public function testIndex(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test view method
     *
     * @return void
     * @uses \App\Controller\FeesController::view()
     */
    public function testView(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test add method
     *
     * @return void
     * @uses \App\Controller\FeesController::add()
     */
    public function testAdd(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test edit method
     *
     * @return void
     * @uses \App\Controller\FeesController::edit()
     */
    public function testEdit(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test delete method
     *
     * @return void
     * @uses \App\Controller\FeesController::delete()
     */
    public function testDelete(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
