<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;

/**
 * EMS append-only decision trail `ems_application_reviews` (§3.6). Rows are
 * never updated or deleted — the review history IS the audit for admissions.
 * Only `created` is stamped (there is no `modified` column).
 */
class EmsApplicationReviewsTable extends EmsTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('ems_application_reviews');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp', [
            'events' => ['Model.beforeSave' => ['created' => 'new']],
        ]);
    }
}
