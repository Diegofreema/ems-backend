<?php
declare(strict_types=1);

use Migrations\BaseMigration;

final class ExpireEmsInvitations extends BaseMigration
{
    public function change(): void
    {
        $this->table('ems_users')
            ->changeColumn('invite_code', 'string', ['limit' => 64, 'null' => true, 'default' => null])
            ->addColumn('invite_expires_at', 'datetime', ['null' => true, 'default' => null, 'after' => 'invite_code'])
            ->update();
    }
}
