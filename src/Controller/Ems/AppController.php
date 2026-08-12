<?php
declare(strict_types=1);

namespace App\Controller\Ems;

use App\Api\Jwt;
use App\Ems\Audit;
use App\Ems\Policy;
use App\Ems\RateLimited;
use App\Ems\RateLimiter;
use App\Ems\Scope;
use App\Ems\SubjectCatalog;
use App\Ems\Viewer;
use App\Ems\ViewerDenied;
use App\Ems\ViewerResolver;
use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Datasource\EntityInterface;
use Cake\Http\Exception\HttpException;
use Cake\Http\Response;
use Closure;
use Throwable;

/**
 * Base controller for the EMS contract API at /api/ems (document.md).
 *
 * Deliberately extends the framework Controller directly (like Api\AppController)
 * so it inherits no session Auth, FormProtection or Flash. Differences from the
 * legacy /api/v1 surface:
 *
 *  - Auth is Bearer-JWT only, requiring claims type === 'ems' (v1 tokens carry
 *    type 'access', so the two surfaces reject each other's tokens).
 *  - Responses are RAW contract payloads — lists as {items,total,page,pageSize},
 *    errors as {message} — never the v1 {success,data,meta} envelope.
 *  - Every request under /schools/{schoolId} asserts the token holds a
 *    membership in that school before the action runs (tenancy, §1.1/§1.4).
 *  - Every response carries Cache-Control: private, no-store (§3.18).
 */
class AppController extends Controller
{
    /**
     * Actions in the subclass that skip authentication.
     *
     * @var array<int, string>
     */
    protected array $publicActions = [];

    /**
     * @var \App\Ems\Viewer|null
     */
    protected ?Viewer $viewer = null;

    /**
     * @var \App\Ems\Scope|null
     */
    private ?Scope $scopeInstance = null;

    /**
     * @var \App\Ems\Tenant|null
     */
    private ?\App\Ems\Tenant $tenantInstance = null;

    /**
     * @var \App\Ems\Audit|null
     */
    private ?Audit $auditInstance = null;

    /**
     * @var \App\Ems\Storage|null
     */
    private ?\App\Ems\Storage $storageInstance = null;

    /**
     * @var \App\Ems\Sequences|null
     */
    private ?\App\Ems\Sequences $sequencesInstance = null;

    /**
     * @var \App\Ems\Grading|null
     */
    private ?\App\Ems\Grading $gradingInstance = null;

    /**
     * @var \App\Ems\Academics|null
     */
    private ?\App\Ems\Academics $academicsInstance = null;

    /**
     * @var \App\Ems\Fees|null
     */
    private ?\App\Ems\Fees $feesInstance = null;

    /**
     * @var \App\Ems\Analytics|null
     */
    private ?\App\Ems\Analytics $analyticsInstance = null;

    /**
     * @var \App\Ems\Dashboard|null
     */
    private ?\App\Ems\Dashboard $dashboardInstance = null;

    /**
     * @var \App\Ems\Comms|null
     */
    private ?\App\Ems\Comms $commsInstance = null;

    /**
     * @var \App\Ems\Reports|null
     */
    private ?\App\Ems\Reports $reportsInstance = null;

    /**
     * @var \App\Ems\FinanceSecurity|null
     */
    private ?\App\Ems\FinanceSecurity $financeSecurityInstance = null;

    /**
     * @var \App\Ems\Imports|null
     */
    private ?\App\Ems\Imports $importsInstance = null;

    public function initialize(): void
    {
        parent::initialize();
        // Stateless JSON API — no components.
    }

