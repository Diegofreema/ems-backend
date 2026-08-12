<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Students Model
 *
 * @property \App\Model\Table\DepartmentsTable&\Cake\ORM\Association\BelongsTo $Departments
 * @property \App\Model\Table\StatesTable&\Cake\ORM\Association\BelongsTo $States
 * @property \App\Model\Table\CountriesTable&\Cake\ORM\Association\BelongsTo $Countries
 * @property \App\Model\Table\LgasTable&\Cake\ORM\Association\BelongsTo $Lgas
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\BorrowedbooksTable&\Cake\ORM\Association\HasMany $Borrowedbooks
 * @property \App\Model\Table\CourseregistrationsTable&\Cake\ORM\Association\HasMany $Courseregistrations
 * @property \App\Model\Table\InvoicesTable&\Cake\ORM\Association\HasMany $Invoices
 * @property \App\Model\Table\ResultsTable&\Cake\ORM\Association\HasMany $Results
 * @property \App\Model\Table\StudentmessagesTable&\Cake\ORM\Association\HasMany $Studentmessages
 * @property \App\Model\Table\TransactionsTable&\Cake\ORM\Association\HasMany $Transactions
 * @property \App\Model\Table\TrequestsTable&\Cake\ORM\Association\HasMany $Trequests
 * @property \App\Model\Table\FeesTable&\Cake\ORM\Association\BelongsToMany $Fees
 * @property \App\Model\Table\HostelroomsTable&\Cake\ORM\Association\BelongsToMany $Hostelrooms
 * @property \App\Model\Table\SparentsTable&\Cake\ORM\Association\BelongsToMany $Sparents
 * @property \App\Model\Table\SubjectsTable&\Cake\ORM\Association\BelongsToMany $Subjects
 *
 * @method \App\Model\Entity\Student newEmptyEntity()
 * @method \App\Model\Entity\Student newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Student[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Student get($primaryKey, $options = [])
 * @method \App\Model\Entity\Student findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Student patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Student[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Student|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Student saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Student[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Student[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Student[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Student[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class StudentsTable extends Table
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

        $this->setTable('students');
        $this->setDisplayField('regno');
        $this->setPrimaryKey('id');

        $this->belongsTo('Departments', [
            'foreignKey' => 'department_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('States', [
            'foreignKey' => 'state_id',
            'joinType' => 'INNER',
        ]);
        //  $this->belongsTo('Modes', [
        //     'foreignKey' => 'mode_id',
        //     'joinType' => 'INNER',
        // ]);
        $this->belongsTo('Countries', [
            'foreignKey' => 'country_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Lgas', [
            'foreignKey' => 'lga_id',
        ]);
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Sparents', [
            'foreignKey' => 'sparent_id',
            'joinType' => 'INNER'
        ]);
        // $this->belongsTo('Levels', [
        //     'foreignKey' => 'level_id',
        //     'joinType' => 'INNER',
        // ]);
        // $this->belongsTo('Faculties', [
        //     'foreignKey' => 'faculty_id',
        //     'joinType' => 'INNER',
        // ]);
        // $this->belongsTo('Programmes', [
        //     'foreignKey' => 'programme_id',
        //     'joinType' => 'INNER',
        // ]);
        //   $this->belongsTo('Programetypes', [
        //     'foreignKey' => 'programtype_id',
        //     'joinType' => 'INNER',
        // ]);
        //   $this->belongsTo('Categories', [
        //     'foreignKey' => 'category_id',
        //     //'joinType' => 'INNER',
        // ]);
        $this->hasMany('Borrowedbooks', [
            'foreignKey' => 'student_id',
        ]);
        $this->hasMany('Courseregistrations', [
            'foreignKey' => 'student_id',
        ]);
        $this->hasMany('Invoices', [
            'foreignKey' => 'student_id',
        ]);
        $this->hasMany('Results', [
            'foreignKey' => 'student_id',
        ]);
        $this->hasMany('Studentmessages', [
            'foreignKey' => 'student_id',
        ]);
        $this->hasMany('Transactions', [
            'foreignKey' => 'student_id',
        ]);
        $this->hasMany('Trequests', [
            'foreignKey' => 'student_id',
        ]);
        $this->belongsToMany('Fees', [
            'foreignKey' => 'student_id',
            'targetForeignKey' => 'fee_id',
            'joinTable' => 'fees_students',
        ]);
        $this->belongsToMany('Hostelrooms', [
            'foreignKey' => 'student_id',
            'targetForeignKey' => 'hostelroom_id',
            'joinTable' => 'hostelrooms_students',
        ]);
        // $this->belongsToMany('Sparents', [
        //     'foreignKey' => 'student_id',
        //     'targetForeignKey' => 'sparent_id',
        //     'joinTable' => 'sparents_students',
        // ]);
        // $this->belongsToMany('Sparents', [
        //     'through' => 'SparentsStudents',
        //     'foreignKey' => 'student_id',
        //     'targetForeignKey' => 'parent_id',
        // ]);

        $this->hasMany('StudentsClasses', ['foreignKey' => 'student_id']);
        $this->hasMany('ResultsSecondary', ['foreignKey' => 'student_id']);

        $this->belongsToMany('Subjects', [
            'foreignKey' => 'student_id',
            'targetForeignKey' => 'subject_id',
            'joinTable' => 'subjects_students',
        ]);
        $this->belongsTo('Sessions', [
            'foreignKey' => 'session_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('ClassArms', [
            'foreignKey' => 'class_arm_id',
            'joinType' => 'LEFT',
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
            ->allowEmpty('id', 'create');

        $validator
            ->scalar('fname')
            ->maxLength('fname', 188)
            ->requirePresence('fname', 'create')
            ->notEmpty('fname');

        $validator
            ->scalar('lname')
            ->maxLength('lname', 188)
            ->requirePresence('lname', 'create')
            ->notEmpty('lname');

        $validator
            ->scalar('mname')
            ->maxLength('mname', 188)
            ->allowEmpty('mname');

        $validator
            ->scalar('dob')
            ->maxLength('dob', 44)
            ->requirePresence('dob', 'create')
            ->notEmpty('dob');


//        $validator
//            ->scalar('birthcerturl')
//            ->maxLength('birthcerturl', 188)
//            ->requirePresence('birthcerturl', 'create')
//            ->notEmpty('birthcerturl');

    //        $validator
    //            ->scalar('othercerts')
    //            ->maxLength('othercerts', 188)
    //            ->allowEmpty('othercerts');
    //
    //        $validator
    //            ->email('email')
    //            ->requirePresence('email', 'create')
    //            ->notEmpty('email');

        $validator
            ->scalar('address')
            ->maxLength('address', 200)
            ->requirePresence('address', 'create')
            ->notEmpty('address');

    //        $validator
    //            ->scalar('phone')
    //            ->maxLength('phone', 16)
    //            ->requirePresence('phone', 'create')
    //            ->notEmpty('phone');

    //        $validator
    //            ->scalar('fathersname')
    //            ->maxLength('fathersname', 188)
    //            ->requirePresence('fathersname', 'create')
    //            ->notEmpty('fathersname');
    //
    //        $validator
    //            ->scalar('mothersname')
    //            ->maxLength('mothersname', 188)
    //            ->requirePresence('mothersname', 'create')
    //            ->notEmpty('mothersname');
    //
    //        $validator
    //            ->scalar('fatherphone')
    //            ->maxLength('fatherphone', 16)
    //            ->requirePresence('fatherphone', 'create')
    //            ->notEmpty('fatherphone');
    //
    //        $validator
    //            ->scalar('motherphone')
    //            ->maxLength('motherphone', 16)
    //            ->requirePresence('motherphone', 'create')
    //            ->notEmpty('motherphone');
    //
    //        $validator
    //            ->scalar('fathersjob')
    //            ->maxLength('fathersjob', 199)
    //            ->allowEmpty('fathersjob');
    //
    //        $validator
    //            ->scalar('mothersjob')
    //            ->maxLength('mothersjob', 188)
    //            ->allowEmpty('mothersjob');

        // $validator
        //     ->scalar('passporturl')
        //     ->maxLength('passporturl', 199)
        //     ->allowEmpty('passporturl');

        $validator
            ->scalar('regno')
            ->maxLength('regno', 50)
            ->allowEmpty('regno');

    //        $validator
    //            ->scalar('status')
    //            ->maxLength('status', 30)
    //            ->requirePresence('status', 'create')
    //            ->notEmpty('status');
    //
    //        $validator
    //            ->scalar('admissiondate')
    //            ->maxLength('admissiondate', 54)
    //            ->allowEmpty('admissiondate');

        $validator
            ->scalar('gender')
            ->maxLength('gender', 32)
            ->requirePresence('gender', 'create')
            ->notEmpty('gender');

        $validator
            ->integer('class_arm_id')
            ->allowEmptyString('class_arm_id');

    //        $validator
    //            ->scalar('application_no')
    //            ->maxLength('application_no', 66)
    //            ->requirePresence('application_no', 'create')
    //            ->notEmpty('application_no');

        return $validator;
    }

    /**
     * Custom finder for students list with full names
     *
     * @param \Cake\ORM\Query $query The query to modify
     * @param array $options The options for the finder
     * @return \Cake\ORM\Query
     */
    public function findWithFullNames(\Cake\ORM\Query $query, array $options)
    {
        return $query->select(['id', 'fname', 'lname']);
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
        // $rules->add($rules->isUnique(['email']));
        $rules->add($rules->existsIn(['department_id'], 'Departments'));
        $rules->add($rules->existsIn(['state_id'], 'States'));
        $rules->add($rules->existsIn(['country_id'], 'Countries'));
        $rules->add($rules->existsIn(['user_id'], 'Users'));
        $rules->add($rules->existsIn(['class_arm_id'], 'ClassArms'));
        // $rules->add($rules->existsIn(['level_id'], 'Levels'));
                 // $rules->add($rules->existsIn(['sparent_id'], 'Sparents'));
         // Temporarily disable sparent_id validation to allow saving
         // $rules->add($rules->existsIn(['sparent_id'], 'Sparents'));

        return $rules;
    }
}
