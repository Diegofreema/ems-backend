<?php
declare(strict_types=1);

use Migrations\BaseMigration;

/**
 * Soft-archive for guardians. A guardian is student-related data, which a school
 * may never hard-delete (the register's archival rule). DELETE /guardians/{id}
 * now stamps this column instead of destroying the row, and EmsGuardiansTable's
 * default scope hides archived rows from every read — so denormalized contacts,
 * messaging audiences and reports behave exactly as if the guardian were gone,
 * while the record itself survives.
 */
class AddGuardianArchivedAt extends BaseMigration
{
    public function up(): void
    {
        $this->table('ems_guardians')
            ->addColumn('archived_at', 'datetime', ['null' => true, 'default' => null])
            ->update();
    }

    public function down(): void
    {
        $this->table('ems_guardians')
            ->removeColumn('archived_at')
            ->update();
    }
}
