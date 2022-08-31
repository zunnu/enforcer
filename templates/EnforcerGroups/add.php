<?php
/**
 * @var \App\View\AppView $this
 * @var \Cake\Datasource\EntityInterface $enforcerGroup
 */
?>
<div class="container-fluid">
    <div class="col-md-12 mt-4">
        <h2><?= __('Add group') ?></h2>
        <hr>
    </div>

    <div class="col-md-12 layout">
        <?= $this->Form->create($group, [
            'url' => ['action' => 'add'],
            'style' => 'padding-bottom: 20px;'
        ]) ?>

        <div class="form-row">
            <?= $this->Form->control('name', ['label' => __('Name*'), 'required' => true, 'class' => 'form-control', 'templates' => [
                'inputContainer' => '<div class="form-group col-md-4 offset-md-4">{{content}}</div>'
            ]]); ?>
        </div>

        <div class="form-group col-md-4 offset-md-4">
            <div style="text-align: left; font-weight: bold;">
                <?= __d('Enforcer', 'Is admin?') ?>
            </div>

            <?= $this->Form->control('is_admin', [
                'type'=>'checkbox',
                'label' => false,
                'id' => 'is_admin',
                'templates' => [ 
                    'inputContainer' => '<div class="switchToggle">
                        {{content}}
                        <label for="is_admin" denied-text=' . __d("Enforcer", "No") . ' allowed-text=' . __d("Enforcer", "Yes") . '></label>
                    </div>',
                ],
                'checked' => false,
            ]); ?>
        </div>

        <?= $this->Form->submit(__('Add'), array('class' => 'btn btn-primary')); ?>
        <?= $this->Form->end(); ?>
    </div>
</div>