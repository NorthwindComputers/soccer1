<?php
/*****************************
 * Order screen information  
 *        
 * By: Ricahrd Tuttle        
 * Updated: 16 August 2016      
 ****************************/

require_once 'db.php';
	
// show compelte orders listing
if($_POST["type"] == "orderlist") {		
?>
<table cellpadding="5" cellspacing="1" width="980px">
<tr>
	<td class="row1" style="text-align: center;"><input style="border: 1px solid #bebebe; height: 25px; width: 90%;" type="text" id="sorderid" name="sorderid" value="Search Order ID" /></td>
	<td class="row1" style="text-align: center;"><input style="border: 1px solid #bebebe; height: 25px; width: 90%;" type="text" id="sname" name="sname" value="Search By Customer Name" /></td>
	<td class="row1" style="text-align: center;"><input style="border: 1px solid #bebebe; height: 25px; width: 90%;" type="text" id="sdate" name="sdate" value="Search By Date" /></td>
	<td class="row1" style="text-align: center;"><select  style="border: 1px solid #bebebe; height: 25px; width: 90%;" id='sstatus' name='sstatus'><option value=''>Select Status</option><option value='Pending' selected>Pending</option><option value='Processing'>Processing</option><option value='Processing - Import Done'>Processing - Import Done</option><option value='Fraud'>Fraud</option><option value='Stop Order'>Stop Order</option><option value='Return'>Return</option><option value='Cancel'>Cancel</option><option value='Shipped'>Shipped</option></select></td>
</tr>
<tr>
	<td></td>
	<td></td>
	<td></td>
	<td class="row1"><input type="button" style="float: right; border: 1px solid #bebebe; background-color: #ff6600; width: 120px; height: 25px; color: #fff;" id="btnSearch" name="btnSearch" value="Search" /></td>
</tr>
</table>
<script>
$("#sdate").datepicker();
$("#sorderid").focus(function() {
	if($(this).val() == 'Search Order ID') {
		$(this).val('');
	}
});
			$("#sname").focus(function() {
				if($(this).val() == 'Search By Customer Name') {
					$(this).val('');
				}
			});
			$("#sdate").focus(function() {
				if($(this).val() == 'Search By Date') {
					$(this).val('');
				}
			});

			$("#sorderid").focusout(function() {
				if($(this).val() == '') {
					$(this).val('Search Order ID');
				}
			});
			$("#sname").focusout(function() {
				if($(this).val() == '') {
					$(this).val('Search By Customer Name');
				}
			});
			$("#sdate").focusout(function() {
				if($(this).val() == '') {
					$(this).val('Search By Date');
				}
			});

			$("#btnSearch").click(function() {
				$("#orderlist").html('<img src="images/loader.gif" />');
				var oid;
				var oname;
				var odate;

				if($("#sorderid").val() == 'Search Order ID') {
					oid = '';
				} else {
					oid = $("#sorderid").val();
				}

				if($("#sname").val() == 'Search By Customer Name') {
					oname = '';
				} else {
					oname = $("#sname").val();
				}

				if($("#sdate").val() == 'Search By Date') {
					odate = '';
				} else {
					odate = $("#sdate").val();
				}

				$("#orderlist").load("includes/inc_orders.php", {
					"type":"orders", 
					"id":oid, 
					"name":oname, 
					"date":odate, 
					"status":$("#sstatus").val()
				});
			});

			$("#orderlist").load("includes/inc_orders.php", {
				"type":"orders",
				"status":"pending"
			});
		</script>
        <form action="" method="post">
		<div id="orderlist">
        	<img src="images/loader.gif" />
		</div>
        </form>
        <?php
		mysql_close($conn);
		exit();
	}

	if($_POST["type"] == "orders") {
		$id = mysql_real_escape_string($_POST["id"]);
		$name = mysql_real_escape_string($_POST["name"]);
		$date = mysql_real_escape_string($_POST["date"]);
		$status = mysql_real_escape_string($_POST["status"]);
		?>
		<table cellpadding="5" cellspacing="1" width="980px" style="margin-top: 20px;">
                <tr>
                    <td colspan="8" class="headersmain">Order Management <small>(limited to 75)</small>
                    	<input type="submit" style="float: right; border: 1px solid #bebebe; background-color: #ff6600; width: 120px; height: 25px; color: #fff;" id="btnUpdate" name="btnUpdate" value="Update" />
                    </td>
                </tr>
                <tr>
                    <td class="headers" style="width: 97px;">Order ID</td>
                    <td class="headers" style="width: 109px;">Purchased On</td>
                    <td class="headers" style="width: 295px;">Customer</td>
                    <td class="headers" style="width: 140px;">Amount</td>
                    <td class="headers" style="width: 80px;">VIP</td>
                    <td class="headers" style="width: 150px;">Status</td>
                    <td class="headers" style="width: 114px;">Initial</td>
                    <td class="headers" style="width: 40px;"></td>
                </tr>
                
                <?php
					$sql_orders  = "SELECT DISTINCT o.id AS id, o.OrderDate, o.GrandTotal, o.OrderStatus, o.Initial, c.FirstName, c.LastName, c.Status, c.id AS cid FROM orders o, customers c WHERE o.EmailAddress=c.EmailAddress";
					if ($id != '') {
						$sql_orders .= " AND o.id=$id";
					}
					if ($name != '') {
						$sql_orders .= " AND (c.FirstName LIKE '%$name%' OR c.LastName LIKE '%$name%')";
					}
					if ($date != '') {
						$sql_orders .= " AND str_to_date(OrderDate, '%Y-%c-%d') = str_to_date('$date', '%c/%d/%Y')";
					}
					if ($status != '') {
						$sql_orders .= " AND o.OrderStatus = '$status'";
					}
					$sql_orders .= " ORDER BY o.id DESC LIMIT 75";
					// echo "SQL: " . $sql_orders; exit(); // TESTING USE ONLY
					$result_orders = mysql_query($sql_orders);

					$r_num = 1;
					$num = 1;
					while ($row_orders=mysql_fetch_array($result_orders)) {
						if($r_num == 1) {
							$color = "row1";
							$r_num++;
						} else {
							$color = "row2";
							$r_num = 1;
						}
						?>
                        	<tr>
								<td class="<?=$color;?>" style="text-align: center; font-weight: bold;">
                                	<input type="hidden" id="id_<?=$num;?>" name="id_<?=$num;?>" value="<?=$row_orders["id"];?>" />
									<a href="orders.php?id=<?=$row_orders["id"];?>"><?=$row_orders["id"];?></a>
                                </td>  
                                <td class="<?=$color;?>"><?=$row_orders["OrderDate"];?></td>
                                <td class="<?=$color;?>"><a href="customers.php?id=<?=$row_orders['cid'];?>"><?=$row_orders["FirstName"]." ".$row_orders["LastName"];?></a></td>
                                <td class="<?=$color;?>" style="text-align: center;"><?="$".number_format($row_orders["GrandTotal"],2);?></td>
                                <td class="<?=$color;?>"><?=$row_orders["Status"];?></td>
                                <td class="<?=$color;?>">
									<select id="orderstatus_<?=$num;?>" name="orderstatus_<?=$num;?>">
                                    	<option <?php if($row_orders["OrderStatus"]=="Pending") { echo ' selected="selected"'; } ?> value="Pending">Pending</option>
                                        <option <?php if($row_orders["OrderStatus"]=="Processing") { echo ' selected="selected"'; } ?> value="Processing">Processing</option>
                                        <option <?php if($row_orders["OrderStatus"]=="Processing - Import Done") { echo ' selected="selected"'; } ?> value="Processing - Import Done">Processing - Import Done</option>
                                        <option <?php if($row_orders["OrderStatus"]=="Fraud") { echo ' selected="selected"'; } ?> value="Fraud">Fraud</option>
                                        <option <?php if($row_orders["OrderStatus"]=="Stop Order") { echo ' selected="selected"'; } ?> value="Stop Order">Stop Order</option>
                                        <option <?php if($row_orders["OrderStatus"]=="Return") { echo ' selected="selected"'; } ?> value="Return">Return</option>
                                        <option <?php if($row_orders["OrderStatus"]=="Cancel") { echo ' selected="selected"'; } ?> value="Cancel">Cancel</option>
                                        <option <?php if($row_orders["OrderStatus"]=="Shipped") { echo ' selected="selected"'; } ?> value="Shipped">Shipped</option>
                                    </select>
                                </td>
                                <td class="<?=$color;?>" style="text-align: center;">
                                	<input type="text" class="initial" id="initial_<?=$num;?>" name="initial_<?=$num;?>" value="<?=$row_orders["Initial"];?>" />
                                </td>
                                <td class="<?=$color;?>" style="text-align: center;"><img class="delorder" style="cursor: pointer; width: 17px;" src="images/delete.png" id="<?=$row_orders["id"];?>" /></td>
                            </tr>
                        <?php
						$num++;
					}
				?>
            </table>
            <input type="hidden" id="total" name="total" value="<?=$num;?>" />
			<script>
				$(".delorder").click(function() {
					var del = confirm('Delete Order?');
					if(del) {
						$.post('includes/inc_orders.php', {"type":"delete", "id":$(this).attr("id")}, 
							function(data) {
								alert(data);
								$("#orderlist").html('<img src="images/loader.gif" />');
								var oid;
								var oname;
								var odate;
				
								if($("#sorderid").val() == 'Search Order ID') {
									oid = '';
								} else {
									oid = $("#sorderid").val();
								}
				
								if($("#sname").val() == 'Search By Customer Name') {
									oname = '';
								} else {
									oname = $("#sname").val();
								}
				
								if($("#sdate").val() == 'Search By Date') {
									odate = '';
								} else {
									odate = $("#sdate").val();
								}
								
								$("#orderlist").load("includes/inc_orders.php", {"type":"orders", 
														 "id":oid, 
														 "name":oname, 
														 "date":odate, 
														 "status":$("#sstatus").val()});
							});
					}
				});
			</script>
		<?php
		mysql_close($conn);
		exit();
	}

	// delete the order
	if($_POST["type"] == "delete") {
		$id = $_POST["id"];
		$sql_del = "DELETE FROM orders WHERE id=$id LIMIT 1";
		if(!mysql_query($sql_del)) {
			$err = "error removing order: ".mysql_error();
		}
		$sql_del = "DELETE FROM orders_address WHERE OrderID=$id LIMIT 1";
		if(!mysql_query($sql_del)) {
			$err = "error removing order information: ".mysql_error();
		}
		$sql_del = "DELETE FROM orders_items WHERE OrderID=$id";
		if(!mysql_query($sql_del)) {
			$err = "error removing items: ".mysql_error();
		}
		
		if($err == '') {
			echo "Order deleted!";
		} else {
			echo "error deleteing order!";
		}
	
		mysql_close($conn);
		exit();
	}
 
