<?php
declare(strict_types=1);

namespace App\Model\Table;

/**
 * EMS contract table `ems_documents` (Phase 2). Explicit setTable() keeps it distinct
 * from any legacy `tss` table.
 */
class EmsDocumentsTable extends EmsTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('ems_documents');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');
    }
}
