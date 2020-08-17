<?php

echo json_encode([
	'response' => [
		'msg' => $_redirect['msg'],
		'code' => $_redirect['status'],
	]
]);
// exit();

?>