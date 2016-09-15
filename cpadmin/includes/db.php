<?php
	/********************************
	 * Database connection script
	 *
	 * By: Richard Tuttle
	 * Last updated: 19 January 2016
	 ********************************/
	/*
	 $user = 'root';
	 $pwd = 'pede7424';
	 $db = 'socnet';
	 $host = 'localhost';
	 $success = new mysqli($host, $user, $pwd, $db);
	 if ($success->connect_errno > 0) {
	 	die("DATABASE CONNECTION FAILED - " . $success->connect_error);
	 }
	 */
	$conn = mysql_Connect('localhost', 'root', 'pede7424');
	if(!$conn) die('could not connect');
	$db = mysql_select_db('socnet');
?>