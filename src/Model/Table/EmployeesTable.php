<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Employees Model
 *
 * @property \App\Model\Table\StatesTable&\Cake\ORM\Association\BelongsTo $States
 * @property \App\Model\Table\LgasTable&\Cake\ORM\Association\BelongsTo $Lgas
 * @property \App\Model\Table\StaffgradesTable&\Cake\ORM\Association\BelongsTo $Staffgrades
 * @property \App\Model\Table\StaffdepartmentsTable&\Cake\ORM\Association\BelongsTo $Staffdepartments
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\AdminsTable&\Cake\ORM\Association\BelongsTo $Admins
 *
 * @method \App\Model\Entity\Employee newEmptyEntity()
 * @method \App\Model\Entity\Employee newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Employee[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Employee get($primaryKey, $options = [])
 * @method \App\Model\Entity\Employee findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Employee patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Employee[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Employee|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Employee saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Employee[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Employee[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Employee[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Employee[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class EmployeesTable extends Table
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

        $this->setTable('employees');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('States', [
            'foreignKey' => 'state_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Lgas', [
            'foreignKey' => 'lga_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Staffgrades', [
            'foreignKey' => 'staffgrade_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Staffdepartments', [
            'foreignKey' => 'staffdepartment_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Admins', [
            'foreignKey' => 'admin_id',
            'joinType' => 'INNER',
        ]);
         $this->hasMany('Payslips', [
            'foreignKey' => 'employee_id',
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
            ->scalar('fname')
            ->maxLength('fname', 222)
            ->requirePresence('fname', 'create')
            ->notEmptyString('fname');

        $validator
            ->scalar('mname')
            ->maxLength('mname', 222)
            ->requirePresence('mname', 'create')
            ->notEmptyString('mname');

        $validator
            ->scalar('sname')
            ->maxLength('sname', 222)
            ->requirePresence('sname', 'create')
            ->notEmptyString('sname');

//        $validator
//            ->scalar('empid')
//            ->maxLength('empid', 18)
//            ->requirePresence('empid', 'create')
//            ->notEmptyString('empid');

        $validator
            ->scalar('address')
            ->maxLength('address', 800)
            ->requirePresence('address', 'create')
            ->notEmptyString('address');

        $validator
            ->scalar('phone')
            ->maxLength('phone', 18)
            ->requirePresence('phone', 'create')
            ->notEmptyString('phone');

        $validator
            ->scalar('photo')
            ->maxLength('photo', 222)
            ->allowEmptyString('photo');

        $validator
            ->scalar('dod')
            ->maxLength('dod', 44)
            ->requirePresence('dod', 'create')
            ->notEmptyString('dod');

        $validator
            ->scalar('hqn')
            ->maxLength('hqn', 122)
            ->requirePresence('hqn', 'create')
            ->notEmptyString('hqn');

        $validator
            ->scalar('profile')
            ->allowEmptyFile('profile');

        $validator
            ->scalar('gender')
            ->maxLength('gender', 12)
            ->requirePresence('gender', 'create')
            ->notEmptyString('gender');

        $validator
            ->dateTime('dateadded')
            ->allowEmptyDateTime('dateadded');

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
        $rules->add($rules->existsIn(['state_id'], 'States'));
        $rules->add($rules->existsIn(['lga_id'], 'Lgas'));
        $rules->add($rules->existsIn(['staffgrade_id'], 'Staffgrades'));
        $rules->add($rules->existsIn(['staffdepartment_id'], 'Staffdepartments'));
        $rules->add($rules->existsIn(['user_id'], 'Users'));
        $rules->add($rules->existsIn(['admin_id'], 'Admins'));

        return $rules;
    }
}
