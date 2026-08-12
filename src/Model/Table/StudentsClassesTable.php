<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class StudentsClassesTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('students_classes');
        $this->setPrimaryKey('id');

        $this->belongsTo('Students', ['foreignKey' => 'student_id']);
        $this->belongsTo('Classes',  ['foreignKey' => 'class_id']);
        $this->belongsTo('Terms',    ['foreignKey' => 'term_id']);
    }
}
