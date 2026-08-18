<?php
declare(strict_types=1);

namespace App\Ems;

/**
 * The authenticated principal a request is evaluated against (document.md §1.4).
 *
 * Built by Ems\AppController from the verified JWT claims — never from the
 * request body (hardening note §3.9 #1). `schoolId` is the tenant of the
 * CURRENT request (the path's schoolId once membership is asserted).
 */
final class Viewer
{
    /**
     * @var string
     */
    public string $schoolId;

    /**
     * @var string
     */
    public string $userId;

    /**
     * @var string One of: administrator|registrar|bursar|teacher|parent|student
     */
    public string $role;

    /**
     * @var string Display name, used as the audit `actor`.
     */
    public string $name;

    /**
     * @var string The current sign-in session's token-family id, carried in the
     *   access token's `sid` claim (stable across token rotation). Empty for a
     *   token minted before sessions were tracked. Used by the Account surface to
     *   mark and preserve "this device" among the active sessions.
     */
    public string $sessionId;

    public function __construct(
        string $schoolId,
        string $userId,
        string $role,
        string $name,
        string $sessionId = '',
    ) {
        $this->schoolId = $schoolId;
        $this->userId = $userId;
        $this->role = $role;
        $this->name = $name;
        $this->sessionId = $sessionId;
    }

    /**
     * Whole-school roles — relationship never narrows them (§1.4).
     */
    public function isOfficer(): bool
    {
        return in_array($this->role, ['administrator', 'registrar', 'bursar'], true);
    }
}
