<?php
/**
 * Administrative Setting spage
 *
 * Updated: 03 December 2015
 * By: Richard Tuttle
 */
 
if (isset($_GET['salesorder'])) {
	$filtered_var = htmlspecialchars($_GET['salesorder'], ENT_QUOTES); 
	$_GET['salesorder'] = $filtered_var;
}
if (isset($_POST['salesorder'])) { 
	$filtered_var = htmlspecialchars($_POST['salesorder'], ENT_QUOTES); 
	$_POST['salesorder'] = $filtered_var;
}
if (isset($_REQUEST['salesorder'])) { 
	$filtered_var = htmlspecialchars($_REQUEST['salesorder'], ENT_QUOTES); 
	$_REQUEST['salesorder'] = $filtered_var;
}
if (isset($_GET['salescomment'])) {
	$filtered_var = htmlspecialchars($_GET['salescomment'], ENT_QUOTES); 
	$_GET['salescomment'] = $filtered_var;
}
if (isset($_POST['salescomment'])) { 
	$filtered_var = htmlspecialchars($_POST['salescomment'], ENT_QUOTES); 
	$_POST['salescomment'] = $filtered_var;
}
if (isset($_REQUEST['salescomment'])) { 
	$filtered_var = htmlspecialchars($_REQUEST['salescomment'], ENT_QUOTES);
	$_REQUEST['salescomment'] = $filtered_var;
}
if (isset($_GET['customerservice'])) { 
	$filtered_var = htmlspecialchars($_GET['customerservice'], ENT_QUOTES);
	$_GET['customerservice'] = $filtered_var;
}
if (isset($_POST['customerservice'])) { 
	$filtered_var = htmlspecialchars($_POST['customerservice'], ENT_QUOTES); 
	$_POST['customerservice'] = $filtered_var;
}
if (isset($_REQUEST['customerservice'])) { 
	$filtered_var = htmlspecialchars($_REQUEST['customerservice'], ENT_QUOTES); 
	$_REQUEST['customerservice'] = $filtered_var;
}
if (isset($_GET['btnSubmit'])) { 
	$filtered_var = htmlspecialchars($_GET['btnSubmit'], ENT_QUOTES); 
	$_GET['btnSubmit'] = $filtered_var;
}
if (isset($_POST['btnSubmit'])) { 
	$filtered_var = htmlspecialchars($_POST['btnSubmit'], ENT_QUOTES);
	$_POST['btnSubmit'] = $filtered_var;
}
if (isset($_REQUEST['btnSubmit'])) { 
	$filtered_var = htmlspecialchars($_REQUEST['btnSubmit'], ENT_QUOTES);
	$_REQUEST['btnSubmit'] = $filtered_var;
}
include_once('includes/header.php');
$pgTitle = "Settings Admin";
include_once("includes/mainHeader.php");
?>
<link rel="stylesheet" href="css/jquery.treeview.css" type="text/css" />
<script type="text/javascript" src="../ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="js/jquery.treeview.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	$('#leftmenu ul').hide();
	$('#leftmenu li a').click(function() {
		$(this).next().slideToggle('normal');
	});
});
	
function showDatepicker(id) {
    $('#'+id).datepicker({
    	dateFormat: 'mm/dd/yy'
    }).datepicker("show");
}

function hideDatepicker(id) {
    $('#'+id).datepicker('hide');
}

function ChangeCat() {}
</script>
</head>
<body>
<div class="Master_div"> 
	<div class="PD_header">
    	<div class="upper_head"></div>
    	<div class="navi"><?php include_once('includes/menu_main.php'); ?><div class="clear"></div></div>
	</div>
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
          <li><a class="menu" href="settings.php?p=Imprint">Imprint Message</a></li>
          <li><a class="menu" href="settings.php?p=Pricing">Product Pricing</a></li>
	 	  <li><a class="menu" href="settings.php?p=Manufacturer">Manufacturers</a></li>
	      <li><a class="menu" href="settings.php?p=Vendor">Vendors</a></li>
          <li><a class="menu" href="settings.php?p=Shipping">Shipping</a></li>
          <li><a class="menu" href="settings.php?p=Payment">Payments</a></li>
          <li><a class="menu" href="settings.php?p=">Google API</a></li>
          <li><a class="menu" href="settings.php?p=Tax">Tax Setup</a></li>
          <li><a class="menu" href="settings.php?p=Coupon">Coupon Manager</a></li>
          <li><a class="menu" href="settings.php?p=Banner">Banner</a></li>
          <li><a class="menu" href="settings.php?p=Ads">Ads</a></li>
          <li><a class="menu" href="#">VIP</a>
          	<ul>
            	<li><a class="menu" href="settings.php?p=VIP">Edit VIP</a></li>
                <li><a class="menu" href="settings.php?p=VIPManage">Manage VIPs</a></li>
                <li><a class="menu" href="settings.php?p=VIPLevel">Levels</a></li>
            </ul>
          </li>
          <li><a class="menu" href="settings.php?p=Cert">Certificates</a></li>
        </ul>
    </td>
    <td align="left" valign="top" id="content">
<?php
  	$p = $_GET['p'];
  	// echo "<p>P = " . $p . "</p>"; exit; // testing only
	if ($p != '') {
		include('includes/inc_settings' . $p . ".php");
	} else {
		echo "Please choose an option to the left....";
	}
?>
	</td>
  </tr>
</table></div>
</body>
</html>
<?php mysql_close($conn); ?>