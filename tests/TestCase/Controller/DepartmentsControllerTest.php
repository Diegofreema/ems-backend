<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use App\Controller\DepartmentsController;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\DepartmentsController Test Case
 *
 * @uses \App\Controller\DepartmentsController
 */
class DepartmentsControllerTest extends TestCase
{
    use IntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'app.Departments',
        'app.Faculties',
        'app.Admins',
        'app.Courseassignments',
        'app.Coursematerials',
        'app.Dstudents',
        'app.Feeallocations',
        'app.Results',
        'app.Students',
        'app.Subjects',
        'app.Teachers',
        'app.Users',
        'app.Fees',
        'app.Levels',
        'app.Programes',
        'app.Programmes',
        'app.Semesters',
        'app.DepartmentsSubjects',
        'app.DepartmentsFees',
        'app.DepartmentsLevels',
        'app.DepartmentsProgrames',
        'app.DepartmentsProgrammes',
        'app.DepartmentsSemesters',
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
