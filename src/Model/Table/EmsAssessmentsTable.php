<?php
declare(strict_types=1);

namespace App\Model\Table;

/**
 * EMS contract table `ems_assessments` (Phase 3, §3.2) — teacher-created CA.
 */
class EmsAssessmentsTable extends EmsTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('ems_assessments');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');
        $this->normalizesSubject();
    }
}
