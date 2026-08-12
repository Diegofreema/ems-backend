<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Sparents Model
 *
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\StudentsTable&\Cake\ORM\Association\BelongsToMany $Students
 *
 * @method \App\Model\Entity\Sparent newEmptyEntity()
 * @method \App\Model\Entity\Sparent newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Sparent[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Sparent get($primaryKey, $options = [])
 * @method \App\Model\Entity\Sparent findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Sparent patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Sparent[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Sparent|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Sparent saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Sparent[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Sparent[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Sparent[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Sparent[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class SparentsTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    // public function initialize(array $config): void
    // {
    //     parent::initialize($config);

    //     $this->setTable('sparents');
    //     $this->setDisplayField('id');
    //     $this->setPrimaryKey('id');

    //     $this->belongsTo('Users', [
    //         'foreignKey' => 'user_id',
    //         'joinType' => 'INNER',
    //     ]);
    //     $this->belongsToMany('Students', [
    //         'foreignKey' => 'sparent_id',
    //         'targetForeignKey' => 'student_id',
    //         'joinTable' => 'sparents_students',
    //     ]);
    // }


    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('sparents');
        $this->setDisplayField('mothersname');
        $this->setPrimaryKey('id');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER'
        ]);
        $this->hasMany('Parentsmessages', [
            'foreignKey' => 'sparent_id'
        ]);
        $this->hasMany('Students', [
            'foreignKey' => 'sparent_id'
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

//        $validator
//            ->scalar('fathersname')
//            ->maxLength('fathersname', 188)
//            ->requirePresence('fathersname', 'create')
//            ->notEmptyString('fathersname');

//        $validator
//            ->scalar('mothersname')
//            ->maxLength('mothersname', 188)
//            ->requirePresence('mothersname', 'create')
//            ->notEmptyString('mothersname');

//        $validator
//            ->scalar('fatherphone')
//            ->maxLength('fatherphone', 18)
//            ->requirePresence('fatherphone', 'create')
//            ->notEmptyString('fatherphone');

        $validator
            ->scalar('motherphone')
            ->maxLength('motherphone', 18)
            ->allowEmptyString('motherphone');

        $validator
            ->scalar('fathersjob')
            ->maxLength('fathersjob', 166)
            ->allowEmptyString('fathersjob');

//        $validator
//            ->scalar('mothersjob')
//            ->maxLength('mothersjob', 166)
//            ->requirePresence('mothersjob', 'create')
//            ->notEmptyString('mothersjob');
//
//        $validator
//            ->scalar('pemailaddress')
//            ->maxLength('pemailaddress', 202)
//            ->requirePresence('pemailaddress', 'create')
//            ->notEmptyString('pemailaddress');

        // $validator
        //     ->scalar('address')
        //     ->maxLength('address', 200)
        //     ->requirePresence('address', 'create')
        //     ->notEmptyString('address');

//        $validator
//            ->scalar('status')
//            ->maxLength('status', 32)
//            ->notEmptyString('status');

        return $validator;
    }

    /**
     * Custom finder for parents list with both father's and mother's names
     *
     * @param \Cake\ORM\Query $query The query to modify
     * @param array $options The options for the finder
     * @return \Cake\ORM\Query
     */
    public function findWithFullNames(\Cake\ORM\Query $query, array $options)
    {
        return $query->select(['id', 'fathersname', 'mothersname']);
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
        $rules->add($rules->existsIn(['user_id'], 'Users'));

        return $rules;
    }
}
