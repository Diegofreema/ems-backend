<?php
declare(strict_types=1);

namespace App\Model\Table;

use App\Model\Entity\QuestionOption;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * QuestionOptions Model
 *
 * @property \App\Model\Table\QuestionsTable&\Cake\ORM\Association\BelongsTo $Questions
 * @property \App\Model\Table\StudentAnswersTable&\Cake\ORM\Association\HasMany $StudentAnswers
 * @method \App\Model\Entity\QuestionOption newEmptyEntity()
 * @method \App\Model\Entity\QuestionOption newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\QuestionOption[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\QuestionOption get($primaryKey, $options = [])
 * @method \App\Model\Entity\QuestionOption findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\QuestionOption patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\QuestionOption[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\QuestionOption|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\QuestionOption saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\QuestionOption[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\QuestionOption[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\QuestionOption[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\QuestionOption[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class QuestionOptionsTable extends Table
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

        $this->setTable('question_options');
        $this->setDisplayField('option_text');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Questions', [
            'foreignKey' => 'question_id',
            'joinType' => 'INNER',
        ]);

        $this->hasMany('StudentAnswers', [
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
            ->integer('question_id')
            ->notEmptyString('question_id');

        $validator
            ->scalar('option_text')
            ->maxLength('option_text', 500)
            ->notEmptyString('option_text', 'Option text cannot be empty');

        $validator
            ->boolean('is_correct')
            ->notEmptyString('is_correct');

        $validator
            ->integer('order_number')
            ->notEmptyString('order_number')
            ->greaterThan('order_number', 0, 'Order number must be greater than 0');

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
        $rules->add($rules->existsIn('question_id', 'Questions'), ['errorField' => 'question_id']);

        return $rules;
    }

    /**
     * Find options by question ID
     *
     * @param \Cake\ORM\Query $query The query to modify
     * @param array $options The options containing question_id
     * @return \Cake\ORM\Query
     */
    public function findByQuestionId(Query $query, array $options): Query
    {
        return $query->where(['QuestionOptions.question_id' => $options['question_id']]);
    }

    /**
     * Find correct options
     *
     * @param \Cake\ORM\Query $query The query to modify
     * @param array $options The options containing is_correct
     * @return \Cake\ORM\Query
     */
    public function findByCorrect(Query $query, array $options): Query
    {
        return $query->where(['QuestionOptions.is_correct' => $options['is_correct']]);
    }

    /**
     * Find options by order
     *
     * @param \Cake\ORM\Query $query The query to modify
     * @param array $options The options containing order_number
     * @return \Cake\ORM\Query
     */
    public function findByOrder(Query $query, array $options): Query
    {
        return $query->where(['QuestionOptions.order_number' => $options['order_number']]);
    }

    /**
     * Get correct option for a question
     *
     * @param int $questionId The question ID
     * @return \App\Model\Entity\QuestionOption|null
     */
    public function getCorrectOption(int $questionId): ?QuestionOption
    {
        return $this->find()
            ->where(['question_id' => $questionId, 'is_correct' => true])
            ->first();
    }

    /**
     * Get all options for a question ordered by order_number
     *
     * @param int $questionId The question ID
     * @return \Cake\ORM\Query
     */
    public function getOptionsForQuestion(int $questionId): Query
    {
        return $this->find()
            ->where(['question_id' => $questionId])
            ->order(['order_number' => 'ASC']);
    }
}
