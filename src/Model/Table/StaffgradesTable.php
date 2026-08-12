<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Staffgrades Model
 *
 * @property \App\Model\Table\TeachersTable&\Cake\ORM\Association\HasMany $Teachers
 *
 * @method \App\Model\Entity\Staffgrade newEmptyEntity()
 * @method \App\Model\Entity\Staffgrade newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Staffgrade[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Staffgrade get($primaryKey, $options = [])
 * @method \App\Model\Entity\Staffgrade findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Staffgrade patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Staffgrade[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Staffgrade|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Staffgrade saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Staffgrade[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Staffgrade[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Staffgrade[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Staffgrade[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class StaffgradesTable extends Table
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

        $this->setTable('staffgrades');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->hasMany('Teachers', [
            'foreignKey' => 'staffgrade_id',
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
            ->maxLength('name', 120)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->integer('basicsalary')
            ->requirePresence('basicsalary', 'create')
            ->notEmptyString('basicsalary');

        $validator
            ->integer('tax')
            ->notEmptyString('tax');

        $validator
            ->integer('deduction')
            ->notEmptyString('deduction');

        $validator
            ->integer('allowance')
            ->notEmptyString('allowance');

        return $validator;
    }
}
