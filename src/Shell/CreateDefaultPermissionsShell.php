<?php
namespace Enforcer\Shell;

use Cake\Console\Shell;
use Enforcer\Plugin as Enforcer;
use Cake\Log\Log;
use Enforcer\PermissionManager;

/**
 * CreateDefaultPermissions shell command.
 */
class CreateDefaultPermissionsShell extends Shell
{
    public function initialize() {
        $this->Permissions = $this->loadModel('Enforcer.EnforcerGroupPermissions');
        $this->Permissions->addBehavior('Timestamp');
    }

    public function getOptionParser()
    {
        $parser = parent::getOptionParser();

        $parser->addOption('createFile', array(
            'short' => 'c',
            'help' => 'Creates the permissions json',
            'boolean' => true
        ));

        $parser->addOption('importFile', array(
            'short' => 'i',
            'help' => 'Imports the permissions json file',
            'boolean' => true
        ));

        $parser->addOption('path', array(
            'short' => 'p',
            'help' => 'Path where to export the file or path where from import the file',
        ));

        $parser->addOption('addGroups', array(
            'short' => 'g',
            'help' => 'Add the groups to the file and or import the groups too',
            'boolean' => true
        ));


        return $parser;
    }

    /**
     * main() method.
     *
     * @return bool|int|null Success or error code.
     */
    public function main()
    {   
        // default path
        $plugin = new Enforcer();
        $path = $plugin->getClassPath() . 'default_permissions.json';

        if(!empty($this->params['path'])) {
            $path = $this->params['path'];
        }

        if(!empty($this->params['createFile'])) {
            return $this->createPermissionFile($path);
        } elseif(!empty($this->params['importFile'])) {
            $this->importPermissionFile($path);

            Log::info('Refreshing permission cache..');
            $permissionManager = new PermissionManager();
            $permissionManager->refreshCaches();
            Log::info('Permission cache set!');
        } else {
            $this->out($this->OptionParser->help());
        }

        die();
    }

    public function createPermissionFile($path) {
        $permissions = $this->Permissions->find('all')->enableHydration(false)->select([
            "user_id",
            "group_id",
            "prefix",
            "plugin",
            "controller",
            "action",
        ])->toArray();

        $permissionArray = ['permissions' => $permissions];

        if(!empty($this->params['addGroups'])) {
            $baseGroups = $this->Permissions->Groups->find('all')->enableHydration(false)->select([
                'id',
                'name',
                'is_admin',
            ])->toArray();

            $permissionArray['baseGroups'] = $baseGroups;

            $usersGroups = $this->Permissions->Groups->UsersGroups->find('all')->enableHydration(false)->select([
                'id',
                'user_id',
                'group_id'
            ])->toArray();

            $permissionArray['usersGroups'] = $usersGroups;
        }

        // write to file
        $permissions = json_encode($permissionArray);
        $file = fopen($path, 'w');
        fwrite($file, $permissions);
        fclose($file);
    }

    public function importPermissionFile($path) {
        if(file_exists($path)) {
            Log::info('Loading file...');
            $permissions = file_get_contents($path);
            $permissions = json_decode($permissions, true);
            Log::info('File loaded!');
            
            if(!empty($permissions['permissions'])) {
                Log::info('Importing permissions...');

                foreach($permissions['permissions'] as $permission) {
                    unset($permission['id']);
                    unset($permission['modified']);
                    unset($permission['created']);
                    unset($permission['rght']);
                    unset($permission['lft']);

                    $checkPermission = $this->Permissions->find('all')->where([
                        "user_id" => $permission['user_id'],
                        "group_id" => $permission['group_id'],
                        "prefix" => $permission['prefix'],
                        "plugin" => $permission['plugin'],
                        "controller" => $permission['controller'],
                        "action" => $permission['action'],
                    ])->first();

                    if(!$checkPermission) {
                        $checkPermission = $this->Permissions->newEntity();
                        $checkPermission = $this->Permissions->patchEntity($checkPermission, $permission);
                        $this->Permissions->save($checkPermission);
                    }
                }

                Log::info('Permissions imported!');
            } else {
                Log::warning('File did not contain any permissions');
            }

            // import the group data also
            if(!empty($this->params['addGroups'])) {
                Log::info('Importing groups...');

                if(!empty($permissions['baseGroups'])) {
                    foreach ($permissions['baseGroups'] as $group) {
                        $checkGroup = $this->Permissions->Groups->find('all')->where([
                            "name" => $group['name'],
                        ])->first();

                        if(!$checkGroup) {
                            $checkGroup = $this->Permissions->Groups->newEntity();
                        }

                        $checkGroup = $this->Permissions->Groups->patchEntity($checkGroup, $group);
                        $checkGroup->name = $group['name'];
                        $checkGroup->is_admin = $group['is_admin'] ? 1 : 0;
                        $this->Permissions->Groups->save($checkGroup);
                    }
                }

                if(!empty($permissions['usersGroups'])) {
                    foreach ($permissions['usersGroups'] as $usersGroup) {
                        $checkUsersGroup = $this->Permissions->Groups->UsersGroups->find('all')->where([
                            'user_id' => $usersGroup['user_id'],
                            'group_id' => $usersGroup['group_id'],
                        ])->first();

                        if(!$checkUsersGroup) {
                            $checkUsersGroup = $this->Permissions->Groups->UsersGroups->newEntity();
                        }

                        $checkUsersGroup = $this->Permissions->Groups->UsersGroups->patchEntity($checkUsersGroup, $usersGroup);
                        $checkUsersGroup->user_id = $usersGroup['user_id'];
                        $checkUsersGroup->group_id = $usersGroup['group_id'];
                        $this->Permissions->Groups->UsersGroups->save($checkUsersGroup);
                    }
                }

                Log::info('Groups imported!');
            }
        } else {
            Log::error('Import file not found in path: ' . $path);
        }
    }
}
