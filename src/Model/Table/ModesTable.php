<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Modes Model
 *
 * @method \App\Model\Entity\Mode newEmptyEntity()
 * @method \App\Model\Entity\Mode newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Mode[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Mode get($primaryKey, $options = [])
 * @method \App\Model\Entity\Mode findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Mode patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Mode[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Mode|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Mode saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Mode[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Mode[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Mode[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Mode[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class ModesTable extends Table
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

        $this->setTable('modes');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');
        
        
         $this->hasMany('Students', [
            'foreignKey' => 'mode_id',
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
            ->maxLength('name', 44)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        return $validator;
    }
}
