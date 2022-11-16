<?php
use Migrations\AbstractMigration;

class AddModules extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * @return void
     */
    public function change()
    {
        $table = $this->table('enforcer_modules');
        $table->addColumn(
            'name', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => false,
            ]
        );
        $table->addColumn(
            'group_id', 'integer', [
            'default' => 0,
            'limit' => 11,
            'null' => false,
            ]
        );
        $table->addColumn(
            'modified', 'datetime', [
            'default' => null,
            'null' => false,
            ]
        );
        $table->addColumn(
            'created', 'datetime', [
            'default' => null,
            'null' => false,
            ]
        );
        $table->addIndex(['group_id']);
        $table->create();


        $table = $this->table('enforcer_module_permissions');
        $table->addColumn(
            'group_id', 'integer', [
            'default' => 0,
            'limit' => 11,
            'null' => false,
            ]
        );
        $table->addColumn(
            'module_id', 'integer', [
            'default' => 0,
            'limit' => 11,
            'null' => false,
            ]
        );
        $table->addColumn(
            'permission_id', 'integer', [
            'default' => 0,
            'limit' => 11,
            'null' => false,
            ]
        );
        $table->addColumn(
            'allowed', 'boolean', [
            'default' => 0,
            'null' => false,
            ]
        );
        $table->addColumn(
            'rght', 'integer', [
            'default' => 0,
            'limit' => 11,
            'null' => false,
            ]
        );
        $table->addColumn(
            'lft', 'integer', [
            'default' => 0,
            'limit' => 11,
            'null' => false,
            ]
        );
        $table->addColumn(
            'modified', 'datetime', [
            'default' => null,
            'null' => false,
            ]
        );
        $table->addColumn(
            'created', 'datetime', [
            'default' => null,
            'null' => false,
            ]
        );
        $table->addIndex(['module_id']);
        $table->addIndex(['group_id']);
        $table->addIndex(['allowed']);
        $table->addIndex(['permission_id']);
        $table->create();


        $table = $this->table('enforcer_users_groups');
        $table->addColumn(
            'group_id', 'integer', [
            'default' => 0,
            'limit' => 11,
            'null' => false,
            ]
        );
        $table->addColumn(
            'user_id', 'integer', [
            'default' => 0,
            'limit' => 11,
            'null' => false,
            ]
        );
        $table->addColumn(
            'modified', 'datetime', [
            'default' => null,
            'null' => false,
            ]
        );
        $table->addColumn(
            'created', 'datetime', [
            'default' => null,
            'null' => false,
            ]
        );
        $table->addIndex(['group_id']);
        $table->addIndex(['user_id']);
        $table->create();
    }
}
