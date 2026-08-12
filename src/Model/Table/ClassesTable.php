<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class ClassesTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('classes');
        $this->setPrimaryKey('id');

        $this->belongsTo('ClassLevels', ['foreignKey' => 'class_level_id']);
        $this->belongsTo('ClassArms',   ['foreignKey' => 'class_arm_id']);
        $this->belongsTo('Sessions',    ['foreignKey' => 'session_id']);
        $this->belongsTo('Teachers',    ['foreignKey' => 'homeroom_teacher_id']);

        $this->hasMany('StudentsClasses', ['foreignKey' => 'class_id']);
        $this->hasMany('ResultsSecondary', ['foreignKey' => 'class_id']);
        $this->hasMany('TimetablesSecondary', ['foreignKey' => 'class_id']);
    }
}
