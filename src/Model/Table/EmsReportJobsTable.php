<?php
declare(strict_types=1);

namespace App\Model\Table;

/**
 * EMS contract table `ems_report_jobs` (Phase 5, §3.21).
 */
class EmsReportJobsTable extends EmsTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('ems_report_jobs');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');
    }
}
