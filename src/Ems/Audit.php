<?php
declare(strict_types=1);

namespace App\Ems;

use Cake\I18n\FrozenTime;
use Cake\ORM\Locator\LocatorInterface;

/**
 * Append-only audit writer (document.md §1.6).
 *
 * Every regulated mutation calls log() with a dotted action key from the
 * catalog in document.md §4. Rows are never updated or deleted. Never place
 * password material, tokens, or payment instruments in audit data.
 */
class Audit
{
    /**
     * @var \Cake\ORM\Locator\LocatorInterface
     */
    private $locator;

    /**
     * @var string
     */
    private $requestId;

    /**
     * @var string
     */
    private $ipAddress;

    public function __construct(LocatorInterface $locator, string $requestId = '', string $ipAddress = '')
    {
        $this->locator = $locator;
        $this->requestId = $requestId;
        $this->ipAddress = $ipAddress;
    }

    public function log(
        Viewer $viewer,
        string $action,
        string $entityType,
        string $entityId,
        string $summary,
        ?string $reason = null,
        string $outcome = 'success',
        ?array $before = null,
        ?array $after = null
    ): void {
        $events = $this->locator->get('EmsAuditEvents');
        [$keyId, $key] = FinanceKeys::active();
        // Serialize both signed chains per tenant. Finance callers are already
        // inside a database transaction, so this lock is held through commit.
        $this->locator->get('EmsSchools')->find()
            ->select(['id'])->where(['id' => $viewer->schoolId])->epilog('FOR UPDATE')->firstOrFail();
        $previous = $events->find()
            ->select(['event_hash'])
            ->where(['school_id' => $viewer->schoolId, 'event_hash IS NOT' => null])
            ->orderByDesc('seq')
            ->first();
        $previousHash = $previous !== null ? (string)$previous->event_hash : str_repeat('0', 64);
        $at = FrozenTime::now('UTC');
        $canonical = json_encode([
            'schoolId' => $viewer->schoolId,
            'actorUserId' => $viewer->userId,
            'actor' => $viewer->name,
            'role' => $viewer->role,
            'requestId' => $this->requestId,
            'ipAddress' => $this->ipAddress,
            'action' => $action,
            'outcome' => $outcome,
            'entityType' => $entityType,
            'entityId' => $entityId,
            'summary' => $summary,
            'reason' => $reason,
            'before' => $before,
            'after' => $after,
            'at' => $at->format('Y-m-d H:i:s'),
            'previousHash' => $previousHash,
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $event = $events->newEntity([
            'school_id' => $viewer->schoolId,
            'actor_user_id' => $viewer->userId,
            'actor' => $viewer->name,
            'actor_role' => $viewer->role,
            'request_id' => $this->requestId,
            'ip_address' => $this->ipAddress,
            'action' => $action,
            'outcome' => $outcome,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'summary' => $summary,
            'reason' => $reason,
            'before_value' => $before,
            'after_value' => $after,
            'key_id' => $keyId,
            'previous_hash' => $previousHash,
            'event_hash' => hash_hmac('sha256', (string)$canonical, $key),
            'at' => $at,
        ]);
        $events->saveOrFail($event);
    }
}
