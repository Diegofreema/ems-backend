<?php
declare(strict_types=1);

namespace App\Model\Table;


/**
 * EMS contract table `ems_admission_cycles` (Phase 2). Explicit setTable() keeps it distinct
 * from any legacy `tss` table.
 */
class EmsAdmissionCyclesTable extends EmsTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('ems_admission_cycles');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');
    }
}
