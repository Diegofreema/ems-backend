<?php
declare(strict_types=1);

namespace App\Model\Table;

/**
 * EMS contract table `ems_users`. Explicit setTable() so the class can
 * never be conflated with a legacy `tss` table of a similar name.
 */
class EmsUsersTable extends EmsTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('ems_users');
        $this->setPrimaryKey('id');
        $this->setEntityClass('EmsUser');
        $this->addBehavior('Timestamp');
    }
}
