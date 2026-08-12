<?php
declare(strict_types=1);

namespace App\Model\Table;


/**
 * EMS contract table `ems_timetable_slots`. Explicit setTable() so the class can
 * never be conflated with a legacy `tss` table of a similar name.
 */
class EmsTimetableSlotsTable extends EmsTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('ems_timetable_slots');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');
        $this->normalizesSubject();
    }
}
