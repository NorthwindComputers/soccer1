<?php 
require('includes/db.php');
$month = date("M") . " - " . date("Y");
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
$sql_newcustomer = "SELECT COUNT(id) AS NewCustomer FROM customers WHERE (Month(RegisterDate)=Month(current_date) AND Year(RegisterDate)=Year(current_date))";
$result_newcustomer = mysql_query($sql_newcustomer);
$row_newcustomer = mysql_fetch_assoc($result_newcustomer);
$newcustomer = $row_newcustomer["NewCustomer"];
$sql_newvips = "SELECT COUNT(id) AS NewVIP FROM customers WHERE (Month(VIPDate)=MONTH(CURDATE()) AND Year(VIPDate)=YEAR(CURDATE()))";
$result_newvips = mysql_query($sql_newvips);
$row_newvips = mysql_fetch_assoc($result_newvips);
$newVips = $row_newvips["NewVIP"]; 
?>
<p><b>Number of orders: <?=$totalorders;?></b><br>
<?php
$numOrders = "SELECT id, OrderTotal, OrderDate FROM orders WHERE (Month(OrderDate)=MONTH(CURDATE()) AND Year(OrderDate)=YEAR(CURDATE()))";
$numResults = mysql_query($numOrders);
echo "<ul class='panelList'>";
while ($numRows = mysql_fetch_array($numResults)) {
	echo "<li>Order #" . $numRows["id"] . " on " . $numRows["OrderDate"] . " -> $" . number_format($numRows["OrderTotal"],2) . "</li>";
} 
echo "</ul>";
?>
</p><b>New customers this month: <?=$newcustomer;?></b><br>
<?php
$newCust = "SELECT id, FirstName, LastName, RegisterDate FROM customers WHERE (Month(RegisterDate)=MONTH(CURDATE()) AND Year(RegisterDate)=YEAR(CURDATE()))";
$cusResults = mysql_query($newCust);
echo "<ul class='panelList'>";
while ($cusRows = mysql_fetch_array($cusResults)) {
	echo "<li>" . $cusRows["FirstName"] . " " . $cusRows["LastName"] . " joined on " . $cusRows["RegisterDate"] . "</li>";
}
echo "</ul>";
?>
<p><b>New VIP members this month: <?=$newVips;?></b><br>
<?php
$newVIP = "SELECT id, FirstName, LastName, VIPDate, VIPLevel FROM customers WHERE (Month(VIPDate)=MONTH(CURDATE()) AND Year(VIPDate)=YEAR(CURDATE()))";
$vipResults = mysql_query($newVIP);
echo "<ul class='panelList'>";
while ($vipRows = mysql_fetch_array($vipResults)) {
	echo "<li>" . $vipRows["FirstName"] . " " . $vipRows["LastName"] . " joined on " . $vipRows["VIPDate"] . " at level " . $vipRows["VIPLevel"] . "</li>";
}
echo "</ul>";
?></p>