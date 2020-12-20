<?php
namespace Enforcer\Shell;

use Cake\Console\Shell;

/**
 * CreateDefaultPermissions shell command.
 */
class CreateDefaultPermissionsShell extends Shell
{
    /**
     * Manage the available sub-commands along with their arguments and help
     *
     * @see http://book.cakephp.org/3.0/en/console-and-shells.html#configuring-options-and-generating-help
     *
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();

        $parser->addOption('createFile', array(
            'short' => 'f',
            'help' => 'Creates the permissions json',
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
        $this->Permissions = $this->loadModel('Enforcer.EnforcerGroupPermissions');

        // do not create permissions json
        if(empty($this->params['createFile'])) {
            $path = APP . '../plugins/Enforcer/src/default_permissions.json';

            if(file_exists($path)) {
                $permissions = file_get_contents($path);
                $permissions = json_decode($permissions, true);
                
                if(!empty($permissions)) {
                    foreach($permissions as $permission) {
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
                }
            }

        } else {
            $permissions = $this->Permissions->find('all')->toArray();

            foreach ($permissions as $key => $permission) {
                $permissions[$key] = $permission->toArray();
            }

            $permissions = json_encode($permissions);
            $path = APP . '../plugins/Enforcer/src/default_permissions.json';

            $file = fopen($path, 'w');
            fwrite($file, $permissions);
            fclose($file);
        }

    }
}
