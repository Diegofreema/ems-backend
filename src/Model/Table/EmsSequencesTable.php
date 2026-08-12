<?php
declare(strict_types=1);

namespace App\Model\Table;


/**
 * EMS contract table `ems_sequences` (Phase 2). Explicit setTable() keeps it distinct
 * from any legacy `tss` table.
 */
class EmsSequencesTable extends EmsTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('ems_sequences');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');
    }
}
