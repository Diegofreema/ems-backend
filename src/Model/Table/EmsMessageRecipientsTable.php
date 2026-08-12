<?php
declare(strict_types=1);

namespace App\Model\Table;

/**
 * EMS contract table `ems_message_recipients` (Phase 5, §3.20).
 */
class EmsMessageRecipientsTable extends EmsTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('ems_message_recipients');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');
    }
}
