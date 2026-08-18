<?php
declare(strict_types=1);

namespace App\Model\Table;

/**
 * EMS contract table `ems_trusted_devices` (30-day 2FA device trust).
 * Explicit setTable() so the class can never be conflated with a legacy `tss`
 * table of a similar name.
 */
class EmsTrustedDevicesTable extends EmsTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('ems_trusted_devices');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');
    }
}
