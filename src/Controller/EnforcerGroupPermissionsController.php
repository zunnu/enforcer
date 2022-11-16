<?php
namespace Enforcer\Controller;

use Enforcer\Controller\AppController;
use Cake\Log\Log;
use Enforcer\PermissionManager;

/**
 * EnforcerGroupPermissions Controller
 *
 * @property \Enforcer\Model\Table\EnforcerGroupPermissionsTable $EnforcerGroupPermissions
 *
 * @method \Enforcer\Model\Entity\EnforcerGroupPermission[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class EnforcerGroupPermissionsController extends AppController
{
    public function permissions()
    {
        $permissionManager = new PermissionManager();
        $groupsList = $this->EnforcerGroupPermissions->Groups->find('all')->toArray();

        if ($this->request->is('post')) {
            $permissions = $this->request->getData();

            if(!empty($permissions)) {
                foreach ($permissions as $pluginName => $plugin) {
                    foreach ($plugin as $prefix => $controllers) {
                        foreach ($controllers as $controllerName => $groups) {
                            foreach ($groups as $groupName => $actions) {
                                // if the entireController is true set this to true to set the same permission to all the actions
                                $setAll = false;
                                foreach ($actions as $actionName => $value) {
                                    $group = $this->EnforcerGroupPermissions->Groups->find('all')->where(
                                        [
                                        'name' => $groupName
                                        ]
                                    )->first();

                                    if($group) {
                                        // if the Controller word is not present
                                        if(strpos($controllerName, 'Controller') == false) {
                                            $controllerName = $controllerName . 'Controller';
                                        }

                                        if($actionName == 'entireController' && $value !== '0') {
                                            $setAll = true;
                                        }

                                        if($setAll) {
                                            // if the value is other than 0 it will be read as allowed
                                            $value = 'all';
                                        }

                                        $permission = $this->EnforcerGroupPermissions->find('all')->where(
                                            [
                                            'user_id' => 0,
                                            'group_id' => $group->id,
                                            'plugin' => $pluginName == 'App' ? '' : $pluginName,  
                                            'prefix' => $prefix == 'App' ? '' : $prefix,
                                            'controller' => $controllerName,
                                            'action' => $actionName,
                                            ]
                                        )->first();

                                        if(!$permission) {
                                            $permission = $this->EnforcerGroupPermissions->newEntity();
                                            $permission->user_id = 0;
                                            $permission->group_id = $group->id;
                                            $permission->plugin = $pluginName == 'App' ? '' : $pluginName;
                                            $permission->prefix = $prefix == 'App' ? '' : $prefix;
                                            $permission->controller = $controllerName;
                                            $permission->action = $actionName;
                                        }

                                        $permission->allowed = ($value == '0' ? 0 : 1);

                                        if(!$this->EnforcerGroupPermissions->save($permission)) {
                                            Log::error('Failed to save permission!');
                                            Log::error(print_r($permission, true));
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            // refresh cache
            $permissionManager->refreshCaches();
            return $this->redirect(['action' => 'permissions']);
        }

        $groupsList = array_column($groupsList, 'name', 'id');
        $plugins = $permissionManager->getPermissions();
        $this->set(compact('groupsList', 'plugins'));
    }
}
