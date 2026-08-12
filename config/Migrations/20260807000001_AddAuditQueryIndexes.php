<?php
declare(strict_types=1);

use Migrations\BaseMigration;

/**
 * The audit trail (§3.23) is read newest-first by `seq`, filtered by school and
 * optionally by entity type. The existing indexes all lead with `at`, so
 * `WHERE school_id = ? ORDER BY seq DESC` had to filesort the whole tenant's
 * history on every page view — and ems_audit_events is append-only, so it only
 * ever grows. These two composites let the default and the type-filtered views
 * satisfy their WHERE + ORDER BY straight from the index (a backward scan the
 * LIMIT can stop early), instead of loading every row to sort in memory.
 */
class AddAuditQueryIndexes extends BaseMigration
{
    public function up(): void
    {
        $this->table('ems_audit_events')
            ->addIndex(['school_id', 'seq'], ['name' => 'idx_ems_audit_school_seq'])
            ->addIndex(['school_id', 'entity_type', 'seq'], ['name' => 'idx_ems_audit_school_type_seq'])
            ->update();
    }

    public function down(): void
    {
        $this->table('ems_audit_events')
            ->removeIndexByName('idx_ems_audit_school_seq')
            ->removeIndexByName('idx_ems_audit_school_type_seq')
            ->update();
    }
}
