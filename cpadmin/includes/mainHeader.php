<?php
/********************************
 * Main html head information   
 *                              
 * Updated: 03 December 2015       
 * Programmer: Richard Tuttle   
 *******************************/

echo '<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>';
if ($pgTitle != '') {
	echo $pgTitle;
} else {
	echo "SoccerOne Administration Portal";
}
echo '</title>';
if ($MetaDescription) {
	echo '<meta name="description" content="';
	echo $MetaDescription;
	echo '" />';
}
if ($MetaKeywords) {
	echo '<meta name="keywords" content="';
	echo $MetaKeywords;
	echo '" />';
}
echo '<link rel="stylesheet" href="css/styles.css" type="text/css" />
<link rel="stylesheet" href="jqtransformplugin/jqtransform.css" type="text/css" media="all" />
<link rel="stylesheet" href="../../css/jquery-ui.min.css">
<script src="../../js/jquery.min.js"></script>
<script type="text/javascript" src="../../js/jquery-ui.min.js"></script>
<script type="text/javascript" src="jqtransformplugin/jquery.jqtransform.js"></script>';
?>