    /**
     * CORS, preflight, authentication and tenant-membership assertion.
     *
     * @param \Cake\Event\EventInterface $event Controller event.
     * @return void
     */
    public function beforeFilter(\Cake\Event\EventInterface $event): void
    {
        parent::beforeFilter($event);

        $this->applyCors();

        if ($this->request->is('options')) {
            $event->setResult($this->response->withStatus(204));

            return;
        }

        $origin = $this->request->getHeaderLine('Origin');
        if ($origin !== '' && !in_array(rtrim($origin, '/'), $this->corsOrigins(), true)) {
            $event->setResult($this->errorResponse(403, 'This website is not allowed to use the EMS API.'));

            return;
        }

        $action = $this->request->getParam('action');
        if (in_array($action, $this->publicActions, true)) {
            return;
        }

        $header = (string)$this->request->getHeaderLine('Authorization');
        if (stripos($header, 'bearer ') !== 0) {
            $event->setResult($this->errorResponse(401, 'Sign in to continue.'));

            return;
        }

        try {
            $claims = Jwt::decode(trim(substr($header, 7)), time());
        } catch (Throwable $e) {
            $event->setResult($this->errorResponse(401, 'Your session has expired. Please sign in again.'));

            return;
        }
        if (($claims['type'] ?? null) !== 'ems') {
            $event->setResult($this->errorResponse(401, 'Your session has expired. Please sign in again.'));

            return;
        }

        // Authorization reads LIVE server state, not the frozen token (§ security
        // candidate #2). The token is trusted only for WHO is calling (`sub`);
        // the account's status, role and school are read from the ems_users row
        // on every request, so a disabled or demoted user loses access on their
        // next call rather than whenever the 1h token expires.
        $pathSchoolId = (string)$this->request->getParam('schoolId', '');
        try {
            $this->viewer = ViewerResolver::resolve(
                $this->getTableLocator()->get('EmsUsers'),
                $claims,
                $pathSchoolId
            );
        } catch (ViewerDenied $e) {
            $event->setResult($this->errorResponse($e->statusCode, $e->getMessage()));

            return;
        }

        // Coarse capability gate (App\Ems\Policy, §1.4). One table answers
        // "may this role invoke this action" for every authenticated route;
        // an action the table does not list is denied (fail-closed). Row-level
        // reach is then narrowed inside the action by Scope / DocumentPolicy.
        $controller = (string)$this->request->getParam('controller');
        if (!Policy::allows($controller, (string)$action, $this->viewer->role)) {
            $event->setResult($this->errorResponse(403, Policy::messageFor($controller, (string)$action)));

            return;
        }

        if ($this->tenant()->query('EmsFinanceIntegrityLocks')->where(['cleared_at IS' => null])->count() > 0) {
            $this->response = $this->response->withHeader('X-Finance-Integrity-Warning', 'writes-locked');
        }

    }

    /**
     * Render any exception thrown inside an action as the contract's
     * {message} error body instead of the framework HTML error page.
     *
     * @param \Closure $action The action closure.
     * @param array $args Action arguments.
     * @return void
     */
    public function invokeAction(Closure $action, array $args): void
    {
        try {
            parent::invokeAction($action, $args);
        } catch (HttpException $e) {
            $status = $e->getCode();
            $this->response = $this->errorResponse($status >= 400 ? $status : 400, $e->getMessage());
            // A throttled request tells the client when to come back (§ candidate
            // #4 polish). Only RateLimited carries the seconds; other HttpExceptions
            // render as a plain {message}.
            if ($e instanceof RateLimited) {
                $this->response = $this->response->withHeader('Retry-After', (string)$e->retryAfter);
            }
        } catch (Throwable $e) {
            $message = Configure::read('debug')
                ? $e->getMessage()
                : 'The school server did not respond. Please try again.';
            $this->response = $this->errorResponse(500, $message);
        }
    }

    /**
     * Refuse the request with a contract error (§1.3). The message is shown
     * to the user verbatim.
     *
     * @return never
     */
    protected function fail(int $status, string $message): void
    {
        throw new HttpException($message, $status);
    }

    /**
     * Throttle the current request against a named bucket, keyed by client IP
     * (§ security candidate #4). Shared by every public controller so the one
     * App\Ems\RateLimiter seam covers sign-in, reset, invite, download and
     * public apply alike. Throws 429 once the bucket's limit is exceeded.
     *
     * @param string $bucket Stable name for the protected endpoint.
     * @param int $limit Requests allowed within the window before refusal.
     * @param int $window Window length in seconds (default 5 minutes).
     * @return void
     */
    protected function rateLimit(string $bucket, int $limit, int $window = 300): void
    {
        RateLimiter::hit($bucket, $this->clientIp(), $limit, $window);
    }

