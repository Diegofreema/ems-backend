<?php
declare(strict_types=1);

namespace App\Model\Table;

/**
 * EMS audit trail `ems_audit_events` — APPEND-ONLY (document.md §1.6).
 *
 * No Timestamp behavior (rows carry their own `at`) and deliberately no
 * update/delete helpers; nothing in the codebase may modify an audit row.
 */
class EmsAuditEventsTable extends EmsTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('ems_audit_events');
        $this->setPrimaryKey('id');
    }
}
