<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * StudentAnswers Model
 *
 * @property \App\Model\Table\AssignmentsTable&\Cake\ORM\Association\BelongsTo $Assignments
 * @property \App\Model\Table\QuestionsTable&\Cake\ORM\Association\BelongsTo $Questions
 * @property \App\Model\Table\QuestionOptionsTable&\Cake\ORM\Association\BelongsTo $QuestionOptions
 *
 * @method \App\Model\Entity\StudentAnswer newEmptyEntity()
 * @method \App\Model\Entity\StudentAnswer newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\StudentAnswer[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\StudentAnswer get($primaryKey, $options = [])
 * @method \App\Model\Entity\StudentAnswer findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\StudentAnswer patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\StudentAnswer[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\StudentAnswer|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\StudentAnswer saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\StudentAnswer[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\StudentAnswer[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\StudentAnswer[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\StudentAnswer[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class StudentAnswersTable extends Table
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

        $this->setTable('student_answers');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Assignments', [
            'foreignKey' => 'assignment_id',
            'joinType' => 'INNER',
        ]);

        $this->belongsTo('Questions', [
            'foreignKey' => 'question_id',
            'joinType' => 'INNER',
        ]);

        $this->belongsTo('QuestionOptions', [
            'foreignKey' => 'selected_option_id',
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
            ->integer('assignment_id')
            ->notEmptyString('assignment_id');

        $validator
            ->integer('question_id')
            ->notEmptyString('question_id');

        $validator
            ->scalar('theory_answer')
            ->maxLength('theory_answer', 65535)
            ->allowEmptyString('theory_answer');

        $validator
            ->integer('theory_score')
            ->allowEmptyString('theory_score')
            ->greaterThanOrEqual('theory_score', 0, 'Theory score cannot be negative')
            ->lessThanOrEqual('theory_score', 100, 'Theory score cannot exceed 100');

        $validator
            ->dateTime('answered_at')
            ->allowEmptyDateTime('answered_at');

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
        $rules->add($rules->existsIn('assignment_id', 'Assignments'), ['errorField' => 'assignment_id']);
        $rules->add($rules->existsIn('question_id', 'Questions'), ['errorField' => 'question_id']);

        return $rules;
    }

    /**
     * Find answers by assignment ID
     *
     * @param \Cake\ORM\Query $query The query to modify
     * @param array $options The options containing assignment_id
     * @return \Cake\ORM\Query
     */
    public function findByAssignmentId(Query $query, array $options)
    {
        return $query->where(['StudentAnswers.assignment_id' => $options['assignment_id']]);
    }

    /**
     * Find answers by question ID
     *
     * @param \Cake\ORM\Query $query The query to modify
     * @param array $options The options containing question_id
     * @return \Cake\ORM\Query
     */
    public function findByQuestionId(Query $query, array $options)
    {
        return $query->where(['StudentAnswers.question_id' => $options['question_id']]);
    }

    /**
     * Find answers by selected option
     *
     * @param \Cake\ORM\Query $query The query to modify
     * @param array $options The options containing selected_option_id
     * @return \Cake\ORM\Query
     */
    public function findBySelectedOption(Query $query, array $options)
    {
        return $query->where(['StudentAnswers.selected_option_id' => $options['selected_option_id']]);
    }

    /**
     * Get all answers for an assignment
     *
     * @param int $assignmentId The assignment ID
     * @return \Cake\ORM\Query
     */
    public function getAnswersForAssignment($assignmentId)
    {
        return $this->find()
            ->where(['assignment_id' => $assignmentId])
            ->contain(['Questions', 'QuestionOptions'])
            ->order(['Questions.order_number' => 'ASC']);
    }

    /**
     * Get answer for a specific question in an assignment
     *
     * @param int $assignmentId The assignment ID
     * @param int $questionId The question ID
     * @return \App\Model\Entity\StudentAnswer|null
     */
    public function getAnswerForQuestion($assignmentId, $questionId)
    {
        return $this->find()
            ->where(['assignment_id' => $assignmentId, 'question_id' => $questionId])
            ->contain(['Questions', 'QuestionOptions'])
            ->first();
    }

    /**
     * Check if student has answered a question
     *
     * @param int $assignmentId The assignment ID
     * @param int $questionId The question ID
     * @return bool
     */
    public function hasAnswered($assignmentId, $questionId)
    {
        return $this->exists(['assignment_id' => $assignmentId, 'question_id' => $questionId]);
    }
}
