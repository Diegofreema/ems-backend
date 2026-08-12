<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Api\Jwt;
use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\HttpException;
use Cake\Http\Response;
use Closure;
use Throwable;

/**
 * Base controller for all /api/v1 endpoints.
 *
 * Deliberately extends the framework Controller directly (NOT the app's web
 * AppController) so it does NOT inherit session-based Auth, FormProtection,
 * Flash or the settings-in-session bootstrap. API auth is stateless:
 *
 *   - Authorization: Bearer <jwt>   -> user context (mobile / SPA / third-party)
 *   - X-Api-Key + X-Api-Secret      -> trusted server-to-server client
 *
 * Subclasses list actions that skip auth in $publicActions.
 */
class AppController extends Controller
{
    /**
     * Actions in the subclass that do NOT require authentication.
     *
     * @var array<int, string>
     */
    protected array $publicActions = [];

    /**
     * The authenticated identity for the current request.
     * ['type' => 'user', 'user' => [...]] or ['type' => 'apikey', 'client' => [...]]
     *
     * @var array|null
     */
    protected ?array $identity = null;

    /**
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();
        // No FormProtection / Auth / Flash here — this is a stateless JSON API.
    }

    /**
     * Runs before every action: applies CORS, short-circuits preflight, and
     * enforces authentication unless the action is public.
     *
     * @param \Cake\Event\EventInterface $event Controller event.
     * @return void
     */
    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);

        $this->applyCors();

        // CORS preflight — answer immediately, no auth.
        if ($this->request->is('options')) {
            $event->setResult($this->response->withStatus(204));

            return;
        }

        $action = $this->request->getParam('action');
        if (in_array($action, $this->publicActions, true)) {
            return;
        }

        try {
            $this->identity = $this->authenticate();
        } catch (Throwable $e) {
            $event->setResult($this->respondError($e->getMessage(), 401));

            return;
        }
    }

    /**
     * Wrap action dispatch so ANY exception thrown inside an action is rendered
     * as a JSON error response instead of the framework's HTML error page.
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
            $this->response = $this->respondError($e->getMessage(), $status >= 400 ? $status : 400);
        } catch (Throwable $e) {
            $message = Configure::read('debug') ? $e->getMessage() : 'Internal server error.';
            $this->response = $this->respondError($message, 500);
        }
    }

    /**
     * Resolve the caller's identity from JWT or API-key headers.
     *
     * @return array The identity descriptor.
     * @throws \RuntimeException When no valid credential is present.
     */
    protected function authenticate(): array
    {
        // 1) Bearer JWT
        $header = (string)$this->request->getHeaderLine('Authorization');
        if (stripos($header, 'bearer ') === 0) {
            $token = trim(substr($header, 7));
            $claims = Jwt::decode($token, time());
            if (($claims['type'] ?? null) !== 'access') {
                throw new \RuntimeException('A valid access token is required.');
            }

            return ['type' => 'user', 'user' => $claims];
        }

        // 2) API key + secret (server-to-server)
        $apiKey = (string)$this->request->getHeaderLine('X-Api-Key');
        $apiSecret = (string)$this->request->getHeaderLine('X-Api-Secret');
        if ($apiKey !== '') {
            $ApiKeys = $this->fetchTable('ApiKeys');
            $client = $ApiKeys->findActiveByKey($apiKey);
            if ($client === null || !$client->verifySecret($apiSecret)) {
                throw new \RuntimeException('Invalid API key or secret.');
            }
            $client->last_used_at = \Cake\I18n\FrozenTime::now();
            $ApiKeys->save($client, ['checkRules' => false]);

            return ['type' => 'apikey', 'client' => $client];
        }

        throw new \RuntimeException('Authentication required. Provide a Bearer token or API key.');
    }

    /**
     * Guard an action to specific user role ids. API-key clients bypass this
     * (they are trusted server integrations) unless $allowApiKey is false.
     *
     * @param array<int, int> $roleIds Allowed role ids.
     * @param bool $allowApiKey Whether server-to-server keys satisfy the guard.
     * @return void
     * @throws \Cake\Http\Exception\ForbiddenException
     */
    protected function requireRole(array $roleIds, bool $allowApiKey = true): void
    {
        if ($this->identity === null) {
            throw new ForbiddenException('Not authenticated.');
        }
        if ($this->identity['type'] === 'apikey') {
            if ($allowApiKey) {
                return;
            }
            throw new ForbiddenException('This endpoint requires a user token.');
        }
        $roleId = (int)($this->identity['user']['role_id'] ?? 0);
        if (!in_array($roleId, $roleIds, true)) {
            throw new ForbiddenException('You do not have permission to perform this action.');
        }
    }

    /**
     * Send a successful JSON response.
     *
     * @param mixed $data Serializable payload.
     * @param int $status HTTP status code.
     * @param array $meta Optional meta block (e.g. pagination).
     * @return \Cake\Http\Response
     */
    protected function respond($data, int $status = 200, array $meta = []): Response
    {
        $body = ['success' => true, 'data' => $data];
        if ($meta) {
            $body['meta'] = $meta;
        }

        return $this->jsonBody($body, $status);
    }

    /**
     * Send an error JSON response.
     *
     * @param string $message Human-readable message.
     * @param int $status HTTP status code.
     * @param array $errors Optional field-level validation errors.
     * @return \Cake\Http\Response
     */
    protected function respondError(string $message, int $status = 400, array $errors = []): Response
    {
        $body = ['success' => false, 'message' => $message];
        if ($errors) {
            $body['errors'] = $errors;
        }

        return $this->jsonBody($body, $status);
    }

    /**
     * Serialize an array to a JSON response.
     *
     * @param array $body Response body.
     * @param int $status HTTP status code.
     * @return \Cake\Http\Response
     */
    protected function jsonBody(array $body, int $status): Response
    {
        return $this->response
            ->withStatus($status)
            ->withType('application/json')
            ->withStringBody((string)json_encode($body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    /**
     * Attach permissive CORS headers. Tighten the allowed origin for production.
     *
     * @return void
     */
    protected function applyCors(): void
    {
        $origin = $this->request->getHeaderLine('Origin') ?: '*';
        $this->response = $this->response
            ->withHeader('Access-Control-Allow-Origin', $origin)
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
            ->withHeader('Access-Control-Allow-Headers', 'Authorization, Content-Type, X-Api-Key, X-Api-Secret')
            ->withHeader('Access-Control-Max-Age', '86400')
            ->withHeader('Vary', 'Origin');
    }

    /**
     * Read pagination params from the query string.
     *
     * @return array{page:int, limit:int}
     */
    protected function paginationParams(): array
    {
        $page = max(1, (int)$this->request->getQuery('page', 1));
        $limit = (int)$this->request->getQuery('limit', 25);
        $limit = max(1, min($limit, 100));

        return ['page' => $page, 'limit' => $limit];
    }
}
