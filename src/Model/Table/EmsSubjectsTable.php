<?php
declare(strict_types=1);

namespace App\Model\Table;

/**
 * EMS contract table `ems_subjects` (Stage 0 completeness pass) — the
 * per-school subject catalogue every academic module references by id.
 */
class EmsSubjectsTable extends EmsTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('ems_subjects');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');
    }
}
