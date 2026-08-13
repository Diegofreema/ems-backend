<?php
declare(strict_types=1);

namespace App\Model\Table;

/**
 * EMS contract table `ems_academic_term_records`. Explicit setTable() so the class can
 * never be conflated with a legacy `tss` table of a similar name.
 */
class EmsAcademicTermRecordsTable extends EmsTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('ems_academic_term_records');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');
    }
}
