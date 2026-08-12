<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use App\Controller\SubjectsController;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\SubjectsController Test Case
 *
 * @uses \App\Controller\SubjectsController
 */
class SubjectsControllerTest extends TestCase
{
    use IntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'app.Subjects',
        'app.Departments',
        'app.Users',
        'app.Semesters',
        'app.Levels',
        'app.Coursematerials',
        'app.Results',
        'app.Topics',
        'app.Courseassignments',
        'app.Courseregistrations',
        'app.Students',
        'app.Teachers',
        'app.DepartmentsSubjects',
        'app.SemestersSubjects',
        'app.LevelsSubjects',
        'app.CourseassignmentsSubjects',
        'app.CourseregistrationsSubjects',
        'app.SubjectsStudents',
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
