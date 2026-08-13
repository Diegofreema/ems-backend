<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Assignments Model
 *
 * @property \App\Model\Table\SubjectsTable&\Cake\ORM\Association\BelongsTo $Subjects
 * @property \App\Model\Table\StudentsTable&\Cake\ORM\Association\BelongsTo $Students
 * @property \App\Model\Table\SessionsTable&\Cake\ORM\Association\BelongsTo $Sessions
 * @property \App\Model\Table\SetassignmentsTable&\Cake\ORM\Association\BelongsTo $Setassignments
 * @property \App\Model\Table\StudentAnswersTable&\Cake\ORM\Association\HasMany $StudentAnswers
 * @method \App\Model\Entity\Assignment newEmptyEntity()
 * @method \App\Model\Entity\Assignment newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Assignment[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Assignment get($primaryKey, $options = [])
 * @method \App\Model\Entity\Assignment findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Assignment patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Assignment[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Assignment|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Assignment saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Assignment[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Assignment[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Assignment[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Assignment[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class AssignmentsTable extends Table
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

        $this->setTable('assignments');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Subjects', [
            'foreignKey' => 'subject_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Students', [
            'foreignKey' => 'student_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Sessions', [
            'foreignKey' => 'session_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Setassignments', [
            'foreignKey' => 'setassignment_id',
            'joinType' => 'INNER',
        ]);

        $this->hasMany('StudentAnswers', [
            'foreignKey' => 'assignment_id',
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
            ->integer('student_id')
            ->notEmptyString('student_id');

        $validator
            ->scalar('details')
            ->maxLength('details', 4000)
            ->requirePresence('details', 'create')
            ->notEmptyString('details');

        $validator
            ->dateTime('datecreated')
            ->notEmptyDateTime('datecreated');

        $validator
            ->scalar('status')
            ->maxLength('status', 16)
            ->notEmptyString('status');

        $validator
            ->integer('session_id')
            ->notEmptyString('session_id');

        $validator
            ->integer('setassignment_id')
            ->notEmptyString('setassignment_id');

        $validator
            ->dateTime('start_time')
            ->allowEmptyDateTime('start_time');

        $validator
            ->dateTime('end_time')
            ->allowEmptyDateTime('end_time');

        $validator
            ->integer('total_score')
            ->allowEmptyString('total_score')
            ->greaterThanOrEqual('total_score', 0, 'Total score cannot be negative')
            ->lessThanOrEqual('total_score', 1000, 'Total score cannot exceed 1000');

        $validator
            ->scalar('teacher_comments')
            ->maxLength('teacher_comments', 65535)
            ->allowEmptyString('teacher_comments');

        $validator
            ->dateTime('graded_at')
            ->allowEmptyDateTime('graded_at');

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
        $rules->add($rules->existsIn('student_id', 'Students'), ['errorField' => 'student_id']);
        $rules->add($rules->existsIn('session_id', 'Sessions'), ['errorField' => 'session_id']);
        $rules->add($rules->existsIn('setassignment_id', 'Setassignments'), ['errorField' => 'setassignment_id']);

        return $rules;
    }
}
