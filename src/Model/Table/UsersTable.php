<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Users Model
 *
 * @property \App\Model\Table\RolesTable&\Cake\ORM\Association\BelongsTo $Roles
 * @property \App\Model\Table\CountriesTable&\Cake\ORM\Association\BelongsTo $Countries
 * @property \App\Model\Table\StatesTable&\Cake\ORM\Association\BelongsTo $States
 * @property \App\Model\Table\DepartmentsTable&\Cake\ORM\Association\BelongsTo $Departments
 * @property \App\Model\Table\AdminsTable&\Cake\ORM\Association\HasMany $Admins
 * @property \App\Model\Table\AdmisionconditionsTable&\Cake\ORM\Association\HasMany $Admisionconditions
 * @property \App\Model\Table\BooksTable&\Cake\ORM\Association\HasMany $Books
 * @property \App\Model\Table\CourseassignmentsTable&\Cake\ORM\Association\HasMany $Courseassignments
 * @property \App\Model\Table\DstudentsTable&\Cake\ORM\Association\HasMany $Dstudents
 * @property \App\Model\Table\FeeallocationsTable&\Cake\ORM\Association\HasMany $Feeallocations
 * @property \App\Model\Table\FeesTable&\Cake\ORM\Association\HasMany $Fees
 * @property \App\Model\Table\LogsTable&\Cake\ORM\Association\HasMany $Logs
 * @property \App\Model\Table\NewsTable&\Cake\ORM\Association\HasMany $News
 * @property \App\Model\Table\NotificationsTable&\Cake\ORM\Association\HasMany $Notifications
 * @property \App\Model\Table\ResultsTable&\Cake\ORM\Association\HasMany $Results
 * @property \App\Model\Table\SessionsTable&\Cake\ORM\Association\HasMany $Sessions
 * @property \App\Model\Table\SparentsTable&\Cake\ORM\Association\HasMany $Sparents
 * @property \App\Model\Table\StaffmessagesTable&\Cake\ORM\Association\HasMany $Staffmessages
 * @property \App\Model\Table\StudentmessagesTable&\Cake\ORM\Association\HasMany $Studentmessages
 * @property \App\Model\Table\StudentsTable&\Cake\ORM\Association\HasMany $Students
 * @property \App\Model\Table\SubjectsTable&\Cake\ORM\Association\HasMany $Subjects
 * @property \App\Model\Table\TeachersTable&\Cake\ORM\Association\HasMany $Teachers
 * @property \App\Model\Table\TopicsTable&\Cake\ORM\Association\HasMany $Topics
 * @property \App\Model\Table\UserloginsTable&\Cake\ORM\Association\HasMany $Userlogins
 * @method \App\Model\Entity\User newEmptyEntity()
 * @method \App\Model\Entity\User newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\User[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\User get($primaryKey, $options = [])
 * @method \App\Model\Entity\User findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\User patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\User[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\User|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\User saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class UsersTable extends Table
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

        $this->setTable('users');
        $this->setDisplayField('username');
        $this->setPrimaryKey('id');

        $this->belongsTo('Roles', [
        'foreignKey' => 'role_id',
        'joinType' => 'INNER',
        ]);
        $this->belongsTo('Countries', [
        'foreignKey' => 'country_id',
        'joinType' => 'INNER',
        ]);
        $this->belongsTo('States', [
        'foreignKey' => 'state_id',
        'joinType' => 'INNER',
        ]);
        $this->belongsTo('Departments', [
        'foreignKey' => 'department_id',
        'joinType' => 'INNER',
        ]);
        $this->hasMany('Admins', [
        'foreignKey' => 'user_id',
        ]);
        $this->hasMany('Admisionconditions', [
        'foreignKey' => 'user_id',
        ]);
        $this->hasMany('Books', [
        'foreignKey' => 'user_id',
        ]);
        $this->hasMany('Courseassignments', [
        'foreignKey' => 'user_id',
        ]);
        $this->hasMany('Dstudents', [
        'foreignKey' => 'user_id',
        ]);
        $this->hasMany('Feeallocations', [
        'foreignKey' => 'user_id',
        ]);
        $this->hasMany('Fees', [
        'foreignKey' => 'user_id',
        ]);
        $this->hasMany('Logs', [
        'foreignKey' => 'user_id',
        ]);
        $this->hasMany('News', [
        'foreignKey' => 'user_id',
        ]);
        $this->hasMany('Notifications', [
        'foreignKey' => 'user_id',
        ]);
        $this->hasMany('Results', [
        'foreignKey' => 'user_id',
        ]);
        $this->hasMany('Sessions', [
        'foreignKey' => 'user_id',
        ]);
        $this->hasMany('Sparents', [
        'foreignKey' => 'user_id',
        ]);
        $this->hasMany('Staffmessages', [
        'foreignKey' => 'user_id',
        ]);
        $this->hasMany('Studentmessages', [
        'foreignKey' => 'user_id',
        ]);
        $this->hasMany('Students', [
        'foreignKey' => 'user_id',
        ]);
        $this->hasMany('Subjects', [
        'foreignKey' => 'user_id',
        ]);
        $this->hasMany('Teachers', [
        'foreignKey' => 'user_id',
        ]);
        $this->hasMany('Topics', [
        'foreignKey' => 'user_id',
        ]);
        $this->hasMany('Userlogins', [
        'foreignKey' => 'user_id',
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
        ->scalar('username')
        ->maxLength('username', 250)
        ->requirePresence('username', 'create')
        ->notEmptyString('username', 'Please provide a valid email address as your user name')
        ->add('username', ['unique' => ['rule' => 'validateUnique', 'provider' => 'table', 'message' => 'username already exists']])
    //->add('username', 'valid-email', ['rule' => 'email'])
        ->notEmptyString('username');

        $validator
        ->scalar('password')
        ->maxLength('password', 230)
        ->requirePresence('password', 'create')
        ->add('password', ['length' => ['rule' => ['minLength', 6],
        'message' => 'Invalid password. password must not be less than six characters',]])
        ->notEmptyString('password');

        $validator
        ->add('cpassword', [
        'equalToPassword' => [
        'rule' => function ($value, $context) {
            return $value === $context['data']['password'];
        },
        'message' => __('Password mismatch, both password must match.'),
        ],
        ]);
        $validator
        ->add('currentpass', 'custom', [
        'rule' => function ($value, $context) {
            $user = $this->get($context['data']['id']);
            if ($user) {
                if ((new DefaultPasswordHasher())->check($value, $user->password)) {
                    return true;
                }
            }

            return false;
        },
        'message' => 'The old password does not match the current password!',
        ])
        ->allowEmptyString('currentpass');

        $validator
        ->scalar('fname')
        ->maxLength('fname', 64)
        ->requirePresence('fname', 'create')
        ->notEmptyString('fname');

        $validator
        ->scalar('lname')
        ->maxLength('lname', 64)
        ->requirePresence('lname', 'create')
        ->notEmptyString('lname');

        $validator
        ->scalar('mname')
        ->maxLength('mname', 64)
        ->allowEmptyString('mname');

        $validator
        ->scalar('gender')
        ->maxLength('gender', 15)
        ->requirePresence('gender', 'create')
        ->notEmptyString('gender');

        $validator
        ->scalar('address')
        ->maxLength('address', 250)
        ->requirePresence('address', 'create')
        ->notEmptyString('address');

        $validator
        ->scalar('phone')
        ->maxLength('phone', 32)
        ->requirePresence('phone', 'create')
        ->notEmptyString('phone');

        $validator
        ->scalar('profile')
        ->allowEmptyFile('profile');

        $validator
        ->scalar('dob')
        ->maxLength('dob', 64)
        ->allowEmptyString('dob');

        $validator
        ->dateTime('created_date')
        ->notEmptyDateTime('created_date');

        $validator
        ->integer('created_by')
        ->requirePresence('created_by', 'create')
        ->notEmptyString('created_by');

        $validator
        ->scalar('passport')
        ->maxLength('passport', 128)
        ->allowEmptyString('passport');

        $validator
        ->scalar('useruniquid')
        ->maxLength('useruniquid', 28)
        ->requirePresence('useruniquid', 'create')
        ->notEmptyString('useruniquid');

        $validator
        ->scalar('userstatus')
        ->maxLength('userstatus', 30)
        ->notEmptyString('userstatus');

        $validator
        ->scalar('verification_key')
        ->maxLength('verification_key', 188)
        ->allowEmptyString('verification_key');

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
        $rules->add($rules->isUnique(['username']));
        $rules->add($rules->existsIn(['role_id'], 'Roles'));
        $rules->add($rules->existsIn(['country_id'], 'Countries'));
        $rules->add($rules->existsIn(['state_id'], 'States'));
        $rules->add($rules->existsIn(['department_id'], 'Departments'));

        return $rules;
    }
}
