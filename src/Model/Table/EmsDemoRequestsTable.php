<?php
declare(strict_types=1);

namespace App\Model\Table;

/**
 * Book-a-demo requests. Non-tenant (no `school_id`): the caller is a prospect
 * with no school, so this table is queried directly, never through `Tenant`.
 */
class EmsDemoRequestsTable extends EmsTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('ems_demo_requests');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');
    }
}
