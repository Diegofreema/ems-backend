<?php
declare(strict_types=1);

use Migrations\BaseMigration;

/**
 * Bill each class arm separately. A bulk invoice batch used to record only the
 * class NAMES it targets (ems_invoice_batches.class_groups), so a name shared by
 * two arms ("JSS 1A" twice in a level) invoiced both together. This adds the
 * canonical class-group id list the batch now bills by; the name list stays as
 * the human-readable snapshot. Nullable so batches drafted before this release
 * (which have no ids) keep resolving through their name list.
 *
 * ADD COLUMN is DDL, so the table's immutable-finance row triggers (which fire
 * only on row UPDATE/DELETE) are not involved.
 */
class AddInvoiceBatchClassGroupIds extends BaseMigration
{
    public function up(): void
    {
        $this->table('ems_invoice_batches')
            ->addColumn('class_group_ids', 'json', ['null' => true, 'default' => null, 'after' => 'class_groups'])
            ->update();
    }

    public function down(): void
    {
        $this->table('ems_invoice_batches')
            ->removeColumn('class_group_ids')
            ->update();
    }
}
