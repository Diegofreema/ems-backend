<?php
declare(strict_types=1);

namespace App\Ems;

use Cake\I18n\FrozenDate;
use Cake\ORM\Locator\LocatorInterface;
use Throwable;

/**
 * Mints parent portal accounts from the guardian records the school already
 * keeps (§3.19). One prospective account per family identity — primary
 * guardians sharing an e-mail (or, failing that, a phone number) collapse
 * into a single account whose wards are the union of their students — so a
 * parent of three children is invited once, not three times.
 *
 * The account carries `link_guardian_id` (provenance) alongside the
 * operative `link_student_ids`, plus `phone_key` so a no-email family can
 * sign in with the phone number printed on their code sheet.
 */
final class FamilyInvites
{
    /** Invitations processed per request — the frontend loops chunks. */
    public const BATCH_LIMIT = 25;

    private LocatorInterface $locator;
    private string $schoolId;
    private ?Tenant $tenantScope = null;

    public function __construct(LocatorInterface $locator, string $schoolId)
    {
        $this->locator = $locator;
        $this->schoolId = $schoolId;
    }

    /**
     * This engine's tenant-scope choke point — reads narrowed to $this->schoolId
     * by construction. See App\Ems\Tenant.
     */
    private function tenant(): Tenant
    {
        return $this->tenantScope ??= new Tenant($this->locator, $this->schoolId);
    }

    /**
     * Every family the school could invite, grouped by identity, with the
     * account state each is in. Lean and unpaginated, like /classes/options —
     * the frontend drives chunked sends off this list.
     *
     * @return array{counts: array<string,int>, targets: array<int,array<string,mixed>>}
     */
    public function plan(?string $classGroupId = null): array
    {
        $targets = array_map(
            function (array $t): array {
                unset($t['phoneKey'], $t['studentIds'], $t['wardLine']);

                return $t;
            },
            $this->uniqueTargets($this->targetsByGuardianId($classGroupId)),
        );
        $counts = [
            'invitable' => 0,
            'codeOnly' => 0,
            'unreachable' => 0,
            'invited' => 0,
            'active' => 0,
            'disabled' => 0,
        ];
        foreach ($targets as $t) {
            $counts[$t['status']] = ($counts[$t['status']] ?? 0) + 1;
        }

        return ['counts' => $counts, 'targets' => $targets];
    }

    /**
     * Create-and-deliver for up to BATCH_LIMIT plan targets. Partial success
     * by design: each guardian id yields its own outcome, and a delivery
     * failure keeps the account (resend is the retry path, unlike the single
     * admin invite which rolls back).
     *
     * The raw code is returned ONLY when there is no mailbox to send it to
     * (or the send failed) — that is what the printed code sheet is built
     * from. Codes are stored hashed, as always.
     *
     * @param array<int,string> $guardianIds
     * @return array<int, array{guardianId:string, userId?:string, status:string, code?:string}>
     */
    public function create(array $guardianIds): array
    {
        $planned = $this->targetsByGuardianId(null);
        $users = $this->locator->get('EmsUsers');
        $school = $this->locator->get('EmsSchools')->get($this->schoolId);

        $results = [];
        foreach ($guardianIds as $guardianId) {
            $guardianId = (string)$guardianId;
            $target = $planned[$guardianId] ?? null;
            if ($target === null) {
                $results[] = ['guardianId' => $guardianId, 'status' => 'unknown'];
                continue;
            }
            if (in_array($target['status'], ['invited', 'active', 'disabled'], true)) {
                $results[] = ['guardianId' => $guardianId, 'status' => 'exists'];
                continue;
            }
            if ($target['status'] === 'unreachable') {
                $results[] = ['guardianId' => $guardianId, 'status' => 'unreachable'];
                continue;
            }

            $email = $target['email'];
            $phoneKey = $target['phoneKey'];
            // Global uniqueness (email spans tenants; phone_key likewise).
            if ($email !== null && $users->exists(['LOWER(email)' => $email])) {
                $results[] = ['guardianId' => $guardianId, 'status' => 'exists'];
                continue;
            }
            if ($phoneKey !== null && $users->exists(['phone_key' => $phoneKey])) {
                if ($email === null) {
                    $results[] = ['guardianId' => $guardianId, 'status' => 'exists'];
                    continue;
                }
                $phoneKey = null;
            }

            $issued = Invitations::issue($users);
            $user = $users->newEntity([
                'school_id' => $this->schoolId,
                'name' => $target['guardianName'],
                'email' => $email,
                'phone' => $target['phone'],
                'phone_key' => $phoneKey,
                'role' => 'parent',
                'status' => 'invited',
                'added_on' => FrozenDate::today(),
                'invite_code' => $issued['hash'],
                'invite_expires_at' => $issued['expiresAt'],
                'link_kind' => 'parent',
                'link_student_ids' => $target['studentIds'],
                'link_guardian_id' => $guardianId,
            ], ['validate' => false]);
            $users->saveOrFail($user);

            $result = ['guardianId' => $guardianId, 'userId' => (string)$user->id];
            if ($email === null) {
                $result += ['status' => 'code', 'code' => $issued['raw']];
            } else {
                try {
                    Invitations::deliver($user, $school, $issued['raw'], $target['wardLine']);
                    $result += ['status' => 'sent'];
                } catch (Throwable) {
                    $result += ['status' => 'failed', 'code' => $issued['raw']];
                }
            }
            $results[] = $result;
        }

        return $results;
    }

