<?php
use Migrations\AbstractMigration;

class AddIsAdminField extends AbstractMigration
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
        $table->addColumn('is_admin', 'boolean', [
            'default' => 0,
            'null' => true,
            'after' => 'name',
            'comment' => 'If the group is tagged as admin the isAdmin function can be used to check if user is admin or not',
        ]);
        $table->update();
    }
}
