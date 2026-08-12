<?php
declare(strict_types=1);

namespace App\Model\Table;

/**
 * EMS contract table `ems_announcements` (Phase 5, §3.20).
 */
class EmsAnnouncementsTable extends EmsTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('ems_announcements');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');
    }
}
