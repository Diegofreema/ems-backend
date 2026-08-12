<?php
declare(strict_types=1);

namespace App\Controller\Ems;

use App\Ems\Invitations;
use App\Ems\Messages;
use App\Ems\Serializer\SettingsSerializer;
use Cake\Datasource\EntityInterface;
use Cake\Http\Response;
use Cake\I18n\FrozenDate;
use Throwable;

/**
 * School accounts (document.md §3.14). The PersonLink stored on an account
 * is what drives all parent/teacher/student scoping (§1.4).
 */
class UsersController extends AppController
{
    private const ROLES = ['administrator', 'registrar', 'bursar', 'teacher', 'parent', 'student'];

    /**
     * GET /users — Paginated<SchoolUser>, active → invited → disabled, then name.
     */
    public function index(): Response
    {
        $params = $this->pageParams();

        $query = $this->tenant()->query('EmsUsers');
        $total = $query->count();

        $rows = $query
            ->orderByAsc($query->newExpr("FIELD(status, 'active', 'invited', 'disabled')"))
            ->orderByAsc('name')
            ->limit($params['pageSize'])
            ->offset(($params['page'] - 1) * $params['pageSize'])
            ->all();

        return $this->paginated(
            array_map([SettingsSerializer::class, 'user'], $rows->toList()),
            $total,
            $params['page'],
            $params['pageSize']
        );
    }

    /**
     * POST /users/invite — InviteUserInput { name, email, role, link? }.
     */
    public function invite(): Response
    {
        $body = $this->body();
        $name = trim((string)($body['name'] ?? ''));
        $email = strtolower(trim((string)($body['email'] ?? '')));
        $role = (string)($body['role'] ?? '');
        $users = $this->fetchTable('EmsUsers');

        if ($name === '') {
            $this->fail(422, Messages::ACCOUNT_NAME_REQUIRED);
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->fail(422, Messages::EMAIL_INVALID);
        }
        if ($users->exists(['LOWER(email)' => $email])) {
            $this->fail(422, Messages::EMAIL_EXISTS);
        }
        if (!in_array($role, self::ROLES, true)) {
            $this->fail(422, Messages::USER_ROLE_INVALID);
        }

        $issued = Invitations::issue($users);

        $data = [
            'school_id' => $this->viewer->schoolId,
            'name' => $name,
            'email' => $email,
            'role' => $role,
            'status' => 'invited',
            'added_on' => FrozenDate::today(),
            'invite_code' => $issued['hash'],
            'invite_expires_at' => $issued['expiresAt'],
        ] + $this->validatedLinkColumns($role, $body['link'] ?? null);

        $user = $users->saveOrFail($users->newEntity($data));

        try {
            $school = $this->fetchTable('EmsSchools')->get($this->viewer->schoolId);
            Invitations::deliver($user, $school, $issued['raw']);
        } catch (Throwable $e) {
            $users->delete($user);
            $this->fail(503, Messages::INVITE_DELIVERY_FAILED);
        }

        return $this->json(SettingsSerializer::user($user), 201);
    }

    /**
     * PUT /users/{id}/role — administrators may change other staff roles only.
     */
    public function updateRole(string $id): Response
    {
        $users = $this->fetchTable('EmsUsers');
        $user = $this->findUser($id);
        $role = (string)($this->body()['role'] ?? '');
        if ($user->id === $this->viewer->userId) {
            $this->fail(403, Messages::SELF_ROLE_CHANGE_FORBIDDEN);
        }
        $staffRoles = ['administrator', 'registrar', 'bursar', 'teacher'];
        if (!in_array($role, $staffRoles, true) || in_array((string)$user->role, ['parent', 'student'], true)) {
            $this->fail(422, Messages::USER_ROLE_INVALID);
        }

        if (
            $user->role === 'administrator' && $role !== 'administrator'
            && $user->status === 'active' && $this->isLastActiveAdmin($user)
        ) {
            $this->fail(422, Messages::LAST_ADMIN);
        }

        $user = $users->patchEntity($user, ['role' => $role] + [
            'link_kind' => $role === 'teacher' ? $user->link_kind : null,
            'link_teacher_id' => $role === 'teacher' ? $user->link_teacher_id : null,
            'link_student_id' => null,
            'link_student_ids' => null,
        ], ['validate' => false]);
        $users->saveOrFail($user);

        return $this->json(SettingsSerializer::user($user));
    }

    /** PUT /users/{id}/link — replace or clear the account's person link. */
    public function updateLink(string $id): Response
    {
        $users = $this->fetchTable('EmsUsers');
        $user = $this->findUser($id);
        $columns = $this->validatedLinkColumns((string)$user->role, $this->body()['link'] ?? null, $id, false);
        $user = $users->patchEntity($user, $columns, ['validate' => false]);
        $users->saveOrFail($user);

        return $this->json(SettingsSerializer::user($user));
    }

