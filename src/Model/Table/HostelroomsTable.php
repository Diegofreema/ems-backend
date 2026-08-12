<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Hostelrooms Model
 *
 * @property \App\Model\Table\HostelsTable&\Cake\ORM\Association\BelongsTo $Hostels
 * @property \App\Model\Table\StudentsTable&\Cake\ORM\Association\BelongsToMany $Students
 *
 * @method \App\Model\Entity\Hostelroom newEmptyEntity()
 * @method \App\Model\Entity\Hostelroom newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Hostelroom[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Hostelroom get($primaryKey, $options = [])
 * @method \App\Model\Entity\Hostelroom findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Hostelroom patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Hostelroom[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Hostelroom|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Hostelroom saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Hostelroom[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Hostelroom[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Hostelroom[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Hostelroom[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class HostelroomsTable extends Table
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

        $this->setTable('hostelrooms');
        $this->setDisplayField('room_number');
        $this->setPrimaryKey('id');

        $this->belongsTo('Hostels', [
            'foreignKey' => 'hostel_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsToMany('Students', [
            'foreignKey' => 'hostelroom_id',
            'targetForeignKey' => 'student_id',
            'joinTable' => 'hostelrooms_students',
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
            ->scalar('floor')
            ->maxLength('floor', 100)
            ->requirePresence('floor', 'create')
            ->notEmptyString('floor');

        $validator
            ->scalar('room_number')
            ->maxLength('room_number', 12)
            ->requirePresence('room_number', 'create')
            ->notEmptyString('room_number');

        $validator
            ->integer('available_beds')
            ->allowEmptyString('available_beds');

        $validator
            ->integer('occupiedbeds')
            ->allowEmptyString('occupiedbeds');

        $validator
            ->scalar('description')
            ->maxLength('description', 180)
            ->requirePresence('description', 'create')
            ->notEmptyString('description');

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
        $rules->add($rules->existsIn(['hostel_id'], 'Hostels'));

        return $rules;
    }
}
