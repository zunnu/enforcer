<?php
if (!isset($params['escape']) || $params['escape'] !== false) {
    $message = h($message);
}
?>
<!-- <div class="message success" onclick="this.classList.add('hidden')"><?= $message ?></div> -->

<script>
	$(document).ready(function(){
		toastr.success('<?= $message ?>');
	});
</script>