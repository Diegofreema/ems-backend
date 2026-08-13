<?php
declare(strict_types=1);

namespace App\Model\Table;

/**
 * EMS contract table `ems_absence_alerts`. Explicit setTable() so the class can
 * never be conflated with a legacy `tss` table of a similar name.
 */
class EmsAbsenceAlertsTable extends EmsTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('ems_absence_alerts');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');
    }
}
