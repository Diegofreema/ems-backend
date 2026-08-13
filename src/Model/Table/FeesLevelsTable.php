<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * FeesLevels Model
 *
 * @property \App\Model\Table\FeesTable&\Cake\ORM\Association\BelongsTo $Fees
 * @property \App\Model\Table\LevelsTable&\Cake\ORM\Association\BelongsTo $Levels
 * @method \App\Model\Entity\FeesLevel newEmptyEntity()
 * @method \App\Model\Entity\FeesLevel newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\FeesLevel[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\FeesLevel get($primaryKey, $options = [])
 * @method \App\Model\Entity\FeesLevel findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\FeesLevel patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\FeesLevel[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\FeesLevel|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\FeesLevel saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\FeesLevel[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\FeesLevel[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\FeesLevel[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\FeesLevel[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class FeesLevelsTable extends Table
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

        $this->setTable('fees_levels');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Fees', [
            'foreignKey' => 'fee_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Levels', [
            'foreignKey' => 'level_id',
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
            ->integer('level_id')
            ->notEmptyString('level_id');

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
        $rules->add($rules->existsIn('level_id', 'Levels'), ['errorField' => 'level_id']);

        return $rules;
    }
}
