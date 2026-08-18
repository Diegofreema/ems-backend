<?php
declare(strict_types=1);

use Migrations\BaseMigration;

/**
 * Internal notes on a demo request — the activity trail platform staff keep as
 * they work a lead (the CRM-lite inbox, §ems platform surface).
 *
 * Like `ems_demo_requests`, this table is OUTSIDE the tenant model: a lead has no
 * school, and the author is a platform-staff account, not a school user. The
 * author's display name is denormalised onto the row (as `ems_attendance_sessions`
 * does with `submitted_by`) so the timeline renders without a join back to a
 * mutable account.
 */
class CreateEmsDemoRequestNotes extends BaseMigration
{
    public function up(): void
    {
        $this->table('ems_demo_request_notes', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'uuid', ['null' => false])
            ->addColumn('demo_request_id', 'uuid', ['null' => false])
            ->addColumn('author_user_id', 'uuid', ['null' => false])
            ->addColumn('author_name', 'string', ['limit' => 190, 'null' => false])
            ->addColumn('body', 'text', ['null' => false])
            ->addColumn('created', 'datetime', ['null' => true, 'default' => null])
            ->addColumn('modified', 'datetime', ['null' => true, 'default' => null])
            ->addIndex(['demo_request_id', 'created'])
            ->create();
    }

    public function down(): void
    {
        $this->table('ems_demo_request_notes')->drop()->save();
    }
}
