<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Candidates Model
 *
 * @property \App\Model\Table\StudentsTable&\Cake\ORM\Association\BelongsTo $Students
 * @property \App\Model\Table\PositionsTable&\Cake\ORM\Association\BelongsTo $Positions
 * @property \App\Model\Table\SessionsTable&\Cake\ORM\Association\BelongsTo $Sessions
 * @property \App\Model\Table\VotesTable&\Cake\ORM\Association\HasMany $Votes
 * @method \App\Model\Entity\Candidate newEmptyEntity()
 * @method \App\Model\Entity\Candidate newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Candidate[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Candidate get($primaryKey, $options = [])
 * @method \App\Model\Entity\Candidate findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Candidate patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Candidate[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Candidate|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Candidate saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Candidate[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Candidate[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Candidate[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Candidate[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class CandidatesTable extends Table
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

        $this->setTable('candidates');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Students', [
            'foreignKey' => 'student_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Positions', [
            'foreignKey' => 'position_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Sessions', [
            'foreignKey' => 'session_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('Votes', [
            'foreignKey' => 'candidate_id',
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
            ->integer('student_id')
            ->notEmptyString('student_id');

        $validator
            ->integer('position_id')
            ->notEmptyString('position_id');

        $validator
            ->integer('session_id')
            ->notEmptyString('session_id');

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
        $rules->add($rules->existsIn('student_id', 'Students'), ['errorField' => 'student_id']);
        $rules->add($rules->existsIn('position_id', 'Positions'), ['errorField' => 'position_id']);
        $rules->add($rules->existsIn('session_id', 'Sessions'), ['errorField' => 'session_id']);

        return $rules;
    }
}
