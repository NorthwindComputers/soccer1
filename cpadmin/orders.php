<?php
	/********************************
	 * Admin Orders Review Screen   
	 *                              
	 * Version: 1.9.4               
	 * Updated: 17 July 2013         
	 * By: Richard Tuttle           
	 *******************************/

// load header information file
include_once("includes/header.php");
	
// was update button pressed?
if (isset($_POST["btnUpdate"])) {
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
	
$pageTitle = "Orders Detail Page";
include_once("includes/mainHeader.php");
?>
<script language="javascript" type="text/javascript">
$(document).ready(function() {
	$("#orders").load("includes/inc_orders.php", {
		"type":"<?=$page;?>", 
		"id":"<?=$id;?>"
	});
});
</script>
</head>
<body>
<!-- Master Div starts from here -->
<div class="Master_div"> 
	<!-- Header Div starts from here -->
    <div class="PD_header">
    	<div class="upper_head"></div>
    	<div class="navi"><?php include_once('includes/menu_main.php'); ?>
        	<div class="clear"></div>
        </div>
  	</div>
    <!-- Header Div ends here --> 
    <!-- Product Detail Div starts from here -->
    <div class="PD_main_form">
        <div class="orders" id="orders"></div>
        <div class="clear"></div>
  	</div>
    <!-- Product Detail Div ends here --> 
</div>
</body>
</html>
<?php mysql_close($conn); ?>