<?php
namespace Enforcer\Model\Entity;

use Cake\ORM\Entity;

/**
 * EnforcerGroupPermission Entity
 *
 * @property int $id
 * @property int|null $parent_id
 * @property int $group_id
 * @property string|null $plugin
 * @property string $controller
 * @property string $action
 * @property bool|null $allowed
 * @property int|null $rght
 * @property int|null $lft
 * @property \Cake\I18n\FrozenTime $modified
 * @property \Cake\I18n\FrozenTime $created
 *
 * @property \Enforcer\Model\Entity\ParentEnforcerGroupPermission $parent_enforcer_group_permission
 * @property \Enforcer\Model\Entity\Group $group
 * @property \Enforcer\Model\Entity\ChildEnforcerGroupPermission[] $child_enforcer_group_permissions
 */
class EnforcerGroupPermission extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        'parent_id' => true,
        'group_id' => true,
        'plugin' => true,
        'controller' => true,
        'action' => true,
        'allowed' => true,
        'user_id' => true,
        'prefix' => true,
        'rght' => true,
        'lft' => true,
        'modified' => true,
        'created' => true,
        'parent_enforcer_group_permission' => true,
        'group' => true,
        'child_enforcer_group_permissions' => true,
    ];
}
