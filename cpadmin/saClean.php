<?php
/*****************************
 * cron file to auto
 * delete expired contents
 * from shopping address after
 * 2 days
 *
 * By: Richard Tuttle
 * Updated: 24 August 2015
 ******************************/
 
 require_once("includes/db.php");
 $sqlCheck = "SELECT * FROM shopping_address";
 $resultCheck = mysql_query($sqlCheck);
 while ($rowCheck = mysql_fetch_array($resultCheck)) {
 	$del = "DELETE FROM shopping_address WHERE insertDate < (NOW() - INTERVAL 2 DAY)";
 	mysql_query($del) or die("Cleaning Error: " . mysql_error());
 } 
 echo "Shopping Address table cleaned!";
 mysql_close($conn);
?>