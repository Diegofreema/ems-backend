<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Departments Model
 *
 * @property \App\Model\Table\FacultiesTable&\Cake\ORM\Association\BelongsTo $Faculties
 * @property \App\Model\Table\AdminsTable&\Cake\ORM\Association\HasMany $Admins
 * @property \App\Model\Table\CourseassignmentsTable&\Cake\ORM\Association\HasMany $Courseassignments
 * @property \App\Model\Table\CoursematerialsTable&\Cake\ORM\Association\HasMany $Coursematerials
 * @property \App\Model\Table\DstudentsTable&\Cake\ORM\Association\HasMany $Dstudents
 * @property \App\Model\Table\FeeallocationsTable&\Cake\ORM\Association\HasMany $Feeallocations
 * @property \App\Model\Table\ResultsTable&\Cake\ORM\Association\HasMany $Results
 * @property \App\Model\Table\StudentsTable&\Cake\ORM\Association\HasMany $Students
 * @property \App\Model\Table\SubjectsTable&\Cake\ORM\Association\HasMany $Subjects
 * @property \App\Model\Table\TeachersTable&\Cake\ORM\Association\HasMany $Teachers
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\HasMany $Users
 * @property \App\Model\Table\FeesTable&\Cake\ORM\Association\BelongsToMany $Fees
 * @property \App\Model\Table\LevelsTable&\Cake\ORM\Association\BelongsToMany $Levels
 * @property \App\Model\Table\ProgrammesTable&\Cake\ORM\Association\BelongsToMany $Programmes
 * @property \App\Model\Table\SemestersTable&\Cake\ORM\Association\BelongsToMany $Semesters
 * @property \App\Model\Table\SubjectsTable&\Cake\ORM\Association\BelongsToMany $Subjects
 *
 * @method \App\Model\Entity\Department newEmptyEntity()
 * @method \App\Model\Entity\Department newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Department[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Department get($primaryKey, $options = [])
 * @method \App\Model\Entity\Department findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Department patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Department[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Department|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Department saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Department[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Department[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Department[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Department[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class DepartmentsTable extends Table
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

        $this->setTable('departments');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->belongsTo('Faculties', [
            'foreignKey' => 'faculty_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('Admins', [
            'foreignKey' => 'department_id',
        ]);
        $this->hasMany('Courseassignments', [
            'foreignKey' => 'department_id',
        ]);
        $this->hasMany('Coursematerials', [
            'foreignKey' => 'department_id',
        ]);
        $this->hasMany('Dstudents', [
            'foreignKey' => 'department_id',
        ]);
        $this->hasMany('Feeallocations', [
            'foreignKey' => 'department_id',
        ]);
        $this->hasMany('Results', [
            'foreignKey' => 'department_id',
        ]);
        $this->hasMany('Students', [
            'foreignKey' => 'department_id',
        ]);
        $this->hasMany('Subjects', [
            'foreignKey' => 'department_id',
        ]);
        $this->hasMany('Teachers', [
            'foreignKey' => 'department_id',
        ]);
        $this->hasMany('Users', [
            'foreignKey' => 'department_id',
        ]);
        $this->belongsToMany('Fees', [
            'foreignKey' => 'department_id',
            'targetForeignKey' => 'fee_id',
            'joinTable' => 'departments_fees',
        ]);
        $this->belongsToMany('Levels', [
            'foreignKey' => 'department_id',
            'targetForeignKey' => 'level_id',
            'joinTable' => 'departments_levels',
        ]);
//        $this->belongsToMany('Programes', [
//            'foreignKey' => 'department_id',
//            'targetForeignKey' => 'programe_id',
//            'joinTable' => 'departments_programes',
//        ]);
        $this->belongsToMany('Programmes', [
            'foreignKey' => 'department_id',
            'targetForeignKey' => 'programme_id',
            'joinTable' => 'departments_programmes',
        ]);
        $this->belongsToMany('Semesters', [
            'foreignKey' => 'department_id',
            'targetForeignKey' => 'semester_id',
            'joinTable' => 'departments_semesters',
        ]);
        $this->belongsToMany('Subjects', [
            'foreignKey' => 'department_id',
            'targetForeignKey' => 'subject_id',
            'joinTable' => 'departments_subjects',
        ]);
        $this->hasMany('ClassArms', [
            'foreignKey' => 'department_id',
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
            ->integer('faculty_id')
            ->allowEmptyString('faculty_id', null, 'create');

        $validator
            ->scalar('name')
            ->maxLength('name', 64)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->scalar('deptcode')
            ->maxLength('deptcode', 44)
            ->requirePresence('deptcode', 'create')
            ->notEmptyString('deptcode');

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
        // Only check faculty_id exists if it's not 0 or null
        $rules->add(function ($entity, $options) {
            if (empty($entity->faculty_id) || $entity->faculty_id == 0) {
                return true; // Skip validation for null/0 faculty_id
            }
            $faculty = $this->Faculties->find()->where(['id' => $entity->faculty_id])->first();
            return !empty($faculty);
        }, 'validFaculty', [
            'errorField' => 'faculty_id',
            'message' => 'Faculty does not exist'
        ]);

        return $rules;
    }
}
