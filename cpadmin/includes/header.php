<?php
	session_start();
	
	if ($_SESSION["userid"] == '') {
		header("location:login.php");
	}

	require_once "includes/db.php";
?>