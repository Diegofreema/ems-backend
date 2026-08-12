<?php
declare(strict_types=1);

use Migrations\BaseMigration;

/**
 * Let recipient delivery rows belong to either an announcement or an alert
 * notification. Existing announcement rows keep their current identifier.
 */
class LinkAlertRecipientsToNotifications extends BaseMigration
{
    public function up(): void
    {
        $this->table('ems_message_recipients')
            ->changeColumn('announcement_id', 'uuid', ['null' => true, 'default' => null])
            ->addColumn('notification_id', 'uuid', ['null' => true, 'default' => null, 'after' => 'announcement_id'])
            ->addIndex(['school_id', 'notification_id'])
            ->update();
    }

    public function down(): void
    {
        $this->execute('DELETE FROM ems_message_recipients WHERE announcement_id IS NULL');
        $this->table('ems_message_recipients')
            ->removeIndex(['school_id', 'notification_id'])
            ->removeColumn('notification_id')
            ->changeColumn('announcement_id', 'uuid', ['null' => false])
            ->update();
    }
}
