<?php
/********************************************
 * SoccerOne Administrative CMS Interface   
 *                                                                     
 * Programming by: Richard Tuttle           
 * Last Update: 24 August 2016              
 *******************************************/

include_once("includes/header.php");
include_once("includes/mainHeader.php");
?>
<style type="text/css">
.style11 {font-family: Arial, Helvetica, sans-serif; line-height: 35px;}
.style12 {font-family: "Century Gothic"}
.style13 {font-family: "Century Gothic"; color: #FFFFFF; }
</style>
<script src="js/Chart.js"></script>
<script type="text/javascript">
$(function() {
	$("#fromDate").datepicker({dateFormat: "yy-mm-dd"});
	$("#toDate").datepicker({dateFormat: "yy-mm-dd"});
});
</script>
</head>
<body>
<div class="Master_div"> 
    <div class="PD_header">
    	<div class="upper_head"></div>
    		<div class="navi"><?php include_once('includes/menu_main.php'); ?>
          		<div class="clear"></div>
        	</div>
  		</div>
    <div class="PD_main_form">
    	<div>
        <?php
        	// count number of products total
        	$prodSQL = "SELECT COUNT(id) as TotalProducts FROM products";
        	$prodResult = mysql_query($prodSQL);
        	$rowProd = mysql_fetch_assoc($prodResult);
        	echo "<small>Number of Products in database: " . $rowProd["TotalProducts"] . "</small>";
        	// count number of products enabled with > 0 inventory
        	$activeProdSQL = "SELECT COUNT(id) AS ActiveProducts FROM products WHERE Status='Enabled' AND AvailableQTY > 0";
        	$activeResult = mysql_query($activeProdSQL);
        	$rowActive = mysql_fetch_assoc($activeResult);
        	echo "<br><small>Number of Active Products: " . $rowActive["ActiveProducts"] . "</small><br><br>";
			$month = date("M"). " - ".date("Y");
			$sql_orders = "SELECT COUNT(id) AS TotalOrders, SUM(OrderTotal) AS OrderTotals, SUM(ShippingTotal) AS TotalShipping, SUM(Tax) AS TotalTax, CURDATE() FROM orders WHERE (Month(OrderDate)=MONTH(CURDATE()) AND Year(OrderDate)=YEAR(CURDATE()))";
			$result_orders = mysql_query($sql_orders);
			$row_orders = mysql_fetch_assoc($result_orders);
			$totalorders = $row_orders["TotalOrders"];
			$ordertotals = $row_orders["OrderTotals"];
			$totalshipping = $row_orders["TotalShipping"];
			$totaltax = $row_orders["TotalTax"];
			$sql_customers = "SELECT COUNT(id) AS TotalCustomer FROM customers";
			$result_customers = mysql_query($sql_customers);
			$row_customers = mysql_fetch_assoc($result_customers);
			$totalcustomers = $row_customers["TotalCustomer"];
			$sql_newcustomer = "SELECT COUNT(id) AS NewCustomer FROM customers WHERE (Month(RegisterDate)=Month(current_date) AND Year(RegisterDate)=Year(current_date)) AND RegisterDate IS NOT NULL";
			$result_newcustomer = mysql_query($sql_newcustomer);
			$row_newcustomer = mysql_fetch_assoc($result_newcustomer);
			$newcustomer = $row_newcustomer["NewCustomer"];
			$sql_newvips = "SELECT COUNT(id) AS NewVIP FROM customers WHERE (Month(VIPDate)=MONTH(CURDATE()) AND Year(VIPDate)=YEAR(CURDATE())) AND VIPDate IS NOT NULL";
			$result_newvips = mysql_query($sql_newvips);
			$row_newvips = mysql_fetch_assoc($result_newvips);
			$newVips = $row_newvips["NewVIP"];
		?>
		<table width="980" border="0" align="center" cellpadding="5" cellspacing="1">
		<tr>
			<th align="center" width="300" bgcolor="#00CCFF"><h3 class="style12"><?=$month;?></strong> at a Glance</h3></th>
			<th width="25">&nbsp;</th>
			<th width="300" bgcolor="#00CCFF"><h3><span class="style12">Top 5 Products Sold (Unit)</span></h5></th>
			<th width="25">&nbsp;</th>
			<th width="300" bgcolor="#00CCFF"><h3><span class="style12">Top 5 Customers</span></h5></th>
		</tr>
		<tr>
			<td width="300" id="monthGlance" style="line-height:150%;"><strong>Number of orders:</strong> <?=$totalorders;?><br>
			<strong>Monthly sales:</strong> $<?=number_format($ordertotals,2);?><br>
			<strong>Monthly shipping:</strong> $<?=number_format($totalshipping,2);?><br>
			<strong>Monthly taxes:</strong> $<?=number_format($totaltax,2);?><br>
			<strong>New customers this month:</strong> <?=$newcustomer;?><br>
			<strong>New VIP members this month:</strong> <?=$newVips;?></td>
			<td width="25">&nbsp;</td>
			<td width="300" style="line-height:150%;"><?php
			$sql_prods = "SELECT ProductName, RootSKU, SUM(Qty) AS TotalSold FROM orders_items, orders WHERE orders.id=orders_items.OrderID AND Month(OrderDate)=Month(current_date) AND Year(OrderDate) = Year(current_date) GROUP BY ProductName, RootSKU ORDER BY SUM(Qty) DESC LIMIT 5";
			$result_prods = mysql_query($sql_prods);
			while ($row_prods = mysql_fetch_array($result_prods)) {
				echo $row_prods["ProductName"]." - ".$row_prods["RootSKU"]." (".$row_prods["TotalSold"].")<br/>";
			}
			?></td>
			<td width="25">&nbsp;</td>
			<td width="300" style="line-height:150%;"><?php
			$sql_customers = "SELECT EmailAddress, COUNT(id) AS TotalOrders, SUM(GrandTotal) AS Total FROM orders WHERE Month(OrderDate)=Month(current_date) AND Year(OrderDate)=Year(current_date) GROUP BY EmailAddress ORDER BY Total DESC LIMIT 5 ";
			$result_customers = mysql_query($sql_customers);
			while($row_customers = mysql_fetch_assoc($result_customers)) {
				$sql_name = "SELECT FirstName, LastName FROM customers WHERE EmailAddress='$row_customers[EmailAddress]' LIMIT 1";
				$result_name = mysql_query($sql_name);
				$row_name = mysql_fetch_assoc($result_name);
				echo $row_name["FirstName"]." ".$row_name["LastName"]." (".$row_customers["TotalOrders"].") - $" .number_format($row_customers["Total"],2). "<br>";
			}
			?></td>
		</tr>
		<tr>
			<th align="center" width="300" bgcolor="#00CCFF"><h3 class="style12">Top Promo Codes</h3></th>
			<th width="25">&nbsp;</th>
			<th width="300" bgcolor="#00CCFF"><h3><span class="style12">Top Referrers</span></h5></th>
			<th width="25">&nbsp;</th>
			<th width="300" bgcolor="#00CCFF"><h3><span class="style12">Ordering Information</span></h5></th>
		</tr>
		<tr>
			<td width="300" style="line-height:150%;"><?php
			$sql_prods = "SELECT ProductName, Type, SUM(GrandTotal) AS TotalOrder FROM orders_items, orders WHERE orders.id=orders_items.OrderID AND Month(OrderDate)=Month(current_date) AND Year(OrderDate)=Year(current_date) AND Type='Coupon' GROUP BY ProductName ORDER BY SUM(GrandTotal) DESC LIMIT 5";
			$result_prods = mysql_query($sql_prods);
			$count = 0;
			while ($row_prods = mysql_fetch_array($result_prods)) {
				$tempSQL = "SELECT COUNT(*) AS TotalCount FROM orders_items WHERE Type='Coupon' AND ProductName='$row_prods[ProductName]'";
				$tempResult = mysql_query($tempSQL);
				$tempRow = mysql_fetch_assoc($tempResult);
				echo $row_prods["ProductName"]." - $" .number_format($row_prods["TotalOrder"],2). " - (" . $tempRow["TotalCount"] . ")<br>";
			}
			?></td>
			<td width="25">&nbsp;</td>
			<td width="300" style="line-height:150%;">
		<?php
			$sql_ref = "SELECT DISTINCT referrer FROM orders WHERE (Month(OrderDate)=Month(current_date) AND Year(OrderDate)=Year(current_date) AND (referrer <> '' OR referrer <> NULL)) ORDER BY SUBSTRING_INDEX(referrer, '.',-1), SUBSTRING_INDEX(SUBSTRING_INDEX(CONCAT('.',referrer),'.',-2),'.',1), SUBSTRING_INDEX(SUBSTRING_INDEX(CONCAT('..',referrer),'.',-3),'.',1)";
			$result_ref = mysql_query($sql_ref) or die("SQL ERROR: " . mysql_error());
			$counter = 0;
			$th = '';
			while ($row_ref = mysql_fetch_array($result_ref)) {
				if ($row_ref["referrer"] == '' || $row_ref["referrer"] == NULL || $row_ref["referrer"] == "unknown") {
				} else {
					$sql_count = "SELECT COUNT(*) AS CountTotal FROM orders WHERE referrer='$row_ref[referrer]' AND (Month(OrderDate)=Month(current_date) AND Year(OrderDate)=Year(current_date)) ORDER BY CountTotal";
					$result_count = mysql_query($sql_count);
					$row_count = mysql_fetch_assoc($result_count);
					$url = $row_ref["referrer"];
					$host = str_ireplace('www.', '', parse_url($url, PHP_URL_HOST));
					if ($host == $th) {
					} else {
						echo $host . " - (" . $row_count["CountTotal"] . ")<br>";
						$th = $host;
						$counter++;
					}
				}
			}
			?></td>
			<td width="25">&nbsp;</td>
			<td width="300" style="line-height:150%;"><form action="orderInfo.php" method="post">
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
            $sql_sales = "SELECT day(OrderDate) AS OrderDay, COUNT(id) AS NumOrders FROM orders WHERE Month(OrderDate)=Month(current_date) AND Year(OrderDate)=Year(current_date) GROUP BY OrderDate";
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
            <canvas id="myChart2" width="980" height="250"></canvas>
			</td>
        </tr>
        </table>
        
        <table width="980" border="0" align="center" cellpadding="5" cellspacing="0">
        <tr>
            <td colspan="2" width="25%" height="35" align="left" bgcolor="#333333"><h3 class="style13">YTD vs. Previous Year Monthly Sales Comparison</h3></td>
        </tr>
        <tr>
        	<td colspan="2">
        	<?php
        	// get last years monthly sales figures for first dataset
        	$fill = 2;
        	$Count = 1;
        	$time2 = strtotime('-2 year');
        	$time = strtotime('-1 year');
        	$sdYear = date('Y', $time2);
        	$lastYear = date('Y', $time);
        	$thisYear = date('Y');
        	while ($Count <= 12) {
        		$monthTotal = 0;
        		// echo "Month Total Initial: " . $monthTotal . "<br>"; // testing only
        		$monthCount = str_pad($Count, $fill, '0', STR_PAD_LEFT);
        		$sql_lastYear = "SELECT OrderDate, OrderTotal FROM orders WHERE Month(OrderDate)=$monthCount AND Year(OrderDate)=$lastYear";
        		// echo "SQL: " . $sql_lastYear; exit; // testing only
        		$result_lastYear = mysql_query($sql_lastYear) or die("SQL ERROR: last year - " . mysql_error());
        		while ($row_lastYear = mysql_fetch_array($result_lastYear)) {
        			$monthTotal += $row_lastYear["OrderTotal"];
        		}
        		// echo "Month Total: " . $Count . " " . number_format($monthTotal, 2, '.', '') . "<br>"; // testing only
        		if ($Count == "1") {
        			$preJanTotal = $monthTotal;
        		} elseif ($Count == "2") {
        			$preFebTotal = $monthTotal;
        		} elseif ($Count == "3") {
        			$preMarTotal = $monthTotal;
        		} elseif ($Count == "4") {
        			$preAprTotal = $monthTotal;
        		} elseif ($Count == "5") {
        			$preMayTotal = $monthTotal;
        		} elseif ($Count == "6") {
        			$preJunTotal = $monthTotal;
        		} elseif ($Count == "7") {
        			$preJulTotal = $monthTotal;
        		} elseif ($Count == "8") {
        			$preAugTotal = $monthTotal;
        		} elseif ($Count == "9") {
        			$preSepTotal = $monthTotal;
        		} elseif ($Count == "10") {
        			$preOctTotal = $monthTotal;
        		} elseif ($Count == "11") {
        			$preNovTotal = $monthTotal;
        		} elseif ($Count == "12") {
        			$preDecTotal = $monthTotal;
        		}
        		$Count++;
        	}

        	$fill = 2;
        	$Count = 1;
        	while ($Count <= 12) {
        		$monthTotal = 0;
        		// echo "Month Total Initial: " . $monthTotal . "<br>"; // testing only
        		$monthCount = str_pad($Count, $fill, '0', STR_PAD_LEFT);
        		$sql_sdYear = "SELECT OrderDate, OrderTotal FROM orders WHERE Month(OrderDate)=$monthCount AND Year(OrderDate)=$sdYear";
        		// echo "SQL: " . $sql_sdYear; exit; // testing only
        		$result_sdYear = mysql_query($sql_sdYear) or die("SQL ERROR: year before last year - " . mysql_error());
        		while ($row_sdYear = mysql_fetch_array($result_sdYear)) {
        			$monthTotal += $row_sdYear["OrderTotal"];
        		}
        		// echo "Month Total: " . $Count . " " . number_format($monthTotal, 2, '.', '') . "<br>"; // testing only
        		if ($Count == "1") {
        			$preJanTotal2 = $monthTotal;
        		} elseif ($Count == "2") {
        			$preFebTotal2 = $monthTotal;
        		} elseif ($Count == "3") {
        			$preMarTotal2 = $monthTotal;
        		} elseif ($Count == "4") {
        			$preAprTotal2 = $monthTotal;
        		} elseif ($Count == "5") {
        			$preMayTotal2 = $monthTotal;
        		} elseif ($Count == "6") {
        			$preJunTotal2 = $monthTotal;
        		} elseif ($Count == "7") {
        			$preJulTotal2 = $monthTotal;
        		} elseif ($Count == "8") {
        			$preAugTotal2 = $monthTotal;
        		} elseif ($Count == "9") {
        			$preSepTotal2 = $monthTotal;
        		} elseif ($Count == "10") {
        			$preOctTotal2 = $monthTotal;
        		} elseif ($Count == "11") {
        			$preNovTotal2 = $monthTotal;
        		} elseif ($Count == "12") {
        			$preDecTotal2 = $monthTotal;
        		}
        		$Count++;
        	}
        	
        	// get YTD monthly sales figures for second dataset
        	$fill = 2;
        	$Count = 1;
        	while ($Count <= 12) {
        		$monthCount = str_pad($Count, $fill, '0', STR_PAD_LEFT);
        		$sql_YTD = "SELECT OrderDate, OrderTotal FROM orders WHERE Year(OrderDate)=$thisYear AND Month(OrderDate)=$monthCount";
        		// echo "SQL: " . $sql_YTD; exit; // testing only
        		$result_YTD = mysql_query($sql_YTD) or die("SQL ERROR: YTD - " . mysql_error());
        		$monthTotal = 0;
        		while ($row_YTD = mysql_fetch_array($result_YTD)) {
        			$monthTotal += $row_YTD["OrderTotal"];
        		}
        		if ($Count == "1") {
        			$JanTotal = $monthTotal;
        		} elseif ($Count == "2") {
        			$FebTotal = $monthTotal;
        		} elseif ($Count == "3") {
        			$MarTotal = $monthTotal;
        		} elseif ($Count == "4") {
        			$AprTotal = $monthTotal;
        		} elseif ($Count == "5") {
        			$MayTotal = $monthTotal;
        		} elseif ($Count == "6") {
        			$JunTotal = $monthTotal;
        		} elseif ($Count == "7") {
        			$JulTotal = $monthTotal;
        		} elseif ($Count == "8") {
        			$AugTotal = $monthTotal;
        		} elseif ($Count == "9") {
        			$SepTotal = $monthTotal;
        		} elseif ($Count == "10") {
        			$OctTotal = $monthTotal;
        		} elseif ($Count == "11") {
        			$NovTotal = $monthTotal;
        		} elseif ($Count == "12") {
        			$DecTotal = $monthTotal;
        		}
        		$Count++;
        	}
        	?>
        	<canvas id="myChart" width="980" height="400"></canvas>
        	</td>
        </tr>
        </table>
        <strong>LEGEND</strong>
        <table width="100" cellpadding="10" cellspacing="10">
        <tr>
        	<td width="33%" bgcolor="#B3DBE8"><?php $time2=strtotime('-2 year'); echo date('Y', $time2); ?></td>
        	<td width="33%" bgcolor="#d1e0ff"><?php $time=strtotime('-1 year'); echo date('Y', $time); ?></td>
        	<td width="33%" bgcolor="#000099"><font color="#ffffff"><?php echo date("Y"); ?></font></td>
        </tr>
        </table>
		<script>
		var ctx = $("#myChart").get(0).getContext("2d");
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
		var data = {
			labels: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],
			datasets: [{
				label: "Year Before Last Year Monthly Sales",
				fillColor: "rgba(179, 219, 232, 0.3)",
				strokeColor: "#B3DBE8",
				pointColor: "#B3DBE8",
				pointStrokeColor: "#B3DBE8",
				pointHighlightFill: "#9FDAED",
				pointHightlistStroke: "#9FDAED",
				data: [<?=$preJanTotal2;?>, <?=$preFebTotal2;?>, <?=$preMarTotal2;?>, <?=$preAprTotal2;?>, <?=$preMayTotal2;?>, <?=$preJunTotal2;?>, <?=$preJulTotal2;?>, <?=$preAugTotal2;?>, <?=$preSepTotal2;?>, <?=$preOctTotal2;?>, <?=$preNovTotal2;?>, <?=$preDecTotal2;?>]
			},
			{
				label: "Previous Year Monthly Sales",
				fillColor: "rgba(209, 224, 255, 0.5)",
				strokeColor: "#D1E0FF",
				pointColor: "#d1e0ff",
				pointStrokeColor: "#d1e0ff",
				pointHighlightFill: "#D1B2C2",
				pointHightlistStroke: "#D1B2C2",
				data: [<?=$preJanTotal;?>, <?=$preFebTotal;?>, <?=$preMarTotal;?>, <?=$preAprTotal;?>, <?=$preMayTotal;?>, <?=$preJunTotal;?>, <?=$preJulTotal;?>, <?=$preAugTotal;?>, <?=$preSepTotal;?>, <?=$preOctTotal;?>, <?=$preNovTotal;?>, <?=$preDecTotal;?>]
			},
			{
				label: "YTD Monthly Sales",
				fillColor: "rgba(0, 0, 153, 0.5)",
				strokeColor: "#000099",
				pointColor: "#000099",
				pointStrokeColor: "#000099",
				pointHighlightFill: "#660033",
				pointHightlistStroke: "rgba(220,220,220,1)",
				data: [<?=$JanTotal;?>, <?=$FebTotal;?>, <?=$MarTotal;?>, <?=$AprTotal;?>, <?=$MayTotal;?>, <?=$JunTotal;?>, <?=$JulTotal;?>, <?=$AugTotal;?>, <?=$SepTotal;?>, <?=$OctTotal;?>, <?=$NovTotal;?>, <?=$DecTotal;?>]
			}]
		};
		var myBarChart = new Chart(ctx2).Bar(data2);
		var myLineChart = new Chart(ctx).Line(data);
		</script>
    </div>
    <div class="clear"></div>
