<?php
declare(strict_types=1);

use Migrations\BaseMigration;

/**
 * Pending e-mail changes, proved by a link to the NEW address.
 *
 * Changing a login e-mail requires the current password AND proof of the new
 * mailbox: a row is staged here holding the new address and a single-use token
 * (SHA-256 hashed, 30-minute expiry), and the swap only happens when the link
 * mailed to that address is opened. Until then the old e-mail keeps working, so
 * a typo can never lock anyone out. Earlier unspent requests are retired on a
 * new one so only the newest link is live.
 */
class CreateEmsEmailChanges extends BaseMigration
{
    public function up(): void
    {
        $this->table('ems_email_changes', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'uuid', ['null' => false])
            ->addColumn('user_id', 'uuid', ['null' => false])
            ->addColumn('new_email', 'string', ['limit' => 190, 'null' => false])
            ->addColumn('token', 'string', ['limit' => 64, 'null' => false])
            ->addColumn('expires_at', 'datetime', ['null' => false])
            ->addColumn('used_at', 'datetime', ['null' => true, 'default' => null])
            ->addColumn('created', 'datetime', ['null' => true, 'default' => null])
            ->addIndex(['token'], ['unique' => true])
            ->addIndex(['user_id'])
            ->create();
    }

    public function down(): void
    {
        $this->table('ems_email_changes')->drop()->save();
    }
}
