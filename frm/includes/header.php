<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>MachForm - PHP Form Builder</title>
<meta name="robots" content="index, follow">
<meta name="description" content="Appnitro Form Builder">
<?php if(!empty($header_data)){ echo $header_data; } ?>
<link rel="stylesheet" type="text/css" href="index.css" media="all">
</head>
<body id="form_builder">

<?php if(!empty($show_status_bar) && $show_status_bar == true){ ?>
<div id="statusBar">
<div style="display: block;" id="statusPanel">
<div id="statusText">Loading Data ...</div>
</div>
</div>
<?php } ?>

<img id="top" src="images/top.png" alt="" class="fix_png">
<div id="container">

<!--start header-->
<div id="header">
<img src="images/appnitro_logo.png" class="fix_png" id="appnitro_logo" width="179" height="55"/>
<?php if(empty($hide_nav)){ ?>
<ul id="header_tab">
	<li><a href="manage_form.php" title="Form Manager"><img src="images/manage_forms_active.gif" alt=""></a></li>
	<li class="lgo"><a href="logout.php" class="lbOn" title="Logout!"><img src="images/logout.gif" alt=""></a></li>
</ul>
<?php } ?>
</div>
<!--end header-->

<!--start panel-->
<div id="panel">
