<?php
declare(strict_types=1);

use Migrations\BaseMigration;

/**
 * The announcement notification row is the durable one send marker, including
 * when its resolved audience is empty and no recipient rows are written.
 */
class LinkAnnouncementSendLog extends BaseMigration
{
    public function up(): void
    {
        $this->table('ems_notifications')
            ->addColumn('announcement_id', 'uuid', ['null' => true, 'default' => null, 'after' => 'school_id'])
            ->addIndex(['school_id', 'announcement_id'], ['unique' => true])
            ->update();
    }

    public function down(): void
    {
        $this->table('ems_notifications')
            ->removeIndex(['school_id', 'announcement_id'])
            ->removeColumn('announcement_id')
            ->update();
    }
}
