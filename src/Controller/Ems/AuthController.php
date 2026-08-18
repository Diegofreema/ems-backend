<?php
declare(strict_types=1);

namespace App\Controller\Ems;

use App\Api\Jwt;
use App\Ems\Email;
use App\Ems\EmailChanges;
use App\Ems\EmailVerifications;
use App\Ems\Invitations;
use App\Ems\LoginChallenges;
use App\Ems\Messages;
use App\Ems\RateLimited;
use App\Ems\RateLimiter;
use App\Ems\RefreshDenied;
use App\Ems\RefreshTokens;
use App\Ems\Resend;
use App\Ems\Serializer\SettingsSerializer;
use App\Ems\SubjectCatalog;
use App\Ems\TrustedDevices;
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
        'signIn', 'signInVerify', 'registerSchool', 'inviteLookup', 'inviteAccept',
        'resetRequest', 'resetConfirm', 'refresh', 'logout',
        'verifyEmail', 'verifyResend', 'emailChangeVerify',
    ];

    /** Verification e-mails allowed per address per window (register + resends
     *  combined) before further sends are silently skipped — the inbox already
     *  holds a live link, and only the newest one works. */
    private const VERIFY_SEND_LIMIT = 3;
    private const VERIFY_SEND_WINDOW = 900;

    /** Wrong guesses allowed against one reset code before it is dead. */
    private const RESET_MAX_ATTEMPTS = 5;

    /** Failed sign-ins allowed against ONE email before it is throttled, and the
     *  window in seconds. Bounds distributed (many-IP) brute force of a single
     *  account, on top of the per-IP 'signin' bucket. Only FAILURES count, so a
     *  correct password is never blocked — a targeted victim cannot be locked
     *  out, only an attacker's continued guessing is. */
    private const SIGNIN_EMAIL_LIMIT = 10;
    private const SIGNIN_EMAIL_WINDOW = 900;

    /** A real bcrypt hash of a throwaway string, verified against on the
     *  unknown-email / no-password paths so every sign-in spends exactly one
     *  bcrypt. Without it, skipping password_verify() when no account matches
     *  leaks account existence through response timing. */
    private const TIMING_HASH = '$2y$12$5Tbz.P3DtuwbAElSdbT68eGfKYShQj3gNtXQFHj3kHiaumrSRQgoK';

    /** The httpOnly refresh cookie's name and path (scoped to the auth endpoints). */
    private const REFRESH_COOKIE = 'ems_refresh';
    private const REFRESH_COOKIE_PATH = '/api/ems/auth';

    /** The httpOnly "remember this device" cookie for 2FA — same path/attrs as
     *  the refresh cookie, but deliberately outlives logout (30-day trust). */
    private const DEVICE_COOKIE = 'ems_device';

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

        $hash = $user?->password_hash;
        if ($hash === null || !password_verify($password, (string)$hash)) {
            // Constant-time on the miss paths: an unknown e-mail (or an account
            // with no password yet) still spends one bcrypt, so response timing
            // never distinguishes "no such account" from "wrong password".
            if ($hash === null) {
                password_verify($password, self::TIMING_HASH);
            }
            // Count this failure against the account (all source IPs). Only
            // failures are counted — the success branch below never touches this
            // bucket — so a correct password can never be throttled.
            if ($email !== '') {
                RateLimiter::hit('signin_email', $email, self::SIGNIN_EMAIL_LIMIT, self::SIGNIN_EMAIL_WINDOW);
            }
            // Same message for unknown e-mail and wrong password (§3.18).
            $this->fail(401, Messages::BAD_CREDENTIALS);
        }

        // The password is correct — only NOW is it safe to reveal account state.
        // An anonymous prober without the password can never reach this branch,
        // so 'invited'/'disabled' no longer leak that an address is registered.
        if ($user->status === 'invited') {
            $this->fail(403, Messages::ACCOUNT_INVITED);
        }
        if ($user->status === 'disabled') {
            $this->fail(403, Messages::ACCOUNT_DISABLED);
        }
        // Self-served registrations must prove their address before first
        // sign-in. The correct password re-earns a fresh 30-minute link, so a
        // creator who lost (or outlived) the original e-mail is never stuck.
        if ($user->email_verified_at === null) {
            $this->sendVerification($user);
            $this->fail(403, Messages::EMAIL_UNVERIFIED);
        }

        // Second factor: a 2FA account on an UNtrusted device does not get a
        // session yet — it gets a mailed code and a challenge to redeem at
        // /auth/sign-in/verify. A device the user chose to remember (a live
        // trust cookie) skips straight through.
        if ($user->two_factor_enabled && !$this->deviceIsTrusted($user)) {
            $issued = LoginChallenges::issue($this->fetchTable('EmsLoginChallenges'), (string)$user->id);
            $this->sendLoginCode($user, $issued['code']);

            return $this->json([
                'twoFactorRequired' => true,
                'challengeId' => $issued['challengeId'],
                'email' => $this->maskEmail((string)$user->email),
            ]);
        }

        return $this->authResult($user);
    }

    /**
     * POST /auth/sign-in/verify { challengeId, code, rememberDevice? } — finish a
     * 2FA sign-in by redeeming the mailed code for a session. `rememberDevice`
     * sets a 30-day trust cookie so this browser can skip the code next time.
     */
    public function signInVerify(): Response
    {
        $this->rateLimit('signin_verify', 10);
        $body = $this->body();
        $challengeId = trim((string)($body['challengeId'] ?? ''));
        $code = trim((string)($body['code'] ?? ''));

        $userId = LoginChallenges::verify($this->fetchTable('EmsLoginChallenges'), $challengeId, $code);
        if ($userId === null) {
            $this->fail(400, Messages::TWO_FACTOR_CHALLENGE_INVALID);
        }

        // Re-read live account state: the challenge is only a factor, never
        // authority — a since-disabled or since-unverified account cannot pass.
        $user = $this->fetchTable('EmsUsers')->find()->where(['id' => $userId])->first();
        if ($user === null || $user->status !== 'active' || $user->email_verified_at === null) {
            $this->fail(400, Messages::TWO_FACTOR_CHALLENGE_INVALID);
        }

        $response = $this->authResult($user);
        if (!empty($body['rememberDevice'])) {
            $meta = $this->deviceMeta();
            $device = TrustedDevices::issue(
                $this->fetchTable('EmsTrustedDevices'),
                (string)$user->id,
                $meta['userAgent'],
                time(),
            );
            $response = $response->withCookie($this->deviceCookie($device['token'], $device['expiresAt']));
        }

        return $response;
    }

    /**
     * POST /auth/email-change/verify { token } — confirm a login-e-mail change
     * from the link mailed to the NEW address. Swaps the address and marks it
     * verified (the link IS the proof). Starts no session; the user's existing
     * sessions continue, and a logged-out opener is sent to sign in.
     */
    public function emailChangeVerify(): Response
    {
        $this->rateLimit('email_change_verify', 10);
        $token = trim((string)($this->body()['token'] ?? ''));
        if ($token === '') {
            $this->fail(400, Messages::EMAIL_CHANGE_LINK_INVALID);
        }

        $changes = $this->fetchTable('EmsEmailChanges');
        $row = $changes->find()
            ->where(['token' => EmailChanges::hash($token), 'used_at IS' => null, 'expires_at >=' => FrozenTime::now()])
            ->first();
        if ($row === null) {
            $this->fail(400, Messages::EMAIL_CHANGE_LINK_INVALID);
        }

        $users = $this->fetchTable('EmsUsers');
        $user = $users->find()->where(['id' => $row->user_id])->first();
        if ($user === null || $user->status === 'disabled') {
            $this->fail(400, Messages::EMAIL_CHANGE_LINK_INVALID);
        }

        $newEmail = strtolower((string)$row->new_email);
        // The address may have been claimed by someone else since the request.
        if ($users->exists(['LOWER(email)' => $newEmail, 'id !=' => $user->id])) {
            $this->fail(422, Messages::EMAIL_EXISTS);
        }

        $users->getConnection()->transactional(function () use ($users, $changes, $user, $row, $newEmail): void {
            $row->used_at = FrozenTime::now(); // single-use: the link is burned
            $changes->saveOrFail($row);
            $user->email = $newEmail;
            $user->email_verified_at = $user->email_verified_at ?? FrozenTime::now();
            $users->saveOrFail($user);
        });

        return $this->json(['changed' => true, 'email' => $newEmail]);
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
                // Verified only once the mailed link is opened (§3.18). Until
                // then sign-in is refused and re-sends a fresh link.
                'email_verified_at' => null,
            ]));
        });

        // No session yet — the creator signs in after opening the mailed link.
        // Delivery is best-effort: if it fails, the first sign-in attempt
        // re-sends, so registration itself never bounces on a mail outage.
        $this->sendVerification($user);

        return $this->json([
            'verificationRequired' => true,
            'email' => $email,
            'message' => Messages::VERIFY_CHECK_INBOX,
        ]);
    }

    /**
     * POST /auth/verify-email { token } — prove the address from the mailed
     * link. Burns the token, stamps the account verified, sends the welcome
     * message, and starts the first real session.
     */
    public function verifyEmail(): Response
    {
        $this->rateLimit('verify_email', 10);
        $token = trim((string)($this->body()['token'] ?? ''));
        if ($token === '') {
            $this->fail(400, Messages::VERIFY_LINK_INVALID);
        }

        $verifications = $this->fetchTable('EmsEmailVerifications');
        $row = $verifications->find()
            ->where([
                'token' => EmailVerifications::hash($token),
                'used_at IS' => null,
                'expires_at >=' => FrozenTime::now(),
            ])
            ->first();
        if ($row === null) {
            $this->fail(400, Messages::VERIFY_LINK_INVALID);
        }

        $users = $this->fetchTable('EmsUsers');
        $user = $users->find()->where(['id' => $row->user_id])->firstOrFail();
        if ($user->status === 'disabled') {
            $this->fail(403, Messages::ACCOUNT_DISABLED);
        }

        $firstVerification = $user->email_verified_at === null;
        $users->getConnection()->transactional(function () use ($users, $verifications, $user, $row): void {
            $row->used_at = FrozenTime::now(); // single-use: the link is burned
            $verifications->saveOrFail($row);
            if ($user->email_verified_at === null) {
                $user->email_verified_at = FrozenTime::now();
                $users->saveOrFail($user);
            }
        });

        // The welcome message is a courtesy — never fail the verification
        // (the account IS verified now) over a mail outage.
        if ($firstVerification) {
            try {
                EmailVerifications::deliverWelcome($user, $this->fetchTable('EmsSchools')->get($user->school_id));
            } catch (Throwable $e) {
                Log::error(sprintf(
                    'EMS welcome delivery failed for user %s: %s',
                    (string)$user->id,
                    $e->getMessage(),
                ));
            }
        }

        return $this->authResult($user);
    }

    /**
     * POST /auth/verify-email/resend { email } — always { sent: true }; never
     * reveals whether the address exists or its verification state (§3.18).
     */
    public function verifyResend(): Response
    {
        $this->rateLimit('verify_resend', 5);
        $email = strtolower(trim((string)($this->body()['email'] ?? '')));

        if ($email !== '') {
            $user = $this->fetchTable('EmsUsers')->find()
                ->where(['LOWER(email)' => $email])
                ->first();
            if ($user !== null && $user->status === 'active' && $user->email_verified_at === null) {
                $this->sendVerification($user);
            }
        }

        return $this->json(['sent' => true]);
    }

    /**
     * Issue a fresh verification link and mail it, best-effort. Per-address
     * throttled across register/sign-in/resend so a stranger typing someone
     * else's address cannot flood their inbox; within the budget each send
     * retires earlier links, so only the newest one works.
     */
    private function sendVerification(EntityInterface $user): void
    {
        try {
            RateLimiter::hit(
                'verify_send',
                strtolower((string)$user->email),
                self::VERIFY_SEND_LIMIT,
                self::VERIFY_SEND_WINDOW,
            );
        } catch (RateLimited) {
            return; // a live link is already in the inbox — do not spam it
        }

        try {
            $issued = EmailVerifications::issue($this->fetchTable('EmsEmailVerifications'), (string)$user->id);
            EmailVerifications::deliver(
                $user,
                $this->fetchTable('EmsSchools')->get($user->school_id),
                $issued['raw'],
            );
        } catch (Throwable $e) {
            Log::error(sprintf(
                'EMS verification delivery failed for user %s: %s',
                (string)$user->id,
                $e->getMessage(),
            ));
        }
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
        // Redeeming a code that was mailed to this address IS the proof of the
        // mailbox — invited accounts never need a separate verification step.
        $user->email_verified_at = $user->email_verified_at ?? FrozenTime::now();
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
                $code = $this->newResetCode();
                $resets = $this->fetchTable('EmsPasswordResets');
                $reset = $resets->saveOrFail($resets->newEntity([
                    'user_id' => $user->id,
                    'code' => $code,
                    'expires_at' => FrozenTime::now()->addMinutes(30),
                ]));
                try {
                    $school = $this->fetchTable('EmsSchools')->get($user->school_id);
                    $message = Email::passwordReset(
                        (string)$school->name,
                        (string)$user->name,
                        $code,
                    );
                    Resend::deliver(
                        (string)$user->email,
                        sprintf('Reset your %s EMS password', (string)$school->name),
                        $message['text'],
                        $message['html'],
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
        // Codes are issued uppercase; normalise so case never fails a valid entry.
        $code = strtoupper(trim((string)($body['code'] ?? '')));
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
        $users->getConnection()->transactional(function () use ($users, $resets, $user, $reset, $password): void {
            $reset->used_at = FrozenTime::now(); // single-use: the code is burned
            $resets->saveOrFail($reset);

            $user->password_hash = password_hash($password, PASSWORD_DEFAULT);
            if ($user->status === 'invited') {
                // Resetting also finishes an un-redeemed invitation.
                $user->status = 'active';
                $user->invite_code = null;
                $user->invite_expires_at = null;
            }
            // The reset code reached this mailbox, which is exactly what
            // verification proves — stamp it if it was still outstanding.
            $user->email_verified_at = $user->email_verified_at ?? FrozenTime::now();
            $users->saveOrFail($user);
        });

        return $this->authResult($user);
    }

    /**
     * A single-use password-reset code: 8 chars from an unambiguous uppercase
     * alphanumeric alphabet (no 0/O, 1/I/L). ~40 bits of entropy — a large jump
     * over the old 6-digit numeric code — on top of the 5-guess-per-code cap, so
     * the code is safe against online guessing without a hard lockout.
     */
    private function newResetCode(): string
    {
        $alphabet = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';
        $max = strlen($alphabet) - 1;
        $code = '';
        for ($i = 0; $i < 8; $i++) {
            $code .= $alphabet[random_int(0, $max)];
        }

        return $code;
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
                $this->errorResponse(401, 'Your session has expired. Please sign in again.'),
            );
        }

        $school = $this->fetchTable('EmsSchools')->get($user->school_id);

        return $this->json([
            'user' => SettingsSerializer::user($user),
            'school' => SettingsSerializer::school($school),
            'token' => $this->accessToken($user, (string)$rotated['familyId']),
        ])->withCookie($this->refreshCookie($rotated['token'], $rotated['expiresAt']));
    }

    /**
     * POST /auth/logout — revoke the presented refresh token's whole family
     * (this device's lineage) and clear the cookie. Idempotent; always 204.
     */
    public function logout(): Response
    {
        // Cookie-only + SameSite=None → require a trusted Origin so a cross-site
        // page cannot force-revoke the victim's session (CSRF nuisance logout).
        $this->assertBrowserOrigin();
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

        // The session's family id becomes the access token's `sid` claim, so the
        // Account surface can mark and preserve "this device" without ever seeing
        // the path-scoped refresh cookie. inviteLookup starts no session → no sid.
        $familyId = '';
        $refreshCookie = null;
        if ($withRefresh) {
            $meta = $this->deviceMeta();
            $issued = RefreshTokens::issue(
                $this->fetchTable('EmsRefreshTokens'),
                (string)$user->id,
                time(),
                null,
                $meta['userAgent'],
                $meta['ip'],
            );
            $familyId = $issued['familyId'];
            $refreshCookie = $this->refreshCookie($issued['token'], $issued['expiresAt']);
        }

        $response = $this->json([
            'user' => SettingsSerializer::user($user),
            'school' => SettingsSerializer::school($school),
            'token' => $this->accessToken($user, $familyId),
        ]);

        if ($refreshCookie !== null) {
            $response = $response->withCookie($refreshCookie);
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
    private function accessToken(EntityInterface $user, string $familyId = ''): string
    {
        $claims = [
            'type' => 'ems',
            'sub' => (string)$user->id,
        ];
        // `sid` names the sign-in session (its refresh-token family), stable
        // across rotation, so the Account surface knows which device is "this
        // one". Identity-only, never authorization (which is read live per
        // request from the ems_users row).
        if ($familyId !== '') {
            $claims['sid'] = $familyId;
        }

        return Jwt::encode($claims, null, time());
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
     * The "remember this device" cookie: same httpOnly/Secure/SameSite=None/path
     * as the refresh cookie so it is sent on sign-in, but a 30-day life that
     * deliberately OUTLIVES logout — the whole point of a trusted device.
     */
    private function deviceCookie(string $value, int $expiresAt): Cookie
    {
        return (new Cookie(self::DEVICE_COOKIE, $value))
            ->withPath(self::REFRESH_COOKIE_PATH)
            ->withExpiry(FrozenTime::createFromTimestamp($expiresAt))
            ->withHttpOnly(true)
            ->withSecure((bool)Configure::read('Ems.cookieSecure', true))
            ->withSameSite('None');
    }

    /** Does the request carry a live 2FA trust cookie for this account? */
    private function deviceIsTrusted(EntityInterface $user): bool
    {
        $token = (string)$this->request->getCookie(self::DEVICE_COOKIE);

        return $token !== '' && TrustedDevices::isTrusted(
            $this->fetchTable('EmsTrustedDevices'),
            $token,
            (string)$user->id,
            time(),
        );
    }

    /** The device metadata captured for a new session's refresh token / trust. */
    private function deviceMeta(): array
    {
        $userAgent = trim((string)$this->request->getHeaderLine('User-Agent'));

        return [
            'userAgent' => $userAgent !== '' ? $userAgent : null,
            'ip' => $this->clientIp(),
        ];
    }

    /** Mail a 2FA sign-in code, best-effort: a retry of sign-in simply re-sends. */
    private function sendLoginCode(EntityInterface $user, string $code): void
    {
        try {
            $school = $this->fetchTable('EmsSchools')->get($user->school_id);
            $message = Email::loginCode((string)$school->name, (string)$user->name, $code);
            Resend::deliver(
                (string)$user->email,
                sprintf('Your %s EMS sign-in code', (string)$school->name),
                $message['text'],
                $message['html'],
            );
        } catch (Throwable $e) {
            Log::error(sprintf('EMS 2FA code delivery failed for user %s: %s', (string)$user->id, $e->getMessage()));
        }
    }

    /** A privacy-preserving hint of where a code was sent: `j•••@school.com`. */
    private function maskEmail(string $email): string
    {
        $at = strpos($email, '@');
        if ($at === false || $at < 1) {
            return $email;
        }

        return substr($email, 0, 1)
            . str_repeat('•', max(1, min(6, $at - 1)))
            . substr($email, $at);
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
