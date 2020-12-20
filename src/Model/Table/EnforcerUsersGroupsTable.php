<?php
namespace Enforcer\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * EnforcerUsersGroups Model
 *
 * @property \Enforcer\Model\Table\GroupsTable&\Cake\ORM\Association\BelongsTo $Groups
 * @property \Enforcer\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 *
 * @method \Enforcer\Model\Entity\EnforcerUsersGroup get($primaryKey, $options = [])
 * @method \Enforcer\Model\Entity\EnforcerUsersGroup newEntity($data = null, array $options = [])
 * @method \Enforcer\Model\Entity\EnforcerUsersGroup[] newEntities(array $data, array $options = [])
 * @method \Enforcer\Model\Entity\EnforcerUsersGroup|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \Enforcer\Model\Entity\EnforcerUsersGroup saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \Enforcer\Model\Entity\EnforcerUsersGroup patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \Enforcer\Model\Entity\EnforcerUsersGroup[] patchEntities($entities, array $data, array $options = [])
 * @method \Enforcer\Model\Entity\EnforcerUsersGroup findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class EnforcerUsersGroupsTable extends Table
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

        $this->setTable('enforcer_users_groups');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Groups', [
            'foreignKey' => 'group_id',
            'joinType' => 'INNER',
            'className' => 'Enforcer.EnforcerGroups',
        ]);

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
            'className' => 'Users',
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
        $rules->add($rules->existsIn(['group_id'], 'Groups'));
        $rules->add($rules->existsIn(['user_id'], 'Users'));

        return $rules;
    }
}
