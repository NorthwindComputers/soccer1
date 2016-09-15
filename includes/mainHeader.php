<?php
/********************************
 * Main html head information   
 *                                          
 * Updated: 09 August 2016        
 * Programmer: Richard Tuttle   
 *******************************/ 
session_start();
date_default_timezone_set('America/Los_Angeles');
if (!isset($_SESSION['org_referrer'])) {
	if (isset($_SERVER['HTTP_REFERER'])) {
		$_SESSION['org_referrer'] = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);
	}
}
$_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];
include_once 'includes/siteCheck.php';
echo '<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>';
	if ($MetaTitle) {
		echo $MetaTitle;
	} elseif ($PageTitle) {
		echo $PageTitle;
	} elseif ($pgTitle) {
		echo $pgTitle;
	} else {
		echo 'SoccerOne | Your One-stop Shop for all Soccer Equipment and Supplies';
	}
	echo '</title>';
	if ($MetaDescription) {
		echo '<meta name="description" content="' . $MetaDescription . '">';
	}
	if ($MetaTag) {
		echo '<meta name="keywords" content="' . $MetaTag . '">';
	} else {
		echo '<meta name="keywords" content="' . $MetaTitle . '">';
	}
	if ($test == TRUE) {
		echo '<base href="https://www.soccerone.net">';
	} else {
		echo "<base href='https://www.soccerone.com'>";
	}
	// if (stristr($_SERVER['HTTP_USER_AGENT'], "Mobile")) { } else { 
	echo '<link rel="stylesheet" href="jqtransformplugin/jqtransform.css" type="text/css">'; // }
	if (stristr($_SERVER['HTTP_USER_AGENT'], "Mobile")) { echo '<link rel="stylesheet" href="css/jquery.mmenu.all.css">'; }
	echo '<link rel="stylesheet" href="css/jquery-ui.min.css">
	<link rel="stylesheet" type="text/css" href="css/css_styles.css" id="size-stylesheet">
	<script type="text/javascript" src="js/jquery.min.js"></script>';
	echo '<script type="text/javascript" src="js/jquery-ui.min.js"></script>
	<script type="text/javascript" src="js/restive.min.js"></script>
	<script type="text/javascript" src="js/jquery.validate.min.js"></script>';
	if (stristr($_SERVER['HTTP_USER_AGENT'], "Mobile")) { echo '<script type="text/javascript" src="js/jquery.mmenu.all.min.js"></script>'; }
	// if (stristr($_SERVER['HTTP_USER_AGENT'], "Mobile")) { } else { 
	echo '<script type="text/javascript" src="jqtransformplugin/jquery.jqtransform.js"></script>'; // }
	include_once("ga.php");
?>
<script>
// restive script to detect for mobile
$(document).ready(function() {
	$('body').restive({
		breakpoints: 	['240', '320', '480', '640', '720', '960', '1280'],
		classes: 	 	['rp_240', 'rp_320', 'rp_480', 'rp_640', 'rp_720', 'rp_960', 'rp_1280'],
		turbo_classes: 	'is_mobile=mobi,is_phone=phone,is_tablet=tablet',
		force_dip:		true
	});
	
  if ($('body').hasClass("mobi")) { 
	  $('ul#nav').removeAttr('id');
		$("nav#menu").mmenu({
			"extensions": ['effect-slide-menu', 'pagedim-black'],
			"offCanvas": {"position": "right"},
			"navbars": [{
				"position": "bottom",
				"content": ["<small>&copy;2016 Youth Sports Publishing Inc. All Rights Reserved</small>"]
			}]
		});
	} 
});
</script>