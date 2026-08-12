<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Letters Model
 *
 * @property \App\Model\Table\ModesTable&\Cake\ORM\Association\BelongsTo $Modes
 *
 * @method \App\Model\Entity\Letter newEmptyEntity()
 * @method \App\Model\Entity\Letter newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Letter[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Letter get($primaryKey, $options = [])
 * @method \App\Model\Entity\Letter findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Letter patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Letter[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Letter|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Letter saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Letter[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Letter[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Letter[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Letter[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class LettersTable extends Table
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

        $this->setTable('letters');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->belongsTo('Modes', [
            'foreignKey' => 'mode_id',
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
            ->scalar('letterbody')
            ->requirePresence('letterbody', 'create')
            ->notEmptyString('letterbody');

        $validator
            ->scalar('title')
            ->maxLength('title', 222)
            ->requirePresence('title', 'create')
            ->notEmptyString('title');

//        $validator
//            ->dateTime('datecreated')
//            ->notEmptyDateTime('datecreated');

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
        $rules->add($rules->existsIn('mode_id', 'Modes'), ['errorField' => 'mode_id']);

        return $rules;
    }
}
