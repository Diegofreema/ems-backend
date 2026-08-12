<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Eresources Model
 *
 * @property \App\Model\Table\DepartmentsTable&\Cake\ORM\Association\BelongsTo $Departments
 *
 * @method \App\Model\Entity\Eresource newEmptyEntity()
 * @method \App\Model\Entity\Eresource newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Eresource[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Eresource get($primaryKey, $options = [])
 * @method \App\Model\Entity\Eresource findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Eresource patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Eresource[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Eresource|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Eresource saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Eresource[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Eresource[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Eresource[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Eresource[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class EresourcesTable extends Table
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

        $this->setTable('eresources');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->belongsTo('Departments', [
            'foreignKey' => 'department_id',
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
            ->scalar('title')
            ->maxLength('title', 222)
            ->requirePresence('title', 'create')
            ->notEmptyString('title');

        $validator
            ->scalar('pubdate')
            ->maxLength('pubdate', 40)
            ->requirePresence('pubdate', 'create')
            ->notEmptyString('pubdate');

        $validator
            ->scalar('isbn')
            ->maxLength('isbn', 20)
            ->allowEmptyString('isbn');

        $validator
            ->scalar('author')
            ->maxLength('author', 144)
            ->requirePresence('author', 'create')
            ->notEmptyString('author');

        $validator
            ->integer('department_id')
            ->notEmptyString('department_id');

        $validator
            ->dateTime('dateadded')
            ->notEmptyDateTime('dateadded');

        $validator
            ->integer('viewcount')
            ->allowEmptyString('viewcount');

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
        $rules->add($rules->existsIn('department_id', 'Departments'), ['errorField' => 'department_id']);

        return $rules;
    }
}
