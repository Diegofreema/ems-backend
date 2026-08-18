<?php
declare(strict_types=1);

namespace App\Controller\Ems;

use App\Ems\Email;
use App\Ems\EmailChanges;
use App\Ems\LoginChallenges;
use App\Ems\Messages;
use App\Ems\RefreshTokens;
use App\Ems\Resend;
use App\Ems\Serializer\SettingsSerializer;
use App\Ems\TrustedDevices;
use Cake\Datasource\EntityInterface;
use Cake\Http\Response;
use Cake\Log\Log;
use Throwable;

/**
 * Self-service "Account & security" surface (document.md §3.18). Every action
 * operates on the AUTHENTICATED viewer's own account — never a body-supplied id
 * — so the Policy SELF tier (which admits every role) is safe: the row reach is
 * the viewer themselves. Sign-in-flow changes (the 2FA challenge, the e-mail
 * change confirm link) are public and live in AuthController.
 *
 * The account's durable credential is unchanged: a rotating refresh token in an
 * httpOnly cookie. These endpoints are Bearer-authenticated (no cookie needed),
 * and identify "this device" among the active sessions from the access token's
 * `sid` (session/family) claim carried on Viewer.
 */
class AccountController extends AppController
{
    /** Avatar images are inlined as data URLs; cap the decoded bytes at 512 KB. */
    private const AVATAR_MAX_BYTES = 512 * 1024;

    /**
     * GET /me/account — the viewer's own account, including phone, avatar and
     * the two-factor flag.
     */
    public function show(): Response
    {
        return $this->json(SettingsSerializer::user($this->me()));
    }

    /**
     * PATCH /me/account/profile { name, phone?, avatar? } — edit display name,
     * phone and avatar. `phone`/`avatar` are only touched when present in the
     * body; a null or empty value clears them.
     */
    public function updateProfile(): Response
    {
        $body = $this->body();
        $me = $this->me();

        $name = trim((string)($body['name'] ?? $me->name));
        if ($name === '') {
            $this->fail(422, Messages::PROFILE_NAME_REQUIRED);
        }
        $me->name = $name;

        if (array_key_exists('phone', $body)) {
            $me->phone = $this->normalizePhone($body['phone']);
        }
        if (array_key_exists('avatar', $body)) {
            $me->avatar = $this->normalizeAvatar($body['avatar']);
        }

        $this->fetchTable('EmsUsers')->saveOrFail($me);

        return $this->json(SettingsSerializer::user($me));
    }

    /**
     * POST /me/account/password { currentPassword, newPassword } — change the
     * password after proving the current one, then sign out every OTHER device.
     */
    public function changePassword(): Response
    {
        $this->rateLimit('account_password', 10);
        $body = $this->body();
        $me = $this->me();

        $this->assertCurrentPassword($me, (string)($body['currentPassword'] ?? ''));

        $newPassword = (string)($body['newPassword'] ?? '');
        if (strlen($newPassword) < 8) {
            $this->fail(422, Messages::PASSWORD_MIN);
        }
        if (password_verify($newPassword, (string)$me->password_hash)) {
            $this->fail(422, Messages::PASSWORD_SAME_AS_OLD);
        }

        $me->password_hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $this->fetchTable('EmsUsers')->saveOrFail($me);

        // Assume compromise: revoke every session except the one making the
        // change. Their refresh tokens die, so other devices fall to sign-in on
        // their next silent refresh.
        RefreshTokens::revokeOthersFor(
            $this->fetchTable('EmsRefreshTokens'),
            (string)$me->id,
            $this->viewer->sessionId,
            time(),
        );

        return $this->json(['changed' => true]);
    }