<style>
.CSSTableGenerator {
	margin:0px;padding:0px;
	width:100%;
	box-shadow: 10px 10px 5px #888888;
	border:1px solid #000000;
	
	-moz-border-radius-bottomleft:0px;
	-webkit-border-bottom-left-radius:0px;
	border-bottom-left-radius:0px;
	
	-moz-border-radius-bottomright:0px;
	-webkit-border-bottom-right-radius:0px;
	border-bottom-right-radius:0px;
	
	-moz-border-radius-topright:0px;
	-webkit-border-top-right-radius:0px;
	border-top-right-radius:0px;
	
	-moz-border-radius-topleft:0px;
	-webkit-border-top-left-radius:0px;
	border-top-left-radius:0px;
}
.CSSTableGenerator table{
    border-collapse: collapse;
        border-spacing: 0;
	width:100%;
	height:100%;
	margin:0px;padding:0px;
}
.CSSTableGenerator tr:last-child td:last-child {
	-moz-border-radius-bottomright:0px;
	-webkit-border-bottom-right-radius:0px;
	border-bottom-right-radius:0px;
}
.CSSTableGenerator table tr:first-child td:first-child {
	-moz-border-radius-topleft:0px;
	-webkit-border-top-left-radius:0px;
	border-top-left-radius:0px;
}
.CSSTableGenerator table tr:first-child td:last-child {
	-moz-border-radius-topright:0px;
	-webkit-border-top-right-radius:0px;
	border-top-right-radius:0px;
}
.CSSTableGenerator tr:last-child td:first-child{
	-moz-border-radius-bottomleft:0px;
	-webkit-border-bottom-left-radius:0px;
	border-bottom-left-radius:0px;
}
.CSSTableGenerator tr:hover td {}
.CSSTableGenerator tr:nth-child(odd) { background-color:#aad4ff; }
.CSSTableGenerator tr:nth-child(even) { background-color:#ffffff; }
.CSSTableGenerator td{
	vertical-align:middle;
	border:1px solid #000000;
	border-width:0px 1px 1px 0px;
	text-align:left;
	padding:9px;
	font-size:10px;
	font-family:Arial;
	font-weight:normal;
	color:#000000;
}
.CSSTableGenerator tr:last-child td{
	border-width:0px 1px 0px 0px;
}
.CSSTableGenerator tr td:last-child{
	border-width:0px 0px 1px 0px;
}
.CSSTableGenerator tr:last-child td:last-child{
	border-width:0px 0px 0px 0px;
}
.CSSTableGenerator tr:first-child td{
	background:-o-linear-gradient(bottom, #005fbf 5%, #003f7f 100%);
	background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #005fbf), color-stop(1, #003f7f) );
	background:-moz-linear-gradient( center top, #005fbf 5%, #003f7f 100% );
	filter:progid:DXImageTransform.Microsoft.gradient(startColorstr="#005fbf", endColorstr="#003f7f");
	background: -o-linear-gradient(top,#005fbf,003f7f);
	background-color:#005fbf;
	border:0px solid #000000;
	text-align:center;
	border-width:0px 0px 1px 1px;
	font-size:14px;
	font-family:Arial;
	font-weight:bold;
	color:#ffffff;
}
.CSSTableGenerator tr:first-child:hover td{
	background:-o-linear-gradient(bottom, #005fbf 5%, #003f7f 100%);
	background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #005fbf), color-stop(1, #003f7f) );
	background:-moz-linear-gradient( center top, #005fbf 5%, #003f7f 100% );
	filter:progid:DXImageTransform.Microsoft.gradient(startColorstr="#005fbf", endColorstr="#003f7f");
	background: -o-linear-gradient(top,#005fbf,003f7f);
	background-color:#005fbf;
}
.CSSTableGenerator tr:first-child td:first-child{
	border-width:0px 0px 1px 0px;
}
.CSSTableGenerator tr:first-child td:last-child{
	border-width:0px 0px 1px 1px;
}
</style>
    <div class="CSSTableGenerator">
    <h2>Live listing of Shopping Cart database table</h2><small>(last 96 hours)</small>
    <table>
    	<tr>
    	<td><strong>Session ID</strong></td>
    	<td><strong>Email</strong></td>
    	<td><strong>Product</strong></td>
    	<td><strong>Root SKU</strong></td>
    	<td><strong>Size SKU</strong></td>
    	<td><strong>Color SKU</strong></td>
    	<td><strong>Qty</strong></td>
    	<td><strong>Gender SKU</strong></td>
    	<td><strong>VIP Price</strong></td>
    	<td><strong>Price Charged</strong></td>
    	<td><strong>Created</strong></td>
    </tr>
    <?php
    $scSQL = "SELECT * FROM shopping_cart ORDER BY CreatedDate DESC, SessionID";
    $scResult = mysql_query($scSQL);
	while ($scRow = mysql_fetch_assoc($scResult)) {
		echo '<tr>';
		echo '<td>' . $scRow['SessionID'] . '</td>';
		echo '<td>';
			if ($scRow['EmailAddress'] == NULL || $scRow['EmailAddress'] == "") {
				echo "---";
			} else {
				echo $scRow['EmailAddress'];
			}
		echo '</td>';
		echo '<td>' . $scRow['ProductName'] . '</td>';
		echo '<td>' . $scRow['RootSKU'] . '</td>';
		echo '<td>';
			if ($scRow['SizeSKU'] == NULL || $scRow['SizeSKU'] == "") {
				echo "---";
			} else {
				echo $scRow['SizeSKU'];
			}
		echo '</td>';
		echo '<td>';
			if ($scRow['ColorSKU'] == NULL || $scRow['ColorSKU'] == "") {
				echo "---";
			} else {
				echo $scRow['ColorSKU'];
			}
		echo '</td>';
		echo '<td>' . $scRow['Qty'] . '</td>';
		echo '<td>';
			if ($scRow['GenderSKU'] == NULL || $scRow['GenderSKU'] == "") {
				echo "---";
			} else {
				echo $scRow['GenderSKU'];
			}
		echo '</td>';
		echo '<td>';
		if ($scRow['VIPPrice'] == NULL) {
			echo "---";
		} else {
			echo '$' . number_format($scRow['VIPPrice'], 2);
		}
		echo '</td>';
		echo '<td>';
		if ($scRow['Price'] == NULL) {
			echo "---";
		} else { 
			echo '$'. number_format($scRow['Price'], 2);
		}
		echo '</td>';
		echo '<td>' . $scRow['CreatedDate'] . '</td>';
		echo '</tr>';
	}    
    ?>
    </table>
  	</div>
</div>
</body>
</html>
<?php mysql_close($conn); ?>