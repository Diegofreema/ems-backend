<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ClassArms Model
 *
 * @property \App\Model\Table\DepartmentsTable&\Cake\ORM\Association\BelongsTo $Departments
 * @property \App\Model\Table\TeachersTable&\Cake\ORM\Association\BelongsTo $Teachers
 * @property \App\Model\Table\StudentsTable&\Cake\ORM\Association\HasMany $Students
 * @property \App\Model\Table\ResultsTable&\Cake\ORM\Association\HasMany $Results
 * @property \App\Model\Table\AttendancesTable&\Cake\ORM\Association\HasMany $Attendances
 * @method \App\Model\Entity\ClassArm newEmptyEntity()
 * @method \App\Model\Entity\ClassArm newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\ClassArm[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ClassArm get($primaryKey, $options = [])
 * @method \App\Model\Entity\ClassArm findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\ClassArm patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\ClassArm[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\ClassArm|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ClassArm saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ClassArm[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, array $options = [])
 * @method \App\Model\Entity\ClassArm[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, array $options = [])
 * @method \App\Model\Entity\ClassArm[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, array $options = [])
 * @method \App\Model\Entity\ClassArm[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, array $options = [])
 */
class ClassArmsTable extends Table
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

        $this->setTable('class_arms');
        $this->setDisplayField('arm_name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Departments', [
            'foreignKey' => 'department_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Teachers', [
            'foreignKey' => 'class_teacher_id',
            'joinType' => 'LEFT',
        ]);
        $this->hasMany('Students', [
            'foreignKey' => 'class_arm_id',
        ]);
        $this->hasMany('Results', [
            'foreignKey' => 'class_arm_id',
        ]);
        $this->hasMany('Attendances', [
            'foreignKey' => 'class_arm_id',
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
            ->integer('department_id')
            ->requirePresence('department_id', 'create')
            ->notEmptyString('department_id');

        $validator
            ->scalar('arm_name')
            ->maxLength('arm_name', 10)
            ->requirePresence('arm_name', 'create')
            ->notEmptyString('arm_name');

        $validator
            ->scalar('arm_description')
            ->maxLength('arm_description', 255)
            ->allowEmptyString('arm_description');

        $validator
            ->integer('class_teacher_id')
            ->allowEmptyString('class_teacher_id');

        $validator
            ->scalar('status')
            ->maxLength('status', 20)
            ->notEmptyString('status')
            ->inList('status', ['active', 'inactive', 'archived']);

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
        $rules->add($rules->existsIn(['department_id'], 'Departments'));
        $rules->add($rules->existsIn(['class_teacher_id'], 'Teachers'));

        // Ensure unique arm name per department
        $rules->add($rules->isUnique(['department_id', 'arm_name'], 'This arm name already exists for this department'));

        return $rules;
    }

    /**
     * Get class arms for a specific department
     *
     * @param int $departmentId
     * @return \Cake\ORM\Query
     */
    public function getArmsForDepartment(int $departmentId): Query
    {
        return $this->find()
            ->where(['department_id' => $departmentId, 'status' => 'active'])
            ->contain(['Teachers.Users', 'Students'])
            ->order(['arm_name' => 'ASC']);
    }

    /**
     * Get class arm with student count
     *
     * @param int $armId
     * @return \Cake\ORM\Query
     */
    public function getArmWithStudentCount(int $armId): Query
    {
        return $this->find()
            ->where(['id' => $armId])
            ->contain([
                'Departments',
                'Teachers.Users',
                'Students' => function ($q) {
                    return $q->where(['Students.status' => 'Admitted']);
                },
            ]);
    }
}
