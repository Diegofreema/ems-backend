<?php
declare(strict_types=1);

namespace App\Model\Table;

/**
 * EMS contract table `ems_import_rows` (Phase 5, §3.17).
 */
class EmsImportRowsTable extends EmsTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('ems_import_rows');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');
    }
}
