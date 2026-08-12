<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Approvedresults Model
 *
 * @property \App\Model\Table\SessionsTable&\Cake\ORM\Association\BelongsTo $Sessions
 * @property \App\Model\Table\SemestersTable&\Cake\ORM\Association\BelongsTo $Semesters
 * @property \App\Model\Table\AdminsTable&\Cake\ORM\Association\BelongsTo $Admins
 *
 * @method \App\Model\Entity\Approvedresult newEmptyEntity()
 * @method \App\Model\Entity\Approvedresult newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Approvedresult[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Approvedresult get($primaryKey, $options = [])
 * @method \App\Model\Entity\Approvedresult findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Approvedresult patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Approvedresult[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Approvedresult|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Approvedresult saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Approvedresult[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Approvedresult[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Approvedresult[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Approvedresult[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class ApprovedresultsTable extends Table
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

        $this->setTable('approvedresults');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Sessions', [
            'foreignKey' => 'session_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Semesters', [
            'foreignKey' => 'semester_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Admins', [
            'foreignKey' => 'admin_id',
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
            ->integer('session_id')
            ->notEmptyString('session_id');

        $validator
            ->integer('semester_id')
            ->notEmptyString('semester_id');

        $validator
            ->scalar('status')
            ->maxLength('status', 44)
            ->notEmptyString('status');

        $validator
            ->integer('admin_id')
            ->notEmptyString('admin_id');

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
        $rules->add($rules->existsIn('session_id', 'Sessions'), ['errorField' => 'session_id']);
        $rules->add($rules->existsIn('semester_id', 'Semesters'), ['errorField' => 'semester_id']);
        $rules->add($rules->existsIn('admin_id', 'Admins'), ['errorField' => 'admin_id']);

        return $rules;
    }
}
