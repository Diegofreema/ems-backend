<?php
declare(strict_types=1);

namespace App\Model\Table;

use App\Ems\SubjectCatalog;
use Cake\Database\Schema\TableSchemaInterface;
use Cake\ORM\Table;

/**
 * Base class for every `ems_` contract table.
 *
 * The migrations store primary keys as CHAR(36). Schema reflection reports a
 * CHAR column as type `string`, and CakePHP only auto-generates a UUID for a
 * new row when the PK column TYPE is `uuid` — so without this override an
 * insert would attempt a NULL primary key. Forcing the `id` column type to
 * `uuid` restores automatic UUID generation on save.
 */
abstract class EmsTable extends Table
{
    protected function _initializeSchema(TableSchemaInterface $schema): TableSchemaInterface
    {
        $schema = parent::_initializeSchema($schema);
        if (in_array('id', $schema->columns(), true)) {
            $schema->setColumnType('id', 'uuid');
        }

        return $schema;
    }

    /**
     * Wire a subject-normalized table (one holding `subject_id`) back to the
     * NAME the wire contract speaks. Reads AND freshly-saved rows decorate the
     * virtual `subject` property from the catalogue; marshals accept `subject`
     * (a name, resolved against the catalogue) as well as `subject_id`.
     *
     * The read and save decorations together make the adapter TOTAL: a row's
     * `subject` name is populated no matter how the entity was obtained, so no
     * caller needs a `?? SubjectCatalog::name(...)` fallback. `beforeFind` alone
     * left a hole — its formatter never runs over an entity returned by save().
     */
    protected function normalizesSubject(): void
    {
        $this->getEventManager()->on('Model.beforeFind', function ($event, $query): void {
            $query->formatResults(function ($results) {
                return $results->map(function ($row) {
                    if (
                        is_object($row)
                        && isset($row->subject_id)
                        && isset($row->school_id)
                        && !isset($row->subject)
                    ) {
                        $row->subject = SubjectCatalog::name(
                            (string)$row->school_id,
                            (string)$row->subject_id,
                        );
                        $row->clean();
                    }

                    return $row;
                });
            });
        });

        $this->getEventManager()->on('Model.afterSave', function ($event, $entity): void {
            if (
                isset($entity->subject_id)
                && isset($entity->school_id)
                && !isset($entity->subject)
            ) {
                $entity->subject = SubjectCatalog::name(
                    (string)$entity->school_id,
                    (string)$entity->subject_id,
                );
                $entity->clean();
            }
        });

        $this->getEventManager()->on('Model.beforeMarshal', function ($event, $data) {
            if (
                isset($data['subject']) && is_string($data['subject']) && $data['subject'] !== ''
                && empty($data['subject_id']) && !empty($data['school_id'])
            ) {
                $data['subject_id'] = SubjectCatalog::requireId(
                    (string)$data['school_id'],
                    $data['subject'],
                );
            }
            unset($data['subject']);
        });
    }
}
