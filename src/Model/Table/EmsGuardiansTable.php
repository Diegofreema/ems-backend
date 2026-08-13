<?php
declare(strict_types=1);

namespace App\Model\Table;

/**
 * EMS contract table `ems_guardians`. Explicit setTable() so the class can
 * never be conflated with a legacy `tss` table of a similar name.
 */
class EmsGuardiansTable extends EmsTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('ems_guardians');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');

        // Soft-archive default scope: a deleted guardian is stamped archived_at
        // rather than destroyed (student-related data is never hard-deleted).
        // Hiding archived rows HERE — not at each call site — guarantees they
        // vanish from every read at once: the guardians list, the primary-
        // contact sync, messaging audiences (Comms) and reports. Pass the
        // finder option ['includeArchived' => true] to see them anyway.
        $this->getEventManager()->on('Model.beforeFind', function ($event, $query, $options): void {
            if (empty($options['includeArchived'])) {
                $query->where([$this->aliasField('archived_at') . ' IS' => null]);
            }
        });
    }
}
