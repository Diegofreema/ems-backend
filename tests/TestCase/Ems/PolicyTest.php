<?php
declare(strict_types=1);

namespace App\Test\TestCase\Ems;

use App\Ems\Policy;
use Cake\Routing\RouteBuilder;
use Cake\Routing\RouteCollection;
use Cake\TestSuite\TestCase;

/**
 * The test surface of the App\Ems\Policy deep module. Three guarantees, none
 * of which needs a database — the whole authorization posture is a table, so
 * the table is what we test:
 *
 *  1. Completeness — every routed authenticated action has a Policy entry, and
 *     Policy holds no entry that isn't routed. Fail-closed only protects you if
 *     the table is complete; this catches an action added to routes.php without
 *     a policy (which would otherwise be silently denied in production) and a
 *     stale key left behind after a route is removed.
 *  2. No family write — the only writes a `parent` or `student` may reach are
 *     their own contact preferences and the DocumentPolicy-governed Documents
 *     surface. Any other write open to a family role is the broken-access-
 *     control class this module exists to close.
 *  3. Headline cells — the specific escalations the security review found are
 *     pinned so a future table edit can't silently re-open them.
 */
class PolicyTest extends TestCase
{
    private const ROLES = ['administrator', 'registrar', 'bursar', 'teacher', 'parent', 'student'];

    /** Public actions authenticate later / not at all — never Policy's concern. */
    private const PUBLIC_CONTROLLERS = ['Auth', 'Public', 'Files'];

    private const WRITE_METHODS = ['POST', 'PUT', 'PATCH', 'DELETE'];

    /**
     * The only writes a family role (parent/student) may reach at the capability
     * layer: their own contact preferences, and the Documents surface — whose
     * own-record reach is enforced in-action by DocumentPolicy, so Policy stays
     * ALL there and lets DocPol be authoritative.
     */
    private const FAMILY_WRITE_ALLOWLIST = [
        'Communication.setPreference',
        'Documents.add',
        'Documents.verify',
        'Documents.link',
        'Documents.remove',
    ];

    /**
     * `Controller.action` => the HTTP verbs it answers, for every authenticated
     * EMS route — read straight from config/routes.php via an isolated
     * RouteCollection so no global Router state is touched.
     *
     * @return array<string, array<int, string>>
     */
    private function routedActions(): array
    {
        $collection = new RouteCollection();
        // phpcs:ignore SlevomatCodingStandard.Variables.UnusedVariable.UnusedVariable -- routes.php consumes this named local variable.
        $routes = new RouteBuilder($collection, '/');
        require CONFIG . 'routes.php';

        $actions = [];
        foreach ($collection->routes() as $route) {
            $d = $route->defaults;
            if (($d['prefix'] ?? null) !== 'Ems') {
                continue;
            }
            $controller = (string)($d['controller'] ?? '');
            $action = (string)($d['action'] ?? '');
            $method = $d['_method'] ?? '';
            if ($controller === '' || $method === 'OPTIONS') {
                continue; // the CORS catch-all carries no concrete controller
            }
            if (in_array($controller, self::PUBLIC_CONTROLLERS, true)) {
                continue;
            }
            $key = $controller . '.' . $action;
            foreach ((array)$method as $verb) {
                $actions[$key][] = strtoupper((string)$verb);
            }
            $actions[$key] = $actions[$key] ?? [];
        }

        return $actions;
    }

    public function testEveryRoutedActionHasAPolicyEntry(): void
    {
        $routed = array_keys($this->routedActions());
        $missing = array_values(array_diff($routed, Policy::keys()));

        $this->assertSame(
            [],
            $missing,
            'Routed actions with no Policy entry (fail-closed would deny these in prod): '
                . implode(', ', $missing),
        );
    }

    public function testPolicyHoldsNoStaleUnroutedEntry(): void
    {
        $routed = array_keys($this->routedActions());
        $extra = array_values(array_diff(Policy::keys(), $routed));

        $this->assertSame(
            [],
            $extra,
            'Policy entries with no matching route (stale after a route change): ' . implode(', ', $extra),
        );
    }

    public function testNoWriteLeaksToAFamilyRole(): void
    {
        foreach ($this->routedActions() as $key => $methods) {
            if (array_intersect($methods, self::WRITE_METHODS) === []) {
                continue; // a read; reach is Scope's job, not this invariant's
            }
            if (in_array($key, self::FAMILY_WRITE_ALLOWLIST, true)) {
                continue;
            }
            $roles = Policy::rolesFor(...explode('.', $key, 2)) ?? [];
            $this->assertNotContains('parent', $roles, "$key is a write reachable by a parent");
            $this->assertNotContains('student', $roles, "$key is a write reachable by a student");
        }
    }

    /**
     * The headline escalations from the security review — pinned so a future
     * table edit can't silently re-open them.
     */
    public function testHeadlineEscalationsAreClosed(): void
    {
        // A family role (or any non-administrator) can no longer invite users
        // or assign roles — the self-promotion-to-administrator path.
        foreach (['registrar', 'bursar', 'teacher', 'parent', 'student'] as $role) {
            $this->assertFalse(Policy::allows('Users', 'invite', $role), "invite open to $role");
            $this->assertFalse(Policy::allows('Users', 'updateRole', $role), "updateRole open to $role");
        }
        $this->assertTrue(Policy::allows('Users', 'invite', 'administrator'));

        // Releasing results, editing a student, and setting fees are staff-only.
        foreach (['parent', 'student'] as $role) {
            $this->assertFalse(Policy::allows('Exams', 'release', $role));
            $this->assertFalse(Policy::allows('Students', 'edit', $role));
            $this->assertFalse(Policy::allows('FeeStructures', 'add', $role));
        }
    }

    public function testLegitimateAccessStillFlows(): void
    {
        // Teachers still enter grades and scores for their classes (reach then
        // narrowed by Scope); families still reach their own portal surfaces.
        $this->assertTrue(Policy::allows('Exams', 'saveGrades', 'teacher'));
        $this->assertTrue(Policy::allows('Assessments', 'saveScores', 'teacher'));
        $this->assertTrue(Policy::allows('Communication', 'setPreference', 'parent'));
        $this->assertTrue(Policy::allows('Invoices', 'ledger', 'parent'));
        $this->assertTrue(Policy::allows('Portal', 'wardOverview', 'student'));
        $this->assertTrue(Policy::allows('Transcripts', 'show', 'parent'));

        // The bursary owns money; the registry office owns academic definition.
        $this->assertTrue(Policy::allows('FeeStructures', 'add', 'bursar'));
        $this->assertFalse(Policy::allows('FeeStructures', 'add', 'registrar'));
        $this->assertTrue(Policy::allows('Exams', 'add', 'registrar'));
        $this->assertFalse(Policy::allows('Exams', 'add', 'bursar'));
    }

    public function testUnlistedActionIsDeniedToEveryRole(): void
    {
        foreach (self::ROLES as $role) {
            $this->assertFalse(Policy::allows('Students', 'noSuchAction', $role));
            $this->assertFalse(Policy::allows('Nonexistent', 'index', $role));
        }
    }
}
