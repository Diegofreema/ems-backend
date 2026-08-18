<?php
declare(strict_types=1);

namespace App\Model\Table;

/**
 * Internal notes platform staff keep on a demo request (the CRM-lite activity
 * trail). Non-tenant (no `school_id`): a lead belongs to no school, so this
 * table is queried directly by `demo_request_id`, never through `Tenant`.
 */
class EmsDemoRequestNotesTable extends EmsTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('ems_demo_request_notes');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');
    }
}
