<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Sponsorshippayments Model
 *
 * @property \App\Model\Table\SponsorshipsTable&\Cake\ORM\Association\BelongsTo $Sponsorships
 *
 * @method \App\Model\Entity\Sponsorshippayment newEmptyEntity()
 * @method \App\Model\Entity\Sponsorshippayment newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Sponsorshippayment[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Sponsorshippayment get($primaryKey, $options = [])
 * @method \App\Model\Entity\Sponsorshippayment findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Sponsorshippayment patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Sponsorshippayment[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Sponsorshippayment|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Sponsorshippayment saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Sponsorshippayment[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Sponsorshippayment[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Sponsorshippayment[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Sponsorshippayment[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class SponsorshippaymentsTable extends Table
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

        $this->setTable('sponsorshippayments');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Sponsorships', [
            'foreignKey' => 'sponsorship_id',
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
            ->scalar('sref')
            ->maxLength('sref', 222)
            ->requirePresence('sref', 'create')
            ->notEmptyString('sref');

        $validator
            ->integer('amount')
            ->requirePresence('amount', 'create')
            ->notEmptyString('amount');

        $validator
            ->dateTime('datecreated')
            ->allowEmptyDateTime('datecreated');

        $validator
            ->scalar('paystatus')
            ->maxLength('paystatus', 22)
            ->notEmptyString('paystatus');

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
        $rules->add($rules->existsIn(['sponsorship_id'], 'Sponsorships'));

        return $rules;
    }
}
