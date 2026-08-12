<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Examquestions Model
 *
 * @property \App\Model\Table\SubjectsTable&\Cake\ORM\Association\BelongsTo $Subjects
 * @property \App\Model\Table\AdminsTable&\Cake\ORM\Association\BelongsTo $Admins
 * @property \App\Model\Table\ExamsTable&\Cake\ORM\Association\BelongsTo $Exams
 * @property \App\Model\Table\DepartmentsTable&\Cake\ORM\Association\BelongsTo $Departments
 * @property \App\Model\Table\LevelsTable&\Cake\ORM\Association\BelongsTo $Levels
 * @property \App\Model\Table\AttemptedexamquestionsTable&\Cake\ORM\Association\HasMany $Attemptedexamquestions
 *
 * @method \App\Model\Entity\Examquestion newEmptyEntity()
 * @method \App\Model\Entity\Examquestion newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Examquestion[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Examquestion get($primaryKey, $options = [])
 * @method \App\Model\Entity\Examquestion findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Examquestion patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Examquestion[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Examquestion|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Examquestion saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Examquestion[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Examquestion[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Examquestion[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Examquestion[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class ExamquestionsTable extends Table
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

        $this->setTable('examquestions');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Subjects', [
            'foreignKey' => 'subject_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Admins', [
            'foreignKey' => 'admin_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Exams', [
            'foreignKey' => 'exam_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Departments', [
            'foreignKey' => 'department_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Levels', [
            'foreignKey' => 'level_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Faculties', [
            'foreignKey' => 'faculty_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('Attemptedexamquestions', [
            'foreignKey' => 'examquestion_id',
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
            ->maxLength('question', 620)
            ->requirePresence('question', 'create')
            ->notEmptyString('question');

        $validator
            ->scalar('op1')
            ->maxLength('op1', 220)
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
            ->maxLength('op4', 220)
            ->requirePresence('op4', 'create')
            ->notEmptyString('op4');

        $validator
            ->scalar('correctans')
            ->maxLength('correctans', 220)
            ->requirePresence('correctans', 'create')
            ->notEmptyString('correctans');

        $validator
            ->integer('mark')
            ->allowEmptyString('mark');

//        $validator
//            ->dateTime('dateadded')
//            ->notEmptyDateTime('dateadded');

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
        $rules->add($rules->existsIn(['subject_id'], 'Subjects'));
        $rules->add($rules->existsIn(['admin_id'], 'Admins'));
        $rules->add($rules->existsIn(['exam_id'], 'Exams'));
        $rules->add($rules->existsIn(['department_id'], 'Departments'));
        $rules->add($rules->existsIn(['level_id'], 'Levels'));
        $rules->add($rules->existsIn(['faculty_id'], 'Faculties'));

        return $rules;
    }
}
