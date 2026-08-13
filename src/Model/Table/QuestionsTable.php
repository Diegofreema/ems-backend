<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Questions Model
 *
 * @property \App\Model\Table\SetassignmentsTable&\Cake\ORM\Association\BelongsTo $Setassignments
 * @property \App\Model\Table\QuestionOptionsTable&\Cake\ORM\Association\HasMany $QuestionOptions
 * @property \App\Model\Table\StudentAnswersTable&\Cake\ORM\Association\HasMany $StudentAnswers
 * @method \App\Model\Entity\Question newEmptyEntity()
 * @method \App\Model\Entity\Question newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Question[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Question get($primaryKey, $options = [])
 * @method \App\Model\Entity\Question findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Question patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Question[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Question|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Question saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Question[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Question[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Question[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Question[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class QuestionsTable extends Table
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

        $this->setTable('questions');
        $this->setDisplayField('question_text');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Setassignments', [
            'foreignKey' => 'setassignment_id',
            'joinType' => 'INNER',
        ]);

        $this->hasMany('QuestionOptions', [
            'foreignKey' => 'question_id',
            'dependent' => true,
        ]);

        $this->hasMany('StudentAnswers', [
            'foreignKey' => 'question_id',
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
            ->integer('setassignment_id')
            ->notEmptyString('setassignment_id');

        $validator
            ->scalar('question_text')
            ->maxLength('question_text', 65535)
            ->notEmptyString('question_text');

        $validator
            ->scalar('question_type')
            ->maxLength('question_type', 20)
            ->notEmptyString('question_type')
            ->inList('question_type', ['multiple_choice', 'theory']);

        $validator
            ->integer('points')
            ->notEmptyString('points')
            ->greaterThan('points', 0, 'Points must be greater than 0')
            ->lessThanOrEqual('points', 100, 'Points cannot exceed 100');

        $validator
            ->integer('order_number')
            ->notEmptyString('order_number')
            ->greaterThan('order_number', 0, 'Order number must be greater than 0');

        $validator
            ->scalar('difficulty_level')
            ->maxLength('difficulty_level', 20)
            ->inList('difficulty_level', ['easy', 'medium', 'hard']);

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
        $rules->add($rules->existsIn('setassignment_id', 'Setassignments'), ['errorField' => 'setassignment_id']);

        return $rules;
    }

    /**
     * Find questions by setassignment ID
     *
     * @param \Cake\ORM\Query $query The query to modify
     * @param array $options The options containing setassignment_id
     * @return \Cake\ORM\Query
     */
    public function findBySetassignmentId(Query $query, array $options): Query
    {
        return $query->where(['Questions.setassignment_id' => $options['setassignment_id']]);
    }

    /**
     * Find questions by type
     *
     * @param \Cake\ORM\Query $query The query to modify
     * @param array $options The options containing question_type
     * @return \Cake\ORM\Query
     */
    public function findByQuestionType(Query $query, array $options): Query
    {
        return $query->where(['Questions.question_type' => $options['question_type']]);
    }

    /**
     * Find questions by difficulty level
     *
     * @param \Cake\ORM\Query $query The query to modify
     * @param array $options The options containing difficulty_level
     * @return \Cake\ORM\Query
     */
    public function findByDifficultyLevel(Query $query, array $options): Query
    {
        return $query->where(['Questions.difficulty_level' => $options['difficulty_level']]);
    }
}
