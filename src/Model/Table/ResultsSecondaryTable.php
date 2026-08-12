<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class ResultsSecondaryTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('results_secondary');
        $this->setPrimaryKey('id');

        $this->belongsTo('Students', ['foreignKey' => 'student_id']);
        $this->belongsTo('Subjects', ['foreignKey' => 'subject_id']);
        $this->belongsTo('Terms',    ['foreignKey' => 'term_id']);
        $this->belongsTo('Sessions', ['foreignKey' => 'session_id']);
        $this->belongsTo('Classes',  ['foreignKey' => 'class_id']);
        $this->belongsTo('Teachers', ['foreignKey' => 'teacher_id']);
    }
}
