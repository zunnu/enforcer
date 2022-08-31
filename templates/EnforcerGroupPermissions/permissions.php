<?php
	$collapseCount = 1;
?>

<style type="text/css">
	.panel-heading a:after {
	    font-family:'Glyphicons Halflings';
	    content:"\2212";
	    float: right;
	    color: grey;
	}
	.panel-heading a.collapsed:after {
	    content:"\2b";
	}

	.panel-heading a:after {
	  font-family:'Glyphicons Halflings';
	  content:"\2212";
	  float: right;
	  color: grey;
	}
	.panel-heading a.collapsed:after {
	  content:"\2b";
	}
</style>

<div class="container-fluid" style="text-align: left; max-width: 1400px;">
	<div class="row">
		<div class="col-md-12 mt-4">
			<h2>Permissions</h2>
			<hr>
		</div>

		<div class="col-md-12">
			<div class="panel-group" id="accordion">

			<?= $this->Form->create(null); ?>
			<?php foreach ($plugins as $pluginName => $plugin) : ?>
				<?php
					$collapseCount++;
					$isApp = (strtolower($pluginName) == 'app' ? true : false);
				?>

			    <div class="panel panel-default card" id="panel1">
			        <div class="panel-heading card-header">
			        	<h4 class="panel-title"><a data-toggle="collapse" data-target="#collapse<?= $collapseCount ?>" href="#collapse<?= $collapseCount ?>">
			          		<?= $pluginName ?>
			        	</a></h4>
			        </div>

			        <div id="collapse<?= $collapseCount ?>" class="panel-collapse collapse in <?= $isApp ? 'show' : '' ?>">
			            <div class="panel-body">
			            	<?= $this->element('Permissions', ['plugin' => $plugin, 'pluginName' => $pluginName]) ?>		
			            </div>
			        </div>
			    </div>
			<?php endforeach; ?>
			<?= $this->Form->submit(__('Update'), ['class' => 'btn btn-primary btn-block mt-4 mb-4']); ?>
			<?= $this->Form->end(); ?>
			</div>
		</div>
	</div>
</div>