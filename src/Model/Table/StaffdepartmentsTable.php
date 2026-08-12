<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Staffdepartments Model
 *
 * @property \App\Model\Table\TeachersTable&\Cake\ORM\Association\HasMany $Teachers
 *
 * @method \App\Model\Entity\Staffdepartment newEmptyEntity()
 * @method \App\Model\Entity\Staffdepartment newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Staffdepartment[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Staffdepartment get($primaryKey, $options = [])
 * @method \App\Model\Entity\Staffdepartment findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Staffdepartment patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Staffdepartment[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Staffdepartment|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Staffdepartment saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Staffdepartment[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Staffdepartment[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Staffdepartment[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Staffdepartment[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class StaffdepartmentsTable extends Table
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

        $this->setTable('staffdepartments');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->hasMany('Teachers', [
            'foreignKey' => 'staffdepartment_id',
        ]);
        
        $this->hasMany('Employees', [
            'foreignKey' => 'staffdepartment_id',
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
            ->maxLength('name', 240)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        return $validator;
    }
}
