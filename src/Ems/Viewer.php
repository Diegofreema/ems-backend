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

    public function __construct(string $schoolId, string $userId, string $role, string $name)
    {
        $this->schoolId = $schoolId;
        $this->userId = $userId;
        $this->role = $role;
        $this->name = $name;
    }

    /**
     * Whole-school roles — relationship never narrows them (§1.4).
     */
    public function isOfficer(): bool
    {
        return in_array($this->role, ['administrator', 'registrar', 'bursar'], true);
    }
}
