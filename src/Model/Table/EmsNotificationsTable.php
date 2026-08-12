<?php
declare(strict_types=1);

namespace App\Model\Table;

/**
 * EMS contract table `ems_notifications` (Phase 5, §3.20).
 */
class EmsNotificationsTable extends EmsTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('ems_notifications');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');
    }
}
