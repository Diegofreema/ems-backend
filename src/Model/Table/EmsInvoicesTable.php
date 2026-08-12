<?php
declare(strict_types=1);

namespace App\Model\Table;

/**
 * EMS contract table `ems_invoices` (Phase 4, §3.7).
 */
class EmsInvoicesTable extends EmsTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('ems_invoices');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');
    }
}
