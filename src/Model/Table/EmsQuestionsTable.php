<?php
declare(strict_types=1);

namespace App\Model\Table;

/**
 * EMS contract table `ems_questions` (Phase 3, §3.4) — the question bank;
 * `options` rides as JSON.
 */
class EmsQuestionsTable extends EmsTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('ems_questions');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');
        $this->normalizesSubject();
    }
}
