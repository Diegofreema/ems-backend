<?php
declare(strict_types=1);

namespace App\Model\Table;

/**
 * EMS contract table `ems_email_verifications`. Explicit setTable() so the
 * class can never be conflated with a legacy `tss` table of a similar name.
 */
class EmsEmailVerificationsTable extends EmsTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('ems_email_verifications');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');
    }
}