    /**
     * POST /me/account/email { currentPassword, newEmail } — request a login
     * e-mail change. Requires the current password and mails a confirm link to
     * the NEW address; the swap only happens when that link is opened
     * (AuthController::emailChangeVerify). The old address keeps working.
     */
    public function requestEmailChange(): Response
    {
        $this->rateLimit('account_email', 5, 900);
        $body = $this->body();
        $me = $this->me();

        $this->assertCurrentPassword($me, (string)($body['currentPassword'] ?? ''));

        $newEmail = strtolower(trim((string)($body['newEmail'] ?? '')));
        if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
            $this->fail(422, Messages::EMAIL_INVALID);
        }
        if ($newEmail === strtolower((string)$me->email)) {
            $this->fail(422, Messages::EMAIL_CHANGE_SAME);
        }
        if ($this->fetchTable('EmsUsers')->exists(['LOWER(email)' => $newEmail])) {
            $this->fail(422, Messages::EMAIL_EXISTS);
        }

        $issued = EmailChanges::issue($this->fetchTable('EmsEmailChanges'), (string)$me->id, $newEmail);
        try {
            EmailChanges::deliver(
                $me,
                $this->fetchTable('EmsSchools')->get($me->school_id),
                $newEmail,
                $issued['raw'],
            );
        } catch (Throwable $e) {
            Log::error(sprintf('EMS email-change delivery failed for user %s: %s', (string)$me->id, $e->getMessage()));
            $this->fail(502, Messages::EMAIL_SEND_FAILED);
        }

