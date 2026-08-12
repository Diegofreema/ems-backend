<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Sponsorships Model
 *
 * @property \App\Model\Table\SponsorsTable&\Cake\ORM\Association\BelongsTo $Sponsors
 * @property \App\Model\Table\SessionsTable&\Cake\ORM\Association\BelongsTo $Sessions
 * @property \App\Model\Table\StudentsTable&\Cake\ORM\Association\BelongsTo $Students
 * @property \App\Model\Table\AdminsTable&\Cake\ORM\Association\BelongsTo $Admins
 * @property \App\Model\Table\SponsorshippaymentsTable&\Cake\ORM\Association\HasMany $Sponsorshippayments
 * @property \App\Model\Table\FeesTable&\Cake\ORM\Association\BelongsToMany $Fees
 * @property \App\Model\Table\StudentsTable&\Cake\ORM\Association\BelongsToMany $Students
 *
 * @method \App\Model\Entity\Sponsorship newEmptyEntity()
 * @method \App\Model\Entity\Sponsorship newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Sponsorship[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Sponsorship get($primaryKey, $options = [])
 * @method \App\Model\Entity\Sponsorship findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Sponsorship patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Sponsorship[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Sponsorship|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Sponsorship saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Sponsorship[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Sponsorship[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Sponsorship[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Sponsorship[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class SponsorshipsTable extends Table
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

        $this->setTable('sponsorships');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Sponsors', [
            'foreignKey' => 'sponsor_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Sessions', [
            'foreignKey' => 'session_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Students', [
            'foreignKey' => 'student_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Admins', [
            'foreignKey' => 'admin_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('Sponsorshippayments', [
            'foreignKey' => 'sponsorship_id',
        ]);
        $this->belongsToMany('Fees', [
            'foreignKey' => 'sponsorship_id',
            'targetForeignKey' => 'fee_id',
            'joinTable' => 'fees_sponsorships',
        ]);
//        $this->belongsToMany('Students', [
//            'foreignKey' => 'sponsorship_id',
//            'targetForeignKey' => 'student_id',
//            'joinTable' => 'sponsorships_students',
//        ]);
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
            ->integer('sponsor_id')
            ->notEmptyString('sponsor_id');

        $validator
            ->integer('session_id')
            ->notEmptyString('session_id');

        $validator
            ->integer('student_id')
            ->notEmptyString('student_id');

        $validator
            ->integer('admin_id')
            ->notEmptyString('admin_id');

        $validator
            ->dateTime('datecreated')
            ->allowEmptyDateTime('datecreated');

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
        $rules->add($rules->existsIn('sponsor_id', 'Sponsors'), ['errorField' => 'sponsor_id']);
        $rules->add($rules->existsIn('session_id', 'Sessions'), ['errorField' => 'session_id']);
        $rules->add($rules->existsIn('student_id', 'Students'), ['errorField' => 'student_id']);
        $rules->add($rules->existsIn('admin_id', 'Admins'), ['errorField' => 'admin_id']);

        return $rules;
    }
}
