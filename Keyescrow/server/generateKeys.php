<?php

	$filepath = "/tmp/";
	$filepath =  $filepath . md5(microtime());

	shell_exec("ssh-keygen -f " . $filepath . " -q -N '' -C ''");

	$privkey = shell_exec("cat " . $filepath);
	$pubkey = shell_exec("cat " . $filepath . ".pub");

	shell_exec("rm " . $filepath . " " . $filepath . ".pub");

	$data = array("pubkey" => $pubkey, "privkey" => $privkey);

	header("Content-type: application/json");
	echo json_encode($data);
?>
