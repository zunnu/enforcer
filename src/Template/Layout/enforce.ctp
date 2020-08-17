<?php
use Cake\View\Helper\UrlHelper;

$session = $this->request->getSession()->read('Auth.User');

?>

<!DOCTYPE html>
<html>
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= 'Enforcer' ?>
    </title>
    <?= $this->Html->meta('icon') ?>

    <?= $this->Html->css('Enforcer.bootstrap.min.css') ?>
    <?= $this->Html->css('Enforcer.style.css') ?>
    <?= $this->Html->css('Enforcer.toastr.min.css') ?>

    <?= $this->Html->script('Enforcer.jquery.min.js'); ?>
    <?= $this->Html->script('Enforcer.popper.min.js'); ?>
    <?= $this->Html->script('Enforcer.bootstrap.min.js'); ?>
    <?= $this->Html->script('Enforcer.toastr.min.js'); ?>
    <?= $this->Html->script('Enforcer.toastr_settings.js'); ?>

    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>
</head>

<body class="text-center">
  <div class="cover-container d-flex h-100 p-3 mx-auto flex-column">
    <header class="masthead mb-auto">
      <div class="inner">
        <h3 class="masthead-brand">Enforcer</h3>
        <nav class="nav nav-masthead justify-content-center">
          <?= $this->Html->link(__d('Enforcer', 'Permissions'), ['controller' => 'EnforcerGroupPermissions', 'action' => 'permissions', 'plugin' => 'Enforcer'], ['class' => 'nav-link', 'escape' => false]) ?>

          <?= $this->Html->link(__d('Enforcer', 'Groups'), ['controller' => 'EnforcerGroups', 'action' => 'index', 'plugin' => 'Enforcer'], ['class' => 'nav-link', 'escape' => false]) ?>
        </nav>
      </div>
    </header>

    <main class="page-content">
      <!-- container here if needed -->
      <?= $this->Flash->render() ?>
        <div>
            <?= $this->fetch('content') ?>
        </div><!-- /.row -->

      <footer></footer>
    </main>
  </div>
</body>
</html>