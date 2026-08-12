<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class FeesClasslevelsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('fees_classlevels');
        $this->setPrimaryKey('id');

        $this->belongsTo('Fees',        ['foreignKey' => 'fee_id']);
        $this->belongsTo('ClassLevels', ['foreignKey' => 'class_level_id']);
    }
}
