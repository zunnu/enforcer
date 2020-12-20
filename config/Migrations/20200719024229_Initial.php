<?php
use Migrations\AbstractMigration;

class Initial extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     * @return void
     */
    public function change()
    {
        $table = $this->table('enforcer_groups');
        $table->addColumn('name', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => false,
        ]);
        $table->addColumn('modified', 'datetime', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('created', 'datetime', [
            'default' => null,
            'null' => false,
        ]);
        $table->create();

        $singleRow = [
            'name'  => 'admin',
            'modified'  => '2020-06-24 08:02:39',
            'created'  => '2020-06-24 08:02:39'
        ];

        $table = $this->table('enforcer_groups');
        $table->insert($singleRow);
        $table->saveData();

        $singleRow = [
            'name'  => 'user',
            'modified'  => '2020-06-24 08:02:39',
            'created'  => '2020-06-24 08:02:39'
        ];

        $table = $this->table('enforcer_groups');
        $table->insert($singleRow);
        $table->saveData();

        $singleRow = [
            'name'  => 'guest',
            'modified'  => '2020-06-24 08:02:39',
            'created'  => '2020-06-24 08:02:39'
        ];

        $table = $this->table('enforcer_groups');
        $table->insert($singleRow);
        $table->saveData();

        $table = $this->table('enforcer_group_permissions');
        $table->addColumn('parent_id', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => true,
        ]);
        $table->addColumn('user_id', 'integer', [
            'default' => 0,
            'limit' => 11,
            'null' => true,
        ]);
        $table->addColumn('group_id', 'integer', [
            'default' => 0,
            'limit' => 11,
            'null' => true,
        ]);
        $table->addColumn('prefix', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => true,
        ]);
        $table->addColumn('plugin', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => true,
        ]);
        $table->addColumn('controller', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => false,
        ]);
        $table->addColumn('action', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => false,
        ]);
        $table->addColumn('allowed', 'boolean', [
            'default' => 1,
            'limit' => 1,
            'null' => true,
        ]);
        $table->addColumn('rght', 'integer', [
            'default' => 0,
            'limit' => 11,
            'null' => true,
        ]);
        $table->addColumn('lft', 'integer', [
            'default' => 0,
            'limit' => 11,
            'null' => true,
        ]);
        $table->addColumn('modified', 'datetime', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('created', 'datetime', [
            'default' => null,
            'null' => false,
        ]);
        $table->create();
    }
}
