<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use App\Controller\SponsorshipsController;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\SponsorshipsController Test Case
 *
 * @uses \App\Controller\SponsorshipsController
 */
class SponsorshipsControllerTest extends TestCase
{
    use IntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected array $fixtures = [
        'app.Sponsorships',
        'app.Sponsors',
        'app.Sessions',
        'app.Students',
        'app.Admins',
        'app.Sponsorshippayments',
        'app.Fees',
        'app.SponsorshipsStudents',
        'app.FeesSponsorships',
    ];

    /**
     * Test index method
     *
     * @return void
     * @uses \App\Controller\SponsorshipsController::index()
     */
    public function testIndex(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test view method
     *
     * @return void
     * @uses \App\Controller\SponsorshipsController::view()
     */
    public function testView(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test add method
     *
     * @return void
     * @uses \App\Controller\SponsorshipsController::add()
     */
    public function testAdd(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test edit method
     *
     * @return void
     * @uses \App\Controller\SponsorshipsController::edit()
     */
    public function testEdit(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test delete method
     *
     * @return void
     * @uses \App\Controller\SponsorshipsController::delete()
     */
    public function testDelete(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
