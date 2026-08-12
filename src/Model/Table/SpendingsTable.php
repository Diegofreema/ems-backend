<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Spendings Model
 *
 * @method \App\Model\Entity\Spending newEmptyEntity()
 * @method \App\Model\Entity\Spending newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Spending[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Spending get($primaryKey, $options = [])
 * @method \App\Model\Entity\Spending findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Spending patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Spending[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Spending|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Spending saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Spending[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Spending[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Spending[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Spending[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class SpendingsTable extends Table
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

        $this->setTable('spendings');
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
            ->scalar('amount')
            ->maxLength('amount', 20)
            ->requirePresence('amount', 'create')
            ->notEmptyString('amount');

        $validator
            ->scalar('description')
            ->maxLength('description', 900)
            ->requirePresence('description', 'create')
            ->notEmptyString('description');

        $validator
            ->dateTime('datecreated')
            ->notEmptyDateTime('datecreated');

        return $validator;
    }
}
