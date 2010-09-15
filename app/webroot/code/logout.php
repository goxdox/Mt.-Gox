<?php
	include('../../noserve/config.inc');
	include('lib/functions.inc');
	include('lib/session.php');

	session_destroy();
	
	$serverName=$_SERVER["SERVER_NAME"];
	header("Location: http://$serverName");
	//exit;
?>
