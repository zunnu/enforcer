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
        <?= $this->Form->create($group, ['action' => 'add', 'style' => 'padding-bottom: 20px;']) ?>

        <div class="form-row">
            <?= $this->Form->control('name', ['label' => __('Name*'), 'required' => true, 'class' => 'form-control', 'templates' => [
                'inputContainer' => '<div class="form-group col-md-6 offset-md-3">{{content}}</div>'
            ]]); ?>
        </div>

        <?= $this->Form->submit(__('Add'), array('class' => 'btn btn-primary')); ?>
        <?= $this->Form->end(); ?>
    </div>
</div>