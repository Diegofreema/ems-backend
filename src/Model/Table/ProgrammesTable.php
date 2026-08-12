<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Programes Model
 *
 * @property \App\Model\Table\StudentsTable&\Cake\ORM\Association\HasMany $Students
 * @property \App\Model\Table\DepartmentsTable&\Cake\ORM\Association\BelongsToMany $Departments
 *
 * @method \App\Model\Entity\Programe newEmptyEntity()
 * @method \App\Model\Entity\Programe newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Programe[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Programe get($primaryKey, $options = [])
 * @method \App\Model\Entity\Programe findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Programe patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Programe[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Programe|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Programe saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Programe[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Programe[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Programe[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Programe[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class ProgrammesTable extends Table
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

        $this->setTable('programmes');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->hasMany('Students', [
            'foreignKey' => 'programme_id',
        ]);
        $this->belongsToMany('Departments', [
            'foreignKey' => 'programme_id',
            'targetForeignKey' => 'department_id',
            'joinTable' => 'departments_programmes',
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
            ->maxLength('name', 180)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

//        $validator
//            ->scalar('programecode')
//            ->maxLength('programecode', 60)
//            ->requirePresence('programecode', 'create')
//            ->notEmptyString('programecode');

        return $validator;
    }
}
