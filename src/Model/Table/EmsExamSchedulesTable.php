<?php
declare(strict_types=1);

namespace App\Model\Table;

/**
 * EMS contract table `ems_exam_schedules` (Phase 3, §3.1).
 */
class EmsExamSchedulesTable extends EmsTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('ems_exam_schedules');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');
        $this->normalizesSubject();
    }
}
