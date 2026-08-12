<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use App\Controller\StudentsController;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\StudentsController Test Case
 *
 * @uses \App\Controller\StudentsController
 */
class StudentsControllerTest extends TestCase
{
    use IntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'app.Students',
        'app.Departments',
        'app.States',
        'app.Countries',
        'app.Lgas',
        'app.Users',
        'app.Levels',
        'app.Faculties',
        'app.Programes',
        'app.Borrowedbooks',
        'app.Courseregistrations',
        'app.Invoices',
        'app.Results',
        'app.Studentmessages',
        'app.Transactions',
        'app.Trequests',
        'app.Fees',
        'app.Hostelrooms',
        'app.Sparents',
        'app.Subjects',
        'app.FeesStudents',
        'app.HostelroomsStudents',
        'app.SparentsStudents',
        'app.SubjectsStudents',
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
