<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use App\Controller\TeachersController;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\TeachersController Test Case
 *
 * @uses \App\Controller\TeachersController
 */
class TeachersControllerTest extends TestCase
{
    use IntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'app.Teachers',
        'app.Users',
        'app.Countries',
        'app.States',
        'app.Departments',
        'app.Staffgrades',
        'app.Staffdepartments',
        'app.Coursematerials',
        'app.Payslips',
        'app.Staffmessages',
        'app.Subjects',
        'app.SubjectsTeachers',
    ];

    /**
     * Test index method
     *
     * @return void
     */
    public function testIndex(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test view method
     *
     * @return void
     */
    public function testView(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test add method
     *
     * @return void
     */
    public function testAdd(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test edit method
     *
     * @return void
     */
    public function testEdit(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test delete method
     *
     * @return void
     */
    public function testDelete(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
