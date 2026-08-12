<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Couriers Model
 *
 * @property \App\Model\Table\TrequestsTable&\Cake\ORM\Association\HasMany $Trequests
 *
 * @method \App\Model\Entity\Courier newEmptyEntity()
 * @method \App\Model\Entity\Courier newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Courier[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Courier get($primaryKey, $options = [])
 * @method \App\Model\Entity\Courier findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Courier patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Courier[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Courier|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Courier saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Courier[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Courier[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Courier[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Courier[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class CouriersTable extends Table
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

        $this->setTable('couriers');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->hasMany('Trequests', [
            'foreignKey' => 'courier_id',
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
            ->maxLength('name', 150)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        return $validator;
    }
}
