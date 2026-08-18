<?php
declare(strict_types=1);

namespace App\Model\Table;

/**
 * EMS contract table `ems_login_challenges` (second-factor sign-in codes).
 * Explicit setTable() so the class can never be conflated with a legacy `tss`
 * table of a similar name.
 */
class EmsLoginChallengesTable extends EmsTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('ems_login_challenges');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');
    }
}
