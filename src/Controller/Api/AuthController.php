<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Api\Jwt;
use Cake\Auth\DefaultPasswordHasher;
use Cake\Core\Configure;
use Cake\Utility\Security;
use Cake\Utility\Text;
use Throwable;

/**
 * Authentication endpoints for the API.
 *
 * POST /api/v1/auth/login    -> exchange username+password for JWT pair
 * POST /api/v1/auth/refresh  -> exchange refresh token for a new access token
 * GET  /api/v1/auth/me       -> current identity (requires access token)
 * POST /api/v1/auth/api-keys -> issue an API key/secret pair (admin only)
 */
class AuthController extends AppController
{
    /**
     * @var array<int, string>
     */
    protected array $publicActions = ['login', 'refresh'];

    /**
     * Exchange credentials for an access + refresh token pair.
     *
     * @return \Cake\Http\Response
     */
    public function login()
    {
        $this->request->allowMethod(['post']);

        $username = trim((string)$this->request->getData('username'));
        $password = (string)$this->request->getData('password');
        if ($username === '' || $password === '') {
            return $this->respondError('Username and password are required.', 422);
        }

        $Users = $this->fetchTable('Users');
        /** @var \App\Model\Entity\User|null $user */
        $user = $Users->find()->where(['username' => $username])->first();

        if ($user === null || !(new DefaultPasswordHasher())->check($password, (string)$user->password)) {
            return $this->respondError('Invalid credentials.', 401);
        }
        if (($user->userstatus ?? null) === 'Disabled') {
            return $this->respondError('This account is disabled.', 403);
        }

        return $this->respond($this->issueTokens($user));
    }

    /**
     * Issue a fresh access token from a valid refresh token.
     *
     * @return \Cake\Http\Response
     */
    public function refresh()
    {
        $this->request->allowMethod(['post']);

        $token = (string)$this->request->getData('refresh_token');
        if ($token === '') {
            return $this->respondError('A refresh_token is required.', 422);
        }

        try {
            $claims = Jwt::decode($token, time());
        } catch (Throwable $e) {
            return $this->respondError($e->getMessage(), 401);
        }
        if (($claims['type'] ?? null) !== 'refresh') {
            return $this->respondError('Not a refresh token.', 401);
        }

        $Users = $this->fetchTable('Users');
        /** @var \App\Model\Entity\User|null $user */
        $user = $Users->find()->where(['id' => $claims['sub'] ?? 0])->first();
        if ($user === null || ($user->userstatus ?? null) === 'Disabled') {
            return $this->respondError('User no longer valid.', 401);
        }

        return $this->respond($this->issueTokens($user, false));
    }

    /**
     * Return the current authenticated identity.
     *
     * @return \Cake\Http\Response
     */
    public function me()
    {
        $this->request->allowMethod(['get']);

        if ($this->identity['type'] === 'apikey') {
            return $this->respond([
                'type' => 'apikey',
                'client' => ['id' => $this->identity['client']->id, 'name' => $this->identity['client']->name],
            ]);
        }

        $claims = $this->identity['user'];
        $Users = $this->fetchTable('Users');
        /** @var \App\Model\Entity\User|null $user */
        $user = $Users->find()
            ->contain(['Roles'])
            ->where(['Users.id' => $claims['sub'] ?? 0])
            ->first();

        if ($user === null) {
            return $this->respondError('User not found.', 404);
        }

        return $this->respond(['type' => 'user', 'user' => $this->publicUser($user)]);
    }

    /**
     * Issue a new API key/secret pair for a server-to-server consumer.
     * Admin-only (roles 1/5/7). The plaintext secret is returned ONCE.
     *
     * @return \Cake\Http\Response
     */
    public function apiKeys()
    {
        $this->request->allowMethod(['post']);
        $this->requireRole([1, 5, 7], false);

        $name = trim((string)$this->request->getData('name'));
        if ($name === '') {
            return $this->respondError('A client name is required.', 422);
        }

        $key = 'lk_' . bin2hex(Security::randomBytes(16));
        $secret = bin2hex(Security::randomBytes(24));

        $ApiKeys = $this->fetchTable('ApiKeys');
        $entity = $ApiKeys->newEntity([
            'name' => $name,
            'api_key' => $key,
            'scopes' => (string)$this->request->getData('scopes'),
            'active' => true,
        ]);
        $entity->set('secret', $secret); // triggers _setSecret hashing

        if (!$ApiKeys->save($entity)) {
            return $this->respondError('Could not create API key.', 422, $entity->getErrors());
        }

        return $this->respond([
            'id' => $entity->id,
            'name' => $entity->name,
            'api_key' => $key,
            'api_secret' => $secret,
            'note' => 'Store the api_secret now — it will not be shown again.',
        ], 201);
    }

    /**
     * Build the access (+ optional refresh) token payload for a user.
     *
     * @param \App\Model\Entity\User $user The authenticated user.
     * @param bool $withRefresh Whether to include a refresh token.
     * @return array
     */
    protected function issueTokens($user, bool $withRefresh = true): array
    {
        $now = time();
        $base = ['sub' => $user->id, 'role_id' => $user->role_id, 'username' => $user->username];

        $result = [
            'token_type' => 'Bearer',
            'access_token' => Jwt::encode($base + ['type' => 'access'], (int)Configure::read('Jwt.accessTtl'), $now),
            'expires_in' => (int)Configure::read('Jwt.accessTtl'),
            'user' => $this->publicUser($user),
        ];
        if ($withRefresh) {
            $result['refresh_token'] = Jwt::encode(
                ['sub' => $user->id, 'type' => 'refresh'],
                (int)Configure::read('Jwt.refreshTtl'),
                $now
            );
        }

        return $result;
    }

    /**
     * Shape a User entity into a safe public representation.
     *
     * @param \App\Model\Entity\User $user User entity.
     * @return array
     */
    protected function publicUser($user): array
    {
        return [
            'id' => $user->id,
            'username' => $user->username,
            'email' => $user->email ?? null,
            'role_id' => $user->role_id,
            'role' => isset($user->role) ? $user->role->name ?? null : null,
            'status' => $user->userstatus ?? null,
        ];
    }
}
