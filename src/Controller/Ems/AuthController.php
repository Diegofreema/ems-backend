<?php
declare(strict_types=1);

namespace App\Controller\Ems;

use App\Api\Jwt;
use App\Ems\Invitations;
use App\Ems\Messages;
use App\Ems\RefreshDenied;
use App\Ems\RefreshTokens;
use App\Ems\Resend;
use App\Ems\Serializer\SettingsSerializer;
use App\Ems\SubjectCatalog;
use Cake\Core\Configure;
use Cake\Datasource\EntityInterface;
use Cake\Http\Cookie\Cookie;
use Cake\Http\Response;
use Cake\I18n\FrozenDate;
use Cake\I18n\FrozenTime;
use Cake\Log\Log;
use Throwable;

/**
 * Tenant-less auth endpoints (document.md §3.18). Every action is public;
 * each returns AuthResult = { user, school } plus an additive `token` field
 * (extra fields are tolerated by the contract, §1.8) that the frontend holds
 * IN MEMORY and sends as `Authorization: Bearer`.
 *
 * Token-at-rest hardening (§ security candidate): the durable credential is a
 * rotating refresh token that never reaches JS — it is issued in an httpOnly
 * cookie by every true session-start (sign-in / register / invite-accept /
 * reset-confirm) and spent at /auth/refresh for a fresh short-lived access
 * token. /auth/logout revokes it. See App\Ems\RefreshTokens.
 */
class AuthController extends AppController
{
    protected array $publicActions = [
        'signIn', 'registerSchool', 'inviteLookup', 'inviteAccept',
        'resetRequest', 'resetConfirm', 'refresh', 'logout',
    ];

    /** Wrong guesses allowed against one reset code before it is dead. */
    private const RESET_MAX_ATTEMPTS = 5;

    /** The httpOnly refresh cookie's name and path (scoped to the auth endpoints). */
    private const REFRESH_COOKIE = 'ems_refresh';
    private const REFRESH_COOKIE_PATH = '/api/ems/auth';

    /**
     * POST /auth/sign-in { email, password }
     */
    public function signIn(): Response
    {
        $this->rateLimit('signin', 10);
        $body = $this->body();
        $email = strtolower(trim((string)($body['email'] ?? '')));
        $password = (string)($body['password'] ?? '');

        $user = $email === '' ? null : $this->fetchTable('EmsUsers')->find()
            ->where(['LOWER(email)' => $email])
            ->first();

        // Same message for unknown e-mail and wrong password (§3.18).
        if ($user === null) {
            $this->fail(401, Messages::BAD_CREDENTIALS);
        }
        if ($user->status === 'invited') {
            $this->fail(403, Messages::ACCOUNT_INVITED);
        }
        if ($user->status === 'disabled') {
            $this->fail(403, Messages::ACCOUNT_DISABLED);
        }
        if ($user->password_hash === null || !password_verify($password, $user->password_hash)) {
            $this->fail(401, Messages::BAD_CREDENTIALS);
        }

        return $this->authResult($user);
    }

