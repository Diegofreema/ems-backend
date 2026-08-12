<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Semesters Model
 *
 * @property \App\Model\Table\CourseassignmentsTable&\Cake\ORM\Association\HasMany $Courseassignments
 * @property \App\Model\Table\CourseregistrationsTable&\Cake\ORM\Association\HasMany $Courseregistrations
 * @property \App\Model\Table\ResultsTable&\Cake\ORM\Association\HasMany $Results
 * @property \App\Model\Table\SettingsTable&\Cake\ORM\Association\HasMany $Settings
 * @property \App\Model\Table\SubjectsTable&\Cake\ORM\Association\HasMany $Subjects
 * @property \App\Model\Table\DepartmentsTable&\Cake\ORM\Association\BelongsToMany $Departments
 * @property \App\Model\Table\SubjectsTable&\Cake\ORM\Association\BelongsToMany $Subjects
 *
 * @method \App\Model\Entity\Semester newEmptyEntity()
 * @method \App\Model\Entity\Semester newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Semester[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Semester get($primaryKey, $options = [])
 * @method \App\Model\Entity\Semester findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Semester patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Semester[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Semester|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Semester saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Semester[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Semester[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Semester[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Semester[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class SemestersTable extends Table
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

        $this->setTable('semesters');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->hasMany('Courseassignments', [
            'foreignKey' => 'semester_id',
        ]);
        $this->hasMany('Courseregistrations', [
            'foreignKey' => 'semester_id',
        ]);
        $this->hasMany('Results', [
            'foreignKey' => 'semester_id',
        ]);
        $this->hasMany('Settings', [
            'foreignKey' => 'semester_id',
        ]);
        $this->hasMany('Subjects', [
            'foreignKey' => 'semester_id',
        ]);
        $this->belongsToMany('Departments', [
            'foreignKey' => 'semester_id',
            'targetForeignKey' => 'department_id',
            'joinTable' => 'departments_semesters',
        ]);
        $this->belongsToMany('Subjects', [
            'foreignKey' => 'semester_id',
            'targetForeignKey' => 'subject_id',
            'joinTable' => 'semesters_subjects',
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
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('name')
            ->maxLength('name', 44)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        return $validator;
    }
}
