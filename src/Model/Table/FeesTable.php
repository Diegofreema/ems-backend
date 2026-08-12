<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Fees Model
 *
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\FeeallocationsTable&\Cake\ORM\Association\HasMany $Feeallocations
 * @property \App\Model\Table\InvoicesTable&\Cake\ORM\Association\HasMany $Invoices
 * @property \App\Model\Table\TransactionsTable&\Cake\ORM\Association\HasMany $Transactions
 * @property \App\Model\Table\TrequestsTable&\Cake\ORM\Association\HasMany $Trequests
 * @property \App\Model\Table\DepartmentsTable&\Cake\ORM\Association\BelongsToMany $Departments
 * @property \App\Model\Table\LevelsTable&\Cake\ORM\Association\BelongsToMany $Levels
 * @property \App\Model\Table\StudentsTable&\Cake\ORM\Association\BelongsToMany $Students
 *
 * @method \App\Model\Entity\Fee newEmptyEntity()
 * @method \App\Model\Entity\Fee newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Fee[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Fee get($primaryKey, $options = [])
 * @method \App\Model\Entity\Fee findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Fee patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Fee[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Fee|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Fee saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Fee[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Fee[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Fee[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Fee[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class FeesTable extends Table
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

        $this->setTable('fees');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('Feeallocations', [
            'foreignKey' => 'fee_id',
        ]);
        $this->hasMany('Invoices', [
            'foreignKey' => 'fee_id',
        ]);
        $this->hasMany('Transactions', [
            'foreignKey' => 'fee_id',
        ]);
        $this->hasMany('Trequests', [
            'foreignKey' => 'fee_id',
        ]);
        $this->belongsToMany('Departments', [
            'foreignKey' => 'fee_id',
            'targetForeignKey' => 'department_id',
            'joinTable' => 'departments_fees',
        ]);
        $this->belongsToMany('Levels', [
            'foreignKey' => 'fee_id',
            'targetForeignKey' => 'level_id',
            'joinTable' => 'fees_levels',
        ]);
        $this->belongsToMany('Students', [
            'foreignKey' => 'fee_id',
            'targetForeignKey' => 'student_id',
            'joinTable' => 'fees_students',
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
            ->maxLength('name', 98)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->integer('amount')
            ->requirePresence('amount', 'create')
            ->notEmptyString('amount');

        $validator
            ->integer('user_id')
            ->notEmptyString('user_id');

        $validator
            ->integer('status')
            ->notEmptyString('status');

        $validator
            ->scalar('startdate')
            ->maxLength('startdate', 34)
            ->allowEmptyString('startdate');

        $validator
            ->scalar('enddate')
            ->maxLength('enddate', 34)
            ->allowEmptyString('enddate');

        $validator
            ->scalar('feetype')
            ->maxLength('feetype', 40)
            ->notEmptyString('feetype');

        $validator
            ->scalar('itemcode')
            ->maxLength('itemcode', 22)
            ->allowEmptyString('itemcode');

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
        $rules->add($rules->existsIn('user_id', 'Users'), ['errorField' => 'user_id']);

        return $rules;
    }
}
