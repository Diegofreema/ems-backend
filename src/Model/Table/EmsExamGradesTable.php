<?php
declare(strict_types=1);

namespace App\Model\Table;

/**
 * EMS contract table `ems_exam_grades` (Phase 3, §3.1) — the score matrix.
 */
class EmsExamGradesTable extends EmsTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('ems_exam_grades');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');
        $this->normalizesSubject();
    }
}
