<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * DepartmentsFees Model
 *
 * @property \App\Model\Table\FeesTable&\Cake\ORM\Association\BelongsTo $Fees
 * @property \App\Model\Table\DepartmentsTable&\Cake\ORM\Association\BelongsTo $Departments
 * @method \App\Model\Entity\DepartmentsFee newEmptyEntity()
 * @method \App\Model\Entity\DepartmentsFee newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\DepartmentsFee[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\DepartmentsFee get($primaryKey, $options = [])
 * @method \App\Model\Entity\DepartmentsFee findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\DepartmentsFee patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\DepartmentsFee[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\DepartmentsFee|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\DepartmentsFee saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\DepartmentsFee[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\DepartmentsFee[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\DepartmentsFee[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\DepartmentsFee[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class DepartmentsFeesTable extends Table
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

        $this->setTable('departments_fees');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Fees', [
            'foreignKey' => 'fee_id',
            'joinType' => 'INNER',
        ]);
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
            ->integer('fee_id')
            ->notEmptyString('fee_id');

        $validator
            ->integer('department_id')
            ->notEmptyString('department_id');

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
        $rules->add($rules->existsIn('department_id', 'Departments'), ['errorField' => 'department_id']);

        return $rules;
    }
}
