<?php
declare(strict_types=1);

namespace App\Model\Table;

/**
 * EMS contract table `ems_assessment_scores` (Phase 3, §3.2).
 */
class EmsAssessmentScoresTable extends EmsTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('ems_assessment_scores');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');
    }
}
