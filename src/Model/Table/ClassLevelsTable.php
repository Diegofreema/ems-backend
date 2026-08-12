<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class ClassLevelsTable extends Table
{

    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('class_levels');
        $this->setPrimaryKey('id');

        $this->hasMany('Classes', ['foreignKey' => 'class_level_id']);
        $this->hasMany('FeesClasslevels', ['foreignKey' => 'class_level_id']);
    }
}
