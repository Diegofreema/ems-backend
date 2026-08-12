<?php
declare(strict_types=1);

use Migrations\BaseMigration;

/**
 * The audit trail (§1.6 / §3.23) is rendered newest-first, but a burst of
 * events written inside one request shares one second on `at` (DATETIME). UUID
 * PKs sort randomly, so a same-second burst (e.g. bulk `import.merged` rows, or
 * a privacy request verified→approved→fulfilled in quick succession) would order
 * non-deterministically. Add a monotonic `seq` to break ties by true insertion
 * order — the same fix ems_application_reviews carries. Internal only — never
 * serialized to the wire.
 */
class AddAuditSeq extends BaseMigration
{
    public function up(): void
    {
        $this->execute(
            'ALTER TABLE ems_audit_events '
            . 'ADD COLUMN seq BIGINT UNSIGNED NOT NULL AUTO_INCREMENT, '
            . 'ADD UNIQUE KEY idx_ems_audit_seq (seq)'
        );
    }

    public function down(): void
    {
        $this->execute('ALTER TABLE ems_audit_events DROP COLUMN seq');
    }
}
