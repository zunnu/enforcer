<?php
namespace Enforcer\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * EnforcerGroups Model
 *
 * @method \Enforcer\Model\Entity\EnforcerGroup get($primaryKey, $options = [])
 * @method \Enforcer\Model\Entity\EnforcerGroup newEntity($data = null, array $options = [])
 * @method \Enforcer\Model\Entity\EnforcerGroup[] newEntities(array $data, array $options = [])
 * @method \Enforcer\Model\Entity\EnforcerGroup|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \Enforcer\Model\Entity\EnforcerGroup saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \Enforcer\Model\Entity\EnforcerGroup patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \Enforcer\Model\Entity\EnforcerGroup[] patchEntities($entities, array $data, array $options = [])
 * @method \Enforcer\Model\Entity\EnforcerGroup findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class EnforcerGroupsTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('enforcer_groups');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('Permissions', [
            'foreignKey' => 'group_id',
            'className' => 'Enforcer.EnforcerGroupPermissions',
            'propertyName' => 'Permissions'
        ]);

        $this->hasMany('UsersGroups', [
            'foreignKey' => 'group_id',
            'className' => 'Enforcer.EnforcerUsersGroups',
        ]);

        $this->hasMany('Users', [
            'foreignKey' => 'group_id'
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('name')
            ->maxLength('name', 255)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        return $validator;
    }
}
