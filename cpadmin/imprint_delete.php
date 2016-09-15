<?php
include('includes/header.php');
session_start();

/** For Delete **/
$id = 0;
if (isset($_GET['id'])) {
	$id = intval($_GET['id']);
	$sql_details = "DELETE FROM imprint_cusom_options WHERE id=".$id;
	mysql_query($sql_details);
	
	$sql_details1 = "DELETE FROM imprint_information WHERE option_id=".$id;
	@mysql_query($sql_details1);
	header("location: imprint_list.php");
}
