<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * FeesSponsorships Model
 *
 * @property \App\Model\Table\FeesTable&\Cake\ORM\Association\BelongsTo $Fees
 * @property \App\Model\Table\SponsorshipsTable&\Cake\ORM\Association\BelongsTo $Sponsorships
 *
 * @method \App\Model\Entity\FeesSponsorship newEmptyEntity()
 * @method \App\Model\Entity\FeesSponsorship newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\FeesSponsorship[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\FeesSponsorship get($primaryKey, $options = [])
 * @method \App\Model\Entity\FeesSponsorship findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\FeesSponsorship patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\FeesSponsorship[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\FeesSponsorship|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\FeesSponsorship saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\FeesSponsorship[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\FeesSponsorship[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\FeesSponsorship[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\FeesSponsorship[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class FeesSponsorshipsTable extends Table
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

        $this->setTable('fees_sponsorships');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Fees', [
            'foreignKey' => 'fee_id',
            'joinType' => 'INNER',
        ]);
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
            ->integer('fee_id')
            ->notEmptyString('fee_id');

        $validator
            ->integer('sponsorship_id')
            ->notEmptyString('sponsorship_id');

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
        $rules->add($rules->existsIn('fee_id', 'Fees'), ['errorField' => 'fee_id']);
        $rules->add($rules->existsIn('sponsorship_id', 'Sponsorships'), ['errorField' => 'sponsorship_id']);

        return $rules;
    }
}
