<?php
declare(strict_types=1);

use Migrations\BaseMigration;

/**
 * Device metadata on ems_refresh_tokens so the Account & security "active
 * sessions" list can name each signed-in device.
 *
 * A token FAMILY is one sign-in on one device; capturing the user agent and IP
 * at sign-in (and the last time the family rotated) lets the sessions view show
 * "Chrome on macOS · Lagos · active 2 minutes ago" and mark the current device.
 * All nullable — pre-existing tokens simply render as an unlabelled session.
 */
class AddEmsRefreshTokenDeviceMeta extends BaseMigration
{
    public function up(): void
    {
        $this->table('ems_refresh_tokens')
            ->addColumn('user_agent', 'string', [
                'limit' => 512,
                'null' => true,
                'default' => null,
                'after' => 'family_id',
            ])
            ->addColumn('ip', 'string', [
                'limit' => 45,
                'null' => true,
                'default' => null,
                'after' => 'user_agent',
            ])
            ->addColumn('last_used_at', 'datetime', [
                'null' => true,
                'default' => null,
                'after' => 'ip',
            ])
            ->update();
    }

    public function down(): void
    {
        $this->table('ems_refresh_tokens')
            ->removeColumn('user_agent')
            ->removeColumn('ip')
            ->removeColumn('last_used_at')
            ->update();
    }
}
