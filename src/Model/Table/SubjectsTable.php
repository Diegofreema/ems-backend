<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Subjects Model
 *
 * @property \App\Model\Table\DepartmentsTable&\Cake\ORM\Association\BelongsTo $Departments
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\SemestersTable&\Cake\ORM\Association\BelongsTo $Semesters
 * @property \App\Model\Table\LevelsTable&\Cake\ORM\Association\BelongsTo $Levels
 * @property \App\Model\Table\CoursematerialsTable&\Cake\ORM\Association\HasMany $Coursematerials
 * @property \App\Model\Table\ResultsTable&\Cake\ORM\Association\HasMany $Results
 * @property \App\Model\Table\TopicsTable&\Cake\ORM\Association\HasMany $Topics
 * @property \App\Model\Table\CourseassignmentsTable&\Cake\ORM\Association\BelongsToMany $Courseassignments
 * @property \App\Model\Table\CourseregistrationsTable&\Cake\ORM\Association\BelongsToMany $Courseregistrations
 * @property \App\Model\Table\DepartmentsTable&\Cake\ORM\Association\BelongsToMany $Departments
 * @property \App\Model\Table\StudentsTable&\Cake\ORM\Association\BelongsToMany $Students
 * @property \App\Model\Table\TeachersTable&\Cake\ORM\Association\BelongsToMany $Teachers
 *
 * @method \App\Model\Entity\Subject newEmptyEntity()
 * @method \App\Model\Entity\Subject newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Subject[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Subject get($primaryKey, $options = [])
 * @method \App\Model\Entity\Subject findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Subject patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Subject[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Subject|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Subject saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Subject[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Subject[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Subject[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Subject[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class SubjectsTable extends Table
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

        $this->setTable('subjects');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->belongsTo('Departments', [
            'foreignKey' => 'department_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Semesters', [
            'foreignKey' => 'semester_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Levels', [
            'foreignKey' => 'level_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('Coursematerials', [
            'foreignKey' => 'subject_id',
        ]);
        $this->hasMany('Results', [
            'foreignKey' => 'subject_id',
        ]);
        $this->hasMany('Topics', [
            'foreignKey' => 'subject_id',
        ]);
        $this->belongsToMany('Courseassignments', [
            'foreignKey' => 'subject_id',
            'targetForeignKey' => 'courseassignment_id',
            'joinTable' => 'courseassignments_subjects',
        ]);
        $this->belongsToMany('Courseregistrations', [
            'foreignKey' => 'subject_id',
            'targetForeignKey' => 'courseregistration_id',
            'joinTable' => 'courseregistrations_subjects',
        ]);
//        $this->belongsToMany('Departments', [
//            'foreignKey' => 'subject_id',
//            'targetForeignKey' => 'department_id',
//            'joinTable' => 'departments_subjects',
//        ]);
        $this->belongsToMany('Students', [
            'foreignKey' => 'subject_id',
            'targetForeignKey' => 'student_id',
            'joinTable' => 'subjects_students',
        ]);
        $this->belongsToMany('Teachers', [
            'foreignKey' => 'subject_id',
            'targetForeignKey' => 'teacher_id',
            'joinTable' => 'subjects_teachers',
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
            ->maxLength('name', 164)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->scalar('subjectcode')
            ->maxLength('subjectcode', 16)
            ->requirePresence('subjectcode', 'create')
            ->notEmptyString('subjectcode');

        // $validator
        //     ->integer('creditload')
        //     ->requirePresence('creditload', 'create')
        //     ->notEmptyString('creditload');

        // $validator
        //     ->dateTime('created_date')
        //     ->notEmptyDateTime('created_date');

        // $validator
        //     ->integer('status')
        //     ->notEmptyString('status');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['department_id'], 'Departments'));
        $rules->add($rules->existsIn(['user_id'], 'Users'));
        // $rules->add($rules->existsIn(['semester_id'], 'Semesters'));
        // $rules->add($rules->existsIn(['level_id'], 'Levels'));
        
        // Add unique constraint: subject name must be unique per department/class
        $rules->add(
            $rules->isUnique(['name', 'department_id'], 'This subject already exists in the selected class.'),
            'uniqueSubjectPerClass'
        );

        return $rules;
    }
}
