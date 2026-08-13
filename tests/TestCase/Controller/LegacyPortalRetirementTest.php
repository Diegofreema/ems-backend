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

        // The retired v1 API used generic CRUD routes that exposed student
        // records and permitted role changes. These paths must remain absent,
        // not merely protected by authentication.
        $this->get('/api/v1/students/1');
        $this->assertResponseCode(404);

        $this->patch('/api/v1/users/1', ['role_id' => 5]);
        $this->assertResponseCode(404);

        $this->options('/api/ems/auth/sign-in');
        $this->assertResponseCode(204);
    }
}
