<?php

	require 'cpadmin/includes/db.php';
	session_start();
	
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>
<link rel="stylesheet" href="css/css_styles.css" type="text/css" />
<link rel="stylesheet" href="jqtransformplugin/jqtransform.css" type="text/css"  media="all" />

<script type="text/javascript" src="js/jquery-1.4.4.min.js"></script>
<script type="text/javascript" src="jqtransformplugin/jquery.jqtransform.js"></script>
<script language="javascript" type="text/javascript">
	$(function(){
				window.print();
	});
</script>
</head>

<body>
<!-- Master Div starts from here -->

<div class="Master_div"> 
  <!-- Header Div starts from here -->
  <!-- Header Div ends here --> 
  <!-- Container Div starts from here -->
  <div class="container container1">
    <div class="navigation">
      
      <div class="clear"></div>
    </div>
	<div class="clear"></div>
	<div class="main">
    	<form action="" method="post" >

		<?php

			$sql_address = "SELECT * FROM orders_address WHERE OrderID=$_SESSION[orderid] LIMIT 1";
			$result_address = mysql_query($sql_address);
			$row_address = mysql_fetch_assoc($result_address);
		?>
		<table cellpadding="5" cellspacing="0" style="padding: 10px 10px 10px 30px;">
        	<tr>
            	<td>

                	<?php
					
						$sql_mess = "SELECT Message FROM messages WHERE Type='neworderconfirmation' LIMIT 1";
						$result_mess = mysql_query($sql_mess);
						$row_mess = mysql_fetch_assoc($result_mess);
						
						echo str_replace("{{ORDERNUMBER}}",$_SESSION["orderid"], $row_mess["Message"]);
					?>
                </td>
            </tr>
        </table>
        
		<table cellpadding="5" cellspacing="0" style="padding: 10px 10px 10px 30px;">
			<tr>
				<td width="50%">
					<table cellpadding="5" cellspacing="1">
						<tr>
							<td colspan="2" class="cartheader" style="border-left: 2px solid #bebebe; border-right: 2px solid #bebebe">Billing Information</td>
						</tr>
						<tr>
							<td class="checkouttext">First Name:<br/>
							    <input type="text" readonly="true" class="address" value="<?=$row_address["BillingFirstName"];?>" /></td>
							<td class="checkouttext">Last Name:<br/>
							    <input type="text" readonly="true" class="address" value="<?=$row_address["BillingLastName"];?>" /></td>
						</tr>
					<tr>
							<td class="checkouttext">Company:<br/>
							    <input type="text" readonly="true" class="address" value="<?=$row_address["BillingCompany"];?>" /></td>
							<td class="checkouttext">Email Address:<br/>
							    <input type="text" readonly="true" class="address" value="<?=$row_address["BillingEmailAddress"];?>" /></td>
						</tr>
						<tr>
							<td class="checkouttext" colspan="2">Address:<br/>
							    <input type="text" readonly="true" class="address" style="width: 410px;" value="<?=$row_address["BillingAddress"];?>" /></td>
						</tr>
						<tr>
							<td class="checkouttext">City:<br/>
							    <input type="text" readonly="true" class="address" value="<?=$row_address["BillingCity"];?>" /></td>
							<td class="checkouttext">State:<br/>
							    <input type="text" readonly="true" class="address" value="<?=$row_address["BillingState"];?>" /></td>
						</tr>
						<tr>
							<td class="checkouttext">Zip:<br/>
							    <input type="text" readonly="true" class="address" value="<?=$row_address["BillingZip"];?>" /></td>
							<td class="checkouttext"></td>
						</tr>
					</table>
				</td>
				<td width="50%">
					<table cellpadding="5" cellspacing="1">
						<tr>
							<td colspan="2" class="cartheader" style="border-left: 2px solid #bebebe; border-right: 2px solid #bebebe">Shipping Information</td>
						</tr>
						<tr>
							<td class="checkouttext">First Name:<br/>
							    <input type="text" readonly="true" class="address" value="<?=$row_address["ShippingFirstName"];?>" /></td>
							<td class="checkouttext">Last Name:<br/>
							    <input type="text" readonly="true" class="address" value="<?=$row_address["ShippingLastName"];?>" /></td>
						</tr>
						<tr>
							<td class="checkouttext">Company:<br/>
							    <input type="text" readonly="true" class="address" value="<?=$row_address["ShippingCompany"];?>" /></td>
							<td class="checkouttext">Email Address:<br/>
							    <input type="text" readonly="true" class="address" value="<?=$row_address["ShippingEmailAddress"];?>" /></td>
						</tr>
						<tr>
							<td class="checkouttext" colspan="2">Address:<br/>
							    <input type="text" readonly="true" class="address" style="width: 410px;" value="<?=$row_address["ShippingAddress"];?>" /></td>
						</tr>
						<tr>
							<td class="checkouttext">City:<br/>
							    <input type="text" readonly="true" class="address" value="<?=$row_address["ShippingCity"];?>" /></td>
							<td class="checkouttext">State:<br/>
							    <input type="text" readonly="true" class="address" value="<?=$row_address["ShippingState"];?>" /></td>
						</tr>
						<tr>
							<td class="checkouttext">Zip:<br/>
							    <input type="text" readonly="true" class="address" value="<?=$row_address["ShippingZip"];?>" /></td>
							<td class="checkouttext"></td>
						</tr>
					</table>
				</td>
			</tr>
			
		</table>


		<table cellpadding="5" cellspacing="0" style="padding: 10px 10px 10px 30px;">
			<tr>
				<td class="cartheaderL"></td>
				<td class="cartheader" style="width: 200px">Product Name</td>
				<td class="cartheader" style="width: 150px">SKU</td>
				<td class="cartheader">Unit Price</td>
				<td class="cartheader">QTY</td>
				<td class="cartheaderR">SubTotal</td>
			</tr>

			<?php

				$sql_cart = "SELECT * FROM orders_items WHERE OrderID=$_SESSION[orderid] AND (`Type`='Product' OR `Type`='Bundle') AND (BundleID='' OR BundleID IS NULL)";
				
				$ordertotal = 0;
				$result_cart = mysql_query($sql_cart);

				while($row_cart=mysql_fetch_array($result_cart)) {

					$sql_shiptype = "SELECT * FROM product_shipping WHERE ProductID=$row_cart[ProductID] LIMIT 1";
					$result_shiptype = mysql_query($sql_shiptype);
					$row_shiptype = mysql_fetch_assoc($result_shiptype);
					
					$sql_ordership = "SELECT ShippingMethod FROM orders WHERE id=$_SESSION[orderid] LIMIT 1";
					$result_ordership = mysql_query($sql_ordership);
					$row_ordership = mysql_fetch_assoc($result_ordership);
						
					if($row_shiptype["ShippingType"] == "PrimaryLocation" && $row_shiptype["UPS"] == "SpecificShipping") {
						$shipmess = "This item ships: ".$row_shiptype["SpecificOption"];
					} else {
						$shipmess = 'This item ships: '.$row_ordership["ShippingMethod"];
					}


					//$sql_image = "SELECT ColorImage FROM product_options WHERE ProductID=$row_cart[ProductID] AND ColorSKU='$row_cart[ColorSKU]' AND SizeSKU='$row_cart[SizeSKU]' LIMIT 1";
					
					if($row_cart["ColorSKU"]=='') {
						$imgColorSKU = "IS NULL ";
					} else {
						$imgColorSKU = "= '$row_cart[ColorSKU]' ";
					}
					
					if($row_cart["SizeSKU"]=='') {
						$imgSizeSKU = "IS NULL ";
					} else {
						$imgSizeSKU = "= '$row_cart[SizeSKU]' ";
					}
				
					$sql_image = "SELECT ColorImage FROM product_options WHERE ProductID=$row_cart[ProductID] AND ColorSKU $imgColorSKU AND SizeSKU $imgSizeSKU LIMIT 1";				
					$result_image = mysql_query($sql_image);
					$row_image = mysql_fetch_assoc($result_image);
					$total = $row_cart["Qty"]*$row_cart["Price"];
					$ordertotal = $ordertotal+$total;
					
					$sql_skuorder = "SELECT SKUOrder FROM products WHERE id=$row_cart[ProductID] LIMIT 1";
					$result_skuorder = mysql_query($sql_skuorder);
					$row_skuorder = mysql_fetch_assoc($result_skuorder);
						
					$skuorder = explode("|", $row_skuorder["SKUOrder"]);
					$prodsku = $row_cart[$skuorder[0]."SKU"]."-".$row_cart[$skuorder[1]."SKU"]."-".$row_cart[$skuorder[2]."SKU"];
					
					if($row_cart["GenderSKU"] != '') {
						$prodsku .= "-".$row_cart["Gender"];
					}
					
			?>
				<tr>
					<td class="cartitem"><img class="cartthumb" src="images/productImages/<?=$row_image["ColorImage"];?>" /></td>
					<td class="cartitem"><?=$row_cart["ProductName"];?>
					<?php if($shipmess != '') { ?>
                                		<br/><span style="font-size: 11px;"><?=$shipmess;?></span>
                                	<?php } ?>
					</td>
					<td class="cartitem"><?=$prodsku;?></td>
					<td class="cartitem">$<?=number_format($row_cart["Price"],2);?></td>
					<td class="cartitem"><?=$row_cart["Qty"];?></td>
					<td class="cartitem">$<?=number_format($total,2);?></td>
				</tr>
			<?php
					if($row_cart["Type"] == "Bundle") {
						$sql_bitems = "SELECT * FROM orders_items WHERE OrderID=$_SESSION[orderid] AND BundleID=$row_cart[id] ORDER BY ProductName";
						$result_bitems = mysql_query($sql_bitems);
						
						while($row_bitems=mysql_fetch_array($result_bitems)) {
							$sql_bimage = "SELECT ColorImage FROM product_options WHERE ProductID=$row_bitems[ProductID] AND ColorSKU='$row_bitems[ColorSKU]' AND SizeSKU='$row_bitems[SizeSKU]' LIMIT 1";
							$result_bimage = mysql_query($sql_bimage);
							$row_bimage = mysql_fetch_assoc($result_bimage);
							
							?>
								<tr>
									<td class="cartitem"><img class="cartthumb" style="width: 30px;" src="images/productImages/<?=$row_bimage["ColorImage"];?>" /></td>
									<td class="cartitem"><?=$row_bitems["ProductName"];?></td>
									<td class="cartitem"><?=$row_bitems["RootSKU"]."-".$row_bitems["ColorSKU"]."-".$row_bitems["SizeSKU"];?></td>
									<td class="cartitem">Bundle</td>
									<td class="cartitem">1</td>
									<td class="cartitem"></td>
								</tr>
							<?php
						}
					}
			
				}

				$sql_coupons = "SELECT * FROM orders_items WHERE OrderID=$_SESSION[orderid] AND `Type`='Coupon'";
				$result_coupons = mysql_query($sql_coupons);
					
				while($row_coupons = mysql_fetch_array($result_coupons)) {
				
					$sql_cdetail = "SELECT * FROM coupons WHERE Code='$row_coupons[ProductID]' LIMIT 1";
					$result_cdetail = mysql_query($sql_cdetail);
					$row_cdetail = mysql_fetch_assoc($result_cdetail);

					if($row_cdetail["Type"] == "dollar") {
						$amount = "- $".$row_cdetail["Amount"];
					} else {
						$amount = $row_cdetail["Amount"]."%";
					}
					?>
						<tr>
							<td class="cartitem"></td>
							<td class="cartitem"><?=$row_coupons["ProductName"];?></td>
							<td class="cartitem"><?=$row_coupons["ProductID"];?></td>
							<td class="cartitem">-</td>
							<td class="cartitem">1</td>
							<td class="cartitem"><?=$amount;?></td>
						</tr>


					<?php
						
				}
				
					$sql_vip = "SELECT * FROM orders_items WHERE OrderID=$_SESSION[orderid] AND `Type`='VIP'";
					$result_vip = mysql_query($sql_vip);
					
					while($row_vip = mysql_fetch_array($result_vip)) {
						$sql_vipd = "SELECT Image FROM vip LIMIT1";
						$result_vipd = mysql_query($sql_vipd);
						$row_vipd = mysql_fetch_assoc($result_vipd);
						
						?>
                        	
                            <tr>
                            	<td class="cartitem"><img class="cartthumb" src="images/productImages/<?=$row_vipd["Image"];?>" /></td>
								<td class="cartitem"><?=$row_vip["ProductName"];?></td>
								<td class="cartitem"><?=$row_vip["ProductID"];?></td>
								<td class="cartitem">-</td>
								<td class="cartitem">1</td>
								<td class="cartitem">$<?=number_format($row_vip["Price"],2);?></td>
                            </tr>
                        
                        <?php
						$ordertotal = $ordertotal+$row_vip["Price"];
					}
	
				$sql_order = "SELECT * FROM orders WHERE id=$_SESSION[orderid] LIMIT 1";
				$result_order = mysql_query($sql_order);
				$row_order = mysql_fetch_assoc($result_order);
			?>
		</table>

		<table cellpadding="5" cellspacing="0" style="padding: 10px; width: 100%;">
			<tr>
				<td style="vertical-align: top; width: 500px;">
					<table cellpadding="5" cellspacing="2" style="margin-top: 5px; margin-left: 20px;">
                            <tr>
                                <td class="totals" style="text-align: right;">Payment Type:</td>
                                <td class="totals"><?=$row_order["CardType"];?></td>
                        </tr>
                        <tr>
                            <td class="totals" style="text-align: right;">Card Number:</td>
                            <td class="totals">xxxxxxxxxx<?php echo substr($row_order["CCNum"],-4);?></td>
                        </tr>
                        <tr>
                            <td class="totals" style="text-align: right;">Exp Date:</td>
                            <td class="totals"><?=$row_order["ExpDate"];?></td>
                        </tr>
                        <tr>
                            <td class="totals" style="text-align: right; height: 17px;"> </td>
                            <td class="totals"></td>
                        </tr>
                    </table>
				</td>
				<td style="vertical-align: top;">
                	<!-- Totals::::::::::::::::::::::::::::::::::: -->
                    <table cellpadding="5" cellspacing="0" >
                        <tr>
                        	<td>
                            	<table cellpadding="5" cellspacing="2">
                                	<tr>
                                    	<td class="totals" style="text-align: right;">Order Total:</td>
                                        <td class="totals"><span id="ordertotalval">$<?=number_format($row_order["OrderTotal"], 2);?></span></td>
                                    </tr>
                                    <tr>
                                    	<td class="totals" style="text-align: right;">Tax:</td>
                                        <td class="totals"><span id="totaltaxval">$<?=number_format($row_order["Tax"], 2);?></span></td>
                                    </tr>
                                    <tr>
                                    	<td class="totals" style="text-align: right;">Discount:</td>
                                        <td class="totals"><span id="totaltaxval">$<?=number_format($row_order["Discount"], 2);?></span></td>
                                    </tr>
								    <tr>
                                    	<td class="totals" style="text-align: right;">Shipping:</td>
                                        <td class="totals"><span id="totalshippingval">$<?=number_format($row_order["ShippingTotal"], 2);?></span></td>
                                    </tr>
                                    <tr>
                                    	<td class="totals" style="text-align: right;">Grand Total:</td>
                                        <td class="totals"><span id="grandtotalval">$<?=number_format($row_order["GrandTotal"],2);?></span></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
				</td>
			</tr>
		</table>
                <!-- End Totals::::::::::::::::::::::::::::::::::: -->
	</form>
	</div>    
    <div class="clear"></div>
  </div>
  <div class="clear"></div>
  <!-- Container Div ends here --> 
  <!-- Footer Starts from here -->
  <div class="footer">
	
  </div>
  <!-- Footer Div ends here --> 
</div>
</body>
</html>

<?php mysql_close($conn); ?>