// show single order detail screen
if ($_POST["type"] == "details") {
		$id = mysql_real_escape_string($_POST["id"]);
		$sql_order = "SELECT * FROM orders WHERE id=$id LIMIT 1";
		$result_order = mysql_query($sql_order);
		$row_order = mysql_fetch_assoc($result_order);

		$sql_address = "SELECT * FROM orders_address WHERE OrderID=$id LIMIT 1";
		$result_address = mysql_query($sql_address);
		$row_address = mysql_fetch_assoc($result_address);

		$sql_cust = "SELECT * FROM customers WHERE EmailAddress='$row_order[EmailAddress]' LIMIT 1";
		$result_cust = mysql_query($sql_cust);
		$row_cust = mysql_fetch_assoc($result_cust);
		
		$orderDateTime = strtotime($row_order["orderTime"]);
		$reformatedDate = date("m/d/y g:i A", $orderDateTime);
		?>	
			<table cellpadding="5" cellspacing="1" width="980px">
            <tr>
                <td colspan="9" class="headersmain">Order Info</td>
            </tr>
            <tr>
                <td class="headers" style="width: 97px;">Order ID</td>
                <td class="headers" style="width: 150px;">Order Date/Time</td>
                <td class="headers" style="width: 295px;">Customer Name</td>
                <td class="headers" style="width: 140px;">Order Total</td>
                <td class="headers" style="width: 140px;">Discount</td>
                <td class="headers" style="width: 80px;">Tax</td>
                <td class="headers" style="width: 150px;">Shipping</td>
                <td class="headers" style="width: 90px;">GC Payment</td>
                <td class="headers" style="width: 114px;">Grand Total</td>
            </tr>
			<tr>
				<td class="row1" style="text-align: center;"><?=$row_order["id"];?></td>
				<td class="row1" style="text-align: center;"><?=$reformatedDate;?></td>
				<td class="row1" style="text-align: center;"><?=$row_address["BillingFirstName"]." ".$row_address["BillingLastName"];?></td>
				<td class="row1" style="text-align: center;">$<?=number_format($row_order["OrderTotal"],2);?></td>
				<?php 
            	if ($row_order["Discount"] != '') {
            		echo '<td class="row1" style="text-align: center;">$' . number_format($row_order["Discount"],2) . '</td>';
            	}
            ?>
				<td class="row1" style="text-align: center;">$<?=number_format($row_order["Tax"],2);?></td>
				<td class="row1" style="text-align: center;">$<?=number_format($row_order["ShippingTotal"],2);?></td>
				<td class="row1" style="text-align: center;">$<?=number_format($row_order["gcTotal"],2);?></td>
				<td class="row1" style="text-align: center;">$<?=number_format($row_order["GrandTotal"],2);?></td>
			</tr>
			</table>
			<table cellpadding="5" cellspacing="1" width="980px" style="margin-top: 20px;">
			<tr>
				<td colspan="3" class="headersmain" style="border: 0px;">Customer Information</td>
			</tr>
			<tr>
				<td colspan="2" class="subheader">Customer Group</td>
				<td class="subheader">Billing Address</td>
			</tr>
			<tr>
				<td colspan="2" style="padding: 20px; height: 50px; vertical-align: top; font-size: 13px;">
				<?php
					if($row_cust["CustomerGroup"] != '' || $row_cust["CustomerGroup"] != NULL) {
						echo $row_cust["CustomerGroup"];
					} else {
						echo "Customer does not belong to any Group";
					}
					?>
				</td>
				<td style="padding: 20px; background-color: #efefef; height: 50px; vertical-align: top; font-size: 13px;">
					<?=$row_address["BillingAddress"];?><br/>
					<?=$row_address["BillingCity"].", ".$row_address["BillingState"]." ".$row_address["BillingZip"];?>
				</td>	
			</tr>
			<tr>
				<td class="subheader" style="width: 25%;">VIP</td>
				<td class="subheader" style="width: 25%;">Exp Date</td>
				<td class="subheader">Shipping Address</td>
			</tr>
			<tr>
					<td style="padding: 20px; height: 50px;"><?php echo strtoupper($row_cust["VIPNum"]);?></td>
					<td style="padding: 20px; height: 50px;"><?php echo $row_cust["VIPExpDate"]; ?></td>
					<td style="padding: 20px; height: 50px; background-color: #efefef; vertical-align: top; font-size: 13px; line-height: 20px;">
						<?=$row_address["ShippingAddress"];?><br/>
						<?=$row_address["ShippingCity"].", ".$row_address["ShippingState"]." ".$row_address["ShippingZip"];?>
					</td>
				</tr>
			</table>
			<table cellpadding="5" cellspacing="1" width="980px" style="margin-top: 20px;">
				<tr>
					<td colspan="2" class="headersmain" style="border: 0px; background-color: #757575; color: #fff;">Customer Information</td>
				</tr>
				<tr>
					<td class="row1" style="font-weight: bold; height: 20px; width: 50%;">First Name</td>
					<td class="row1" style="width: 50%;"><?=$row_cust["FirstName"];?></td>
				</tr>
				<tr>
					<td class="row2" style="font-weight: bold; width: 50%;">Last Name</td>
					<td class="row2" style="width: 50%;"><?=$row_cust["LastName"];?></td>
				</tr>
				<tr>
					<td class="row1" style="font-weight: bold; width: 50%;">Phone Number</td>
					<td class="row1" style="width: 50%;"><?=$row_cust["Telephone"];?></td>
				</tr>
				<tr>
					<td class="row2" style="font-weight: bold; width: 50%;">Email Address</td>
					<td class="row2" style="width: 50%;"><?=$row_cust["EmailAddress"];?></td>
				</tr>
				<tr>
					<td class="row2" style="font-weight: bold; width: 50%;">Ordered from this IP Address</td>
					<td class="row2" style="width: 50%;"><?=$row_order["ipAddr"];?></td>
				</tr>
			</table>
			
			<table cellpadding="5" cellspacing="1" width="980px" style="margin-top: 20px;">
                		<tr>
                    			<td colspan="5" class="headersmain">Items Order</td>
               			</tr>
                		<tr>
                    			<td class="headers" style="width: 250px;">Product Name</td>
                    			<td class="headers" style="width: 150px;">SKU</td>
                    			<td class="headers" style="width: 100px;">Qty</td>
                    			<td class="headers" style="width: 150px;">Price</td>
                    			<td class="headers" style="width: 150px;">Total</td>
                		</tr>
				<?php
					// display order details information
					$sql_items = "SELECT * FROM orders_items WHERE OrderID=$id";
					$result_items = mysql_query($sql_items);
					$row_num = 1;
					$gc = "no";
					$gcCount = 0;
					while ($row_items=mysql_fetch_array($result_items)) {
						if ($row_num == 1) {
							$color = "row1";
							$row_num++;
						} else {
							$color = "row2";
							$row_num=1;
						}
						
						if ($row_items["Type"] == "GC") {
							$gc = "yes";
							// echo "<h2>gcCount = " . $gcCount . "</h2>"; // testing only
							$gcNum[$gcCount] = $row_items["ProductID"];
							$gcValue[$gcCount] = $row_items["Price"];
							$gcCount++;
						} elseif (!$row_items["BundleID"]) {
						?>
							<tr>
								<td class="<?=$color;?>"><?=$row_items["ProductName"];?></td>
								<td class="<?=$color;?>" style="text-align: center;"><?=$row_items["RootSKU"]."-".$row_items["ColorSKU"]."-".$row_items["SizeSKU"]."-".$row_items["GenderSKU"];?></td>
								<td class="<?=$color;?>" style="text-align: center;"><?=$row_items["Qty"];?></td>
								<td class="<?=$color;?>" style="text-align: center;">$<?=number_format($row_items["Price"],2);?></td>
								<td class="<?=$color;?>" style="text-align: center;">$<?php echo number_format($row_items["Price"]*$row_items["Qty"],2);?></td>
							</tr>
						<?php
						}
						
						if ($row_items["Type"] = "Single") {
						   	$sql_sitems = "SELECT * FROM orders_items WHERE OrderID=$id AND BundleID='".$row_items['id']."' AND Type='Single'";
						   	$result_sitems = mysql_query($sql_sitems);
						   	while($row_sitems = mysql_fetch_array($result_sitems)) {
						   		$row_num = 1;
						?>
						   	<tr>
						   		<td>&nbsp;&nbsp;</td>
						   		<td style="text-align: center;"><?=$row_sitems["RootSKU"]."-".$row_sitems["ColorSKU"]."-".$row_sitems["SizeSKU"]."-".$row_sitems["GenderSKU"];?></td>
						   		<td style="text-align: center;"><?=$row_sitems["Qty"];?></td>
						   		<td></td>
						   		<td></td>
						   	</tr>
						<?php
							} // end Single while
						} // end Single if
						
						if ($row_items["Type"] = "Bundle") { 
							$sql_bitems = "SELECT * FROM orders_items WHERE OrderID=$id AND BundleID='".$row_items['id']."' AND Type='Bundle'";
							$result_bitems = mysql_query($sql_bitems);
							while($row_bitems=mysql_fetch_array($result_bitems)) {
								$row_num = 1;
						?>
							<tr>
								<td>&nbsp;&nbsp;<?=$row_bitems["ProductName"];?></td>
								<td style="text-align: center;">
									<?=$row_bitems["RootSKU"]."-".$row_bitems["ColorSKU"]."-".$row_bitems["SizeSKU"]."-".$row_bitems["GenderSKU"];?>
								</td>
								<td style="text-align: center;"><?=$row_bitems["Qty"];?></td>
								<td style="text-align: center;"></td>
								<td style="text-align: center;"></td>
							</tr>
						<?php
						   } // end Bundle while
						} // end Bundle if
						// DISPLAY IMPRINT INFORMATION ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
							$sql_imp = "SELECT * FROM imprint_orders WHERE OrderNumber=$id AND OrderItemID=$row_items[id]";
							$result_imp = mysql_query($sql_imp);
							$num_imp = mysql_num_rows($result_imp);
							$impPrice = 0;
							if($num_imp > 0) {
								$imprint_data = '<table class="imprintOptions" style="width: 790px;" cellpadding="3" cellspacing="0"><tr><td class="impheader" colspan="3">Imprint Options</td><td class="impheader"></td></tr>';
								while($row_imp = mysql_fetch_array($result_imp)) {
									$impPrice += floatval($row_imp["ImprintPrice"]);
									
									$optName = ucfirst($row_imp["Opt1Type"]);
									if($row_imp["Opt2Type"] != "") {
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
									
									$imprint_data .= '<tr>
															<td class="impLocation">'.$row_imp["Opt1Loc"].'</td>
															<td class="impType">'.$optType1;
									
									if($optTeam != '') {
										$imprint_data .= " ( Team:".$optTeam." )";
									}
									
									if($row_imp["Opt1Text"] != '') {
										$imprint_data .= ':<br/>'.str_replace("|","<br/>",$row_imp["Opt1Text"]).' ';
									}
									
									//////////////////////////////////////
									if($row_imp["Opt2Type"] != '') {
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
									}
									
									if($optTeam != '') {
										$imprint_data .= " ( Team:".$optTeam." )";
									}
									
									////////////////////
									
									if($row_imp["Opt2Text"] != '') {
										$imprint_data .= ':<br/>'.str_replace("|","<br/>",$row_imp["Opt2Text"]).' ';
									}
									
									$imprint_data .= '		</td>
															<td class="impPrice">$'.number_format($row_imp["ImprintPrice"],2).'</td>
															<td class="impImage">';
									if($row_imp["Opt1Image"] != '') {
										$imprint_data .= '<img src="'.str_replace("cpadmin/","",$row_imp["Opt1Image"]).'" alt="'.$row_imp["Opt1Type"].'" title="'.$row_imp["Opt1Loc"]." - ".$row_imp["Opt1Type"].'" />';
									}
									
									if($row_imp["Opt2Image"] != '') {
										$imprint_data .= '&nbsp; <img src="'.str_replace("cpadmin/","",$row_imp["Opt2Image"]).'" alt="'.$row_imp["Opt2Type"].'" title="'.$row_imp["Opt2Loc"]." - ".$row_imp["Opt2Type"].'" />';
									}
									
									$imprint_data .= '		</td>
														</tr>';
								}
								$imprint_data .= '<tr>
													<td class="noBtmBdr"> </td>
													<td class="noBtmBdr right">Imprint Total:</td>
													<td class="noBtmBdr">$'.number_format($impPrice, 2).'</td>
													<td class="noBtmBdr"></td>
													</tr>
												</table>';
								?>
									<tr>
										<td colspan="5">
											<?=$imprint_data;?>
										</td>
									</tr>
								<?php
								//$ordertotal = $ordertotal + $impPrice;
							} else {
								$imprint_data = "";
							}
						// END DISPLAY IMPRINT INFORMATION ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
					}
				?>
			</table>
			<table cellpadding="5" cellspacing="1" width="980px" style="margin-top: 20px;">
				<tr>
					<td colspan="2" class="headersmain" style="border: 0px; background-color: #757575; color: #fff;">Billing Information</td>
				</tr>
				<tr>
					<td class="row1" style="font-weight: bold; height: 20px; width: 50%;">Payment Type</td>
					<td class="row1" style="width: 50%;">
				<?php
					if ($gc == "yes") {
						for ($i = 0; $i < $gcCount; $i++) {
							echo "Gift Certifcate used (#";
							echo $gcNum[$i];
							echo ") - $";
							echo $gcValue[$i];
							echo "<br>";
						}
					} 
					if ($row_order["CardType"] == "OpenAccount") {
						echo "Customer Open Account";
						echo "</td></tr>";
					} else { 
					    echo $row_order["CardType"] . '</td>
				</tr>
				<tr>
					<td class="row1" style="font-weight: bold; width: 50%;">Credit Card Number</td>
					<td class="row1" style="width: 50%;">' . $row_order["CCNum"] . '</td>
				</tr>
				<tr>
					<td class="row2" style="font-weight: bold; width: 50%;">Authorization Code</td>
					<td class="row2" style="width: 50%;">' . $row_order["authCode"] . '</td>
				</tr>
				<tr>
					<td class="row1" style="font-weight: bold; width: 50%;">Transaction ID</td>
					<td class="row1" style="width: 50%;">' . $row_order["transID"] . '</td>
				</tr>';
			} 
		?>
			</table>
			<table cellpadding="5" cellspacing="1" width="980px" style="margin-top: 20px;">
				<tr>
					<td colspan="2" class="headersmain" style="border: 0px; background-color: #757575; color: #fff;">Shipping</td>
				</tr>
				<tr>
					<td class="row1" style="font-weight: bold; height: 20px; width: 50%;">Method</td>
					<td class="row1" style="width: 50%;"><?=$row_order["ShippingMethod"];?></td>
				</tr>
				<tr>
					<td class="row2" style="font-weight: bold; width: 50%;">Shipping Total</td>
					<td class="row2" style="width: 50%;">$<?=number_format($row_order["ShippingTotal"],2);?></td>
				</tr>
				<tr>
					<td class="row1" style="font-weight: bold; width=50%;">Shipping Weight Total <small>(direct &amp; drop)</small></td>
					<td class="row1" style="width: 50%;"><?=$row_order["WeightTotal"];?> lbs</td>
				</tr>
				<tr>
					<td class="row2" style="font-weight: bold; width=50%;">Shipping Notes</td>
					<td class="row2" style="width: 50%;"><?=$row_order["shipnotes"];?></td>
				</tr>
			</table>
            <table cellpadding="5" cellspacing="1" width="980px" style="margin-top: 20px;">
				<tr>
					<td class="headersmain" style="border: 0px; background-color: #757575; color: #fff;">Customer Included Notes</td>
				</tr>
				<tr>
					<td align="center" class="row1" style="font-weight: bold; height: 100px; width: 50%;"><?php echo $row_order["OrderNotes"];?></td>
				</tr>
				
			</table>
		<?php
		mysql_close($conn);
		exit();
	}
?>