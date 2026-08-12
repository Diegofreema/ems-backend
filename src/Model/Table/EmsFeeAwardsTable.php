<?php
declare(strict_types=1);

namespace App\Model\Table;

/**
 * EMS contract table `ems_fee_awards` (Phase 4, §3.7).
 */
class EmsFeeAwardsTable extends EmsTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('ems_fee_awards');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');
    }
}
