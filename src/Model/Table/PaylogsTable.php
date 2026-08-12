<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Paylogs Model
 *
 * @property \App\Model\Table\StudentsTable&\Cake\ORM\Association\BelongsTo $Students
 *
 * @method \App\Model\Entity\Paylog newEmptyEntity()
 * @method \App\Model\Entity\Paylog newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Paylog[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Paylog get($primaryKey, $options = [])
 * @method \App\Model\Entity\Paylog findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Paylog patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Paylog[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Paylog|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Paylog saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Paylog[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Paylog[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Paylog[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Paylog[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class PaylogsTable extends Table
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

        $this->setTable('paylogs');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Students', [
            'foreignKey' => 'student_id',
            'joinType' => 'INNER',
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
            ->dateTime('transdate')
            ->allowEmptyDateTime('transdate');

//        $validator
//            ->integer('tref')
//            ->requirePresence('tref', 'create')
//            ->notEmptyString('tref');

        $validator
            ->scalar('responsecode')
            ->maxLength('responsecode', 22)
            ->requirePresence('responsecode', 'create')
            ->notEmptyString('responsecode');

        $validator
            ->scalar('amount')
            ->maxLength('amount', 12)
            ->requirePresence('amount', 'create')
            ->notEmptyString('amount');

        $validator
            ->scalar('paymethod')
            ->maxLength('paymethod', 44)
            ->requirePresence('paymethod', 'create')
            ->notEmptyString('paymethod');

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
        $rules->add($rules->existsIn(['student_id'], 'Students'));

        return $rules;
    }
}
