<?php
declare(strict_types=1);

namespace App\Model\Table;

/**
 * EMS contract table `ems_exams` (Phase 3, §3.1). Explicit setTable() keeps it
 * distinct from any legacy `tss` table.
 */
class EmsExamsTable extends EmsTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('ems_exams');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');
    }
}
