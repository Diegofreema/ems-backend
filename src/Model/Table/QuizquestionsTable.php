<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Quizquestions Model
 *
 * @property \App\Model\Table\QuizzesTable&\Cake\ORM\Association\BelongsTo $Quizzes
 * @property \App\Model\Table\AttemptedquizzesTable&\Cake\ORM\Association\HasMany $Attemptedquizzes
 *
 * @method \App\Model\Entity\Quizquestion newEmptyEntity()
 * @method \App\Model\Entity\Quizquestion newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Quizquestion[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Quizquestion get($primaryKey, $options = [])
 * @method \App\Model\Entity\Quizquestion findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Quizquestion patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Quizquestion[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Quizquestion|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Quizquestion saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Quizquestion[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Quizquestion[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Quizquestion[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Quizquestion[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class QuizquestionsTable extends Table
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

        $this->setTable('quizquestions');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Quizzes', [
            'foreignKey' => 'quiz_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('Attemptedquizzes', [
            'foreignKey' => 'quizquestion_id',
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
            ->scalar('question')
            ->maxLength('question', 600)
            ->requirePresence('question', 'create')
            ->notEmptyString('question');

        $validator
            ->scalar('op1')
            ->maxLength('op1', 222)
            ->requirePresence('op1', 'create')
            ->notEmptyString('op1');

        $validator
            ->scalar('op2')
            ->maxLength('op2', 222)
            ->requirePresence('op2', 'create')
            ->notEmptyString('op2');

        $validator
            ->scalar('op3')
            ->maxLength('op3', 222)
            ->requirePresence('op3', 'create')
            ->notEmptyString('op3');

        $validator
            ->scalar('op4')
            ->maxLength('op4', 222)
            ->requirePresence('op4', 'create')
            ->notEmptyString('op4');

        $validator
            ->scalar('correctans')
            ->maxLength('correctans', 222)
            ->requirePresence('correctans', 'create')
            ->notEmptyString('correctans');

        $validator
            ->integer('mark')
            ->allowEmptyString('mark');

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
        $rules->add($rules->existsIn(['quiz_id'], 'Quizzes'));

        return $rules;
    }
}