    /**
     * The request's client IP for throttling. Defaults to REMOTE_ADDR — correct
     * for a direct deployment. A deployment behind a trusted reverse proxy sets
     * `Ems.trustProxy` true so the real client is read from X-Forwarded-For;
     * left off by default because a spoofable XFF would let an attacker bypass
     * every throttle.
     *
     * @return string
     */
    protected function clientIp(): string
    {
        if (Configure::read('Ems.trustProxy', false)) {
            $this->request->trustProxy = true;
        }

        return $this->request->clientIp() ?: 'unknown';
    }

    /**
     * Serialize a contract payload.
     *
     * @param mixed $payload Any JSON-serializable value (array, scalar, null).
     */
    protected function json($payload, int $status = 200): Response
    {
        return $this->response
            ->withStatus($status)
            ->withType('application/json')
            ->withStringBody((string)json_encode(
                $payload,
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            ));
    }

    /**
     * Resolve a subject NAME from the wire to its catalogue id for a WHERE
     * clause. An unknown name resolves to '' — which matches no rows — so
     * FILTER lookups by a stray name behave as "not found" rather than erroring.
     * For a name that MUST exist (a mutation, or a computed read model that would
     * otherwise silently return empty), use requireSubjectId instead.
     */
    protected function subjectIdOrNone(string $name): string
    {
        return SubjectCatalog::idFor($this->viewer->schoolId, $name) ?? '';
    }

    /**
     * Resolve a subject NAME that MUST be in the catalogue — 422 with the
     * verbatim refusal if not. The loud counterpart of subjectIdOrNone, for
     * writes and read models where an unknown subject is a client error, not an
     * empty result.
     */
    protected function requireSubjectId(string $name): string
    {
        return SubjectCatalog::requireId($this->viewer->schoolId, $name);
    }

    /**
     * The tenant-scoped by-id lookup every module repeats: find a row in
     * `$table` whose id is `$id` AND whose school_id is the viewer's, or fail
     * 404 with `$message`. Keeping it here means the school_id predicate can
     * never be forgotten — a missing scope would otherwise leak or 404-mask
     * another tenant's row.
     */
    protected function findOr404(string $table, string $id, string $message): EntityInterface
    {
        $entity = $this->tenant()->query($table)->where(['id' => $id])->first();
        if ($entity === null) {
            $this->fail(404, $message);
        }

        return $entity;
    }

    /**
     * Refuse a destructive action (409, `$message`) when any dependent row
     * still points at `$id`. `$refs` maps a table name to the foreign-key
     * column that holds the id, e.g. ['EmsEnrolments' => 'student_id']. Each
     * table is checked within the viewer's tenant.
     *
     * @param array<string, string> $id Table => foreign-key column.
     */
    protected function assertNoReferences(string $id, array $refs, string $message): void
    {
        foreach ($refs as $table => $column) {
            if ($this->tenant()->exists($table, [$column => $id])) {
                $this->fail(409, $message);
            }
        }
    }

    /**
     * The list envelope (§1.2): {items, total, page, pageSize}.
     *
     * @param array $items The current page's serialized rows.
     * @param int $total Post-filter total across all pages.
     */
    protected function paginated(array $items, int $total, int $page, int $pageSize): Response
    {
        return $this->json([
            'items' => array_values($items),
            'total' => $total,
            'page' => $page,
            'pageSize' => $pageSize,
        ]);
    }

    /**
     * page / pageSize query params — 1-based, capped at 100 (§1.2).
     *
     * @return array{page:int, pageSize:int}
     */
    protected function pageParams(): array
    {
        $page = max(1, (int)$this->request->getQuery('page', 1));
        $pageSize = (int)$this->request->getQuery('pageSize', 20);
        $pageSize = max(1, min($pageSize, 100));

        return ['page' => $page, 'pageSize' => $pageSize];
    }

    /**
     * The current request's tenant-scope choke point (§1.1). Use
     * `$this->tenant()->query('EmsStudents')->where([...])` instead of hand-
     * spelling `'school_id' => $this->viewer->schoolId` — the predicate is then
     * impossible to forget. See App\Ems\Tenant.
     */
    protected function tenant(): \App\Ems\Tenant
    {
        if ($this->tenantInstance === null) {
            $this->tenantInstance = new \App\Ems\Tenant(
                $this->getTableLocator(),
                $this->viewer->schoolId
            );
        }

        return $this->tenantInstance;
    }

