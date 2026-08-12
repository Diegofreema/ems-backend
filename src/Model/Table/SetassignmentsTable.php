<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Setassignments Model
 *
 * @property \App\Model\Table\SubjectsTable&\Cake\ORM\Association\BelongsTo $Subjects
 * @property \App\Model\Table\TeachersTable&\Cake\ORM\Association\BelongsTo $Teachers
 * @property \App\Model\Table\SemestersTable&\Cake\ORM\Association\BelongsTo $Semesters
 * @property \App\Model\Table\QuestionsTable&\Cake\ORM\Association\HasMany $Questions
 *
 * @method \App\Model\Entity\Setassignment newEmptyEntity()
 * @method \App\Model\Entity\Setassignment newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Setassignment[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Setassignment get($primaryKey, $options = [])
 * @method \App\Model\Entity\Setassignment findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Setassignment patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Setassignment[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Setassignment|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Setassignment saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Setassignment[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Setassignment[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Setassignment[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Setassignment[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class SetassignmentsTable extends Table
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

        $this->setTable('setassignments');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Subjects', [
            'foreignKey' => 'subject_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Teachers', [
            'foreignKey' => 'teacher_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Semesters', [
            'foreignKey' => 'semester_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('Assignments', [
            'foreignKey' => 'setassignment_id',
        ]);
        
        $this->hasMany('Questions', [
            'foreignKey' => 'setassignment_id',
            'dependent' => true,
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
            ->integer('subject_id')
            ->notEmptyString('subject_id');

        $validator
            ->scalar('title')
            ->maxLength('title', 255)
            ->notEmptyString('title', 'Test title is required');

        $validator
            ->scalar('details')
            ->requirePresence('details', 'create')
            ->notEmptyString('details');

        $validator
            ->scalar('test_type')
            ->maxLength('test_type', 20)
            ->inList('test_type', ['assignment', 'cbt_test'])
            ->allowEmptyString('test_type');

        $validator
            ->integer('total_questions')
            ->notEmptyString('total_questions', 'Total questions is required')
            ->greaterThan('total_questions', 0, 'Total questions must be greater than 0')
            ->lessThanOrEqual('total_questions', 100, 'Total questions cannot exceed 100');

        $validator
            ->integer('time_limit')
            ->notEmptyString('time_limit', 'Time limit is required')
            ->greaterThan('time_limit', 0, 'Time limit must be greater than 0')
            ->lessThanOrEqual('time_limit', 300, 'Time limit cannot exceed 300 minutes');

        $validator
            ->integer('passing_score')
            ->notEmptyString('passing_score', 'Passing score is required')
            ->greaterThanOrEqual('passing_score', 0, 'Passing score cannot be negative')
            ->lessThanOrEqual('passing_score', 100, 'Passing score cannot exceed 100');

        $validator
            ->dateTime('opendate')
            ->allowEmptyDateTime('opendate');

        $validator
            ->integer('teacher_id')
            ->notEmptyString('teacher_id');

        $validator
            ->integer('semester_id')
            ->notEmptyString('semester_id');

        $validator
            ->scalar('status')
            ->maxLength('status', 10)
            ->notEmptyString('status');

        $validator
            ->dateTime('closedate')
            ->requirePresence('closedate', 'create')
            ->notEmptyDateTime('closedate');

        $validator
            ->dateTime('datecreated')
            ->notEmptyDateTime('datecreated');

        // Custom validation for CBT tests
        $validator->add('total_questions', 'cbt_required', [
            'rule' => function ($value, $context) {
                if ($context['data']['test_type'] === 'cbt_test') {
                    return !empty($value) && $value > 0;
                }
                return true;
            },
            'message' => 'Total questions is required for CBT tests'
        ]);

        $validator->add('time_limit', 'cbt_required', [
            'rule' => function ($value, $context) {
                if ($context['data']['test_type'] === 'cbt_test') {
                    return !empty($value) && $value > 0;
                }
                return true;
            },
            'message' => 'Time limit is required for CBT tests'
        ]);

        $validator->add('passing_score', 'cbt_required', [
            'rule' => function ($value, $context) {
                if ($context['data']['test_type'] === 'cbt_test') {
                    return !empty($value) && $value >= 0;
                }
                return true;
            },
            'message' => 'Passing score is required for CBT tests'
        ]);

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
        $rules->add($rules->existsIn('subject_id', 'Subjects'), ['errorField' => 'subject_id']);
        $rules->add($rules->existsIn('teacher_id', 'Teachers'), ['errorField' => 'teacher_id']);
        $rules->add($rules->existsIn('semester_id', 'Semesters'), ['errorField' => 'semester_id']);

        return $rules;
    }
}
