<?php
namespace Enforcer;
use Cake\Core\Configure;
use Cake\Core\Exception\Exception;
use Cake\Log\Log;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use Cake\Filesystem\Folder;
use Cake\Routing\Router;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;
use Cake\Core\App;
use Cake\Core\Plugin;
use Cake\Cache\Cache;

class PermissionManager {
    // prefixes
    protected $prefixes = [];

    // routes
    protected $routes = [];

    protected $controllerTree = [];

    // multiple = groupManagement either single or multiple
    public function __construct($multiple = false) {
        $this->multipleGroupManagement = $multiple;

        // this is not the right place to use this
        // TODO: move this later
        if(!Cache::getConfig('Enforcer')) {
            Cache::setConfig('Enforcer', [
                'className' => 'Cake\Cache\Engine\FileEngine',
                'duration' => '+100 week',
                'path' => CACHE . 'enforcer' . DS,
            ]);
        }

        // the names of the funtions that will be skipped
        $this->functionBlackList = [
            'initialize',
            'beforeFilter',
            'beforeRender',
            'isAuthorized',
        ];

        $this->Permissions = TableRegistry::get('Enforcer.EnforcerGroupPermissions');
        $this->Groups = TableRegistry::get('Enforcer.EnforcerGroups');
    }

    public function checkAccess($requestInfo, $groupID) {
        // if the group id is 1 and the action is for this plugin we will allow the user in
        if(!is_array($groupID) && $groupID == 1 && strtolower($requestInfo['plugin']) == 'enforcer') {
            return true;
        } elseif (is_array($groupID) && in_array(1, $groupID) && strtolower($requestInfo['plugin']) == 'enforcer') {
            return true;
        }

        if(!is_array($groupID)) {
            $permissions = $this->getGroupPermissions($groupID);
        } else {
            // multiple groups ($groupID = array of group id's)
            $permissions = [];

            foreach ($groupID as $group) {
                $permissions = array_merge($permissions, $this->getGroupPermissions($group));
            }

            usort($permissions, function ($permission1, $permission2) {
                return $permission2['allowed'] <=> $permission1['allowed'];
            });
        }

        foreach ($permissions as $key => $permission) {
            if(!$permission['allowed']) {
                if ($this->hasAccess($permission, $requestInfo)) return false;
                // Log::info([$permission, $this->hasAccess($permission, $requestInfo)]);
            } elseif($permission['allowed']) {
                if ($this->hasAccess($permission, $requestInfo)) return true;
                // dd($this->hasAccess($permission, $requestInfo));
            }
        }

        return false;
    }

    private function hasAccess($perm, $requestInfo) {
        return (
            ($perm['plugin'] == '*' || $perm['plugin'] == $requestInfo['plugin']) &&
            ($perm['prefix'] == '*' || strtolower($perm['prefix']) == strtolower($requestInfo['prefix'])) &&
            ($perm['controller'] == '*' || strtolower($perm['controller']) == strtolower($requestInfo['controller'])) &&
            ($perm['action'] == '*' || strtolower($perm['action']) == strtolower($requestInfo['action']))
        );
    }

    // public function isGuestAccess() {
        
    // }

    public function setRoutes() {
        $this->routes = Router::routes();
    }

    public function getPermissions($prefix = 'admin') {
        $this->setRoutes();
        $this->buildPrefixes();
        $this->handleApp();
        $plugins = Plugin::loaded();

        if(!empty($plugins)) {
            $this->handlePlugins(Plugin::loaded());
        }

        // add existing permissions to controller tree
        $this->buildPermissions();
        return $this->controllerTree;
    }

    private function buildPrefixes() {
        $routes = $this->routes;

        foreach ($routes as $key => $route) {
            if (isset($route->defaults['prefix'])) {
                $prefixes = explode('/', $route->defaults['prefix']);
                $prefix = implode('/', array_map(
                    'Cake\\Utility\\Inflector::camelize',
                    $prefixes
                ));
                if (!isset($route->defaults['plugin'])) {
                    $this->prefixes['App'][$prefix] = true;
                } else {
                    $this->prefixes[$route->defaults['plugin']][$prefix] = true;
                }
            }
        }
    }