    /**
     * POST /auth/register-school { school: SchoolProfileInput, admin: { name, email, password } }
     */
    public function registerSchool(): Response
    {
        $this->rateLimit('register', 5, 900);
        $body = $this->body();
        $school = is_array($body['school'] ?? null) ? $body['school'] : [];
        $admin = is_array($body['admin'] ?? null) ? $body['admin'] : [];

        $name = trim((string)($school['name'] ?? ''));
        $shortName = trim((string)($school['shortName'] ?? ''));
        $adminName = trim((string)($admin['name'] ?? ''));
        $slug = $this->slugify($name);
        $email = strtolower(trim((string)($admin['email'] ?? '')));
        $password = (string)($admin['password'] ?? '');

        $schools = $this->fetchTable('EmsSchools');
        $users = $this->fetchTable('EmsUsers');

        if ($slug === '') {
            $this->fail(422, Messages::SCHOOL_NAME_REQUIRED);
        }
        if ($shortName === '') {
            $this->fail(422, Messages::SCHOOL_SHORT_NAME_REQUIRED);
        }
        if ($adminName === '') {
            $this->fail(422, Messages::ACCOUNT_NAME_REQUIRED);
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->fail(422, Messages::EMAIL_INVALID);
        }
        if ($schools->exists(['slug' => $slug])) {
            $this->fail(422, Messages::SCHOOL_NAME_TAKEN);
        }
        if ($email !== '' && $users->exists(['LOWER(email)' => $email])) {
            $this->fail(422, Messages::EMAIL_EXISTS);
        }
        if (strlen($password) < 8) {
            $this->fail(422, Messages::PASSWORD_MIN);
        }

        $user = $schools->getConnection()->transactional(function () use ($schools, $users, $school, $name, $shortName, $adminName, $slug, $email, $password) {
            $schoolRow = $schools->saveOrFail($schools->newEntity([
                'slug' => $slug,
                'name' => $name,
                'short_name' => $shortName,
                'motto' => trim((string)($school['motto'] ?? '')),
                'address' => trim((string)($school['address'] ?? '')),
                'logo' => isset($school['logo']) && is_string($school['logo']) ? $school['logo'] : null,
            ]));

            // Seed the subject catalogue with the standard curriculum, so the
            // school can run exams/questions/allocations from day one and
            // customise the list in Settings afterwards.
            $subjects = $this->fetchTable('EmsSubjects');
            foreach (SubjectCatalog::STANDARD_SUBJECTS as $subjectName) {
                $subjects->saveOrFail($subjects->newEntity([
                    'school_id' => $schoolRow->id,
                    'name' => $subjectName,
                    'active' => true,
                ]));
            }

            return $users->saveOrFail($users->newEntity([
                'school_id' => $schoolRow->id,
                'name' => $adminName,
                'email' => $email,
                'role' => 'administrator',
                'status' => 'active',
                'added_on' => FrozenDate::today(),
                'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            ]));
        });

        return $this->authResult($user);
    }

    /**
     * POST /auth/invite/lookup { code }
     */
    public function inviteLookup(): Response
    {
        $this->rateLimit('invite_lookup', 10);

        // A pre-accept lookup, not a session start — no refresh cookie here; the
        // caller still has no password. inviteAccept is where the session begins.
        return $this->authResult($this->findInvite(), false);
    }

    /**
     * POST /auth/invite/accept { code, password }
     */
    public function inviteAccept(): Response
    {
        $this->rateLimit('invite_accept', 10);
        $user = $this->findInvite();
        $password = (string)($this->body()['password'] ?? '');
        if (strlen($password) < 8) {
            $this->fail(422, Messages::PASSWORD_MIN);
        }

        $users = $this->fetchTable('EmsUsers');
        $user->password_hash = password_hash($password, PASSWORD_DEFAULT);
        $user->status = 'active';
        $user->invite_code = null; // the code is burned
        $user->invite_expires_at = null;
        $users->saveOrFail($user);

        return $this->authResult($user);
    }

    /**
     * POST /auth/reset/request { email } — always { sent: true }; never
     * reveals whether the address exists (§3.18).
     */
    public function resetRequest(): Response
    {
        $this->rateLimit('reset', 5);
        $email = strtolower(trim((string)($this->body()['email'] ?? '')));

        if ($email !== '') {
            $user = $this->fetchTable('EmsUsers')->find()
                ->where(['LOWER(email)' => $email])
                ->first();
            if ($user !== null && $user->status !== 'disabled') {
                $code = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
                $resets = $this->fetchTable('EmsPasswordResets');
                $reset = $resets->saveOrFail($resets->newEntity([
                    'user_id' => $user->id,
                    'code' => $code,
                    'expires_at' => FrozenTime::now()->addMinutes(30),
                ]));
                try {
                    $school = $this->fetchTable('EmsSchools')->get($user->school_id);
                    $body = sprintf(
                        "Hello %s,\n\nYour password reset code for %s is:\n\n%s\n\n"
                            . "This code expires in 30 minutes and works only once.\n\n"
                            . "If you did not request this, you can ignore this message.\n",
                        (string)$user->name,
                        (string)$school->name,
                        $code,
                    );
                    Resend::deliver(
                        (string)$user->email,
                        sprintf('Reset your %s EMS password', (string)$school->name),
                        $body,
                    );
                } catch (Throwable $e) {
                    $resets->delete($reset);
                    Log::error(sprintf(
                        'EMS password reset delivery failed for user %s: %s',
                        (string)$user->id,
                        $e->getMessage(),
                    ));
                }
            }
        }

        return $this->json(['sent' => true]);
    }

    /**
     * POST /auth/reset/confirm { email, code, password } — one message for
     * every failure path (§3.18).
     */
    public function resetConfirm(): Response
    {
        $this->rateLimit('reset_confirm', 5);
        $body = $this->body();
        $email = strtolower(trim((string)($body['email'] ?? '')));
        $code = trim((string)($body['code'] ?? ''));
        $password = (string)($body['password'] ?? '');

        $user = $email === '' ? null : $this->fetchTable('EmsUsers')->find()
            ->where(['LOWER(email)' => $email])
            ->first();

        // Load the user's latest active reset by USER, not by code, so a wrong
        // guess can be counted against the code's attempt budget — an IP-only
        // throttle can be spread across a botnet, but this caps total guesses
        // per code no matter how many IPs try.
        $resets = $this->fetchTable('EmsPasswordResets');
        $reset = null;
        if ($user !== null) {
            $reset = $resets->find()
                ->where([
                    'user_id' => $user->id,
                    'used_at IS' => null,
                    'expires_at >=' => FrozenTime::now(),
                ])
                ->orderByDesc('created')
                ->first();
        }

        // No active reset, or its guess budget is spent — one uniform message.
        if ($user === null || $reset === null || (int)$reset->attempts >= self::RESET_MAX_ATTEMPTS) {
            $this->fail(400, Messages::RESET_INVALID);
        }

        // Constant-time comparison; every wrong guess burns one attempt.
        if (!hash_equals((string)$reset->code, $code)) {
            $reset->attempts = (int)$reset->attempts + 1;
            $resets->saveOrFail($reset);
            $this->fail(400, Messages::RESET_INVALID);
        }

        if (strlen($password) < 8) {
            $this->fail(422, Messages::PASSWORD_MIN);
        }

        $users = $this->fetchTable('EmsUsers');
        $users->getConnection()->transactional(function () use ($users, $resets, $user, $reset, $password) {
            $reset->used_at = FrozenTime::now(); // single-use: the code is burned
            $resets->saveOrFail($reset);

            $user->password_hash = password_hash($password, PASSWORD_DEFAULT);
            if ($user->status === 'invited') {
                // Resetting also finishes an un-redeemed invitation.
                $user->status = 'active';
                $user->invite_code = null;
                $user->invite_expires_at = null;
            }
            $users->saveOrFail($user);
        });

        return $this->authResult($user);
    }

    /**
     * Locate a pending invitation by its (uppercase-normalized) code.
     */
    private function findInvite(): EntityInterface
    {
        $code = strtoupper(trim((string)($this->body()['code'] ?? '')));
        if ($code === '') {
            $this->fail(422, Messages::INVITE_CODE_REQUIRED);
        }
        $user = $this->fetchTable('EmsUsers')->find()
            ->where([
                'invite_code' => Invitations::hash($code),
                'invite_expires_at >=' => FrozenTime::now(),
                'status' => 'invited',
            ])
            ->first();
        if ($user === null) {
            $this->fail(404, Messages::INVITE_CODE_UNKNOWN);
        }

        return $user;
    }

    /**
     * POST /auth/refresh — silent re-auth. Reads the httpOnly refresh cookie,
     * rotates it, and returns a fresh AuthResult with a new short-lived access
     * token. The cookie is the only credential: no Authorization header is used
     * (this is a public action). A dead/replayed token, or an account that is no
     * longer active, is answered 401 with the cookie cleared, so the SPA tears
     * the session down through the same path as an expired access token.
     */
    public function refresh(): Response
    {
        $this->rateLimit('refresh', 30);
        $tokens = $this->fetchTable('EmsRefreshTokens');
        $raw = (string)$this->request->getCookie(self::REFRESH_COOKIE);

        try {
            $rotated = RefreshTokens::rotate($tokens, $raw, time());
        } catch (RefreshDenied $e) {
            return $this->clearRefreshCookie($this->errorResponse($e->statusCode, $e->getMessage()));
        }

        // Authorization reads LIVE state here too (candidate #2): a disabled or
        // deleted account cannot refresh, and its just-rotated family is revoked
        // so a stolen cookie dies with it.
        $user = $this->fetchTable('EmsUsers')->find()->where(['id' => $rotated['userId']])->first();
        if ($user === null || $user->status !== 'active') {
            RefreshTokens::revoke($tokens, $rotated['token'], time());

            return $this->clearRefreshCookie(
                $this->errorResponse(401, 'Your session has expired. Please sign in again.')
            );
        }

        $school = $this->fetchTable('EmsSchools')->get($user->school_id);

        return $this->json([
            'user' => SettingsSerializer::user($user),
            'school' => SettingsSerializer::school($school),
            'token' => $this->accessToken($user),
        ])->withCookie($this->refreshCookie($rotated['token'], $rotated['expiresAt']));
    }

    /**
     * POST /auth/logout — revoke the presented refresh token's whole family
     * (this device's lineage) and clear the cookie. Idempotent; always 204.
     */
    public function logout(): Response
    {
        $raw = (string)$this->request->getCookie(self::REFRESH_COOKIE);
        if ($raw !== '') {
            RefreshTokens::revoke($this->fetchTable('EmsRefreshTokens'), $raw, time());
        }

        return $this->clearRefreshCookie($this->response->withStatus(204));
    }

    /**
     * AuthResult = { user, school } + additive access token. Every true session
     * start ($withRefresh) also mints a rotating refresh token and sets it in
     * the httpOnly cookie; inviteLookup passes false (it starts no session).
     */
    private function authResult(EntityInterface $user, bool $withRefresh = true): Response
    {
        $school = $this->fetchTable('EmsSchools')->get($user->school_id);

        $response = $this->json([
            'user' => SettingsSerializer::user($user),
            'school' => SettingsSerializer::school($school),
            'token' => $this->accessToken($user),
        ]);

        if ($withRefresh) {
            $issued = RefreshTokens::issue($this->fetchTable('EmsRefreshTokens'), (string)$user->id, time());
            $response = $response->withCookie($this->refreshCookie($issued['token'], $issued['expiresAt']));
        }

        return $response;
    }

    /**
     * Mint the short-lived access token the frontend holds in memory and sends
     * as `Authorization: Bearer`. `type: 'ems'` so v1 tokens can't cross over.
     *
     * Deliberately carries ONLY `sub` (who) + `type` (which surface). Since the
     * live-viewer resolver (candidate #2) reads role, status and school from the
     * ems_users row on every request, the token is trusted for identity alone —
     * so role/school/name are neither needed here nor safe to expose in a
     * client-readable token.
     */
    private function accessToken(EntityInterface $user): string
    {
        return Jwt::encode([
            'type' => 'ems',
            'sub' => (string)$user->id,
        ], null, time());
    }

    /**
     * The refresh cookie: HttpOnly (JS can never read it), Secure (HTTPS only —
     * localhost is browser-exempted in dev), SameSite=None so separately hosted
     * frontend domains can refresh, and path-scoped to the auth endpoints. The
     * base controller rejects browser origins outside the configured allow list.
     */
    private function refreshCookie(string $value, int $expiresAt): Cookie
    {
        return (new Cookie(self::REFRESH_COOKIE, $value))
            ->withPath(self::REFRESH_COOKIE_PATH)
            ->withExpiry(FrozenTime::createFromTimestamp($expiresAt))
            ->withHttpOnly(true)
            ->withSecure((bool)Configure::read('Ems.cookieSecure', true))
            ->withSameSite('None');
    }

    /** Attach an expired, same-attributes cookie so the browser drops it. */
    private function clearRefreshCookie(Response $response): Response
    {
        return $response->withExpiredCookie($this->refreshCookie('', 1));
    }

    /**
     * Same derivation as the frontend: lower-case, non-alphanumerics to
     * hyphens, trimmed.
     */
    private function slugify(string $name): string
    {
        $slug = strtolower(trim($name));
        $slug = (string)preg_replace('/[^a-z0-9]+/', '-', $slug);

        return trim($slug, '-');
    }
}
