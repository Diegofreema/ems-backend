<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Settings Model
 *
 * @property \App\Model\Table\SemestersTable&\Cake\ORM\Association\BelongsTo $Semesters
 * @property \App\Model\Table\SessionsTable&\Cake\ORM\Association\BelongsTo $Sessions
 * @method \App\Model\Entity\Setting newEmptyEntity()
 * @method \App\Model\Entity\Setting newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Setting[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Setting get($primaryKey, $options = [])
 * @method \App\Model\Entity\Setting findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Setting patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Setting[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Setting|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Setting saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Setting[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Setting[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Setting[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Setting[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class SettingsTable extends Table
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

        $this->setTable('settings');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->belongsTo('Semesters', [
            'foreignKey' => 'semester_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Sessions', [
            'foreignKey' => 'session_id',
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
            ->scalar('description')
            ->maxLength('description', 250)
            ->requirePresence('description', 'create')
            ->notEmptyString('description');

//        $validator
//            ->integer('regfee')
//            ->requirePresence('regfee', 'create')
//            ->notEmptyString('regfee');

        $validator
            ->scalar('name')
            ->maxLength('name', 256)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->scalar('address')
            ->maxLength('address', 278)
            ->requirePresence('address', 'create')
            ->notEmptyString('address');

        $validator
            ->email('email')
            ->requirePresence('email', 'create')
            ->notEmptyString('email');

        $validator
            ->scalar('phone')
            ->maxLength('phone', 26)
            ->requirePresence('phone', 'create')
            ->notEmptyString('phone');

        $validator
            ->scalar('invoiceprefix')
            ->maxLength('invoiceprefix', 10)
            ->requirePresence('invoiceprefix', 'create')
            ->notEmptyString('invoiceprefix');

        $validator
            ->scalar('adminprefix')
            ->maxLength('adminprefix', 10)
            ->requirePresence('adminprefix', 'create')
            ->notEmptyString('adminprefix');

        $validator
            ->scalar('doa')
            ->maxLength('doa', 256)
            ->requirePresence('doa', 'create')
            ->notEmptyString('doa');

        $validator
            ->scalar('staffprefix')
            ->maxLength('staffprefix', 28)
            ->requirePresence('staffprefix', 'create')
            ->notEmptyString('staffprefix');

        $validator
            ->scalar('regnoformat')
            ->maxLength('regnoformat', 30)
            ->requirePresence('regnoformat', 'create')
            ->notEmptyString('regnoformat');

        $validator
            ->scalar('application_no_prefix')
            ->maxLength('application_no_prefix', 10)
            ->requirePresence('application_no_prefix', 'create')
            ->notEmptyString('application_no_prefix');

        $validator
            ->scalar('rector')
            ->maxLength('rector', 222)
            ->requirePresence('rector', 'create')
            ->notEmptyString('rector');

        $validator
            ->scalar('registrar')
            ->maxLength('registrar', 222)
            ->requirePresence('registrar', 'create')
            ->notEmptyString('registrar');

        $validator
            ->scalar('rectorcerts')
            ->maxLength('rectorcerts', 144)
            ->requirePresence('rectorcerts', 'create')
            ->notEmptyString('rectorcerts');

        $validator
            ->scalar('registrarcerts')
            ->maxLength('registrarcerts', 144)
            ->requirePresence('registrarcerts', 'create')
            ->notEmptyString('registrarcerts');

        $validator
            ->scalar('currenttermends')
            ->maxLength('currenttermends', 20)
            ->allowEmptyString('currenttermends');

        $validator
            ->scalar('nexttermbegins')
            ->maxLength('nexttermbegins', 20)
            ->allowEmptyString('nexttermbegins');

        $validator
            ->scalar('school_stamp')
            ->maxLength('school_stamp', 255)
            ->allowEmptyString('school_stamp');

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
        $rules->add($rules->existsIn(['semester_id'], 'Semesters'));
        $rules->add($rules->existsIn(['session_id'], 'Sessions'));

        return $rules;
    }
}