    // add app routings to the controller tree
    private function handleApp() {
        $holder['App'] = $this->getControllers();

        if(!empty($this->prefixes['App'])) {
            foreach ($this->prefixes['App'] as $prefixName => $prefix) {
                $holder[$prefixName] = $this->getControllers(null, $prefixName);
            }
        }

        // add to the controller list
        $this->controllerTree['App'] = $this->getMethods(null, $holder);
    }

    // add plugin controllers to controller tree
    private function handlePlugins($plugins) {
        foreach ($plugins as $key => $plugin) {
            $holder = [];
            $hasPrefix = false;
            $holder['App'] = $this->getControllers($plugin);

            if(!empty($this->prefixes[$plugin])) {
                foreach ($this->prefixes[$plugin] as $prefixName => $prefix) {
                    $holder[$prefixName] = $this->getControllers($plugin, $prefixName);
                }
            }

            // add to the controller list
            $this->controllerTree[$plugin] = $this->getMethods($plugin, $holder);
        }
    }

    // return the path to the controller dir
    private function getPath($plugin, $prefix) {
        if (!$plugin) {
            $path = App::path('Controller' . (empty($prefix) ? '' : DS . Inflector::camelize($prefix)));
        } else {
            $path = App::path('Controller' . (empty($prefix) ? '' : DS . Inflector::camelize($prefix)), $plugin);
        }

        return $path;
    }

    private function getControllers($plugin = null, $prefix = null) {
        // find the controllers
        $path = $this->getPath($plugin, $prefix);
        $dir = new Folder($path[0]);

        if(!empty($prefix) && empty($dir->pwd())) {
            $path[0] = str_replace($prefix, strtolower($prefix), $path[0]);
            $dir = new Folder($path[0]);
        }

        $controllers = $dir->find('.*Controller\.php');
        return $controllers;
    }

    // add methods to the controller tree
    private function getMethods($plugin = null, $controllersTree) {
        $controllersWithMethods = [];

        foreach ($controllersTree as $key => $controllers) {
            $prefix = null;

            if($key !== 'App') {
                $prefix = $key;
            }
            
            foreach ($controllers as $k => $controller) {
                $namespace = null;

                // skip the app controller
                if($controller == 'AppController.php') {
                    continue;
                }

                // build namespace call
                $namespace = !empty($plugin) ? $plugin : 'App' ;
                $namespace .= '\Controller\\';

                if(!empty($prefix)) {
                    if(substr($prefix, 0, 1) === '/') {
                        $prefix = ltrim($prefix, '/');
                    }

                    $namespace .= $prefix . '\\';
                }

                $namespace .= str_replace('.php', '', $controller);
                $namespace = str_replace('/', '\\', $namespace);

                // init class to get methods
                $class = new $namespace();
                $methods = new \ReflectionClass($class);
                $publicMethods = $methods->getMethods(\ReflectionMethod::IS_PUBLIC);

                if(!empty($publicMethods)) {
                    foreach ($publicMethods as $publicMethod) {
                        if($publicMethod->class == $namespace && !in_array($publicMethod->name, $this->functionBlackList)) {
                            // dd($publicMethod);
                            
                            // add methods to the controller list
                            $name = $controllersTree[$key][$k];
                            $controllersWithMethods[$key][$name][] = $publicMethod->name;
                        }
                    }
                }
            }
        }

        return $controllersWithMethods;
    }

