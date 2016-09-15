<?php
	require_once '../includes/db.php';
	$sql_sales = "SELECT day(OrderDate) AS OrderDay, COUNT(id) AS NumOrders FROM orders WHERE Month(OrderDate)=Month(current_date) AND Year(OrderDate) = Year(current_date) GROUP BY OrderDate";
	$result_sales = mysql_query($sql_sales);
	while($row_sales = mysql_fetch_assoc($result_sales)) {
		$orders[$row_sales["OrderDay"]] = $row_sales["NumOrders"];
	}
	
	$totaldays = date("t");
	for($i=1; $i<=$totaldays; $i++) {
		$days .= $i.",";
		if($orders[$i] == '') {
			$sales .= "0,";
		} else {
			if($max < $orders[$i]) {
				$max = $orders[$i];
			}
			$sales .= $orders[$i].",";
		}
	}	
	
	$max = $max+10;
	mysql_close($conn);
?>
&title=,{font-size: 14px;}&
&x_axis_steps=1&
&y_ticks=5,10,6&
&bar=50,#cc0000, , 10&
&values=<?=substr($sales, 0, -1);?>&
&x_labels=<?=substr($days,0,-1);?>&
&y_min=0&
&y_max=<?=$max;?>&