    /**
     * PUT /users/{id}/status — { status: 'active' | 'disabled' }, same guard
     * when disabling the last active administrator.
     */
    public function updateStatus(string $id): Response
    {
        $users = $this->fetchTable('EmsUsers');
        $user = $this->findUser($id);
        $status = (string)($this->body()['status'] ?? '');
        if (!in_array($status, ['active', 'disabled'], true)) {
            $this->fail(422, Messages::ACTION_FORBIDDEN);
        }

        if (
            $status === 'disabled' && $user->role === 'administrator'
            && $user->status === 'active' && $this->isLastActiveAdmin($user)
        ) {
            $this->fail(422, Messages::LAST_ADMIN);
        }

        $user->status = $status;
        $users->saveOrFail($user);

        return $this->json(SettingsSerializer::user($user));
    }

    /**
     * DELETE /users/{id}/invite — only a pending invitation can be revoked;
     * revoking removes the row.
     */
    public function revokeInvite(string $id): Response
    {
        $users = $this->fetchTable('EmsUsers');
        $user = $this->findUser($id);
        if ($user->status !== 'invited') {
            $this->fail(422, Messages::INVITE_ONLY_PENDING);
        }
        $users->deleteOrFail($user);

        return $this->json(null, 204);
    }

    /** Find one user inside the active tenant. */
    private function findUser(string $id): EntityInterface
    {
        return $this->findOr404('EmsUsers', $id, Messages::USER_NOT_FOUND);
    }

    /**
     * True when no OTHER active administrator exists at this school.
     */
    private function isLastActiveAdmin(EntityInterface $user): bool
    {
        $others = $this->tenant()->query('EmsUsers')
            ->where([
                'role' => 'administrator',
                'status' => 'active',
                'id !=' => $user->id,
            ])
            ->count();

        return $others === 0;
    }

    /**
     * Serialize a PersonLink union into its storage columns.
     *
     * @param string $role Account role being linked.
     * @param mixed $link PersonLink request value.
     * @param string|null $userId Account excluded from uniqueness checks.
     * @param bool $required Whether this action requires a link.
     */
    private function validatedLinkColumns(string $role, $link, ?string $userId = null, bool $required = true): array
    {
        $columns = [
            'link_kind' => null,
            'link_teacher_id' => null,
            'link_student_id' => null,
            'link_student_ids' => null,
        ];
        if (in_array($role, ['administrator', 'registrar', 'bursar'], true)) {
            return $columns;
        }
        if ($role === 'teacher' && $link === null) {
            return $columns;
        }
        if (in_array($role, ['parent', 'student'], true) && $link === null && !$required) {
            return $columns;
        }
        if (!is_array($link) || ($link['kind'] ?? null) !== $role) {
            $this->fail(422, $link === null ? Messages::USER_LINK_REQUIRED : Messages::USER_LINK_INVALID);
        }

        if ($role === 'teacher') {
            $teacherId = trim((string)($link['teacherId'] ?? ''));
            $teacherExists = $this->tenant()->query('EmsTeachers')
                ->where(['id' => $teacherId, 'status' => 'active'])
                ->count();
            if ($teacherId === '' || !$teacherExists) {
                $this->fail(422, Messages::USER_LINK_INVALID);
            }
            $columns['link_kind'] = 'teacher';
            $columns['link_teacher_id'] = $teacherId;

            return $columns;
        }

        $ids = $role === 'student' ? [(string)($link['studentId'] ?? '')] : ($link['studentIds'] ?? []);
        $ids = is_array($ids) ? array_values(array_unique(array_filter(array_map('strval', $ids)))) : [];
        if ($ids === []) {
            if (!$required && $role === 'parent') {
                $columns['link_kind'] = 'parent';
                $columns['link_student_ids'] = [];

                return $columns;
            }
            $this->fail(422, Messages::USER_LINK_REQUIRED);
        }
        $found = $this->tenant()->query('EmsStudents')->where(['id IN' => $ids, 'status' => 'enrolled'])->count();
        if ($found !== count($ids)) {
            $this->fail(422, Messages::USER_LINK_TARGET_INVALID);
        }

        if ($role === 'student') {
            $existing = $this->tenant()->query('EmsUsers')->where([
                'role' => 'student',
                'link_student_id' => $ids[0],
                'status IN' => ['active', 'invited'],
            ]);
            if ($userId !== null) {
                $existing->where(['id !=' => $userId]);
            }
            if ($existing->count() > 0) {
                $this->fail(422, Messages::STUDENT_ACCOUNT_EXISTS);
            }
            $columns['link_kind'] = 'student';
            $columns['link_student_id'] = $ids[0];
        } else {
            $columns['link_kind'] = 'parent';
            $columns['link_student_ids'] = $ids;
        }

        return $columns;
    }
}
