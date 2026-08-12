<?php
declare(strict_types=1);

namespace App\Model\Table;

/**
 * EMS contract table `ems_contact_preferences` (Phase 5, §3.20).
 */
class EmsContactPreferencesTable extends EmsTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('ems_contact_preferences');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');
    }
}
