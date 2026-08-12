<?php
declare(strict_types=1);

namespace App\Model\Table;

/**
 * EMS contract table `ems_fee_structures` (Phase 4, §3.7).
 */
class EmsFeeStructuresTable extends EmsTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('ems_fee_structures');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');
    }
}
