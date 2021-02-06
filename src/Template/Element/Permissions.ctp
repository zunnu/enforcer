<?php $switchCount = 0; ?>

<div class="col-md-12 row">
	<?php foreach ($plugin as $prefix => $controllers) : ?>
		<div class="col-md-12 mb-4">
			<h4 class="mb-4 mt-4" style="text-align: center;"><?= $prefix ?></h4>
			<hr>

			<table class="table table-striped">
			  <thead>
			    <tr>
			      <th scope="col"></th>
			      <?php foreach ($groupsList as $groupKey => $group) : ?>
			      	<th scope='col'><?= $group ?></th>
			      <?php endforeach ?>
			    </tr>
			  </thead>

				<?php foreach ($controllers as $controllerName => $methods) : ?>
					<?php
						$first = true;
						++$switchCount;
						$controllerName = str_replace('.php', '', $controllerName);
					?>

					<tbody>
						<!-- if first is true add the main controller event -->
						<?php if($first) : ?>
							<?php ++$switchCount; ?>
							
							<?php $first = false; ?>
							<tr>
							  <th scope="row"><?= $controllerName ?></th>

							  	<?php foreach ($groupsList as $group) : ?>
							  		<?php ++$switchCount; ?>
							  		<?php $inputName = $pluginName.'.'.$prefix.'.'.$controllerName.'.'.$group.'.entireController' ?>
								  	<td>
									  	<?= $this->Form->input($inputName, [
	         								'type'=>'checkbox',
	         								'label' => false,
	         								'id' => 'switch-' . $pluginName . '-' . $switchCount,
										    'templates' => [ 
										        'inputContainer' => '<div class="switchToggle">
										        	{{content}}
										        	<label for="switch-' . $pluginName . '-' . $switchCount . '" denied-text=' . __d("Enforcer", "Denied") . ' allowed-text=' . __d("Enforcer", "Allowed") . '></label>
										        </div>',
										    ],
										    'value' => false,
										    'checked' => false,
	                                  	]); ?>
									</td>
							  	<?php endforeach; ?>
							</tr>
						<?php endif; ?>

						<?php foreach ($methods as $methodName => $perms) : ?>
							<tr>
							  	<th scope="row" style="padding-left: 2rem;"><?= $methodName ?></th>
							  	<?php
							  		// make sure array is in right order (this could use more testing)
							  		$perms = array_replace(array_flip($groupsList), $perms);
							  	?>

							  	<?php foreach ($perms as $groupName => $perm) : ?>
							  		<?php ++$switchCount;?>
							  		<?php $inputName = $pluginName.'.'.$prefix.'.'.$controllerName.'.'.$groupName.'.'.$methodName ?>
								  	<td>
									  	<?= $this->Form->input($inputName, [
	         								'type'=>'checkbox',
	         								'label' => false,
	         								'id' => 'switch-' . $pluginName . '-' . $switchCount,
										    'templates' => [ 
										        'inputContainer' => '<div class="switchToggle">
										        	{{content}}
										        	<label for="switch-' . $pluginName . '-' . $switchCount . '" denied-text=' . __d("Enforcer", "Denied") . ' allowed-text=' . __d("Enforcer", "Allowed") . '></label>
										        </div>',
										    ],
										    'value' => $perm,
										    'checked' => $perm,
	                                  	]); ?>
									</td>
							  	<?php endforeach; ?>
							</tr>
						<?php endforeach; ?>
					</tbody>
				<?php endforeach; ?>
			</table>
		</div>
	<?php endforeach; ?>
</div>