<?php
declare(strict_types=1);

namespace App\Model\Table;

/**
 * EMS contract table `ems_portal_notifications`. Explicit setTable() so the
 * class can never be conflated with a legacy `tss` table of a similar name.
 */
class EmsPortalNotificationsTable extends EmsTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('ems_portal_notifications');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');
    }
}
