<?php
declare(strict_types=1);

use Migrations\BaseMigration;

/**
 * Adds a per-code guess counter to password resets (§ security candidate #4).
 * An IP throttle alone can be spread across a botnet, so the 6-digit reset code
 * is also bounded per code: resetConfirm increments `attempts` on every wrong
 * guess and refuses once it reaches its cap, making the code un-brute-forceable
 * regardless of how many IPs try. Defaults to 0 so existing rows backfill.
 */
class AddPasswordResetAttempts extends BaseMigration
{
    public function up(): void
    {
        $this->table('ems_password_resets')
            ->addColumn('attempts', 'integer', [
                'default' => 0,
                'null' => false,
                'signed' => false,
                'after' => 'used_at',
            ])
            ->update();
    }

    public function down(): void
    {
        $this->table('ems_password_resets')
            ->removeColumn('attempts')
            ->update();
    }
}
