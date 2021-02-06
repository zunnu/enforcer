<?php
namespace Enforcer\Model\Entity;

use Cake\ORM\Entity;

/**
 * EnforcerUsersGroup Entity
 *
 * @property int $id
 * @property int $group_id
 * @property int $user_id
 * @property \Cake\I18n\FrozenTime $modified
 * @property \Cake\I18n\FrozenTime $created
 *
 * @property \Enforcer\Model\Entity\Group $group
 * @property \Enforcer\Model\Entity\User $user
 */
class EnforcerUsersGroup extends Entity
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
        'group_id' => true,
        'user_id' => true,
        'modified' => true,
        'created' => true,
        'group' => true,
        'user' => true,
    ];
}
