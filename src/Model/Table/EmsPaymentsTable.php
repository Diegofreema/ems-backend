<?php
declare(strict_types=1);

namespace App\Model\Table;

/**
 * EMS contract table `ems_payments` (Phase 4, §3.7).
 */
class EmsPaymentsTable extends EmsTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('ems_payments');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');
    }
}
