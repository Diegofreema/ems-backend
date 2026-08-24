<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller\Ems;

use Cake\Utility\Text;

final class TeacherOffboardingTest extends EmsIntegrationTestCase
{
    protected const CLEANUP_TABLES = [
        'ems_teachers',
        'ems_refresh_tokens',
        'ems_password_resets',
        'ems_users',
        'ems_schools',
    ];

    public function testMarkingTeacherFormerDisablesTheirLinkedAccount(): void
    {
        [$teacherId, $userId] = $this->seedLinkedTeacher();

        $this->authAsAdmin();
        $this->post($this->schoolPath('/teachers/' . $teacherId . '/mark-former'));

        $this->assertResponseOk();
        $this->assertSame('former', $this->responseJson()['status']);
        $this->assertTrue($this->rowExists('ems_users', ['id' => $userId, 'status' => 'disabled']));
    }

    public function testDeletingTeacherDisablesTheirLinkedAccount(): void
    {
        [$teacherId, $userId] = $this->seedLinkedTeacher();

        $this->authAsAdmin();
        $this->delete($this->schoolPath('/teachers/' . $teacherId));

        $this->assertResponseCode(204);
        $this->assertFalse($this->rowExists('ems_teachers', ['id' => $teacherId]));
        $this->assertTrue($this->rowExists('ems_users', ['id' => $userId, 'status' => 'disabled']));
    }

    /** @return array{string,string} */
    private function seedLinkedTeacher(): array
    {
        $teacherId = Text::uuid();
        $userId = Text::uuid();
        $this->insertRow('ems_teachers', [
            'id' => $teacherId,
            'school_id' => $this->schoolId,
            'staff_number' => 'STF-9001',
            'first_name' => 'Tosin',
            'last_name' => 'Teacher',
            'email' => 'tosin.teacher@test.school',
            'phone' => '08030000000',
            'gender' => 'female',
            'subjects' => json_encode([]),
            'status' => 'active',
            'hired_on' => '2026-08-01',
        ]);
        $this->insertRow('ems_users', [
            'id' => $userId,
            'school_id' => $this->schoolId,
            'name' => 'Tosin Teacher',
            'email' => 'tosin.account@test.school',
            'role' => 'teacher',
            'status' => 'active',
            'added_on' => '2026-08-01',
            'link_kind' => 'teacher',
            'link_teacher_id' => $teacherId,
        ]);

        return [$teacherId, $userId];
    }
}
