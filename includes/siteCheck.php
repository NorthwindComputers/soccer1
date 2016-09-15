<?php
/*****************************
 * check for DEV or LIVE
 *
 * by: Richard Tuttle
 * updated: 09 August 2016
 *****************************/
 $siteSQL = "SELECT id FROM status WHERE current='yes'";
 $resultCk = mysql_query($siteSQL);
 $siteCk = mysql_fetch_assoc($resultCk);
 if ($siteCk == 1) {
 	$test = FALSE;
 } else {
 	$test = TRUE;
 }
?>