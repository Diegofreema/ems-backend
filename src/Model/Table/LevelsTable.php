<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Levels Model
 *
 * @property \App\Model\Table\CourseassignmentsTable&\Cake\ORM\Association\HasMany $Courseassignments
 * @property \App\Model\Table\CourseregistrationsTable&\Cake\ORM\Association\HasMany $Courseregistrations
 * @property \App\Model\Table\ResultsTable&\Cake\ORM\Association\HasMany $Results
 * @property \App\Model\Table\StudentsTable&\Cake\ORM\Association\HasMany $Students
 * @property \App\Model\Table\SubjectsTable&\Cake\ORM\Association\HasMany $Subjects
 * @property \App\Model\Table\TimetablesTable&\Cake\ORM\Association\HasMany $Timetables
 * @property \App\Model\Table\DepartmentsTable&\Cake\ORM\Association\BelongsToMany $Departments
 * @property \App\Model\Table\FeesTable&\Cake\ORM\Association\BelongsToMany $Fees
 * @method \App\Model\Entity\Level newEmptyEntity()
 * @method \App\Model\Entity\Level newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Level[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Level get($primaryKey, $options = [])
 * @method \App\Model\Entity\Level findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Level patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Level[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Level|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Level saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Level[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Level[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Level[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Level[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class LevelsTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('levels');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->hasMany('Courseassignments', [
            'foreignKey' => 'level_id',
        ]);
        $this->hasMany('Courseregistrations', [
            'foreignKey' => 'level_id',
        ]);
        $this->hasMany('Results', [
            'foreignKey' => 'level_id',
        ]);
        $this->hasMany('Students', [
            'foreignKey' => 'level_id',
        ]);
        $this->hasMany('Subjects', [
            'foreignKey' => 'level_id',
        ]);
        $this->hasMany('Timetables', [
            'foreignKey' => 'level_id',
        ]);
        $this->belongsToMany('Departments', [
            'foreignKey' => 'level_id',
            'targetForeignKey' => 'department_id',
            'joinTable' => 'departments_levels',
        ]);
        $this->belongsToMany('Fees', [
            'foreignKey' => 'level_id',
            'targetForeignKey' => 'fee_id',
            'joinTable' => 'fees_levels',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->scalar('name')
            ->maxLength('name', 44)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        return $validator;
    }
}
