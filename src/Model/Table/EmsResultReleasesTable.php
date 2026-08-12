<?php
declare(strict_types=1);

namespace App\Model\Table;

/**
 * EMS contract table `ems_result_releases` (Phase 3, §3.1) — append-only, pins
 * the grading scheme version each release was graded on.
 */
class EmsResultReleasesTable extends EmsTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('ems_result_releases');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');
    }
}
