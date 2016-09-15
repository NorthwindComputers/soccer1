<?php
	include('includes/header.php');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>
<link rel="stylesheet" href="css/styles.css" type="text/css" />
<link rel="stylesheet" href="js/jquery.wysiwyg.css" type="text/css" />
<link rel="stylesheet" href="css/jquery.ui.datepicker.css">
<link rel="stylesheet" href="css/jquery.ui.theme.css">
<link rel="stylesheet" href="css/jquery.treeview.css" type="text/css" />
<script type="text/javascript" src="js/jquery-1.4.4.min.js"></script>
<script type="text/javascript" src="js/jquery.wysiwyg.js"></script>
<script type="text/javascript" src="ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="js/jquery.ui.core.js"></script>
<script type="text/javascript" src="js/jquery.ui.widget.js"></script>
<script type="text/javascript" src="js/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="js/jquery.treeview.js"></script>

<script type="text/javascript">
	$(document).ready(function(){
		$('#leftmenu ul').hide();
		$('#leftmenu li a').click(function() {
			$(this).next().slideToggle('normal');
		});
		
	});

	function ChangeCat() {
		
	}
</script>
<!--[if lt IE 8]>
   <style type="text/css">
   li a {display:inline-block;}
   li a {display:block;}
   </style>
   <![endif]-->
</head>

<body>
<!-- Master Div starts from here -->
<div class="Master_div"> 
  <!-- Header Div starts from here -->
  <div class="PD_header">
    <div class="upper_head"></div>
    <div class="navi">
      <?php include('includes/menu_main.php'); ?>
      <div class="clear"></div>
    </div>
  </div>
  <!-- Header Div ends here --> 
  <!-- Product Detail Div starts from here -->
  <div class="options">
	<h1>Settings<br />
          <span>----------------------------------------------------------</span></h1>
    	<div class="clear"></div>
	
	<table width="100%" border="0" align="center" cellpadding="5" cellspacing="1">
  		<tr>
    			<td width="180" align="left" valign="top" class="setting">
        

	<ul id="leftmenu" class="leftmenu">
          <li><a class="menu" href="#">Sales Setup</a>
            <ul>
              <li><a class="menu" href="settings.php?p=SalesEmail">Email Setup</a></li>
              <li><a class="menu" href="settings.php?p=SalesMessage">Sales Message</a></li>
              <li><a class="menu" href="settings.php?p=VIPMessage">VIP Message</a></li>
            </ul>
          </li>
          <li><a class="menu" href="#">User Management</a>
            <ul>
              <li><a class="menu" href="settings.php?p=UsersRules">Rules</a></li>
              <li><a class="menu" href="settings.php?p=Users">View Users</a></li>
            </ul>
          </li>
          <li><a class="menu" href="settings.php?p=Gender">Ranges</a></li>
          <li><a class="menu" href="settings.php?p=CustomerGroup">Customer Group</a></li>
          <li><a class="menu" href="settings.php?p=Options">Product Options</a></li>
	  	  <li><a class="menu" href="settings.php?p=Category">Product Category</a></li>
          <li><a class="menu" href="settings.php?p=Style">Product Styles</a></li>
          <li><a class="menu" href="settings.php?p=Pricing">Product Pricing</a></li>
	  	  <li><a class="menu" href="settings.php?p=Manufacturer">Manufacturers</a></li>
	  	  <li><a class="menu" href="settings.php?p=Vendor">Vendors</a></li>
          <li><a class="menu" href="settings.php?p=Shipping">Shipping</a></li>
          <li><a class="menu" href="settings.php?p=Payment">Payments</a></li>
          <li><a class="menu" href="settings.php?p=">Google API</a></li>
          <li><a class="menu" href="settings.php?p=Tax">Tax Setup</a></li>
          <li><a class="menu" href="settings.php?p=Coupon">Coupon Manager</a></li>
          <li><a class="menu" href="settings.php?p=Banner">Banner</a></li>
          <li><a class="menu" href="settings2.php?p=Options">Organise sizes</a></li>
          <li><a class="menu" href="#">VIP</a>
          	<ul>
            	<li><a class="menu" href="settings.php?p=VIP">Edit VIP</a></li>
                <li><a class="menu" href="settings.php?p=VIPManage">Manage VIPs</a></li>
                <li><a class="menu" href="settings.php?p=VIPLevel">Levels</a></li>
            </ul>
          </li>
        </ul>

    </td>

    <td align="left" valign="top">

<!-- ============================================================================================ -->
	<?php
		if(isset($_GET["p"]) && $_GET["p"] != '') {
			include('includes/inc_settings'.$_GET['p']."2.php");
		}
		
	?>
<!-- ===================================================================================================== -->
	</td>
  </tr>
</table>


  </div>

</body>
</html>
<?php
	mysql_close($conn);
?>