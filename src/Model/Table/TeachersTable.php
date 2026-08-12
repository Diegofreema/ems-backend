<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;

/**
 * Teachers Model
 *
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\CountriesTable&\Cake\ORM\Association\BelongsTo $Countries
 * @property \App\Model\Table\StatesTable&\Cake\ORM\Association\BelongsTo $States
 * @property \App\Model\Table\DepartmentsTable&\Cake\ORM\Association\BelongsTo $Departments
 * @property \App\Model\Table\StaffgradesTable&\Cake\ORM\Association\BelongsTo $Staffgrades
 * @property \App\Model\Table\StaffdepartmentsTable&\Cake\ORM\Association\BelongsTo $Staffdepartments
 * @property \App\Model\Table\CoursematerialsTable&\Cake\ORM\Association\HasMany $Coursematerials
 * @property \App\Model\Table\PayslipsTable&\Cake\ORM\Association\HasMany $Payslips
 * @property \App\Model\Table\StaffmessagesTable&\Cake\ORM\Association\HasMany $Staffmessages
 * @property \App\Model\Table\SubjectsTable&\Cake\ORM\Association\BelongsToMany $Subjects
 *
 * @method \App\Model\Entity\Teacher newEmptyEntity()
 * @method \App\Model\Entity\Teacher newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Teacher[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Teacher get($primaryKey, $options = [])
 * @method \App\Model\Entity\Teacher findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Teacher patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Teacher[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Teacher|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Teacher saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Teacher[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Teacher[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Teacher[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Teacher[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class TeachersTable extends Table
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

        $this->setTable('teachers');
        $this->setDisplayField('firstname');
        $this->setPrimaryKey('id');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Countries', [
            'foreignKey' => 'country_id',
            'joinType' => 'LEFT',
        ]);
        $this->belongsTo('States', [
            'foreignKey' => 'state_id',
            'joinType' => 'LEFT',
        ]);
        $this->belongsTo('Departments', [
            'foreignKey' => 'department_id',
            'joinType' => 'LEFT',
        ]);
        $this->belongsTo('Staffgrades', [
            'foreignKey' => 'staffgrade_id',
            'joinType' => 'LEFT',
        ]);
        $this->belongsTo('Staffdepartments', [
            'foreignKey' => 'staffdepartment_id',
            'joinType' => 'LEFT',
        ]);
        $this->hasMany('Coursematerials', [
            'foreignKey' => 'teacher_id',
        ]);
        $this->hasMany('Payslips', [
            'foreignKey' => 'teacher_id',
        ]);
        $this->hasMany('Staffmessages', [
            'foreignKey' => 'teacher_id',
        ]);
        $this->belongsToMany('Subjects', [
            'foreignKey' => 'teacher_id',
            'targetForeignKey' => 'subject_id',
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
            ->scalar('gender')
            ->maxLength('gender', 8)
            ->requirePresence('gender', 'create')
            ->notEmptyString('gender', 'Please provide a gender', 'create');

        $validator
            ->scalar('address')
            ->maxLength('address', 255)
            ->requirePresence('address', 'create')
            ->notEmptyString('address', 'Please provide an address', 'create');

        $validator
            ->scalar('phone')
            ->maxLength('phone', 16)
            ->requirePresence('phone', 'create')
            ->notEmptyString('phone', 'Please provide a phone number', 'create');

        $validator
            ->scalar('profile')
            ->maxLength('profile', 255)
            ->requirePresence('profile', 'create')
            ->notEmptyString('profile', 'Please provide a profile', 'create');

        $validator
            ->scalar('cv')
            ->maxLength('cv', 128)
            ->allowEmptyString('cv');

        $validator
            ->scalar('qualification')
            ->maxLength('qualification', 16)
            ->requirePresence('qualification', 'create')
            ->notEmptyString('qualification', 'Please provide a qualification', 'create');

        $validator
            ->dateTime('date_created')
            ->notEmptyDateTime('date_created', 'Please provide a creation date', 'create');

        $validator
            ->scalar('passport')
            ->maxLength('passport', 156)
            ->allowEmptyString('passport');

        $validator
            ->scalar('firstname')
            ->maxLength('firstname', 188)
            ->requirePresence('firstname', 'create')
            ->notEmptyString('firstname', 'Please provide a first name', 'create');

        $validator
            ->scalar('lastname')
            ->maxLength('lastname', 188)
            ->requirePresence('lastname', 'create')
            ->notEmptyString('lastname', 'Please provide a last name', 'create');

        $validator
            ->scalar('middlename')
            ->maxLength('middlename', 188)
            ->allowEmptyString('middlename');

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
        $rules->add($rules->existsIn(['user_id'], 'Users'));
        $rules->add($rules->existsIn(['country_id'], 'Countries'));
        $rules->add($rules->existsIn(['state_id'], 'States'));
        
        // Custom rule for department_id - only validate if not null
        $rules->add(function ($entity, $options) {
            if ($entity->department_id !== null) {
                $departmentsTable = TableRegistry::get('Departments');
                return $departmentsTable->exists(['id' => $entity->department_id]);
            }
            return true;
        }, 'departmentExists', [
            'errorField' => 'department_id',
            'message' => 'The selected department does not exist'
        ]);
        
        $rules->add($rules->existsIn(['staffgrade_id'], 'Staffgrades'));
        $rules->add($rules->existsIn(['staffdepartment_id'], 'Staffdepartments'));

        return $rules;
    }
}