        return $this->json(['sent' => true, 'email' => $newEmail]);
    }

    /**
     * POST /me/account/2fa/enable — begin turning on email 2FA by mailing a
     * confirmation code the user must enter next.
     */
    public function enableTwoFactor(): Response
    {
        $this->rateLimit('account_2fa_enable', 5, 900);
        $me = $this->me();
        if ($me->two_factor_enabled) {
            $this->fail(422, Messages::TWO_FACTOR_ALREADY_ON);
        }

        $issued = LoginChallenges::issue($this->fetchTable('EmsLoginChallenges'), (string)$me->id);
        $this->deliverCode($me, $issued['code']);

        return $this->json(['sent' => true]);
    }

    /**
     * POST /me/account/2fa/confirm { code } — finish enabling 2FA by proving the
     * mailed code arrived.
     */
    public function confirmTwoFactor(): Response
    {
        $this->rateLimit('account_2fa_confirm', 10);
        $me = $this->me();
        if ($me->two_factor_enabled) {
            $this->fail(422, Messages::TWO_FACTOR_ALREADY_ON);
        }
        $code = trim((string)($this->body()['code'] ?? ''));
        if (!LoginChallenges::verifyForUser($this->fetchTable('EmsLoginChallenges'), (string)$me->id, $code)) {
            $this->fail(422, Messages::TWO_FACTOR_CODE_INVALID);
        }

        $me->two_factor_enabled = true;
        $this->fetchTable('EmsUsers')->saveOrFail($me);

        return $this->json(SettingsSerializer::user($me));
    }

    /**
     * POST /me/account/2fa/disable { currentPassword } — turn off 2FA (password
     * required) and forget every remembered device, since there is no second
     * factor left for them to skip.
     */
    public function disableTwoFactor(): Response
    {
        $this->rateLimit('account_2fa_disable', 10);
        $me = $this->me();
        if (!$me->two_factor_enabled) {
            $this->fail(422, Messages::TWO_FACTOR_ALREADY_OFF);
        }
        $this->assertCurrentPassword($me, (string)($this->body()['currentPassword'] ?? ''));

        $me->two_factor_enabled = false;
        $this->fetchTable('EmsUsers')->saveOrFail($me);
        TrustedDevices::forgetAll($this->fetchTable('EmsTrustedDevices'), (string)$me->id);

        return $this->json(SettingsSerializer::user($me));
    }

    /**
     * GET /me/account/sessions — the account's active sign-in sessions, one per
     * device, with "this device" flagged.
     */
    public function sessions(): Response
    {
        $items = RefreshTokens::sessions(
            $this->fetchTable('EmsRefreshTokens'),
            (string)$this->viewer->userId,
            time(),
            $this->viewer->sessionId,
        );

        return $this->json(['items' => $items]);
    }

    /**
     * DELETE /me/account/sessions/{id} — sign out one device (its token family).
     * Scoped to the viewer, so it can only ever end one of their own sessions.
     */
    public function revokeSession(string $id): Response
    {
        $revoked = RefreshTokens::revokeFamilyFor(
            $this->fetchTable('EmsRefreshTokens'),
            (string)$this->viewer->userId,
            $id,
            time(),
        );
        if (!$revoked) {
            $this->fail(404, Messages::DEVICE_SESSION_NOT_FOUND);
        }

        return $this->json(['revoked' => true]);
    }

    /**
     * POST /me/account/sessions/revoke-others — sign out every device except
     * this one.
     */
    public function revokeOtherSessions(): Response
    {
        RefreshTokens::revokeOthersFor(
            $this->fetchTable('EmsRefreshTokens'),
            (string)$this->viewer->userId,
            $this->viewer->sessionId,
            time(),
        );

        return $this->json(['revoked' => true]);
    }

    /**
     * DELETE /me/account/trusted-devices — forget every remembered 2FA device,
     * so each will ask for a code again next sign-in.
     */
    public function forgetTrustedDevices(): Response
    {
        TrustedDevices::forgetAll($this->fetchTable('EmsTrustedDevices'), (string)$this->viewer->userId);

        return $this->json(['forgotten' => true]);
    }

    // --- helpers --------------------------------------------------------------

    /** The viewer's own live ems_users row. */
    private function me(): EntityInterface
    {
        return $this->fetchTable('EmsUsers')->get($this->viewer->userId);
    }

    /** Refuse unless `$password` matches the account's current hash. */
    private function assertCurrentPassword(EntityInterface $me, string $password): void
    {
        if ($me->password_hash === null || !password_verify($password, (string)$me->password_hash)) {
            $this->fail(422, Messages::CURRENT_PASSWORD_WRONG);
        }
    }

    /** Mail a 2FA code, failing loudly if delivery is impossible. */
    private function deliverCode(EntityInterface $me, string $code): void
    {
        try {
            $school = $this->fetchTable('EmsSchools')->get($me->school_id);
            $message = Email::loginCode((string)$school->name, (string)$me->name, $code);
            Resend::deliver(
                (string)$me->email,
                sprintf('Your %s EMS sign-in code', (string)$school->name),
                $message['text'],
                $message['html'],
            );
        } catch (Throwable $e) {
            Log::error(sprintf('EMS 2FA code delivery failed for user %s: %s', (string)$me->id, $e->getMessage()));
            $this->fail(502, Messages::EMAIL_SEND_FAILED);
        }
    }

    /** A phone number, trimmed; null when blank; 422 when clearly not a number. */
    private function normalizePhone(mixed $value): ?string
    {
        $phone = trim((string)$value);
        if ($phone === '') {
            return null;
        }
        if (!preg_match('/^[+()\d][()\d\s-]{4,19}$/', $phone)) {
            $this->fail(422, Messages::PHONE_INVALID);
        }

        return $phone;
    }

    /** A validated avatar data URL, null to clear, or 422 when unusable. */
    private function normalizeAvatar(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }
        $avatar = (string)$value;
        if ($avatar === '') {
            return null;
        }
        if (!preg_match('#^data:image/[a-zA-Z0-9.+-]+;base64,#', $avatar)) {
            $this->fail(422, Messages::AVATAR_INVALID);
        }
        $decoded = base64_decode(substr($avatar, strpos($avatar, ',') + 1), true);
        if ($decoded === false) {
            $this->fail(422, Messages::AVATAR_INVALID);
        }
        if (strlen($decoded) > self::AVATAR_MAX_BYTES) {
            $this->fail(422, Messages::AVATAR_TOO_LARGE);
        }

        return $avatar;
    }
}
