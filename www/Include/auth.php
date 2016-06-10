<?php

	ini_set( 'session.cookie_httponly', 1 );


	//Start session
	if(!isset($_SESSION)) {
		session_start();
	}

	if(!isset($_SESSION['username']) || (trim($_SESSION['username']) == '')) {
		header('Location: /login.php');
		exit();
	}
	else
	{
		$logged_in = true;

		if(strcmp($_SESSION['role'], "admin") == 0)
		{
			$admin = true;
		}
		else
		{
			$admin = false;
		}

		$user = $_SESSION['username'];
		$group = $_SESSION['role'];
	}

?>
