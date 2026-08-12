<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Cafcredit Model
 *
 * @method \App\Model\Entity\Cafcredit newEmptyEntity()
 * @method \App\Model\Entity\Cafcredit newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Cafcredit[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Cafcredit get($primaryKey, $options = [])
 * @method \App\Model\Entity\Cafcredit findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Cafcredit patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Cafcredit[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Cafcredit|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Cafcredit saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Cafcredit[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Cafcredit[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Cafcredit[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Cafcredit[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class CafcreditTable extends Table
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

        $this->setTable('cafcredit');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');
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
            ->scalar('matricnum')
            ->maxLength('matricnum', 50)
            ->requirePresence('matricnum', 'create')
            ->notEmptyString('matricnum');

        $validator
            ->integer('amount')
            ->requirePresence('amount', 'create')
            ->notEmptyString('amount');

        $validator
            ->dateTime('date1')
            ->requirePresence('date1', 'create')
            ->notEmptyDateTime('date1');

        return $validator;
    }
}
