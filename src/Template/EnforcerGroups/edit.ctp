<?php
/**
 * @var \App\View\AppView $this
 * @var \Cake\Datasource\EntityInterface $enforcerGroup
 */
?>
<div class="container-fluid">
    <div class="col-md-12 mt-4">
        <h2><?= __('Edit group') ?></h2>
        <hr>
    </div>

    <div class="col-md-12 layout">
        <?= $this->Form->create($group, ['action' => 'edit', 'style' => 'padding-bottom: 20px;']) ?>

        <div class="form-row">
            <?= $this->Form->control('name', ['label' => __('Name*'), 'required' => true, 'class' => 'form-control', 'templates' => [
                'inputContainer' => '<div class="form-group col-md-4 offset-md-4">{{content}}</div>'
            ]]); ?>
        </div>

        <div class="form-group col-md-4 offset-md-4">
            <div style="text-align: left; font-weight: bold;">
                <?= __d('Enforcer', 'Is admin?') ?>
            </div>

            <?= $this->Form->input('is_admin', [
                'type'=>'checkbox',
                'label' => false,
                'id' => 'is_admin',
                'templates' => [ 
                    'inputContainer' => '<div class="switchToggle">
                        {{content}}
                        <label for="is_admin" denied-text=' . __d("Enforcer", "No") . ' allowed-text=' . __d("Enforcer", "Yes") . '></label>
                    </div>',
                ],
                'checked' => $group->is_admin,
            ]); ?>
        </div>

        <?= $this->Form->submit(__('Edit'), array('class' => 'btn btn-primary')); ?>
        <?= $this->Form->end(); ?>
    </div>
</div>