<?php
	include("includes/header.php");
	
	if(isset($_POST["btnUpdate"])) {
	
		$num = intval($_POST["total"]);
		for($i=1; $i<$num; $i++) {
			$sql_update = "UPDATE orders SET OrderStatus='".$_POST["orderstatus_".$i]."', Initial='".$_POST["initial_".$i]."' WHERE id=".$_POST["id_".$i]." LIMIT 1";
			if(!mysql_query($sql_update)) {
				echo "Error updating order: ".mysql_error();
			}
		}
	}

	$page = 'orderlist';
	$id = '';
	if($_GET["id"] != '') {
		$page = "details";
		$id = $_GET['id'];
	}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title></title>
	<link rel="stylesheet" href="css/styles.css" type="text/css" />
   	<link rel="stylesheet" href="css/jquery.ui.datepicker.css">
	<link rel="stylesheet" href="css/jquery.ui.theme.css">
	<script type="text/javascript" src="js/jquery-1.4.4.min.js"></script>
    <script type="text/javascript" src="js/jquery.ui.core.js"></script>
	<script type="text/javascript" src="js/jquery.ui.widget.js"></script>
	<script type="text/javascript" src="js/jquery.ui.datepicker.js"></script>
	<script language="javascript" type="text/javascript">
		$(document).ready(function() {
				$("#orders").load("includes/inc_orders.php", {"type":"<?=$page;?>", "id":"<?=$id;?>"});
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
      <!-- Product Detail Div starts from here -->
      	<div class="PD_main_form">
            <div class="orders" id="orders">
            </div>
            <div class="clear"></div>
  		</div>
      <!-- Product Detail Div ends here --> 
    </div>

</body>
</html>
<?php mysql_close($conn); ?>