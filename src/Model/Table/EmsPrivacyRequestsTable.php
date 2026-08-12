<?php
declare(strict_types=1);

namespace App\Model\Table;

/**
 * EMS contract table `ems_privacy_requests` (Phase 5, §3.23).
 */
class EmsPrivacyRequestsTable extends EmsTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('ems_privacy_requests');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');
    }
}
