<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ApiKeys Model
 *
 * Stores credentials for trusted server-to-server API consumers.
 *
 * @method \App\Model\Entity\ApiKey newEmptyEntity()
 * @method \App\Model\Entity\ApiKey newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\ApiKey get($primaryKey, $options = [])
 * @method \App\Model\Entity\ApiKey patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\ApiKey|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 */
class ApiKeysTable extends Table
{
    /**
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('api_keys');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
    }

    /**
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->scalar('name')
            ->maxLength('name', 191)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->scalar('api_key')
            ->maxLength('api_key', 64)
            ->requirePresence('api_key', 'create')
            ->notEmptyString('api_key');

        $validator
            ->scalar('api_secret_hash')
            ->requirePresence('api_secret_hash', 'create')
            ->notEmptyString('api_secret_hash');

        $validator
            ->boolean('active')
            ->notEmptyString('active');

        return $validator;
    }

    /**
     * Find an active key entity by its public api_key value.
     *
     * @param string $apiKey Public key.
     * @return \App\Model\Entity\ApiKey|null
     */
    public function findActiveByKey(string $apiKey): ?\App\Model\Entity\ApiKey
    {
        /** @var \App\Model\Entity\ApiKey|null $entity */
        $entity = $this->find()
            ->where(['api_key' => $apiKey, 'active' => true])
            ->first();

        return $entity;
    }
}
