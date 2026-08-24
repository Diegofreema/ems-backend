<?php
declare(strict_types=1);

use Migrations\BaseMigration;

/**
 * A staff directory record represents one teacher identity, so it may back
 * only one school account. Refuse to guess which legacy account to keep: an
 * operator must repair duplicate links before this irreversible invariant is
 * applied.
 */
class EnforceUniqueTeacherAccountLinks extends BaseMigration
{
    public function up(): void
    {
        $duplicates = $this->fetchAll(
            "SELECT school_id, link_teacher_id
             FROM ems_users
             WHERE role = 'teacher' AND link_teacher_id IS NOT NULL
             GROUP BY school_id, link_teacher_id
             HAVING COUNT(*) > 1",
        );
        if ($duplicates !== []) {
            throw new RuntimeException(
                'Cannot enforce unique teacher account links until duplicate teacher accounts are repaired.',
            );
        }

        $this->table('ems_users')
            ->addIndex(
                ['school_id', 'link_teacher_id'],
                ['name' => 'uq_ems_users_school_teacher_link', 'unique' => true],
            )
            ->update();
    }

    public function down(): void
    {
        $this->table('ems_users')
            ->removeIndexByName('uq_ems_users_school_teacher_link')
            ->update();
    }
}
