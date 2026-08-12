<?php
declare(strict_types=1);

namespace App\Model\Table;

/**
 * EMS contract table `ems_grading_schemes` (Phase 3, §3.3) — versioned scale;
 * `bands` rides as JSON. A version is never mutated in place.
 */
class EmsGradingSchemesTable extends EmsTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('ems_grading_schemes');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');
    }
}
