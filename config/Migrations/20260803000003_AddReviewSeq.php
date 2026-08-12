<?php
declare(strict_types=1);

use Migrations\BaseMigration;

/**
 * The append-only review trail is rendered newest-first, but every review in a
 * same-day burst shares one `decided_on` date (§3.6 stores a DATE). The mock
 * broke ties with monotonic string ids; UUID PKs sort randomly, so we add a
 * monotonic `seq` to order insertion deterministically. Internal only — never
 * serialized to the wire.
 */
class AddReviewSeq extends BaseMigration
{
    public function up(): void
    {
        $this->execute(
            'ALTER TABLE ems_application_reviews '
            . 'ADD COLUMN seq BIGINT UNSIGNED NOT NULL AUTO_INCREMENT, '
            . 'ADD UNIQUE KEY idx_ems_appreviews_seq (seq)'
        );
    }

    public function down(): void
    {
        $this->execute('ALTER TABLE ems_application_reviews DROP COLUMN seq');
    }
}
