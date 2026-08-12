<?php
declare(strict_types=1);

namespace App\Model\Table;

/**
 * EMS contract table `ems_incidents` (Phase 5, §3.24).
 */
class EmsIncidentsTable extends EmsTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('ems_incidents');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');
    }
}
