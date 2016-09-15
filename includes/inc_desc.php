<?php
/**
 * Footer include file
 *
 * Updated: 19 February 2016
 * By: Richard Tuttle
 */

require_once '../cpadmin/includes/db.php';
$id = filter_input(INPUT_GET, 'id');
if($_GET["type"] == "prod") {
	$sql_desc = "SELECT ProductDescription AS Description FROM product_descriptions WHERE ProductID = '". $id ."' LIMIT 1";
} else if($_GET["type"] == "page") {
	$sql_desc = "SELECT Content AS Description FROM cms WHERE PageName = '". $id ."' LIMIT 1";
} else if($_GET["type"] == "club") {
	$sql_desc = "SELECT Content AS Description FROM cms WHERE Type='Club' LIMIT 1";
}
$result_desc = mysql_query($sql_desc);
$row_desc = mysql_fetch_assoc($result_desc);
echo stripslashes($row_desc["Description"]);				
?>
