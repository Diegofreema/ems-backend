<?php
declare(strict_types=1);

namespace App\Model\Table;


/**
 * EMS contract table `ems_students`. Explicit setTable() so the class can
 * never be conflated with a legacy `tss` table of a similar name.
 */
class EmsStudentsTable extends EmsTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('ems_students');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');
    }
}
