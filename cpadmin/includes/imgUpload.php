<?php
/*****************************
 * Image uploader function
 *
 * By: Richard Tuttle
 * Updated: 03 August 2016
 ****************************/
require_once("db.php");
$statusSQL = "SELECT id FROM status WHERE current='yes' LIMIT 1";
$statusResult = mysql_query($statusSQL) or die("Error obtaining site status! - " . mysql_error());
$siteStatus = mysql_fetch_assoc($statusResult);

if ($_FILES["file"]["name"] != '') {
	if ($_FILES["file"]["error"] > 0) {
		echo "Error: " . $_FILES["file"]["error"];
	} else {
		$fileName = $_FILES["file"]["name"];
		if ($_GET["t"] == "option") {
			if ($siteStatus["id"] == 1) {
				$folderLoc = "/home/soccer1/public_html/images/";
			} else {
				$folderLoc = "/home/socnet/public_html/images/";
			}
			move_uploaded_file($_FILES["file"]["tmp_name"], $folderLoc.$fileName);
		} else { 
			if ($siteStatus["id"] == 1) {
				$folderLoc = "/home/soccer1/public_html/images/productImages/";
			} else {
				$folderLoc = "/home/socnet/public_html/images/productImages/";
			}
			move_uploaded_file($_FILES["file"]["tmp_name"], $folderLoc.$fileName);
		}
	}
}
?>
