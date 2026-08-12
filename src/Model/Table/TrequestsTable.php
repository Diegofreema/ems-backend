<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Trequests Model
 *
 * @property \App\Model\Table\StudentsTable&\Cake\ORM\Association\BelongsTo $Students
 * @property \App\Model\Table\ContinentsTable&\Cake\ORM\Association\BelongsTo $Continents
 * @property \App\Model\Table\CountriesTable&\Cake\ORM\Association\BelongsTo $Countries
 * @property \App\Model\Table\StatesTable&\Cake\ORM\Association\BelongsTo $States
 * @property \App\Model\Table\CouriersTable&\Cake\ORM\Association\BelongsTo $Couriers
 * @property \App\Model\Table\FeesTable&\Cake\ORM\Association\BelongsTo $Fees
 *
 * @method \App\Model\Entity\Trequest newEmptyEntity()
 * @method \App\Model\Entity\Trequest newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Trequest[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Trequest get($primaryKey, $options = [])
 * @method \App\Model\Entity\Trequest findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Trequest patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Trequest[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Trequest|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Trequest saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Trequest[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Trequest[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Trequest[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Trequest[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class TrequestsTable extends Table
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

        $this->setTable('trequests');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Students', [
            'foreignKey' => 'student_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Continents', [
            'foreignKey' => 'continent_id',
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
        $this->belongsTo('Couriers', [
            'foreignKey' => 'courier_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Fees', [
            'foreignKey' => 'fee_id',
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
            ->dateTime('orderdate')
            ->notEmptyDateTime('orderdate');

        $validator
            ->scalar('institution')
            ->maxLength('institution', 344)
            ->requirePresence('institution', 'create')
            ->notEmptyString('institution');

        $validator
            ->scalar('status')
            ->maxLength('status', 44)
            ->notEmptyString('status');

        $validator
            ->scalar('address')
            ->maxLength('address', 244)
            ->requirePresence('address', 'create')
            ->notEmptyString('address');

        $validator
            ->scalar('amount')
            ->maxLength('amount', 44)
            ->requirePresence('amount', 'create')
            ->notEmptyString('amount');

        $validator
            ->scalar('deliverystatus')
            ->maxLength('deliverystatus', 44)
            ->allowEmptyString('deliverystatus');

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
        $rules->add($rules->existsIn(['continent_id'], 'Continents'));
        $rules->add($rules->existsIn(['country_id'], 'Countries'));
        $rules->add($rules->existsIn(['state_id'], 'States'));
        $rules->add($rules->existsIn(['courier_id'], 'Couriers'));
        $rules->add($rules->existsIn(['fee_id'], 'Fees'));

        return $rules;
    }
}
