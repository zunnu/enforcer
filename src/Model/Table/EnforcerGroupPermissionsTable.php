<?php
namespace Enforcer\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Log\Log;

/**
 * EnforcerGroupPermissions Model
 *
 * @property \Enforcer\Model\Table\EnforcerGroupPermissionsTable&\Cake\ORM\Association\BelongsTo $ParentEnforcerGroupPermissions
 * @property \Enforcer\Model\Table\GroupsTable&\Cake\ORM\Association\BelongsTo $Groups
 * @property \Enforcer\Model\Table\EnforcerGroupPermissionsTable&\Cake\ORM\Association\HasMany $ChildEnforcerGroupPermissions
 *
 * @method \Enforcer\Model\Entity\EnforcerGroupPermission get($primaryKey, $options = [])
 * @method \Enforcer\Model\Entity\EnforcerGroupPermission newEntity($data = null, array $options = [])
 * @method \Enforcer\Model\Entity\EnforcerGroupPermission[] newEntities(array $data, array $options = [])
 * @method \Enforcer\Model\Entity\EnforcerGroupPermission|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \Enforcer\Model\Entity\EnforcerGroupPermission saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \Enforcer\Model\Entity\EnforcerGroupPermission patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \Enforcer\Model\Entity\EnforcerGroupPermission[] patchEntities($entities, array $data, array $options = [])
 * @method \Enforcer\Model\Entity\EnforcerGroupPermission findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @mixin \Cake\ORM\Behavior\TreeBehavior
 */
class EnforcerGroupPermissionsTable extends Table
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

        $this->setTable('enforcer_group_permissions');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Tree');

        $this->belongsTo('Groups', [
            'foreignKey' => 'group_id',
            'joinType' => 'INNER',
            'className' => 'Enforcer.EnforcerGroups',
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
            ->scalar('plugin')
            ->maxLength('plugin', 255)
            ->allowEmptyString('plugin');

        $validator
            ->scalar('controller')
            ->maxLength('controller', 255)
            ->requirePresence('controller', 'create')
            ->notEmptyString('controller');

        $validator
            ->scalar('action')
            ->maxLength('action', 255)
            ->requirePresence('action', 'create')
            ->notEmptyString('action');

        $validator
            ->boolean('allowed')
            ->allowEmptyString('allowed');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        return $rules;
    }
}