    // add existing permissions to controller tree
    private function buildPermissions() {
        $groups = $this->Groups->find('all')->toArray();
        $groups = array_column($groups, 'name', 'id');
        $permissionsList = [];

        foreach ($this->controllerTree as $plugin => $controllers) {
            foreach ($controllers as $prefix => $controller) {
                foreach ($controller as $controllerName => $methods) {
                    $permissionsList[$plugin][$prefix][$controllerName] = [];
                    $controllerName = str_replace('Controller.php', '', $controllerName);
                    
                    if($prefix == 'App') {
                        $prefixName = '';
                    } else {
                        $prefixName = $prefix;
                    }

                    if($plugin == 'App') {
                        $pluginName = '';
                    } else {
                        $pluginName = $plugin;
                    }
 
                    $permissions = $this->Permissions->find('all')->where([
                        'plugin' => $pluginName,
                        'prefix' => $prefixName,
                        'controller' => $controllerName . 'Controller',
                        'action IN' => $methods,
                    ])->toArray();

                    // prefix query
                    // if($prefix !== 'App') {
                    //     $permissions = $this->Permissions->find('all')->where([
                    //         'plugin' => $plugin,
                    //         'prefix' => $prefix,
                    //         'controller' => $controller,
                    //         'action' => $method,
                    //     ])->toArray();
                    // } else {
                    //     $permissions = $this->Permissions->find('all')->where([
                    //         'OR' => [
                    //             ['prefix' => null],
                    //             ['prefix' => 'App'],
                    //             ['prefix' => ''],
                    //         ],
                    //         'OR' => [
                    //             ['plugin' => null],
                    //             ['plugin' => 'App'],
                    //             ['plugin' => ''],
                    //         ],
                    //         'controller' => $controller,
                    //         'action' => $method,
                    //     ])->toArray();
                    // }

                    // build the permissions
                    if(!empty($permissions)) {
                        foreach ($methods as $method) {
                            foreach ($permissions as $permission) {
                                // add found permissions to array
                                if($permission->action == $method) {
                                    // if the group is still found
                                    if(!empty($groups[$permission->group_id])) {
                                        $permissionsList[$plugin][$prefix][$controllerName . 'Controller.php'][$method][$groups[$permission->group_id]] = $permission->allowed;
                                    }
                                }
                            }
                        }
                    }

                    // add missing permission configurations
                    foreach ($groups as $group) {
                        foreach ($methods as $method) {
                            if(!empty($permissionsList[$plugin][$prefix][$controllerName . 'Controller.php'][$method])) {
                                if(!isset($permissionsList[$plugin][$prefix][$controllerName . 'Controller.php'][$method][$group])) {
                                    $permissionsList[$plugin][$prefix][$controllerName . 'Controller.php'][$method][$group] = false;
                                }
                            } else {
                                $permissionsList[$plugin][$prefix][$controllerName . 'Controller.php'][$method][$group] = false;
                            }
                        }
                    }

                }
            }
        }
        
        $this->controllerTree = $permissionsList;
    }

    /**
     * Returns an array of permissions for the $userGroupID, it includes the Guest group if $includeGuestPermission
     *
     * @param $userGroupID
     * @param $includeGuestPermission
     * @return array of permissions.
     */
    public function getGroupPermissions($userGroupID = 3, $includeGuestPermission = false) {

        // using the cake cache to store rules
        $cacheKey = 'permissions_for_group_'.$userGroupID.'_'.$includeGuestPermission;
        // get public controller actions
        $actions = Cache::read($cacheKey, 'Enforcer');

        // get permissions for the group
        if(!$actions) {
            if($includeGuestPermission) {
                $conditions = [
                    'OR' => [
                        ['group_id' => $userGroupID],
                        ['group_id' => '3'],
                    ]
                ];
            } else {
                $conditions = [
                    'group_id' => $userGroupID,
                ];
            }

            // sort to denials first, they are stronger than allows
            $actionsQuery = $this->Permissions->find('all')->where($conditions)->order([
                'allowed' => 'ASC',
                'controller' => 'ASC',
                'action' => 'ASC',
            ])->toArray();

            $actions = [];

            foreach ($actionsQuery as $action) {
                $actions[] = [
                    'group_id' => $action->group_id,
                    'plugin' => $action->plugin,
                    'prefix' => $action->prefix,
                    'controller' => $action->controller,
                    'action' => $action->action,
                    'allowed' => $action->allowed,
                ];
            }
            Cache::write($cacheKey, $actions, 'Enforcer');
        }

        return $actions;
    }

    public function refreshCaches() {
        $groups = $this->Groups->find('all')->toArray();

        foreach ($groups as $key => $group) {
            $includeGuestPermission = false;
            $cacheKey = 'permissions_for_group_'.$group->id.'_'.$includeGuestPermission;
            Cache::delete($cacheKey, 'Enforcer');
            $this->getGroupPermissions($group->id);
        }
    }
}