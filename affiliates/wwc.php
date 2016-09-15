<?php
/*********************************
 * WCC Management Interface
 *
 * Programming by Richard Tuttle
 * Last Update: 22 December 2014
 *********************************/

require_once '../cpadmin/includes/db.php';
?>
<!DOCTYPE>
<html>
<head>
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Expires" content="-1" />
<meta charset="UTF-8" />
<title>SoccerOne Affiliates Management Portal || World Class Coaching</title>
<link rel="stylesheet" href="../cpadmin/css/styles.css" type="text/css" />
<link rel="stylesheet" href="../jqtransformplugin/jqtransform.css" type="text/css" />
<script type="text/javascript" src="../js/jquery.min.js"></script>
<script type="text/javascript" src="../jqtransformplugin/jquery.jqtransform.js"></script>
<link rel="stylesheet" href="../css/jquery-ui.css" />
<script type="text/javascript" src="../js/jquery-ui.js"></script>
<script src="../cpadmin/js/Chart.js"></script>
<script>
$(function() {
	$("#fromDate").datepicker({dateFormat: "yy-mm-dd"});
	$("#toDate").datepicker({dateFormat: "yy-mm-dd"});
});
</script>
<style>
.affName {color: #ffffff; font-size: 250%; text-align: center; padding: 5px;}
TD.wccText {font-size: 10pt;}
.style11 {font-family: Arial, Helvetica, sans-serif; line-height: 35px;}
.style12 {font-family: "Century Gothic"; }
.style13 {font-family: "Century Gothic"; color: #FFFFFF;}
</style>
</head>
<body>
<div class="Master_div">
	<div class="PD_header">
		<div class="upper_head affName">World Class Coaching</div>
		<div class="clear"></div>
	</div>
</div>
<div class="PD_main_form">
	<div>
	<?php
	$month = date("M"). " - ".date("Y");
	$sql_orders = "SELECT COUNT(o.id) AS TotalOrders, SUM(OrderTotal) AS OrderTotals, o.EmailAddress, c.EmailAddress, VIPNum, CURDATE() FROM orders o, customers c WHERE (MONTH(o.OrderDate)=MONTH(CURDATE()) AND YEAR(o.OrderDate)=YEAR(CURDATE())) AND o.EmailAddress=c.EmailAddress AND (c.VIPNum LIKE 'WCC-%')";
	// echo "SQL: " . $sql_orders; exit; // testing use only
	$result_orders = mysql_query($sql_orders) or die("Database Error: " . mysql_error());
	$row_orders = mysql_fetch_assoc($result_orders);
	$totalorders = $row_orders["TotalOrders"];
	$ordertotals = $row_orders["OrderTotals"];
	$afftotal = $ordertotals * 0.03;
	?>
	<table width="980" border="0" align="center" cellpadding="5" cellspacing="1">
	<tr>
		<th align="center" width="300" bgcolor="#00CCFF"><h3 class="style12"><?=$month;?></strong> at a Glance</h3></th>
		<th width="25">&nbsp;</th>
		<th width="300" bgcolor="#00CCFF"><h3><span class="style12">Top 10 Products Sold</span></h5></th>
		<th width="25">&nbsp;</th>
		<th width="300" bgcolor="#00CCFF"><h3><span class="style12">Ordering Information</span></h5></th>
	</tr>
	<tr>
		<!-- month at a glance -->
		<td width="300" style="line-height:150%;"><b>Number of orders:</b> <?=$totalorders;?><br>
		<b>Monthly sales:</b> $<?=number_format($ordertotals,2);?><br>
		<b>Affiliate commission:</b> $<?=number_format($afftotal,2);?></td>
		<td width="25">&nbsp;</td>
		<!-- top products -->
		<td width="300" style="line-height:150%;" class="wccText">
	<?php
		$sql_prods = "SELECT ProductName, RootSKU, SUM(Qty) as qty, OrderDate, VIPNum FROM orders_items, orders, customers WHERE orders.id=orders_items.OrderID AND (Month(OrderDate)=Month(current_date) AND Year(OrderDate)=Year(current_date)) AND VIPNum LIKE 'WCC-%' GROUP BY ProductName, RootSKU ORDER BY SUM(Qty) DESC LIMIT 10";
		$result_prods = mysql_query($sql_prods) or die("Database Error: " . mysql_error());
		while ($row_prods = mysql_fetch_array($result_prods)) {
			echo "&gt; " . $row_prods["ProductName"] . "<br>";
		}
		?></td>
		<td width="25">&nbsp;</td>
		<!-- date pick for order -->
		<td width="300" style="line-height:150%;"><form action="affOrderInfo.php" method="post">
        <table width="100%" cellpadding="5">
        <tr>
        	<td>from:<br><input type="text" style="margin-bottom: 5px;" id="fromDate" name="fromDate" value=""><br>
        		to:<br><input type="text" style="margin-bottom: 5px;" id="toDate" name="toDate" value=""></td>
        	<td width="50">&nbsp;</td>
        	<td><input type="submit" name="submit" value="Gather Data"></td>
        </tr>
        </table>
        </form></td>
	</tr>
	</table>
	<table width="980" border="0" align="center" cellpadding="5" cellspacing="0">
    <tr>
        <td width="25%" height="35" align="left" bgcolor="#333333"><h3 class="style13">Number of Daily Sales Comparison for <?=$month;?></h3></td>
    </tr>
    <tr>
        <td><?php
        $sql_sales = "SELECT day(o.OrderDate) AS OrderDay, COUNT(o.id) AS NumOrders, o.EmailAddress, c.EmailAddress, VIPNum FROM orders o, customers c WHERE Month(o.OrderDate)=Month(current_date) AND Year(o.OrderDate)=Year(current_date) AND o.EmailAddress=c.EmailAddress AND c.VIPNum LIKE 'WCC-%' GROUP BY o.OrderDate";
		$result_sales = mysql_query($sql_sales);
		while ($row_sales = mysql_fetch_assoc($result_sales)) {
			$orders[$row_sales["OrderDay"]] = $row_sales["NumOrders"];
		}
		$totaldays = date("t");
		for ($i = 1; $i <= $totaldays; $i++) {
			$days .= $i.",";
			if ($orders[$i] == '') {
				$sales .= "0, ";
			} else {
				if ($max < $orders[$i]) {
					$max = $orders[$i];
				}
				$sales .= $orders[$i].", ";
			}
		}	
		$max = $max + 10;
        ?>
        <canvas id="myChart2" width="980" height="400"></canvas>
		</td>
    </tr>
    </table>
	</div>
</div>
<script>
var ctx2 = $("#myChart2").get(0).getContext("2d");
var data2 = {
	labels: [<?php $totaldays=date("t");for($i=1;$i<=$totaldays;$i++){echo '"'.$i.'", ';}?>],
	datasets: [{
		label: "Monthly Daily Sales",
		fillColor: "#3399FF",
		strokeColor: "#297ACC",
		highlightColor: "#BFD7F0",
		HightlistStroke: "#BFD7F0",
		data: [<?php echo $sales;?>]
	}]
};
var myBarChart = new Chart(ctx2).Bar(data2);
</script>
</body>
</html>
<?php mysql_close($conn); ?>