    /**
     * The viewer's scope policy (§1.4), shared by list filters and detail
     * assertions so they can never disagree.
     */
    protected function scope(): Scope
    {
        if ($this->scopeInstance === null) {
            $this->scopeInstance = new Scope($this->viewer, $this->getTableLocator());
        }

        return $this->scopeInstance;
    }

    /**
     * The append-only audit writer (§1.6).
     */
    protected function audit(): Audit
    {
        if ($this->auditInstance === null) {
            $requestId = trim((string)$this->request->getHeaderLine('X-Request-Id'));
            if ($requestId === '') {
                $requestId = bin2hex(random_bytes(16));
            }
            $this->auditInstance = new Audit($this->getTableLocator(), $requestId, $this->clientIp());
        }

        return $this->auditInstance;
    }

    protected function storage(): \App\Ems\Storage
    {
        if ($this->storageInstance === null) {
            $this->storageInstance = new \App\Ems\Storage($this->getTableLocator());
        }

        return $this->storageInstance;
    }

    protected function sequences(): \App\Ems\Sequences
    {
        if ($this->sequencesInstance === null) {
            $this->sequencesInstance = new \App\Ems\Sequences($this->getTableLocator());
        }

        return $this->sequencesInstance;
    }

    /**
     * The grading-scheme authority for the current tenant (§3.3).
     */
    protected function grading(): \App\Ems\Grading
    {
        if ($this->gradingInstance === null) {
            $this->gradingInstance = new \App\Ems\Grading($this->getTableLocator(), $this->viewer->schoolId);
        }

        return $this->gradingInstance;
    }

    /**
     * The academics computation engine for the current tenant (§3.1/§3.2/§3.5).
     */
    protected function academicsEngine(): \App\Ems\Academics
    {
        if ($this->academicsInstance === null) {
            $this->academicsInstance = new \App\Ems\Academics(
                $this->getTableLocator(),
                $this->viewer->schoolId,
                $this->grading()
            );
        }

        return $this->academicsInstance;
    }

    /**
     * The fees computation engine for the current tenant (§3.7). `today` is the
     * server's real date — the mock's fixed clock becomes new Date() live (§1.5).
     */
    protected function feesEngine(): \App\Ems\Fees
    {
        if ($this->feesInstance === null) {
            $this->feesInstance = new \App\Ems\Fees(
                $this->getTableLocator(),
                $this->viewer->schoolId,
                \Cake\I18n\FrozenDate::today()->format('Y-m-d')
            );
        }

        return $this->feesInstance;
    }

    protected function financeSecurity(): \App\Ems\FinanceSecurity
    {
        if ($this->financeSecurityInstance === null) {
            $this->financeSecurityInstance = new \App\Ems\FinanceSecurity(
                $this->getTableLocator(),
                $this->viewer->schoolId,
                $this->feesEngine(),
                $this->audit()
            );
        }

        return $this->financeSecurityInstance;
    }

    /**
     * The analytics engine for the current tenant (§3.22) — reuses the grading
     * and fees engines so figures share the pinned-scheme and net-paid rules.
     */
    protected function analyticsEngine(): \App\Ems\Analytics
    {
        if ($this->analyticsInstance === null) {
            $this->analyticsInstance = new \App\Ems\Analytics(
                $this->getTableLocator(),
                $this->viewer->schoolId,
                $this->grading(),
                $this->feesEngine(),
                \Cake\I18n\FrozenDate::today()->format('Y-m-d')
            );
        }

        return $this->analyticsInstance;
    }

    /**
     * The staff dashboard engine for the current tenant — reuses the fees
     * engine so money figures share the net-paid rule.
     */
    protected function dashboardEngine(): \App\Ems\Dashboard
    {
        if ($this->dashboardInstance === null) {
            $this->dashboardInstance = new \App\Ems\Dashboard(
                $this->getTableLocator(),
                $this->viewer->schoolId,
                $this->feesEngine(),
                \Cake\I18n\FrozenDate::today()->format('Y-m-d')
            );
        }

        return $this->dashboardInstance;
    }

