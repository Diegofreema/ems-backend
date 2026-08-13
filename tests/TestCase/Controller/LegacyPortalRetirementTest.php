<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

final class LegacyPortalRetirementTest extends TestCase
{
    use IntegrationTestTrait;

    public function testLegacyPortalRoutesAreNotRegistered(): void
    {
        $this->get('/users/login');
        $this->assertResponseCode(404);

        $this->get('/pages/home');
        $this->assertResponseCode(404);

        $this->options('/api/ems/auth/sign-in');
        $this->assertResponseCode(204);
    }
}
