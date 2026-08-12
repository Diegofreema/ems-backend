<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Attemptedexamquestions Model
 *
 * @property \App\Model\Table\ExamquestionsTable&\Cake\ORM\Association\BelongsTo $Examquestions
 * @property \App\Model\Table\StudentsTable&\Cake\ORM\Association\BelongsTo $Students
 *
 * @method \App\Model\Entity\Attemptedexamquestion newEmptyEntity()
 * @method \App\Model\Entity\Attemptedexamquestion newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Attemptedexamquestion[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Attemptedexamquestion get($primaryKey, $options = [])
 * @method \App\Model\Entity\Attemptedexamquestion findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Attemptedexamquestion patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Attemptedexamquestion[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Attemptedexamquestion|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Attemptedexamquestion saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Attemptedexamquestion[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Attemptedexamquestion[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Attemptedexamquestion[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Attemptedexamquestion[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class AttemptedexamquestionsTable extends Table
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

        $this->setTable('attemptedexamquestions');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Examquestions', [
            'foreignKey' => 'examquestion_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Students', [
            'foreignKey' => 'student_id',
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
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('sanswer')
            ->maxLength('sanswer', 222)
            ->requirePresence('sanswer', 'create')
            ->notEmptyString('sanswer');

        $validator
            ->scalar('correctans')
            ->maxLength('correctans', 222)
            ->requirePresence('correctans', 'create')
            ->notEmptyString('correctans');

        $validator
            ->dateTime('examdate')
            ->notEmptyDateTime('examdate');

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
        $rules->add($rules->existsIn(['examquestion_id'], 'Examquestions'));
        $rules->add($rules->existsIn(['student_id'], 'Students'));

        return $rules;
    }
}
