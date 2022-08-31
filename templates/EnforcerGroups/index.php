<?php
/**
 * @var \App\View\AppView $this
 * @var \Cake\Datasource\EntityInterface[]|\Cake\Collection\CollectionInterface $enforcerGroups
 */
?>

<div class="container-fluid">
    <h2 class="page-header"><?= __d('Enforcer', 'Groups') ?></h2>
    <hr>

    <div class="col-md-12 row">
        <div class="col-md-4 row">
            <?= $this->Html->link(__d('Enforcer', 'Add Groups'), ['controller' => 'EnforcerGroups', 'action' => 'add', 'plugin' => 'Enforcer'], array('class' => 'btn btn-primary')); ?>
        </div>
    </div>

    <div class="col-md-12 table-responsive layout mt-4"><br>
        <table class="table table-striped">
            <thead>
                <tr>    
                    <th scope="col"><?= '#' ?></th>
                    <th scope="col"><?= __d('Enforcer', 'Name') ?></th>
                    <th scope="col"><?= __d('Enforcer', 'Modified') ?></th>
                    <th scope="col"><?= __d('Enforcer', 'Created') ?></th>
                    <th scope="col" class="actions"><?= __d('Enforcer', 'Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($groups as $group): ?>
                    <?php
                        $deleteDisabled = '';
                        $editDisabled = '';

                        if($group->id == 3) {
                            $deleteDisabled = 'disabled';
                            $editDisabled = 'disabled';
                        } elseif($group->id == 1) {
                            $deleteDisabled = 'disabled';
                        }
                    ?>

                    <tr>
                        <td><?= $this->Number->format($group->id) ?></td>
                        <td><?= h($group->name)?></td>
                        <td><?= $group->created ?></td>
                        <td><?= $group->modified ?></td>
                        <td class="actions">
                            <?= $this->Html->link(__('Edit'), ['action' => 'edit', $group->id], ['class' => 'btn btn-success ' . $editDisabled]); ?>
                            <?= $this->Form->postLink(__d('Enforcer', 'Delete'), ['action' => 'delete', $group->id], ['confirm' => __('Are you sure you want to delete group {0}?', $group->name), 'class' => 'btn btn-danger ' . $deleteDisabled]); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>