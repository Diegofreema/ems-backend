<?php
declare(strict_types=1);

use Migrations\BaseMigration;

/**
 * Re-link students to their class by a stable id instead of the class-name
 * string. Historically ems_students.class_group held the class NAME ("JSS 1A")
 * and every student-side lookup — rosters, grade sheets, viewer scope, the
 * class-in-use guard — matched on that name. Once an administrator may create
 * two arms that share a name (a second "JSS 1A" in the same level), the name is
 * no longer a unique key, so this adds the canonical class_group_id link.
 *
 * The name column stays as a denormalised, synced display label (kept current by
 * ClassesController::edit), so legacy name-based reads keep working during the
 * frontend transition, and true orphans (a class_group that never matched a real
 * class row) remain id-less and continue to resolve through that name fallback.
 *
 * Backfill is idempotent (only fills NULL ids) and safe: before this release
 * class names were unique per tenant, so the name→id match is unambiguous for
 * every existing row.
 */
class LinkStudentsToClassGroupId extends BaseMigration
{
    public function up(): void
    {
        $this->table('ems_students')
            ->addColumn('class_group_id', 'uuid', [
                'null' => true,
                'default' => null,
                'after' => 'class_group',
            ])
            ->addIndex(['school_id', 'class_group_id'])
            ->update();

        // Link every student whose class_group name matches exactly one class in
        // the same school. NULL-guarded so re-running the migration is a no-op;
        // the empty-string default ('' = "no class") is deliberately skipped.
        $this->execute(
            'UPDATE ems_students s
             SET s.class_group_id = (
                 SELECT c.id FROM ems_class_groups c
                 WHERE c.school_id = s.school_id
                   AND LOWER(c.name) = LOWER(s.class_group)
                 ORDER BY c.created LIMIT 1
             )
             WHERE s.class_group_id IS NULL
               AND s.class_group <> \'\'
               AND EXISTS (
                 SELECT 1 FROM ems_class_groups c2
                 WHERE c2.school_id = s.school_id
                   AND LOWER(c2.name) = LOWER(s.class_group)
               )',
        );

        // Report — never fail — the distinct class names that matched no class
        // row. These students keep their name and resolve through the fallback;
        // an administrator can reconcile them via class rename or a real create.
        $orphans = $this->fetchAll(
            'SELECT school_id, class_group, COUNT(*) AS n
             FROM ems_students
             WHERE class_group_id IS NULL AND class_group <> \'\'
             GROUP BY school_id, class_group
             ORDER BY school_id, class_group',
        );
        $io = $this->getIo();
        if ($io !== null && $orphans !== []) {
            $io->warning(sprintf(
                'Linked students to classes. %d class name(s) matched no class row and were left unlinked:',
                count($orphans),
            ));
            foreach ($orphans as $row) {
                $io->out(sprintf(
                    '  school %s — "%s" (%s student(s))',
                    (string)$row['school_id'],
                    (string)$row['class_group'],
                    (string)$row['n'],
                ));
            }
        }
    }

    public function down(): void
    {
        $this->table('ems_students')
            ->removeIndex(['school_id', 'class_group_id'])
            ->removeColumn('class_group_id')
            ->update();
    }
}
