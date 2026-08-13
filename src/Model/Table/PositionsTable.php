<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Positions Model
 *
 * @property \App\Model\Table\CandidatesTable&\Cake\ORM\Association\HasMany $Candidates
 * @method \App\Model\Entity\Position newEmptyEntity()
 * @method \App\Model\Entity\Position newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Position[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Position get($primaryKey, $options = [])
 * @method \App\Model\Entity\Position findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Position patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Position[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Position|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Position saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Position[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Position[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Position[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Position[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class PositionsTable extends Table
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

        $this->setTable('positions');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->hasMany('Candidates', [
            'foreignKey' => 'position_id',
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
            ->scalar('name')
            ->maxLength('name', 200)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        return $validator;
    }
}
