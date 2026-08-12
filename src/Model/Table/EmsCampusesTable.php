<?php
declare(strict_types=1);

namespace App\Model\Table;


/**
 * EMS contract table `ems_campuses`. Explicit setTable() so the class can
 * never be conflated with a legacy `tss` table of a similar name.
 */
class EmsCampusesTable extends EmsTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('ems_campuses');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');
    }
}
