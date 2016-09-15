<?php
/******************************************
 * Final screen when ordering             
 *                                                              
 * Updated: 17 August 2016                  
 * By: Richard Tuttle                     
 *****************************************/
 
session_start();
// connect to the database and start customer session
require_once 'cpadmin/includes/db.php';
include_once 'includes/siteCheck.php';

// page title
$pgTitle = "Order Confirmation";
	
// main page header include file
include_once("includes/mainHeader.php");
if ($test == TRUE) {
	echo '<base href="https://www.soccerone.net">';
} else {
	echo "<base href='https://www.soccerone.com'>";
}
?>
<style type="text/css">
td,th {font-family: Arial;font-size: 14px;color: #333333;}
.style1 {color: #FF0000;}
.style2 {font-size: 18;font-weight: bold !important;}
.style3 {color: #0000FF;font-weight: bold !important;}
.style4 {color: #FFFFFF;font-weight: bold !important;}
.style5 {color: #000000;font-weight: bold !important;}
.style6 {color: #000000;font-weight: bold !important;}
</style>
</head>
<body>
<div class="Master_div"> 
<?php include_once('includes/header.php'); ?>
	<div class="container container1">
    	<div class="navigation">
      		<div class="clear"></div>
    	</div>
		<div class="clear"></div>
		<div class="main">
    	<form action="" method="post">
		<?php
		// print order confirmation information to the screen
		    $sql_order = "SELECT * FROM orders WHERE id=$_SESSION[orderid] LIMIT 1";
			$result_order = mysql_query($sql_order) or die("Order Error: " . mysql_error());
			$row_order = @mysql_fetch_assoc($result_order);
			$sql_address = "SELECT * FROM orders_address WHERE OrderID=$_SESSION[orderid] LIMIT 1";
			$result_address = mysql_query($sql_address) or die("Order Address Error: " . mysql_error());
			$row_address = @mysql_fetch_assoc($result_address);
		?>
		<table cellpadding="5" cellspacing="0" style="padding: 10px 10px 10px 30px;">
        <tr>
        	<td><?php 
                $sql_vip = "SELECT Status, VIPNum, VIPDate, VIPLevel, VIPExpDate FROM customers WHERE EmailAddress='".$row_order["EmailAddress"]."' LIMIT 1";
				$result_vip = mysql_query($sql_vip) or die("VIP Access Error: " . mysql_error());
				$row_vip = @mysql_fetch_assoc($result_vip);
				$vipnumber = isset($row_vip["VIPNum"]) ? 'VIP Member: '.$row_vip["VIPNum"]: '';
				$sql_mess = "SELECT Message FROM messages WHERE Type='neworderconfirmation' LIMIT 1";
				$result_mess = mysql_query($sql_mess) or die("VIP Message error: " . mysql_error());
				$row_mess = @mysql_fetch_assoc($result_mess);
				$row_mess["Message"] = str_replace("{{VIPNUMBER}}",strtoupper($vipnumber), $row_mess["Message"]);
				echo stripcslashes(str_replace("{{ORDERNUMBER}}",$_SESSION["orderid"], $row_mess["Message"]));
			?>
			<input type="button" name="print" id="print" value="PRINT ORDER CONFIRMATION" onclick="window.print();return false;" style="padding:4px;background-color:#ef1b23;color:#fff;border:1px solid #f35153;" /></td>
        </tr>
        </table>
		<table cellpadding="5" cellspacing="0" style="padding: 10px 10px 10px 30px;" width="100%">	
 		<tr>
  			<td width="50%" valign="top">
  			<!-- Billing Information -->
			<table cellpadding="5" cellspacing="1" width="100%">
	  		<tr>
				<td colspan="2" valign="top" bgcolor="#669900"><h2 align="center" class="style4">Billing Information</h2></td>
	 		</tr>
	  		<tr>
		    	<td width="30%" bgcolor="#EBEBEB"><strong>First Name</strong></td>
		    	<td width="70%" bgcolor="#EBEBEB"><?=$row_address["BillingFirstName"];?></td>
	  		</tr>
      		<tr>
        		<td width="30%"><strong>Last Name</strong></td>
        		<td width="70%"><?=$row_address["BillingLastName"];?></td>
      		</tr>
      		<tr>
        		<td width="30%" bgcolor="#EBEBEB"><strong>Company Name</strong></td>
        		<td width="70%" bgcolor="#EBEBEB"><?=$row_address["BillingCompany"];?></td>
      		</tr>
      		<tr>
        		<td width="30%"><strong>Email Address:</strong></td>
        		<td width="70%"><?=$row_address["BillingEmailAddress"];?></td>
      		</tr>
      		<tr>
        		<td width="30%" bgcolor="#EBEBEB"><strong>Address</strong></td>
        		<td width="70%" bgcolor="#EBEBEB"><?=$row_address["BillingAddress"];?></td>
      		</tr>
      		<tr>
        		<td width="30%"><strong>City</strong></td>
        		<td width="70%"><?=$row_address["BillingCity"];?></td>
      		</tr>
      		<tr>
        		<td width="30%" bgcolor="#EBEBEB"><strong>State</strong></td>
        		<td width="70%" bgcolor="#EBEBEB"><?=$row_address["BillingState"];?></td>
      		</tr>
      		<tr>
       			<td width="30%"><strong>Zip Code</strong></td>
        		<td width="70%"><?=$row_address["BillingZip"];?></td>
      		</tr>
			</table></td>
  			<td width="50%" valign="top">
  			<!-- Shipping Information -->
   			<table width="98%" cellpadding="5" cellspacing="1">
  	 		<tr>
				<td colspan="2" bgcolor="#0099CC"><h2 align="center" class="style4">Shipping Information</h2></td>
	 		</tr>
    		<tr>
        		<td width="30%" bgcolor="#EBEBEB"><strong>First Name</strong></td>
        		<td width="70%" bgcolor="#EBEBEB"><?=$row_address["ShippingFirstName"];?></td>
      		</tr>
      		<tr>
        		<td width="30%"><strong>Last Name</strong></td>
        		<td width="70%"><?=$row_address["ShippingLastName"];?></td>
      		</tr>
      		<tr>
        		<td width="30%" bgcolor="#EBEBEB"><strong>Company Name</strong></td>
        		<td width="70%" bgcolor="#EBEBEB"><?=$row_address["ShippingCompany"];?></td>
      		</tr>
      		<tr>
        		<td width="30%"><strong>Email Address:</strong></td>
        		<td width="70%"><?=$row_address["ShippingEmailAddress"];?></td>
      		</tr>
      		<tr>
        		<td width="30%" bgcolor="#EBEBEB"><strong>Address</strong></td>
        		<td width="70%" bgcolor="#EBEBEB"><?=$row_address["ShippingAddress"];?></td>
      		</tr>
      		<tr>
        		<td width="30%"><strong>City</strong></td>
        		<td width="70%"><?=$row_address["ShippingCity"];?></td>
      		</tr>
      		<tr>
        		<td width="30%" bgcolor="#EBEBEB"><strong>State</strong></td>
        		<td width="70%" bgcolor="#EBEBEB"><?=$row_address["ShippingState"];?></td>
      		</tr>
      		<tr>
        		<td width="30%"><strong>Zip Code</strong></td>
        		<td width="70%"><?=$row_address["ShippingZip"];?></td>
      		</tr>
			</table></td>
		</tr>
		</table>
		<?php 
			if ($row_order["OrderNotes"]): 
		?>
		<!-- Special Instruction info -->
		<table width="94%" border="0" align="center" cellpadding="3" cellspacing="0" style="margin-left:34px">
		<tr>
	   		<td width="63%" height="35" align="left" bgcolor="#0099FF" class="style1"><h3 class="style4">Special Instructions</h3></td>
		</tr>
    	<tr>
        	<td width="63%" height="35" align="left" valign="middle" bgcolor="#FFFFFF"><?php echo $row_order["OrderNotes"]; ?></td>
    	</tr>
		</table>
		<?php 
			endif; 
		?>	
		<!-- Order Information Display -->
		<table width="94%" cellspacing="0" cellpadding="10" border="0" style="margin-left: 34px;">
  		<tr>
    		<td bgcolor="#FF0000"><h3 class="style4">Order Information</h3></td>
  		</tr>
		</table>
		<table  width="94%" cellspacing="0" cellpadding="10" border="0" style="margin-left: 34px;">
  		<tr>
    		<td width="20%" bgcolor="#EBEBEB"><h4 align="left" class="style5">Product Name</h4></td>
   			<td width="20%" bgcolor="#EBEBEB"><h4 class="style5">SKU</h4></td>
    		<td width="20%" bgcolor="#EBEBEB"><h4 class="style5">
            <?php 
            	if (isset($row_vip["VIPNum"]) && $row_vip["VIPNum"] !='') : echo 'VIP Price'; 
            	else : echo 'Non Member Price'; 
            	endif; 
            ?></h4></td>
    		<td width="20%" bgcolor="#EBEBEB"><h4 class="style5">QTY</h4></td>
   			<td width="20%" bgcolor="#EBEBEB"><h4 class="style5">Subtotal</h4></td>
 		</tr>
		<?php
			// process VIP details if ordered
			$cartVIP = "SELECT * FROM orders_items WHERE OrderID=$_SESSION[orderid] AND `Type`='VIP' LIMIT 1";
			$resultVIP = mysql_query($cartVIP) or die("VIP Order Error: " . mysql_error());
			if (@mysql_num_rows($resultVIP)) {
				$today = date('Y-m-d');
				if (($row_vip["VIPExpDate"] != '') || ($vip_row["VIPExpDate"] != NULL || $vip_row["VIPExpDate"] != $today)) {
					$date = new DateTime($row_vip["VIPExpDate"]);
				} else {
					$date = new DateTime($today);
				}
				$date->modify('+1 year');
				$expDate = $date->format('Y-m-d');
				$today = date('Y-m-d');
				$updateSQL = "UPDATE customers SET VIPExpDate='" . $expDate . "', VIP_renewal_date='" . $today . "' WHERE EmailAddress='" . $row_order["EmailAddress"] . "'";
				// echo "SQL: " . $updateSQL . "<br />"; exit;
				mysql_query($updateSQL) or die("Update Error: " . mysql_error());
			}
			
			// get order information from the database			
			// $sql_cart = "SELECT * FROM orders_items WHERE OrderID=$_SESSION[orderid] AND (`Type`='Product' OR `Type`='Single' OR `Type`='Bundle') AND (BundleID='' OR BundleID IS NULL)";
			$sql_cart = "SELECT * FROM orders_items WHERE OrderID=$_SESSION[orderid] AND (`Type`='Product' OR `Type`='Single' OR `Type`='Bundle')";
			$ordertotal = 0;
			$result_cart = mysql_query($sql_cart) or die("Cart Info Error: " . mysql_error());		
			if (@mysql_num_rows($result_cart)) {
				while($row_cart = mysql_fetch_assoc($result_cart)) {
					// get shipping information for this order
					$sql_shiptype = "SELECT * FROM product_shipping WHERE ProductID=$row_cart[ProductID] LIMIT 1";
					$result_shiptype = mysql_query($sql_shiptype);
					$row_shiptype = @mysql_fetch_assoc($result_shiptype);
					$sql_ordership = "SELECT ShippingMethod FROM orders WHERE id=$_SESSION[orderid] LIMIT 1";
					$result_ordership = mysql_query($sql_ordership);
					$row_ordership = @mysql_fetch_assoc($result_ordership);
					if ($row_shiptype["ShippingType"] == "PrimaryLocation" && $row_shiptype["UPS"] == "SpecificShipping") {
						$shipmess = "This item ships: ".$row_shiptype["SpecificOption"];
					} else {
						$shipmess = 'This item ships: '.$row_ordership["ShippingMethod"];
					}
					if ($row_cart["ColorSKU"] == '') {
						$imgColorSKU = "IS NULL ";
					} else {
						$imgColorSKU = "= '$row_cart[ColorSKU]' ";
					}
					if ($row_cart["SizeSKU"] == '') {
						$imgSizeSKU = "IS NULL ";
					} else {
						$imgSizeSKU = "= '$row_cart[SizeSKU]' ";
					}
					// get images
					$sql_image = "SELECT ColorImage FROM product_options WHERE ProductID=$row_cart[ProductID] AND ColorSKU $imgColorSKU AND SizeSKU $imgSizeSKU LIMIT 1";				
					$result_image = mysql_query($sql_image);
					$row_image = @mysql_fetch_assoc($result_image);
					// calculate totals
					$total = $row_cart["Qty"] * $row_cart["Price"];
					$ordertotal = $ordertotal + $total;
					// get product SKU information
					$sql_skuorder = "SELECT SKUOrder FROM products WHERE id=$row_cart[ProductID] LIMIT 1";
					$result_skuorder = mysql_query($sql_skuorder);
					$row_skuorder = @mysql_fetch_assoc($result_skuorder);
					$skuorder = explode("|", $row_skuorder["SKUOrder"]);
					$prodsku = $row_cart[$skuorder[0]."SKU"];
					if ($row_cart[$skuorder[1]."SKU"] != '')
						$prodsku .= "-".$row_cart[$skuorder[1]."SKU"];	
					if ($row_cart[$skuorder[2]."SKU"] != '')
						$prodsku .= "-".$row_cart[$skuorder[2]."SKU"];
					if($row_cart["GenderSKU"] != '') { 
						$prodsku .= "-".$row_cart["Gender"];
				} // end while loop			

        		if (($row_cart["ProductName"] != '' && $row_cart["Type"] != "Single" && $row_cart["GenderSKU"] != NULL) || $row_cart["Type"] == "Product") { 
        ?>
					<tr>
						<td class="cartitem"><?=$row_cart["ProductName"];?><?php if($shipmess != '') { ?><br/><span style="font-size: 11px;"><?=$shipmess;?></span><?php } ?></td>
						<td class="cartitem"><?=$prodsku;?></td>
						<td class="cartitem">$<?=number_format($row_cart["Price"], 2);?></td>
						<td class="cartitem"><?=$row_cart["Qty"];?></td>
						<td class="cartitem">$<?=number_format($total, 2);?></td>
					</tr>
        <?php 
        		} 

				// if Single then display subitems
				if ($row_cart["Type"] === "Single") {
					$sql_sitems = "SELECT * FROM orders_items WHERE OrderID='$_SESSION[orderid]' AND BundleID='$row_cart[id]' AND Type='Single' ORDER BY ProductName";
					$result_sitems = mysql_query($sql_sitems) or die("Single Product Error: " . mysql_error());
					while ($row_sitems = mysql_fetch_array($result_sitems)) {
		?>
						<tr>
							<td class="cartitem"> &gt;&gt;&gt; <?=$row_sitems["ProductName"];?></td>
							<td class="cartitem"><?=$row_sitems["RootSKU"]."-".$row_sitems["ColorSKU"]."-".$row_sitems["SizeSKU"]." x ".$row_sitems["Qty"];?></td>
							<td class="cartitem"><!--Single--></td>
							<td class="cartitem"></td>
							<td class="cartitem"></td>
						</tr>
		<?php
					}
				}
			
				// if Bundle then display subitems
				if ($row_cart["Type"] === "Bundle") {
					$sql_bitems = "SELECT * FROM orders_items WHERE OrderID='$_SESSION[orderid]' AND BundleID='$row_cart[id]' AND Type='Bundle' ORDER BY ProductName";
					$result_bitems = mysql_query($sql_bitems) or die("Bundle Product Error: " . mysql_error());
					while ($row_bitems = mysql_fetch_array($result_bitems)) {
		?>
						<tr>
							<td class="cartitem"> &gt;&gt;&gt; <?=$row_bitems["ProductName"];?></td>
							<td class="cartitem"><?=$row_bitems["RootSKU"]."-".$row_bitems["ColorSKU"]."-".$row_bitems["SizeSKU"]." x ".$row_bitems["Qty"];?></td>
							<td class="cartitem"><!--Bundle--></td>
							<td class="cartitem"></td>
							<td class="cartitem"></td>
						</tr>
		<?php
					}
				}
					
				$sql_imp = "SELECT * FROM imprint_orders WHERE OrderNumber=$_SESSION[orderid] AND OrderItemID=$row_cart[id]";
				$result_imp = mysql_query($sql_imp);
				$num_imp = mysql_num_rows($result_imp);
				$impPrice = 0;
				if ($num_imp > 0) {		
					$imprint_data = '<table class="imprintOptions" style="width: 790px;" cellpadding="3" cellspacing="0"><tr><td class="impheader" colspan="3">Imprint Options</td><td class="impheader"></td></tr>';
					while($row_imp = mysql_fetch_array($result_imp)) {
						$impPrice += floatval($row_imp["ImprintPrice"]);
						$optName = ucfirst($row_imp["Opt1Type"]);
						if ($row_imp["Opt2Type"] != "") {
							$optName .= " & ".ucfirst($row_imp["Opt2Type"]);
						}
						$optTeam = '';
						switch($row_imp["Opt1Type"]) {
							case "chestlogo":
								$optType1 = "Chest Logo";
								$optTeam = stripslashes($row_imp["Opt1Team"]);
								break;
							case "pocketlogo":
								$optType1 = "Pocket Logo";
								$optTeam = stripslashes($row_imp["Opt1Team"]);
								break;
							default:
								$optType1 = ucfirst($row_imp["Opt1Type"]);
						}
						$imprint_data .= '<tr><td class="impLocation">'.$row_imp["Opt1Loc"].'</td><td class="impType">'.$optType1;
						if ($optTeam != '') {
							$imprint_data .= " (Team:".$optTeam.")";
						}
								
						if ($row_imp["Opt1Color"] != '') {
							$imprint_data .= " (Color: ".$row_imp["Opt1Color"].")";
						}
								
						if ($row_imp["Opt1Text"] != '') {
							$imprint_data .= ':<br/>'.str_replace("|","<br/>",$row_imp["Opt1Text"]).' ';
						}
						
						if ($row_imp["Opt2Type"] != '') {
							$optTeam = '';
							switch($row_imp["Opt2Type"]) {
								case "chestlogo":
									$optType2 = "Chest Logo";
									$optTeam = stripslashes($row_imp["Opt2Team"]);
									break;
								case "pocketlogo":
									$optType2 = "Pocket Logo";
									$optTeam = stripslashes($row_imp["Opt2Team"]);
									break;
								default:
									$optType2 = ucfirst($row_imp["Opt2Type"]);
							}
							$imprint_data .= '<br/>'.$optType2;
									
							if($optTeam != '') {
								$imprint_data .= " (Team:".$optTeam.")";
							}
									
							if($row_imp["Opt2Color"] != '') {
								$imprint_data .= " (Color: ".$row_imp["Opt2Color"].") ";
							}
						}

						if($row_imp["Opt2Text"] != '') {
							$imprint_data .= ':<br/>'.str_replace("|","<br/>",$row_imp["Opt2Text"]).' ';
						}
								
						$imprint_data .= '</td><td class="impPrice">$'.number_format($row_imp["ImprintPrice"],2).'</td><td class="impImage">';
						if($row_imp["Opt1Image"] != '') {
							$imprint_data .= '<img src="'.$row_imp["Opt1Image"].'" alt="'.$row_imp["Opt1Type"].'" title="'.$row_imp["Opt1Loc"]." - ".$row_imp["Opt1Type"].'" />';
						}
								
						if($row_imp["Opt2Image"] != '') {
							$imprint_data .= '&nbsp; <img src="'.$row_imp["Opt2Image"].'" alt="'.$row_imp["Opt2Type"].'" title="'.$row_imp["Opt2Loc"]." - ".$row_imp["Opt2Type"].'" />';
						}
								
						$imprint_data .= '</td></tr>';
					} // end while loop
					$imprint_data .= '<tr><td class="noBtmBdr"></td><td class="noBtmBdr right">Imprint Total:</td><td class="noBtmBdr">$'.number_format($impPrice, 2).'</td><td class="noBtmBdr"></td></tr></table>';
			?>
			<tr>
				<td colspan="5"><?=$imprint_data;?></td>
			</tr>
			<?php
					// $ordertotal = $ordertotal + $impPrice;
				} else {
					$imprint_data = "";
			}
		}
	 }

	$sql_coupons = "SELECT * FROM orders_items WHERE OrderID=$_SESSION[orderid] AND `Type`='Coupon'";
	$result_coupons = mysql_query($sql_coupons);
	if (@mysql_num_rows($result_coupons)) {	
		while($row_coupons = mysql_fetch_array($result_coupons)) {
			$sql_cdetail = "SELECT * FROM coupons WHERE Code='$row_coupons[ProductID]' LIMIT 1";
			$result_cdetail = mysql_query($sql_cdetail);
			$row_cdetail = @mysql_fetch_assoc($result_cdetail);
			if($row_cdetail["Type"] == "dollar") {
				$amount = "- $".number_format($row_cdetail["Amount"], 2);
			} else {
				$amount = $row_cdetail["Amount"]."%";
			}
	?>
		<tr>
			<td class="cartitem"><?=$row_coupons["ProductName"];?><?php if ($row_cdetail['ApplyTo']  == "SKU" || $row_cdetail['ApplyTo']  == "Category" ): ?><br/><span style="font-size: 11px;"><?=$shipmess;?></span><?php endif; ?></td>
			<td class="cartitem"><?php if($row_cdetail['ApplyTo'] == "SKU") echo $row_cdetail["ApplyOption"];?></td>
			<td class="cartitem">-</td>
			<td class="cartitem">1</td>
			<td class="cartitem"><?=$amount;?></td>
		</tr>
	<?php	
		}
	}
	
	// show certificate used info in order
	$sql_coupons = "SELECT * FROM orders_items WHERE OrderID=$_SESSION[orderid] AND `Type`='Cert'";
	$result_coupons = mysql_query($sql_coupons);
	if (@mysql_num_rows($result_coupons)) {	
		while($row_coupons = mysql_fetch_array($result_coupons)) {
	?>
		<tr>
			<td class="cartitem"><?=$row_coupons["ProductName"];?></td>
			<td class="cartitem"><?=$row_coupons["ProductID"];?></td>
			<td class="cartitem">-</td>
			<td class="cartitem">1</td>
			<td class="cartitem">---</td>
		</tr>
	<?php	
		}
	}
	
	// get GC info in order
	$gct = 0.00;
	$sql_coupons2 = "SELECT * FROM orders_items WHERE OrderID=$_SESSION[orderid] AND Type='GC'";
	$result_coupons2 = mysql_query($sql_coupons2);
	$rows2 = mysql_num_rows($result_coupons2);
	if ($rows2 > 0) {
		while ($row_coupons2 = mysql_fetch_array($result_coupons2)) {
			$gct += $row_coupons2["Price"];
		}
	}
			
	// show VIP membership in order
	$sql_vip = "SELECT * FROM orders_items WHERE OrderID=$_SESSION[orderid] AND `Type`='VIP'";
	$result_vip = mysql_query($sql_vip);
	if (@mysql_num_rows($result_vip)) {
		while($row_vip = mysql_fetch_array($result_vip)) {
			$sql_vipd = "SELECT Image FROM vip LIMIT1";
			$result_vipd = mysql_query($sql_vipd);
			$row_vipd = @mysql_fetch_assoc($result_vipd);
	?>
        <tr>
            <td class="cartitem"><?=$row_vip["ProductName"];?></td>
			<td class="cartitem"><?=$row_vip["ProductID"];?></td>
			<td class="cartitem">-</td>
			<td class="cartitem">1</td>
			<td class="cartitem">$<?=number_format($row_vip["Price"], 2);?></td>
        </tr>
    <?php
			$ordertotal = $ordertotal + $row_vip["Price"];
		}
	}
	?>
		</table>
<br />
<table id="finalTotals" border="0" cellpadding="10" cellspacing="0"> 
  <tr>
    <td width="350" bgcolor="#EBEBEB"><h4 align="left" class="style6">Order Total:</h4></td>
    <td width="300" bgcolor="#EBEBEB">$<?=number_format($row_order["OrderTotal"], 2);?></td>
  </tr>
  <tr>
    <td align="left" valign="top" bgcolor="#FFFFFF"><strong>Tax:</strong></td>
    <td align="left" valign="top" bgcolor="#FFFFFF">$<?=number_format($row_order["Tax"], 2);?></td>
  </tr>
  <tr>
    <td align="left" valign="top" bgcolor="#EBEBEB"><strong>Discount:</strong></td>
    <td align="left" valign="top" bgcolor="#EBEBEB">$<?=number_format($row_order["Discount"], 2);?></td>
  </tr>
  <tr>
    <td align="left" valign="top" bgcolor="#FFFFFF"><strong>Shipping:</strong></td>
    <td align="left" valign="top" bgcolor="#FFFFFF">$<?=number_format($row_order["ShippingTotal"], 2);?></td>
  </tr>
  <?php
  if ($gct > 0) {
  	echo '<tr>
    <td align="left" valign="top" bgcolor="#FFFFFF"><strong>Gift Certificate(s):</strong></td>
    <td align="left" valign="top" bgcolor="#FFFFFF">$' . number_format($gct, 2) . '</td></tr>';
  }
  ?>
  <tr>
    <td align="left" valign="top" bgcolor="#EBEBEB"><strong>Grand Total:</strong></td>
    <td align="left" valign="top" bgcolor="#EBEBEB">$<?=number_format($row_order["GrandTotal"],2);?></td>
  </tr>
</table>
<br>
<!-- End Totals::::::::::::::::::::::::::::::::::: -->
</form></div>   
<!-- Google Anayltics -->
<script>
<?php 
echo "ga('ec:setAction', 'purchase', {'id': '" . $_SESSION["orderid"] . "','affiliation': 'soccerone','revenue': '" . $row_order["OrderTotal"] . "','shipping': '" . $row_order["ShippingTotal"] . "','tax': '" . $row_order["Tax"] . "'});"; 
?>

/* <![CDATA[ */
var google_conversion_id = 1071787657;
var google_conversion_language = "en";
var google_conversion_format = "1";
var google_conversion_color = "666666";
var google_conversion_label = "gvXqCJrEWhCJ3Yj_Aw";
var google_conversion_value = 1.00;
var google_conversion_currency = "USD";
var google_remarketing_only = false;
/* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="//www.googleadservices.com/pagead/conversion/1071787657/?value=1.00&amp;currency_code=USD&amp;label=gvXqCJrEWhCJ3Yj_Aw&amp;guid=ON&amp;script=0"/>
</div>
</noscript>
<div class="clear"></div>
</div>
<div class="clear"></div>
<div class="footer">
  <div class="foot_box"><?php include_once("includes/footer.php"); ?></div>
</div>
</div>
<?php 
session_destroy();
mysql_close($conn); 
?>
</body>
</html>