<?php
declare(strict_types=1);

namespace App\Model\Table;

/**
 * EMS contract table `ems_exam_papers` (Phase 3, §3.1). `question_ids` rides as
 * an ordered JSON list.
 */
class EmsExamPapersTable extends EmsTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('ems_exam_papers');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');
        $this->normalizesSubject();
    }
}
