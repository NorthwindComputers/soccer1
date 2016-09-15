<?php
/************************************
 * script called via cron to auto
 * delete expired contents
 * from shopping cart 
 *
 * By: Richard Tuttle
 * Updated: 02 March 2016
 ************************************/
 
 require_once("includes/db.php");
 
 // delete contents after 24 hours
 $sqlCheck2 = "SELECT * FROM shopping_cart_single";
 $resultCheck2 = mysql_query($sqlCheck2);
 while ($rowCheck2 = mysql_fetch_array($resultCheck2)) {
 	$del2 = "DELETE FROM shopping_cart_single WHERE CreatedDate < (NOW() - INTERVAL 1 DAY)";
 	mysql_query($del2) or die("Cleaning Error: " . mysql_error());
 } 
 echo "Shopping Cart Single cleaned!";
 
 // email abandoned cart (after 24 hours) info to CustomerService
 $acSQL = "SELECT SessionID, EmailAddress, CreatedDate FROM shopping_cart WHERE CreatedDate < (NOW() - INTERVAL 1 DAY)";
 $acResult = mysql_query($acSQL);
 $acNum = mysql_num_rows($acResult);
 // echo "acNUM: " . $acNum . "<br>"; // TESTING
 while ($acRow = mysql_fetch_array($acResult)) {
	 $msg = "<u>SessionID - " . $acRow['SessionID'] . " created on " . $acRow['CreatedDate'] . "</u><br>";
	 if ($acRow['EmailAddress'] != "") {
		$msg .= "<b>Customer login:</b> " . $acRow['EmailAddress'] . "<br>";
	 } else {
		$msg .= "Customer did not login - came from IP location of " . $acRow['IPaddr'] . "<br>";
	 }
	 $msg .= "<b>Cart contents included:</b><br>";
	$acProdSQL = "SELECT * FROM shopping_cart WHERE SessionID='" . $acRow['SessionID'] . "'";
	$acProdResult = mysql_query($acProdSQL);
	while ($acProdRow = mysql_fetch_array($acProdResult)) {
		$msg .= "&nbsp;&nbsp;&nbsp;&nbsp;" . $acProdRow['Qty'] . " of " . $acProdRow['ProductName'] . " (" . $acProdRow['RootSKU'] . " / " . $acProdRow['SizeSKU'] . " / " . $acProdRow['ColorSKU'] . " / " . $acProdRow['GenderSKU'] . ") for $" . $acProdRow['Price'] . "<br>";
	}
	$msg .= "<br>";
}
$subject = "Abandonded Cart left more than 24 hours";
$toAddr = "richard@northwind.us";
$headers = "MIME-Version: 1.0\r\n";
$headers .= "From: SoccerOne Customer Service <customerservice@soccerone.com>\r\n"; 
$headers .= "Content-type: text/html; charset: utf8\r\n"; 
$headers .= "Reply-To: SoccerOne <customerservice@soccerone.com>\r\n";
$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
mail($toAddr, $subject, $msg, $headers, '-f customerservice@soccerone.com');
// echo $msg . "<br>"; // TESTING USE ONLY
echo "Email sent!";

 // delete contents after 96 hours
 $sqlCheck = "SELECT * FROM shopping_cart";
 $resultCheck = mysql_query($sqlCheck);
 while ($rowCheck = mysql_fetch_array($resultCheck)) {
 	$del = "DELETE FROM shopping_cart WHERE CreatedDate < (NOW() - INTERVAL 5 DAY)";
 	mysql_query($del) or die("Cleaning Error: " . mysql_error());
 } 
 echo "Shopping Cart cleaned!";
 mysql_close($conn);
?>