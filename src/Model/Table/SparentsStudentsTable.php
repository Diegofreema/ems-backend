<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * SparentsStudents Model
 *
 * @property \App\Model\Table\StudentsTable&\Cake\ORM\Association\BelongsTo $Students
 * @property \App\Model\Table\SparentsStudentsTable&\Cake\ORM\Association\BelongsTo $ParentSparentsStudents
 * @property \App\Model\Table\SparentsStudentsTable&\Cake\ORM\Association\HasMany $ChildSparentsStudents
 * @method \App\Model\Entity\SparentsStudent newEmptyEntity()
 * @method \App\Model\Entity\SparentsStudent newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\SparentsStudent[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\SparentsStudent get($primaryKey, $options = [])
 * @method \App\Model\Entity\SparentsStudent findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\SparentsStudent patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\SparentsStudent[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\SparentsStudent|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\SparentsStudent saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\SparentsStudent[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\SparentsStudent[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\SparentsStudent[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\SparentsStudent[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class SparentsStudentsTable extends Table
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

        $this->setTable('sparents_students');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Students', [
            'foreignKey' => 'student_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('ParentSparentsStudents', [
            'className' => 'SparentsStudents',
            'foreignKey' => 'parent_id',
        ]);
        $this->hasMany('ChildSparentsStudents', [
            'className' => 'SparentsStudents',
            'foreignKey' => 'parent_id',
        ]);
    }

    // public function initialize(array $config): void
    // {
    //     parent::initialize($config);
    //     $this->setTable('sparents_students');
    //     $this->setPrimaryKey('id');

    //     $this->belongsTo('Sparents', ['foreignKey' => 'parent_id', 'joinType' => 'INNER']);
    //     $this->belongsTo('Students', ['foreignKey' => 'student_id', 'joinType' => 'INNER']);
    // }

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
        $rules->add($rules->existsIn(['student_id'], 'Students'));
        $rules->add($rules->existsIn(['parent_id'], 'ParentSparentsStudents'));

        return $rules;
    }
}
