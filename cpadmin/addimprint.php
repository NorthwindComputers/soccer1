<?php
	include("includes/header.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>add an imprint</title>
<link rel="stylesheet" href="css/styles.css" type="text/css" />
<style type="text/css">
<!--
body,td,th {
	font-family: Arial;
	font-size: 12px;
	color: #333333;
}
.style1 {color: #FFFFFF}
.smallbox {
	width: 35px;
}
.melbox {
	width: 120px;
}
.lglbox {
	width: 230px;
}

.100{width:100%}
.pricebox {
	width: 50px;}
h1,h2,h3,h4,h5,h6 {
	font-family: Arial, Helvetica, sans-serif;
}
h1 {
	font-size: 100px;
	color: #FF0000;
}

.border { border:1px; border-color:#999999; padding:3px;}
.style2 {
	color: #000000;
	font-weight: bold;
}
.style4 {color: #FFFFFF; font-weight: bold; }
-->
</style>


<?php
include("./imprint/Database.class.php");
include("./imprint/cimprint_category.class.php");
$tf22 = mysql_real_escape_string($_POST["textfield22"]);
$tf23 = mysql_real_escape_string($_POST["textfield23"]);
if(isset($tf22)) {
	$cimprint_category = new cimprint_category();
	$cimprint_category->setCATEGORY($tf22);
	$cimprint_category->setADMIN_NOTES($tf23);
	$enabled=0;
	if(isset($_POST["enabled"]))
		$enabled = 1;	
	$cimprint_category->setenabled($enabled);
	$cimprint_category->insert();
	echo "<script>window.location.href='imprint.php'</script>";
}
?>
<script type="text/javascript" src="./js/jquery-1.4.4.min.js"></script>
</head>
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
        <div class="PD_main_form">
		<form action="" method="post">
			<table width="880" cellspacing="3" cellpadding="3" border="0" align="center">
				<tbody><tr>
					<td bgcolor="#66CCCC"><h2><span class="style1"><strong>New Category</strong></span></h2></td>
				</tr>
				</tbody>
			</table>
			<table width="880" cellspacing="0" cellpadding="3" border="0" align="center">
  <tbody><tr>
    <td width="50%" valign="top" bgcolor="#F2F2F2" align="left"><table width="100%" cellspacing="0" cellpadding="3" border="0">
      <tbody>
      <tr>
        <td width="50%" height="35"><strong>Category Name</strong></td>
        <td width="50%" height="35"><input type="text" value="" id="textfield17" class="100" name="textfield22" style="width: 284px;"></td>
      </tr>
      <tr>
        <td width="50%" height="35"><strong>Admin Notes</strong></td>
        <td width="50%" height="35"><textarea type="text" value="" id="textfield23" class="100" name="textfield23"></textarea></td>
      </tr>
      <tr>
        <td height="35"><strong>Enabled</strong></td>
        <td height="35"><input type="checkbox" name="enabled" /></td>
      </tr>
	  <tr>
        <td width="50%" height="35" align="right"></td>
        <td width="50%" height="35"><input type="submit"/></td>
      </tr>
    </tbody></table></td>
    <td width="50%" valign="top" bgcolor="#F2F2F2" align="left"> 
    </td>
  </tr>
</tbody></table>
		</form>
		</div>
</div>
</body>
</html>