<?php
	include("includes/header.php");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Products</title>
	<link rel="stylesheet" href="css/styles.css" type="text/css" />
	<link rel="stylesheet" href="jqtransformplugin/jqtransform_view.css" type="text/css"  media="all" />
	<script type="text/javascript" src="js/jquery-1.4.4.min.js"></script>
	<script type="text/javascript" src="jqtransformplugin/jquery.jqtransform.js"></script>
	<script language="javascript" type="text/javascript">
		$(function(){
			$('form').jqTransform({imgPath:'jqtransformplugin/img/'});
		});
		$(document).ready(function(){
			$("#bundles").load("includes/inc_bundles.php", {"type":"view"});
		});
		
	</script>
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
      <!-- Products view Div starts from here -->
      <div class="product_view">
    	<div class="PV_top">
          <div class="clear"></div>
        </div>
    	<div class="PV_center">
          
          <div id="bundles" class="orders"><img src="images/loader.gif" /></div>
        </div>
  </div>
      <!-- Products view Div ends here --> 
    </div>
</body>
</html>