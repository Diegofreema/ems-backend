<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Hostels Model
 *
 * @property \App\Model\Table\HostelroomsTable&\Cake\ORM\Association\HasMany $Hostelrooms
 *
 * @method \App\Model\Entity\Hostel newEmptyEntity()
 * @method \App\Model\Entity\Hostel newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Hostel[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Hostel get($primaryKey, $options = [])
 * @method \App\Model\Entity\Hostel findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Hostel patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Hostel[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Hostel|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Hostel saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Hostel[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Hostel[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Hostel[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Hostel[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class HostelsTable extends Table
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

        $this->setTable('hostels');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->hasMany('Hostelrooms', [
            'foreignKey' => 'hostel_id',
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
            ->maxLength('name', 200)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->scalar('type')
            ->maxLength('type', 30)
            ->requirePresence('type', 'create')
            ->notEmptyString('type');

        $validator
            ->scalar('address')
            ->maxLength('address', 200)
            ->requirePresence('address', 'create')
            ->notEmptyString('address');

        $validator
            ->scalar('phone')
            ->maxLength('phone', 18)
            ->allowEmptyString('phone');

        return $validator;
    }
}
