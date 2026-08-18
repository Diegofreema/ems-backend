<?php
declare(strict_types=1);

use Migrations\BaseMigration;

/**
 * Book-a-demo requests from prospective institutions (public lead capture).
 *
 * A prospect has no school yet, so — unlike every other `ems_` table — this one
 * carries NO `school_id` and lives outside the tenant model. The public
 * `/public/demo-requests` endpoint writes here; the review trail is the row plus
 * the notification e-mail. `status` defaults to `new` and has no UI to change it
 * yet: it is the spine a later internal review inbox builds its pipeline on.
 */
class CreateEmsDemoRequests extends BaseMigration
{
    public function up(): void
    {
        $this->table('ems_demo_requests', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'uuid', ['null' => false])
            ->addColumn('institution_name', 'string', ['limit' => 190, 'null' => false])
            ->addColumn('contact_name', 'string', ['limit' => 190, 'null' => false])
            ->addColumn('email', 'string', ['limit' => 190, 'null' => false])
            ->addColumn('phone', 'string', ['limit' => 40, 'null' => false])
            ->addColumn('role_title', 'string', ['limit' => 120, 'null' => true, 'default' => null])
            ->addColumn('location', 'string', ['limit' => 190, 'null' => true, 'default' => null])
            ->addColumn('size_band', 'string', ['limit' => 20, 'null' => true, 'default' => null])
            ->addColumn('message', 'text', ['null' => true, 'default' => null])
            ->addColumn('status', 'string', ['limit' => 20, 'null' => false, 'default' => 'new'])
            ->addColumn('created', 'datetime', ['null' => true, 'default' => null])
            ->addColumn('modified', 'datetime', ['null' => true, 'default' => null])
            ->addIndex(['status', 'created'])
            ->create();
    }

    public function down(): void
    {
        $this->table('ems_demo_requests')->drop()->save();
    }
}