    /**
     * One row per family group (the map carries a group once per member
     * guardian id; the plan should list it once).
     *
     * @param array<string, array<string,mixed>> $byGuardianId
     * @return array<int, array<string,mixed>>
     */
    private function uniqueTargets(array $byGuardianId): array
    {
        $seen = [];
        $unique = [];
        foreach ($byGuardianId as $target) {
            $lead = (string)$target['guardianId'];
            if (!isset($seen[$lead])) {
                $seen[$lead] = true;
                $unique[] = $target;
            }
        }

        return $unique;
    }

    /**
     * The grouped plan, keyed by every member guardian id so create() can
     * resolve any of a group's guardian rows to the same target.
     *
     * @return array<string, array<string,mixed>>
     */
    private function targetsByGuardianId(?string $classGroupId): array
    {
        $students = $this->enrolledStudents($classGroupId);
        if ($students === []) {
            return [];
        }

        $guardians = $this->tenant()->query('EmsGuardians')
            ->where(['student_id IN' => array_keys($students), 'is_primary' => true])
            ->orderByAsc('created')
            ->orderByAsc('id')
            ->all()
            ->toList();

        // One group per family identity: shared e-mail first, then shared
        // phone, else the guardian row stands alone.
        $groups = [];
        foreach ($guardians as $g) {
            $email = strtolower(trim((string)$g->email));
            $phoneKey = Dedup::phoneKey((string)$g->phone);
            if ($email !== '') {
                $key = 'e:' . $email;
            } elseif (strlen($phoneKey) >= 7) {
                $key = 'p:' . $phoneKey;
            } else {
                $key = 'g:' . (string)$g->id;
            }
            $groups[$key][] = $g;
        }

        $accounts = $this->accountIndex();
        $targets = [];
        foreach ($groups as $members) {
            $lead = $members[0];
            $email = strtolower(trim((string)$lead->email));
            $phone = trim((string)$lead->phone);
            $phoneKey = Dedup::phoneKey($phone);
            $usablePhone = strlen($phoneKey) >= 7;

            $linked = null;
            foreach ($members as $g) {
                $linked ??= $accounts['byGuardianId'][(string)$g->id] ?? null;
            }
            $linked ??= $email !== '' ? ($accounts['byEmail'][$email] ?? null) : null;
            $linked ??= $usablePhone ? ($accounts['byPhoneKey'][$phoneKey] ?? null) : null;

            if ($linked !== null) {
                $status = (string)$linked->status;
            } elseif ($email !== '') {
                $status = 'invitable';
            } elseif ($usablePhone) {
                $status = 'codeOnly';
            } else {
                $status = 'unreachable';
            }

            $wards = [];
            $studentIds = [];
            foreach ($members as $g) {
                $sid = (string)$g->student_id;
                if (isset($students[$sid]) && !in_array($sid, $studentIds, true)) {
                    $studentIds[] = $sid;
                    $wards[] = $students[$sid];
                }
            }

            $target = [
                'guardianId' => (string)$lead->id,
                'guardianName' => trim((string)$lead->first_name . ' ' . (string)$lead->last_name),
                'relationship' => (string)$lead->relationship,
                'email' => $email === '' ? null : $email,
                'phone' => $phone === '' ? null : $phone,
                'phoneKey' => $usablePhone ? $phoneKey : null,
                'students' => $wards,
                'studentIds' => $studentIds,
                'status' => $status,
                'wardLine' => implode(', ', array_map(
                    fn($w) => $w['name'] . ($w['classGroup'] !== '' ? ' (' . $w['classGroup'] . ')' : ''),
                    $wards,
                )),
            ];
            foreach ($members as $g) {
                $targets[(string)$g->id] = $target;
            }
        }

        return $targets;
    }

    /**
     * Enrolled students in scope, id-keyed for ward lookups.
     *
     * @return array<string, array{id:string, name:string, classGroup:string}>
     */
    private function enrolledStudents(?string $classGroupId): array
    {
        $query = $this->tenant()->query('EmsStudents')->where(['status' => 'enrolled']);
        if ($classGroupId !== null && $classGroupId !== '') {
            $class = $this->tenant()->query('EmsClassGroups')->where(['id' => $classGroupId])->first();
            $or = [['class_group_id' => $classGroupId]];
            if ($class !== null) {
                // Legacy rows may still be linked by name only.
                $or[] = ['class_group_id IS' => null, 'class_group' => (string)$class->name];
            }
            $query = $query->where(['OR' => $or]);
        }

        $students = [];
        foreach ($query->all() as $s) {
            $students[(string)$s->id] = [
                'id' => (string)$s->id,
                'name' => trim((string)$s->first_name . ' ' . (string)$s->last_name),
                'classGroup' => (string)$s->class_group,
            ];
        }

        return $students;
    }

    /**
     * Existing school accounts indexed three ways so a family is never
     * invited twice: by the guardian row an account came from, by e-mail
     * (any role — a staff member's address cannot take a second account),
     * and by normalized phone.
     *
     * @return array{byGuardianId: array<string,\Cake\Datasource\EntityInterface>, byEmail: array<string,\App\Ems\EntityInterface>, byPhoneKey: array<string,\App\Ems\EntityInterface>}
     */
    private function accountIndex(): array
    {
        $index = ['byGuardianId' => [], 'byEmail' => [], 'byPhoneKey' => []];
        foreach ($this->tenant()->query('EmsUsers')->all() as $u) {
            if ($u->link_guardian_id !== null) {
                $index['byGuardianId'][(string)$u->link_guardian_id] = $u;
            }
            if ($u->email !== null && (string)$u->email !== '') {
                $index['byEmail'][strtolower((string)$u->email)] = $u;
            }
            if ($u->phone_key !== null && (string)$u->phone_key !== '') {
                $index['byPhoneKey'][(string)$u->phone_key] = $u;
            }
        }

        return $index;
    }
}