    /**
     * The communication engine for the current tenant (§3.20) — one audience /
     * consent / provider pipeline shared by preview and send. `today` is the
     * server's real date.
     */
    protected function comms(): \App\Ems\Comms
    {
        if ($this->commsInstance === null) {
            $this->commsInstance = new \App\Ems\Comms(
                $this->getTableLocator(),
                $this->viewer->schoolId,
                \Cake\I18n\FrozenDate::today()->format('Y-m-d')
            );
        }

        return $this->commsInstance;
    }

    /**
     * The reporting engine for the current tenant (§3.21) — reuses grading and
     * fees so grade and money reports share the pinned-scheme / net-paid rules.
     */
    protected function reportsEngine(): \App\Ems\Reports
    {
        if ($this->reportsInstance === null) {
            $this->reportsInstance = new \App\Ems\Reports(
                $this->getTableLocator(),
                $this->viewer->schoolId,
                $this->grading(),
                $this->feesEngine(),
                \Cake\I18n\FrozenDate::today()->format('Y-m-d')
            );
        }

        return $this->reportsInstance;
    }

    /**
     * The CSV import engine for the current tenant (§3.17).
     */
    protected function importsEngine(): \App\Ems\Imports
    {
        if ($this->importsInstance === null) {
            $this->importsInstance = new \App\Ems\Imports(
                $this->getTableLocator(),
                $this->viewer->schoolId,
                \Cake\I18n\FrozenDate::today()->format('Y-m-d')
            );
        }

        return $this->importsInstance;
    }

    /**
     * Restrict an action, from INSIDE the action, to the given contract roles.
     * Throws 403 with `$message` (caught by invokeAction). The coarse role gate
     * for the whole surface now lives in App\Ems\Policy (beforeFilter, §1.4);
     * requireRole is for the rarer in-action refinement that is finer than a
     * tier — e.g. Reports' per-report-type roles.
     *
     * @param array<int, string> $roles Allowed role names.
     */
    protected function requireRole(array $roles, string $message): void
    {
        if ($this->viewer === null || !in_array($this->viewer->role, $roles, true)) {
            $this->fail(403, $message);
        }
    }

    /**
     * The parsed JSON request body as an array.
     */
    protected function body(): array
    {
        $data = $this->request->getData();

        return is_array($data) ? $data : [];
    }

    /**
     * Build a `{message}` error response directly (used before the action
     * runs, where fail()'s exception would not be caught by invokeAction).
     */
    protected function errorResponse(int $status, string $message): Response
    {
        return $this->json(['message' => $message], $status);
    }

    /**
     * CORS + the contract's mandatory cache policy (§3.18: every response is
     * private — no shared HTTP caching).
     *
     * The refresh cookie (token-at-rest hardening) rides on CREDENTIALED cross-
     * origin requests, which the browser only permits when the server echoes the
     * exact `Origin` back AND sets Allow-Credentials — never with a wildcard. So
     * we reflect the origin (and permit credentials) ONLY for allow-listed
     * origins (`Ems.corsOrigins`); a stranger origin gets no ACAO header at all,
     * and the browser blocks its cross-origin read. This replaces the old
     * reflect-any-origin behaviour, which combined with credentials would have
     * let any site drive the API with the user's cookie.
     */
    protected function applyCors(): void
    {
        $this->response = $this->response
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
            ->withHeader('Access-Control-Allow-Headers', 'Authorization, Content-Type, Idempotency-Key, X-Request-ID')
            ->withHeader('Access-Control-Expose-Headers', 'X-Finance-Integrity-Warning, X-Request-ID')
            ->withHeader('Access-Control-Max-Age', '86400')
            ->withHeader('Vary', 'Origin')
            ->withHeader('Cache-Control', 'private, no-store');

        $origin = $this->request->getHeaderLine('Origin');
        $allowed = $this->corsOrigins();
        if ($origin !== '' && in_array($origin, $allowed, true)) {
            $this->response = $this->response
                ->withHeader('Access-Control-Allow-Origin', $origin)
                ->withHeader('Access-Control-Allow-Credentials', 'true');
        }
    }

    /**
     * Return the normalized browser origins permitted to call this API.
     *
     * @return array<int, string>
     */
    private function corsOrigins(): array
    {
        return array_values(array_filter(array_map(
            static function ($origin): string {
                return rtrim(trim((string)$origin), '/');
            },
            (array)Configure::read('Ems.corsOrigins', [])
        )));
    }
}
