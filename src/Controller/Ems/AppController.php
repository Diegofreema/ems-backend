<?php
declare(strict_types=1);

namespace App\Controller\Ems;

use App\Api\Jwt;
use App\Ems\Academics;
use App\Ems\Analytics;
use App\Ems\Audit;
use App\Ems\Comms;
use App\Ems\Dashboard;
use App\Ems\Fees;
use App\Ems\FinanceSecurity;
use App\Ems\Grading;
use App\Ems\Imports;
use App\Ems\Policy;
use App\Ems\RateLimited;
use App\Ems\RateLimiter;
use App\Ems\Reports;
use App\Ems\Scope;
use App\Ems\Sequences;
use App\Ems\Storage;
use App\Ems\SubjectCatalog;
use App\Ems\Tenant;
use App\Ems\Viewer;
use App\Ems\ViewerDenied;
use App\Ems\ViewerResolver;
use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\Http\Exception\HttpException;
use Cake\Http\Response;
use Cake\I18n\FrozenDate;
use Closure;
use Throwable;

/**
 * Base controller for the EMS contract API at /api/ems (document.md).
 *
 * Deliberately extends the framework Controller directly so it inherits no
 * session Auth, FormProtection or Flash:
 *
 *  - Auth is Bearer-JWT only, requiring claims type === 'ems'.
 *  - Responses are raw contract payloads — lists as {items,total,page,pageSize}
 *    and errors as {message}.
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
    private ?Tenant $tenantInstance = null;

    /**
     * @var \App\Ems\Audit|null
     */
    private ?Audit $auditInstance = null;

    /**
     * @var \App\Ems\Storage|null
     */
    private ?Storage $storageInstance = null;

    /**
     * @var \App\Ems\Sequences|null
     */
    private ?Sequences $sequencesInstance = null;

    /**
     * @var \App\Ems\Grading|null
     */
    private ?Grading $gradingInstance = null;

    /**
     * @var \App\Ems\Academics|null
     */
    private ?Academics $academicsInstance = null;

    /**
     * @var \App\Ems\Fees|null
     */
    private ?Fees $feesInstance = null;

    /**
     * @var \App\Ems\Analytics|null
     */
    private ?Analytics $analyticsInstance = null;

    /**
     * @var \App\Ems\Dashboard|null
     */
    private ?Dashboard $dashboardInstance = null;

    /**
     * @var \App\Ems\Comms|null
     */
    private ?Comms $commsInstance = null;

    /**
     * @var \App\Ems\Reports|null
     */
    private ?Reports $reportsInstance = null;

    /**
     * @var \App\Ems\FinanceSecurity|null
     */
    private ?FinanceSecurity $financeSecurityInstance = null;

    /**
     * @var \App\Ems\Imports|null
     */
    private ?Imports $importsInstance = null;

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
    public function beforeFilter(EventInterface $event): void
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
                $pathSchoolId,
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
            $requestId = trim((string)$this->request->getHeaderLine('X-Request-ID'));
            if ($requestId === '') {
                $requestId = bin2hex(random_bytes(16));
            }
            error_log(sprintf(
                '[EMS request %s] %s %s failed with %s: %s',
                $requestId,
                $this->request->getMethod(),
                $this->request->getRequestTarget(),
                get_class($e),
                $e->getMessage(),
            ));
            $databaseConfig = ConnectionManager::getConfig('default');
            $databaseUrl = (string)getenv('DATABASE_URL');
            $databaseParts = $databaseUrl !== '' ? parse_url($databaseUrl) : [];
            if ($databaseParts === false) {
                $databaseParts = [];
            }
            $sslCa = (string)getenv('DATABASE_SSL_CA');
            error_log(sprintf(
                '[EMS request %s] database runtime host_match=%s port_match=%s database_match=%s username_match=%s password_match=%s ssl_ca=%s ca_readable=%s',
                $requestId,
                ($databaseConfig['host'] ?? null) === ($databaseParts['host'] ?? null) ? 'yes' : 'no',
                (int)($databaseConfig['port'] ?? 0) === (int)($databaseParts['port'] ?? 0) ? 'yes' : 'no',
                ($databaseConfig['database'] ?? null) === ltrim((string)($databaseParts['path'] ?? ''), '/') ? 'yes' : 'no',
                hash_equals(
                    hash('sha256', rawurldecode((string)($databaseParts['user'] ?? ''))),
                    hash('sha256', (string)($databaseConfig['username'] ?? '')),
                ) ? 'yes' : 'no',
                hash_equals(
                    hash('sha256', rawurldecode((string)($databaseParts['pass'] ?? ''))),
                    hash('sha256', (string)($databaseConfig['password'] ?? '')),
                ) ? 'yes' : 'no',
                $sslCa !== '' ? $sslCa : 'missing',
                $sslCa !== '' && is_readable($sslCa) ? 'yes' : 'no',
            ));
            $message = Configure::read('debug')
                ? $e->getMessage()
                : 'The school server did not respond. Please try again.';
            $this->response = $this->errorResponse(500, $message)
                ->withHeader('X-Request-ID', $requestId);
        }
    }

    /**
     * Assert the request carries a trusted browser Origin — a CSRF guard for
     * cookie-authenticated, state-changing endpoints (e.g. logout). The refresh
     * cookie is SameSite=None, so a cross-site page could otherwise drive these
     * with the victim's cookie. beforeFilter already 403s a PRESENT non-allowed
     * Origin; this additionally refuses an ABSENT one, so the SPA's own fetch
     * (which always sends Origin on a POST) is required. Bearer-authed routes do
     * not need this — an attacker cannot set the Authorization header cross-site.
     *
     * @return void
     */
    protected function assertBrowserOrigin(): void
    {
        $origin = rtrim($this->request->getHeaderLine('Origin'), '/');
        if ($origin === '' || !in_array($origin, $this->corsOrigins(), true)) {
            $this->fail(403, 'This website is not allowed to use the EMS API.');
        }
    }

    /**
     * Refuse the request with a contract error (§1.3). The message is shown
     * to the user verbatim.
     *
     * @return never
     */
    protected function fail(int $status, string $message): never
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
     * The request's client IP for throttling — the key every rate limit counts
     * per, so its integrity is a security property, not a convenience.
     *
     * Behind a reverse proxy the socket peer (REMOTE_ADDR) is the proxy itself,
     * so keying on it collapses every visitor into ONE bucket: one attacker then
     * throttles the whole world (availability DoS) and per-client limits stop
     * isolating anyone. The naive fix — trusting X-Forwarded-For — is worse: XFF
     * is client-supplied, so an attacker rotates it to mint a fresh bucket per
     * request and bypasses every throttle.
     *
     * `Ems.proxyHops` resolves both: it is the number of TRUSTED proxies in
     * front of the app. The real client is the XFF entry appended by the
     * outermost trusted proxy — the Nth from the RIGHT. Everything to its left
     * is caller-supplied and forgeable, so it is ignored: an attacker can pad
     * XFF all they like but can never move the trusted tail. 0 (the default) =
     * no proxy, trust only the socket peer. Set it to the exact hop count for
     * the deployment (e.g. 1 behind a single load balancer).
     *
     * @return string
     */
    protected function clientIp(): string
    {
        $hops = max(0, (int)Configure::read('Ems.proxyHops', 0));
        if ($hops > 0) {
            $forwarded = array_values(array_filter(
                array_map('trim', explode(',', (string)$this->request->getHeaderLine('X-Forwarded-For'))),
                static fn(string $ip): bool => $ip !== '',
            ));
            $count = count($forwarded);
            if ($count > 0) {
                // count === hops → client sent no XFF, the outermost proxy's
                // entry (index 0) is the real client; extra left entries are the
                // attacker's padding and fall away as the index moves right.
                $client = $forwarded[max(0, $count - $hops)];
                if (filter_var($client, FILTER_VALIDATE_IP) !== false) {
                    return $client;
                }
            }
        }

        return $this->request->clientIp() ?: 'unknown';
    }

    /**
     * Serialize a contract payload.
     *
     * @param mixed $payload Any JSON-serializable value (array, scalar, null).
     */
    protected function json(mixed $payload, int $status = 200): Response
    {
        return $this->response
            ->withStatus($status)
            ->withType('application/json')
            ->withStringBody((string)json_encode(
                $payload,
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES,
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
    protected function tenant(): Tenant
    {
        if ($this->tenantInstance === null) {
            $this->tenantInstance = new Tenant(
                $this->getTableLocator(),
                $this->viewer->schoolId,
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

    protected function storage(): Storage
    {
        if ($this->storageInstance === null) {
            $this->storageInstance = new Storage($this->getTableLocator());
        }

        return $this->storageInstance;
    }

    protected function sequences(): Sequences
    {
        if ($this->sequencesInstance === null) {
            $this->sequencesInstance = new Sequences($this->getTableLocator());
        }

        return $this->sequencesInstance;
    }

    /**
     * The grading-scheme authority for the current tenant (§3.3).
     */
    protected function grading(): Grading
    {
        if ($this->gradingInstance === null) {
            $this->gradingInstance = new Grading($this->getTableLocator(), $this->viewer->schoolId);
        }

        return $this->gradingInstance;
    }

    /**
     * The academics computation engine for the current tenant (§3.1/§3.2/§3.5).
     */
    protected function academicsEngine(): Academics
    {
        if ($this->academicsInstance === null) {
            $this->academicsInstance = new Academics(
                $this->getTableLocator(),
                $this->viewer->schoolId,
                $this->grading(),
            );
        }

        return $this->academicsInstance;
    }

    /**
     * The fees computation engine for the current tenant (§3.7). `today` is the
     * server's real date — the mock's fixed clock becomes new Date() live (§1.5).
     */
    protected function feesEngine(): Fees
    {
        if ($this->feesInstance === null) {
            $this->feesInstance = new Fees(
                $this->getTableLocator(),
                $this->viewer->schoolId,
                FrozenDate::today()->format('Y-m-d'),
            );
        }

        return $this->feesInstance;
    }

    protected function financeSecurity(): FinanceSecurity
    {
        if ($this->financeSecurityInstance === null) {
            $this->financeSecurityInstance = new FinanceSecurity(
                $this->getTableLocator(),
                $this->viewer->schoolId,
                $this->feesEngine(),
                $this->audit(),
            );
        }

        return $this->financeSecurityInstance;
    }

    /**
     * The analytics engine for the current tenant (§3.22) — reuses the grading
     * and fees engines so figures share the pinned-scheme and net-paid rules.
     */
    protected function analyticsEngine(): Analytics
    {
        if ($this->analyticsInstance === null) {
            $this->analyticsInstance = new Analytics(
                $this->getTableLocator(),
                $this->viewer->schoolId,
                $this->grading(),
                $this->feesEngine(),
                FrozenDate::today()->format('Y-m-d'),
            );
        }

        return $this->analyticsInstance;
    }

    /**
     * The staff dashboard engine for the current tenant — reuses the fees
     * engine so money figures share the net-paid rule.
     */
    protected function dashboardEngine(): Dashboard
    {
        if ($this->dashboardInstance === null) {
            $this->dashboardInstance = new Dashboard(
                $this->getTableLocator(),
                $this->viewer->schoolId,
                $this->feesEngine(),
                FrozenDate::today()->format('Y-m-d'),
            );
        }

        return $this->dashboardInstance;
    }

    /**
     * The communication engine for the current tenant (§3.20) — one audience /
     * consent / provider pipeline shared by preview and send. `today` is the
     * server's real date.
     */
    protected function comms(): Comms
    {
        if ($this->commsInstance === null) {
            $this->commsInstance = new Comms(
                $this->getTableLocator(),
                $this->viewer->schoolId,
                FrozenDate::today()->format('Y-m-d'),
            );
        }

        return $this->commsInstance;
    }

    /**
     * The reporting engine for the current tenant (§3.21) — reuses grading and
     * fees so grade and money reports share the pinned-scheme / net-paid rules.
     */
    protected function reportsEngine(): Reports
    {
        if ($this->reportsInstance === null) {
            $this->reportsInstance = new Reports(
                $this->getTableLocator(),
                $this->viewer->schoolId,
                $this->grading(),
                $this->feesEngine(),
                FrozenDate::today()->format('Y-m-d'),
            );
        }

        return $this->reportsInstance;
    }

    /**
     * The CSV import engine for the current tenant (§3.17).
     */
    protected function importsEngine(): Imports
    {
        if ($this->importsInstance === null) {
            $this->importsInstance = new Imports(
                $this->getTableLocator(),
                $this->viewer->schoolId,
                FrozenDate::today()->format('Y-m-d'),
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
            (array)Configure::read('Ems.corsOrigins', []),
        )));
    }
}
