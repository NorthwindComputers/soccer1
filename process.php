<?php
/************************************
 * Authorize.net transaction process    
 *                                              
 * Updated: 26 August 2016            
 * By: Richard Tuttle               
 ***********************************/
session_start();
require_once 'cpadmin/includes/db.php';
if ($_SESSION["email"] == '' || $_SESSION["email"] == NULL) {
	echo "<h2>SORRY - major error occured.  Please try again: <a href='https://soccerone.com'>CLICK HERE!</a>";
	exit;
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>.... Processing Order ....</title>
</head>
<body>
<?php
$grandtotal = $_POST['grandtotal'];
$notes = $_POST['orderNotes'];
$referrer = $_POST['referrer'];
$ordertotal = $_POST['ordertotal'];
$discount = $_POST['totaldiscount'];
$isvip = $_POST['isvip'];
$gctotal = $_POST['gctotal'];
$weight = $_POST['Weight'];
$shipping = $_POST['shipping'];
$totaltax = $_POST['totaltax'];
$totalship = $_POST['totalship'];
$shipNotes1 = addslashes($_POST['ppmsg']);
$shipNotes2 = addslashes($_POST['dsmsg']);
// if ($gcfunds = "" || is_nan($gcfunds)) { $gcfunds = 0.00; }
$sql_address = "SELECT * FROM shopping_address WHERE SessionID='".session_id()."' LIMIT 1";
$result_address = mysql_query($sql_address);
$row_address = mysql_fetch_assoc($result_address); // get billing/shipping info
$sql_customer = "SELECT id FROM customers WHERE EmailAddress='" . $_SESSION["email"] . "' LIMIT 1";
$result_customer = mysql_query($sql_customer);
$row_customer = mysql_fetch_assoc($result_customer); // get customer id
$sql_orderNum = "SELECT MAX(id) FROM orders";
$result_orderNum = mysql_query($sql_orderNum) or die("ERROR: order number retrieval - " . mysql_error());
$row_orderNum = mysql_fetch_assoc($result_orderNum); // set next order number
$nextOrder = $row_orderNum["MAX(id)"];
// print_r($row_orderNum); // TESTING USE ONLY
// echo "NEXT ORDER: " . $nextOrder . "<br>";// TESTING USE ONLY
$nextid = (int) $nextOrder;
$nextid++;
require_once("includes/authorizenet.php");
$fingerprint = $loginID . "^" . $sequence . "^" . $timestamp . "^" . $grandtotal . "^USD";
$hash = hash_hmac("md5", $fingerprint, $transactionKey);
include_once("includes/mainHeader.php");
?>
<form method="post" id="orderForm" name="orderForm" action='<?php echo $url; ?>'>
	<input type="hidden" name="x_type" value="<?=$xtype;?>">
    <input type="hidden" name="x_version" value="3.1">
    <input type="hidden" name="x_method" value="CC">
    <input type="hidden" name="x_logo_URL" value="https://secure.authorize.net/mgraphics/Logo_1563994_1.png">
    <input type="hidden" name="x_background_URL" value="https://secure.authorize.net/mgraphics/Logo_1563994.gif">
    <input type="hidden" name="x_cancel_url" value="https://www.soccerone.com/cart.php?s=<?=session_id();?>">
    <input type="hidden" name="x_cancel_url_text" value="Back to Shopping Cart">
    <input type="hidden" name="x_fp_sequence" value='<?=$sequence;?>'>
    <input type="hidden" name="x_fp_timestamp" value='<?=$timestamp;?>'>
    <input type="hidden" name="x_fp_hash" value='<?=$hash;?>'>
    <input type="hidden" name="x_login" value='<?=$loginID;?>'>
    <input type="hidden" name="x_amount" value='<?=$grandtotal;?>'>
    <input type="hidden" name="x_show_form" value="PAYMENT_FORM">
    <input type="hidden" name="x_relay_response" value="TRUE">
    <input type="hidden" name="x_relay_always" value="FALSE">
    <input type="hidden" name="x_relay_url" value='<?=$relayURL;?>'>
    <input type="hidden" name="x_recurring_billing" value="FALSE">
    <input type="hidden" name="x_email_customer" value="FALSE">
    <input type="hidden" name="x_currency_code" value="USD">
    <input type="hidden" name="x_invoice_num" value='pre-<?=$nextid;?>'>
    <input type="hidden" name="x_description" value='<?=$description;?>'>
    <input type="hidden" name="x_first_name" value='<?=$row_address["BillingFirstName"];?>'>
    <input type="hidden" name="x_last_name" value='<?=$row_address["BillingLastName"];?>'>
    <input type="hidden" name="x_address" value='<?=$row_address["BillingAddress"];?>'>
    <input type="hidden" name="x_city" value='<?=$row_address["BillingCity"];?>'>
    <input type="hidden" name="x_state" value='<?=$row_address["BillingState"];?>'>
    <input type="hidden" name="x_zip" value='<?=$row_address["BillingZip"];?>'>
    <input type="hidden" name="x_phone" value='<?=$row_address["BillingTelephone"];?>'>
    <input type="hidden" name="x_email" value='<?=$_SESSION["email"];?>'>
    <input type="hidden" name="x_cust_id" value='<?=$row_customer["id"];?>'>
    <input type="hidden" name="x_ship_to_first_name" value='<?=$row_address["ShippingFirstName"];?>'>
    <input type="hidden" name="x_ship_to_last_name" value='<?=$row_address["ShippingLastName"];?>'>
    <input type="hidden" name="x_ship_to_address" value='<?=$row_address["ShippingAddress"];?>'>
    <input type="hidden" name="x_ship_to_city" value='<?=$row_address["ShippingCity"];?>'>
    <input type="hidden" name="x_ship_to_state" value='<?=$row_address["ShippingState"];?>'>
    <input type="hidden" name="x_ship_to_zip" value='<?=$row_address["ShippingZip"];?>'>
    <input type="hidden" name="x_tax" value='<?=$totaltax;?>'>
    <input type="hidden" name="x_freight" value="<?=$totalship;?>">
    <input type="hidden" name="x_test_request" value="FALSE">
    <input type="hidden" name="notes" value="<?=$notes;?>">
    <input type="hidden" name="referrer" value="<?=$referrer;?>">
    <input type="hidden" name="ordertotal" value="<?=$ordertotal;?>">
    <input type="hidden" name="totaldiscount" value="<?=$discount;?>">
    <input type="hidden" name="isvip" value="<?=$isvip;?>">
    <input type="hidden" name="gctotal" value="<?=$gctotal;?>">
    <input type="hidden" name="Weight" value="<?=$weight;?>">
    <input type="hidden" name="shipping" value="<?=$shipping;?>">
    <input type="hidden" name="sessionID" value="<?=session_id();?>">
    <input type="hidden" name="shipNotes1" value="<?=$shipNotes1;?>">
    <input type="hidden" name="shipNotes2" value="<?=$shipNotes2;?>">
    <input type="hidden" name="ip_address" value="<?=$_SESSION[ip_address];?>">
</form>
<script>
$(document).ready(function() {
	$("form").submit();
});
</script>
</body>
</html>