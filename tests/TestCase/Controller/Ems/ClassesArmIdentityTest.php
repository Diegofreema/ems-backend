<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller\Ems;

use Cake\Utility\Text;

/**
 * Classes are a level plus an arm, identified by id — not by name (§3.12).
 *
 * A level may hold a second arm with the same name ("JSS 1A" twice), so every
 * student-side read (roster, count, placement, delete guard) must key on the
 * class id, never the shared name. These guard that identity end to end.
 *
 * The integration harness resets the request between sends, so every request
 * re-authenticates (authAsAdmin() before each get/post/delete).
 */
class ClassesArmIdentityTest extends EmsIntegrationTestCase
{
    protected const CLEANUP_TABLES = [
        'ems_audit_events',
        'ems_enrolments',
        'ems_students',
        'ems_class_groups',
        'ems_users',
        'ems_schools',
    ];

    public function testCreateComposesNameFromLevelAndArm(): void
    {
        $this->authAsAdmin();
        $this->post($this->schoolPath('/classes'), ['level' => 'JSS 1', 'stream' => 'A']);
        $this->assertResponseCode(201);
        $body = $this->responseJson();
        $this->assertSame('JSS 1A', $body['name']);
        $this->assertSame('JSS 1', $body['level']);
        $this->assertSame('A', $body['stream']);
    }

    public function testLevelIsRequired(): void
    {
        $this->authAsAdmin();
        $this->post($this->schoolPath('/classes'), ['stream' => 'A']);
        $this->assertResponseCode(422);
    }

    public function testArmRequiredWhenNoExplicitName(): void
    {
        $this->authAsAdmin();
        $this->post($this->schoolPath('/classes'), ['level' => 'JSS 1']);
        $this->assertResponseCode(422);
    }

    public function testASecondArmWithTheSameNameIsAllowed(): void
    {
        $first = $this->createClass('JSS 1', 'A');
        $this->authAsAdmin();
        $this->post($this->schoolPath('/classes'), ['level' => 'JSS 1', 'stream' => 'A']);
        $this->assertResponseCode(201);
        $second = $this->responseJson();
        $this->assertSame('JSS 1A', $second['name']);
        $this->assertNotSame($first, $second['id'], 'The duplicate arm must be its own class.');
        $this->assertSame(2, $this->rowCount('ems_class_groups', ['school_id' => $this->schoolId, 'name' => 'JSS 1A']));
    }

    public function testRosterAndCountAreScopedByClassId(): void
    {
        $arm1 = $this->createClass('JSS 1', 'A');
        $arm2 = $this->createClass('JSS 1', 'A');
        $inArm1 = $this->seedStudent('JSS 1A', $arm1, 'Ada', 'Aigbe');
        $inArm2 = $this->seedStudent('JSS 1A', $arm2, 'Bola', 'Bello');

        $this->authAsAdmin();
        $this->get($this->schoolPath('/classes/' . $arm1));
        $this->assertResponseOk();
        $this->assertSame(1, $this->responseJson()['studentCount']);

        $this->authAsAdmin();
        $this->get($this->schoolPath('/classes/' . $arm1 . '/roster'));
        $this->assertResponseOk();
        $rosterIds = array_map(static fn(array $s): string => (string)$s['id'], $this->responseJson());
        $this->assertSame([$inArm1], $rosterIds, 'Arm 1 roster must not include arm 2 students.');
        $this->assertNotContains($inArm2, $rosterIds);
    }

    public function testAssignByClassIdMovesTheStudent(): void
    {
        $target = $this->createClass('JSS 2', 'B');
        $student = $this->seedStudent('', null, 'Chidi', 'Chukwu');

        $this->authAsAdmin();
        $this->post($this->schoolPath('/students/' . $student . '/class'), ['classGroupId' => $target]);
        $this->assertResponseOk();
        $body = $this->responseJson();
        $this->assertSame($target, $body['classId']);
        $this->assertSame('JSS 2B', $body['classGroup']);
    }

    public function testAssignUnknownClassIdIsRejected(): void
    {
        $student = $this->seedStudent('', null, 'Chidi', 'Chukwu');
        $this->authAsAdmin();
        $this->post($this->schoolPath('/students/' . $student . '/class'), ['classGroupId' => Text::uuid()]);
        $this->assertResponseCode(422);
    }

    public function testDeleteRefusedWhileAStudentIsLinkedById(): void
    {
        $class = $this->createClass('JSS 3', 'C');
        $this->seedStudent('JSS 3C', $class, 'Dami', 'Dada');

        $this->authAsAdmin();
        $this->delete($this->schoolPath('/classes/' . $class));
        $this->assertResponseCode(409);
    }

    public function testEmptyClassDeletes(): void
    {
        $class = $this->createClass('JSS 3', 'D');
        $this->authAsAdmin();
        $this->delete($this->schoolPath('/classes/' . $class));
        $this->assertResponseCode(204);
    }

    public function testOptionsListEveryClassWithItsId(): void
    {
        $a = $this->createClass('JSS 1', 'A');
        $b = $this->createClass('JSS 1', 'B');

        $this->authAsAdmin();
        $this->get($this->schoolPath('/classes/options'));
        $this->assertResponseOk();
        $rows = $this->responseJson();
        $ids = array_map(static fn(array $c): string => (string)$c['id'], $rows);
        $this->assertContains($a, $ids);
        $this->assertContains($b, $ids);
        $this->assertArrayHasKey('level', $rows[0]);
    }

    private function createClass(string $level, string $stream): string
    {
        $this->authAsAdmin();
        $this->post($this->schoolPath('/classes'), ['level' => $level, 'stream' => $stream]);
        $this->assertResponseCode(201);

        return (string)$this->responseJson()['id'];
    }

    private function seedStudent(string $className, ?string $classId, string $first, string $last): string
    {
        $id = Text::uuid();
        $this->insertRow('ems_students', [
            'id' => $id,
            'school_id' => $this->schoolId,
            'admission_number' => 'ADM/' . substr($id, 0, 6),
            'first_name' => $first,
            'last_name' => $last,
            'date_of_birth' => '2013-05-06',
            'gender' => 'female',
            'class_group' => $className,
            'class_group_id' => $classId,
            'status' => 'enrolled',
            'enrolled_on' => '2026-09-01',
        ]);

        return $id;
    }
}
