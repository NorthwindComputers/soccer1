<?php
/********************************
 * Cart review information page 
 *                                            
 * Updated: 25 August 2016         
 * By: Richard Tuttle           
 *******************************/
 
require_once 'cpadmin/includes/db.php';
session_start();
require_once 'includes/inc_calcVipprice.php';
require_once 'includes/CouponCalculation.php';
$pgTitle = "Shopping Cart | SoccerOne";
include_once("includes/mainHeader.php");
?>
<style>#dialog { display:none; }</style>
<script language="javascript" type="text/javascript">
var items = 0;
$(function() {
	$('form').jqTransform({imgPath:'jqtransformplugin/img/'});
	
	$("#updatecart").click(function() {
		for(var i=0; i<items; i++) {
			$.post("includes/inc_details.php", {
				"type":"updateqty", 
				"qty":$("#qty_" + i).val(), 
				"id":$("#id_" + i).val()
			}, function(data) {
				if (i == items) {
					$.post("includes/inc_details.php", {
						"type":"chkCoupons"
					}, function(data) {
						window.location.reload();
					});
				}
			});
		}
	});
	
	$('#applycoupon').click(function() {
		var code = $("#couponcode").val();
		if(code != '') {
			$.post("includes/CartCoupon.php", {
				"type":"coupon", 
				"code":$("#couponcode").val()
			}, function(data){
				if(data == 'added') {
					window.location="cart.php";
				} else {
					$("#couponerror").html(data);
				}
			});
		}
	});

	$('#applycode').click(function() {
		if($("#customercode").val() == '') {
			alert("Please Enter A Valid Customer Code");
			return;
		}
		$.post("includes/CartCoupon.php", {
			"type":"customercode", 
			"code":$("#customercode").val()
		}, function(data){
			if (data == '') {
				$("#codeerror").html("Customer Code Not Found");
			} else {
				window.location="myaccount.php?p=register&CG=" + $("#customercode").val();
			}
		});
	});
		
	$('#addVIP').click(function() {
		$.post("includes/inc_details.php", {
			"type":"VIP",
			"qty":$("#qty_" + 1).val()
		}, function(data) {
			location.reload();
		});
	});
		
	$('input[id^="qty_"]').change(function() {
		var inum = $(this).attr("name").replace("qty_","");
		$.post("includes/inc_details.php", {
			"type":"chkCartInv", 
			"qty":$(this).val(), 
			"scid":$("#scid_" + inum).val()
		}, function(data) {
			if (data != "true") {
				alert("Available Qty is: " + data.replace("false_",""));
				$("#qty_" + inum).val($("#oqty_" + inum).val());
			}
		});
	});
});
	
function proceedToCheckOut($configure) {
	if ($configure != '0') {
		window.location='checkout.php';
		return true;
	} else {
		alert('Please Configure Your Free Item');
	}
	return false;
}
	
function setDefaultCat1(filter) {
	var id = new String(filter.id);
	var cid = id.substring(id.indexOf(":")+1,id.length);
	var catName = id.substring(0,id.indexOf(":"))+".html";

	$.post("./includes/inc_browser.php", {
		"type":"initCategId", 
		"idCat":cid
	}, function(data) {
		var pathname = new String(window.location.pathname);
		pathname=pathname.substring(0,pathname.lastIndexOf("/")+1);
		window.location.pathname = pathname+catName;
	});
	return false;
}

function removeItem(id) {
	var rid = id;
	// alert("Product #" + rid); // testing use only
	$("#dialog").dialog({
		title: "Removal Confirmation",
		resizable: false,
		height: 200,
		modal: true,
		show: {
			effect: "blind",
			duration: 800
		},
		buttons: {
			"Confirm": function() {
				// alert("callBack function: " + rid); // testing use only
				// callBack(rid);
				$.post("includes/inc_details.php", {
					"type":"remove",
					"id":rid
				});
				$.post("includes/inc_details.php", {
					"type":"chkCoupons"
				}).then(function() {
					window.location.reload();
					$(this).dialog("close");
				});
			},
			Cancel: function() {
				$(this).dialog("close");
			}
		}
	});
}

function callBack(data) {
	$.post("includes/inc_details.php", {"type":"remove","id":data});
	$.post("includes/inc_details.php", {"type":"chkCoupons"});
}
</script>
<?php 
include_once("./cpadmin/imprint/pricing.class.php");
include_once("./cpadmin/imprint/Database.class.php");
include_once("./cpadmin/imprint/cart_imprint_details.class.php");
$_SESSION["cartImprintDetails"] = array();
?>
</head>
<body>
<div class="Master_div"><?php include_once('includes/header.php'); ?>
  <div class="container container1">
    <div class="navigation">
      <div class="navi_L"></div>
      <div class="navi_C"><?php include_once('includes/topnav.php'); ?>
        <div class="clear"></div>
      </div>
      <div class="navi_R"></div>
      <div class="clear"></div>
    </div>
	<div class="clear"></div>
	<div class="main">
    <?php
		$isvip = "no";
		$isConFiguredFreeItem = 1;

		// check if VIP status is active and not expired
		if (isset($_SESSION["email"]) && $_SESSION["email"] != '') {
			$sql_status = "SELECT Status FROM customers WHERE Status='VIP' AND EmailAddress='".$_SESSION['email']."' AND VIPExpDate >= current_date()";
			$result_status = mysql_query($sql_status) or die("VIP Info Error: " . mysql_error());
			$num_status = mysql_num_rows($result_status);
			if ($num_status > 0) {
				// if customer is VIP already delete the VIP item, if there, from the cart
				$isvip = "yes";
				$sql_remvip = "DELETE FROM shopping_cart WHERE ProductID='VIP' AND (EmailAddress='".$_SESSION['email']."' OR SessionID='".session_id()."')";
				mysql_query($sql_remvip) or die("VIP Deletion Error: " . mysql_error());
			} else {
				$sql_chkcart = "SELECT id FROM shopping_cart WHERE ProductID='VIP' AND EmailAddress='".$_SESSION['email']."'";
				$result_chkcart = mysql_query($sql_chkcart) or die(mysql_error());
				$num_chkcart = mysql_num_rows($result_chkcart);
				if ($num_chkcart > 0) {
					$isvip = "yes";
					$vprod = TRUE;
					$vprodPrice = mysql_result(mysql_query("SELECT Price FROM shopping_cart WHERE Type='VIP'"), 0);
					// echo "vprod: " . $vprod . " / $" . $vprodPrice . "<br />"; // testing use only
				}
			}
		}

		// if customer not a VIP then look in cart to see if VIP product was added
		if ($isvip == "no") {
			$sql_chkcart = "SELECT * FROM shopping_cart WHERE ProductID='VIP' AND SessionID='".session_id()."'";
			$result_chkcart = mysql_query($sql_chkcart) or die("Check Cart Error: " . mysql_error());
			$num_chkcart = mysql_num_rows($result_chkcart);
			if ($num_chkcart > 0) {
				$isvip = "yes";
				$vprod = TRUE;
				$vprodPrice = mysql_result(mysql_query("SELECT Price FROM shopping_cart WHERE Type='VIP'"), 0);
				// echo "vprod: " . $vprod . " / $" . $vprodPrice . "<br />"; // testing use only
			}
			
			// also check for special Gold certificate code in cart
			$couponCk = "SELECT * FROM shopping_cart WHERE Type='Cert' LIMIT 1";
			$result_couponCk = mysql_query($couponCk) or die("Certificate check error: " . mysql_error());
			$num_couponCk = mysql_num_rows($result_couponCk);
			if ($num_couponCk > 0) {
				$isvip = "yes";
				$vprod = TRUE;
				$vprodPrice = mysql_result(mysql_query("SELECT Price FROM shopping_cart WHERE Type='VIP'"), 0);
			}
		}

		// cart clean-up
		$today = date("Y-m-d");
		$sql_delold = "DELETE FROM shopping_cart WHERE EmailAddress='' AND CreatedDate < $today";
		mysql_query($sql_delold) or die("Cart deletion error: " . mysql_error());
		$sql_delimp = "DELETE FROM imprint_shopping_cart WHERE EmailAddress='' AND CreatedDate < $today";
		mysql_query($sql_delimp) or die("Imprint deletion error: " . mysql_error());
		$sql_delsingle = "DELETE FROM shopping_cart_single WHERE EmailAddress='' AND CreatedDate < $today";
		mysql_query($sql_delsingle) or die("Single deletion error: " . mysql_error());
	?>
	<table border="0" align="center" cellpadding="3" cellspacing="0" class="cartDisplay">
	<tr>
		<td style="background-color:#FF0000;color:#fff;border:none;height:35px;width:100px">&nbsp;</td>
		<td style="width:200px;background-color:#FF0000;color:#fff;border:none;height:35px;font-size:0.875em">Product Name</td>
		<td style="width:150px;background-color:#FF0000;color:#fff;border:none;height:35px;font-size:0.875em">SKU</td>
		<td style="background-color:#FF0000;color:#fff;border:none;height:35px;font-size:0.875em"><?php if ($isvip == "yes") echo 'Non-member Price'; else echo 'Non-member Price'; ?></td>
        <?php if ($isvip == "yes"): ?>
		<td style="background-color:#FF0000;color:#fff;border:none;height:35px;font-size:0.875em">VIP Price</td>
        <?php endif; ?>
		<td style="background-color:#FF0000;color:#fff;border:none;height:35px;font-size:0.875em">QTY</td>
		<td style="background-color:#FF0000;color:#fff;border:none;height:35px;font-size:0.875em">Subtotal</td>
		<td style="background-color:#FF0000;color:#fff;border:none;height:35px">&nbsp;</td>
	</tr>
	<?php
	// check for customer being logged in ... 
	if ($_SESSION["email"] == '') {
		$sqlwhere = "SessionID='".session_id()."'";
	} else {
		$sqlwhere = "(EmailAddress='".$_SESSION['email']."' OR SessionID='".session_id()."') ";
	}

	$sql_cart = "SELECT * FROM shopping_cart WHERE ".$sqlwhere." AND (`Type`='Product' or `Type`='Bundle') AND (BundleID = '' OR BundleID IS NULL) AND (singleid = '' OR singleid IS NULL) ORDER BY id DESC";
	$couponCalc = new CouponCalculation(0, 0, $isvip);
	$num = 0;
	$ordertotal = 0;
	$availablediscount = 0;
	$taxableamount = 0;
	$becomevip = 0;
	$orderTotal_price = 0;
	$result_cart = mysql_query($sql_cart) or die("Cart Error: " . mysql_error());
	$num_cart = mysql_num_rows($result_cart);
	$freeitemHtml = '';
	$freeitemSku = '';
	$customVipPrice = 0;
	$orderTotalWithoutSpePrice = 0;

	// if items are in shopping cart
	if ($num_cart > 0) {
		while ($row_cart = mysql_fetch_array($result_cart)) {
			// getting product shipping information 
			$sql_shiptype = "SELECT * FROM product_shipping WHERE ProductID=".$row_cart['ProductID']." LIMIT 1";
			$result_shiptype = mysql_query($sql_shiptype) or die("ShipType Error: " . mysql_error());
			$row_shiptype = mysql_fetch_assoc($result_shiptype);
			if ($row_shiptype["ShippingType"] == "PrimaryLocation" && $row_shiptype["UPS"] == "SpecificShipping") {
				$shipmess = "This item ships: " . $row_shiptype["SpecificOption"];
			} else {
				$shipmess = '';
			}
			if ($row_cart["ColorSKU"] == '') {
				$imgColorSKU = "IS NULL ";
			} else {
				$imgColorSKU = "= '".$row_cart['ColorSKU']."' ";
			}
			if ($row_cart["SizeSKU"] == '') {
				$imgSizeSKU = "IS NULL ";
			} else {
				$imgSizeSKU = "= '".$row_cart['SizeSKU']."' ";
			}
			$sql_image = "SELECT ColorImage FROM product_options WHERE ProductID=".$row_cart['ProductID']." AND ColorSKU ".$imgColorSKU." LIMIT 1";
			$result_image = mysql_query($sql_image) or die("Image Error: " . mysql_error());
			$row_image = mysql_fetch_assoc($result_image);
			
			// set pricing column to use
			if ($isvip == "yes") {
				$pricename = "VIPPrice";
			} else {
				$pricename = "Price";
			}	
			$sql_skuorder = "SELECT SKUOrder FROM products WHERE id=$row_cart[ProductID] LIMIT 1";
			$result_skuorder = mysql_query($sql_skuorder) or die ("SKUerror: " . mysql_error());
			$row_skuorder = mysql_fetch_assoc($result_skuorder);
			$skuorder = explode("|", $row_skuorder["SKUOrder"]);
			$prodsku = $row_cart[$skuorder[0]."SKU"];		
			if (isset($row_cart[$skuorder[1]."SKU"]) && $row_cart[$skuorder[1]."SKU"] != '')
				$prodsku .= "-".$row_cart[$skuorder[1]."SKU"];
						
			if (isset($row_cart[$skuorder[2]."SKU"]) && $row_cart[$skuorder[2]."SKU"] != '')
				$prodsku .= "-".$row_cart[$skuorder[2]."SKU"];

			if ($row_cart["GenderSKU"] != '') {
				$prodsku .= "-".$row_cart["GenderSKU"];
			}
			$prodsku = str_replace("--", "-", $prodsku);
			$isConfirmProduct = false;
			?>
			<tr>
				<td class="cartitem"><a href="details.php?id=<?php echo $row_cart["ProductID"]; ?>">
         <?php 
            $image_src = $row_image["ColorImage"];
			if ($image_src == "") {
				$pp_id = $row_cart['ProductID'];
				$sql_mainimg = "SELECT p.ColorImage, p.AltText FROM product_options p, product_browser b WHERE p.ProductID=b.ProductID AND p.ColorImage=b.Image AND p.ProductID=$pp_id LIMIT 1";
			    $result_mainimg = mysql_query($sql_mainimg) or die("MainImg Error: " . mysql_error());
			    $row_mainimg = mysql_fetch_assoc($result_mainimg);
			    $num_mainimg = mysql_num_rows($result_mainimg);
				$image_src = $row_mainimg["ColorImage"];
			}
		?>
				<img class="cartthumb" src="images/productImages/<?php echo $image_src; ?>" /></a> 
				<input type="hidden" id="id_<?php echo $num; ?>" name="id_<?php echo $num; ?>" value="<?php echo $row_cart["id"]; ?>" /></td>
		<?php 
			if (!$isConfirmProduct) { 
		?>
				<td class="cartitem">
		<?php					
				$productCalcPrice = getVipPrice($row_cart["GenderSKU"], $row_cart['ColorSKU'], $row_cart['SizeSKU'], $row_cart['ProductID'], $row_cart["Qty"]);
				$sql_ProductDetails = "SELECT * FROM products WHERE id ='".$row_cart["ProductID"]."'";
				$result_ProductDetails = mysql_query($sql_ProductDetails) or die("VIP Price Retreval Error: " . mysql_error());
				$row_ProductDetails = @mysql_fetch_array($result_ProductDetails);
				$ProductDetailName = $row_ProductDetails['ProductDetailName'];		
				if ($isvip == "yes") {
					if ($row_cart['Price'] == 0) {
						$customVipPrice = 0;
					} else {
						$customVipPrice = $productCalcPrice['VIPPrice'];
					}
					$NoneMemberPrice = $productCalcPrice['NoneMemberPrice'];
					// $customVipPrice = $row_cart[$pricename]; /***************/
				} else {
					if ($row_cart['Price'] == 0) {
						$customVipPrice = 0;
					} else {
						$NoneMemberPrice = $productCalcPrice['NoneMemberPrice'];
						$becomevipitem = $row_cart["Qty"] * $row_cart["VIPPrice"]; // $productCalcPrice["VIPPrice"];
						$becomevip = $becomevip + $becomevipitem;
					}
				}
				
				
				// is the item on sale?				
				$sql_special_price = "SELECT SpecialPrice, isSpecial FROM products WHERE id =".$row_cart["ProductID"]." AND ((DATE_FORMAT(SpecialFrom, '%Y-%m-%d') <= DATE_FORMAT(current_date, '%Y-%m-%d')  OR SpecialFrom='') AND (DATE_FORMAT(current_date, '%Y-%m-%d') <= DATE_FORMAT(SpecialTo, '%Y-%m-%d') OR SpecialTo='')) AND isSpecial!='' LIMIT 1";
				$result_special_price = mysql_query($sql_special_price) or die("SpecialError: " . mysql_error());
		  		$row_special_price = mysql_fetch_array($result_special_price);
	    		$specialPrice = number_format($row_special_price['SpecialPrice'], 2);
	    		if ($row_special_price['isSpecial'] == "True") {
	    			// echo "** SP: " . $specialPrice . "<br />"; // testing use only
					if ($isvip != "yes") {
						if ($row_cart['Price'] == 0) {
							$NoneMemberPrice = 0;
						} else {
							$NoneMemberPrice = $specialPrice;
						}
					}
					if ($row_cart['Price'] == 0) {
						$customVipPrice = 0;
					} else {
						$customVipPrice = $specialPrice;
					}
					$row_cart[$pricename] = $specialPrice;
					$orderTotalWithoutSpePrice += $row_cart["Qty"] * $specialPrice;
				} else {
					$availablediscount = $availablediscount + $total;
					$orderTotalWithoutSpePrice += $row_cart["Qty"] * $row_cart[$pricename];
				}	
				
				// echo "** CP: " . $customVipPrice . "<br />"; // testing use only
				
				// update pricing in database to match pricing model
				if ($isvip == "yes") {
					if ($row_cart["Price"] != 0) {
						$updateShoppingCartVip = "UPDATE shopping_cart SET Price='".$NoneMemberPrice."', VIPPrice='".$customVipPrice."' WHERE id=".$row_cart["id"];
						mysql_query($updateShoppingCartVip);
					}
				}
			?>						
                <a href="details.php?id=<?php echo $row_cart["ProductID"];?>"><?php echo $ProductDetailName;?></a><br />
				<?php if($shipmess != ''): ?>
                <br/><span style="font-size: 11px;"><!-- shipping message --><?php echo $shipmess; ?></span>
                <?php endif; ?></td>
				<td class="cartitem"><!-- SKU --><?php echo $prodsku;?></td>
				<td class="cartitem"><!-- Non-member Price -->
				<?php  						
					if ($isvip == "yes") {
						echo "<s style=\"color: #909090;\">$".number_format($NoneMemberPrice, 2)."</s>";
					} else {
						echo "$".number_format($NoneMemberPrice, 2);
					}

					// Imprint 
					$sql_imp = "SELECT * FROM imprint_shopping_cart WHERE $sqlwhere AND CartID='$row_cart[id]'";
					$result_imp = mysql_query($sql_imp);
					$num_imp = mysql_num_rows($result_imp);
					$impPrice = 0;
							
					if($num_imp > 0) {
						$imprint_data = '<table class="imprintOptions" cellpadding="3" cellspacing="0"><tr><td class="impheader" colspan="3">Imprint Options</td><td class="impheader"><a href="editimprint.php?cid='.$row_cart[id].'">Edit Imprint Options</a></td></tr>';
						while($row_imp = mysql_fetch_array($result_imp)) {
							$impPrice += floatval($row_imp["ImprintPrice"]);									
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
							if($optTeam != '') {
								$imprint_data .= " (Team: ".$optTeam.")";
							}
							$imprint_data .= " (Color: ".$row_imp["Opt1Color"].")";									
							if($row_imp["Opt1Text"] != '') {
								$imprint_data .= ':<br/>'.str_replace("|","<br/>",$row_imp["Opt1Text"]).' ';
							}
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
								if($optTeam != '') {
									$imprint_data .= " (Team: ".$optTeam.")";
								}
								$imprint_data .= " (Color: ".$row_imp["Opt2Color"].") ";
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
						$table_data = $imprint_data;
					} else {
						$table_data = "";
					}
					// End Imprint 
					
						/*********************/
					if ($isvip == "yes") {
						if ($row_cart['Price'] = "0") {
							$customVipPrice = 0;
						}
						$totalPriceforqty = ($row_cart["Qty"] * $customVipPrice);
					} else {
						$totalPriceforqty = ($row_cart["Qty"] * $NoneMemberPrice);
					}
					$orderTotal_price += $totalPriceforqty + $impPrice;
				?>
                <input type="hidden" id="price_<?php echo $num; ?>" value="<?php echo $NoneMemberPrice;?>"/>
                <input type="hidden" id="vipprice_<?php echo $num; ?>" value="<?php echo $customVipPrice;?>"/></td>
				<?php 
					// update pricing in database to match pricing model
					if ($isvip == "yes") {
						if ($row_cart["Price"] != 0) {
							$updateShoppingCartVip = "UPDATE shopping_cart SET Price='".$NoneMemberPrice."', VIPPrice='".$customVipPrice."' WHERE id=".$row_cart["id"];
							// echo "SQL: " . $updateShoppingCartVip . "<br />"; exit; // testing use only
							mysql_query($updateShoppingCartVip);
						}
				?>
				<td class="cartitem"><!-- VIP Price --><span id="vipprice_<?php echo $num; ?>" style="font-weight:bold; color: #ff0000;">$<?php echo number_format($customVipPrice, 2);?></span></td>
				<?php 
					} 
				?>
				<td class="cartitem"><input type="hidden" name="scid_<?=$num;?>" id="scid_<?=$num;?>" value="<?=$row_cart["id"];?>" /><input type="hidden" name="oqty_<?=$num;?>" id="oqty_<?=$num;?>" value="<?=$row_cart["Qty"];?>" /><input type="text" class="address" style="width: 50px; text-align: center;" <?php if ($row_cart["Type"] == "Bundle") { echo ' readonly="readonly"'; } ?> id="qty_<?php echo $num;?>" name="qty_<?php echo $num;?>" value="<?php echo $row_cart["Qty"];?>" /></td>
				<td class="cartitem"><span id="total_<?php echo $num;?>">$<?php echo number_format($totalPriceforqty, 2);?></span></td>
				<td class="cartitem"><img class="cartremove" src="images/del_icon.png" onClick="removeItem('<?php echo $row_cart["id"];?>');" /></td>
			</tr>
			<?php 
				} 
				
				if ($row_cart["Type"] == "Bundle") {
					$sql_bitems = "SELECT * FROM shopping_cart WHERE ".$sqlwhere." AND BundleID=".$row_cart['id']." ORDER BY ProductName";
					$result_bitems = mysql_query($sql_bitems);
					while($row_bitems=mysql_fetch_array($result_bitems)) {
						$sql_bimage = "SELECT ColorImage FROM product_options WHERE ProductID=".$row_bitems['ProductID']." AND ColorSKU='".$row_bitems['ColorSKU']."' AND SizeSKU='".$row_bitems['SizeSKU']."' LIMIT 1";
						$result_bimage = mysql_query($sql_bimage);
						$row_bimage = mysql_fetch_assoc($result_bimage);
						$ColorSKU = $row_bitems["ColorSKU"];
						$SizeSKU = $row_bitems["SizeSKU"];
						$sql_SizeSKU = "SELECT Size FROM sizes WHERE SKU='".$SizeSKU."' ";
						$result_SizeSKU = mysql_query($sql_SizeSKU);
						$row_SizeSKU = mysql_fetch_assoc($result_SizeSKU);
						$SizeSKU = $row_SizeSKU["Size"];
						$sql_ColorSKU= "SELECT Color FROM product_options WHERE ColorSKU='".$ColorSKU."' ";
						$result_ColorSKU= mysql_query($sql_ColorSKU);
						$row_ColorSKU= mysql_fetch_assoc($result_ColorSKU);
						$ColorSKU=$row_ColorSKU["Color"];
				?>
            <tr>
                <td class="cartitem"></td>
                <td class="cartitem"><img class="cartthumb" style="width: 27px; float: left;" src="images/productImages/<?php echo $row_bimage["ColorImage"];?>" />
				<p style="margin-top:8px">&nbsp;<?php echo $row_bitems["ProductName"];?></p></td>
                <td colspan="2" style="text-align:left;vertical-align:center;"  class="cartitem">
				<?php 
					echo $row_bitems["RootSKU"]."-".$row_bitems["ColorSKU"]."-".$row_bitems["SizeSKU"]." x ".$row_bitems["Qty"];
				?></td>
                <?php 
                	if($isvip == "yes"): 
                ?>
				<td class="cartitem"></td>
				<?php endif; ?>
                <td class="cartitem"></td>
                <td class="cartitem"></td>
                <td class="cartitem"></td>
            </tr>
			<?php
				}
			} else {
				$sql_bitems = "SELECT * FROM shopping_cart_single WHERE ".$sqlwhere." AND singleid=".$row_cart['id']." ORDER BY ProductName";
				$result_bitems = mysql_query($sql_bitems) or die(mysql_error());
				$total_rows = mysql_num_rows($result_bitems);

				while($row_bitems=mysql_fetch_array($result_bitems)) {
					$sql_bimage = "SELECT ColorImage FROM product_options WHERE ProductID=".$row_bitems['ProductID']." AND ColorSKU='".$row_bitems['ColorSKU']."' AND SizeSKU='".$row_bitems['SizeSKU']."' LIMIT 1";
					$result_bimage = mysql_query($sql_bimage);
					$row_bimage = mysql_fetch_assoc($result_bimage);
					$ColorSKU = $row_bitems['ColorSKU'];
					$SizeSKU = $row_bitems["SizeSKU"];
					$sql_SizeSKU = "SELECT Size FROM sizes WHERE SKU='".$SizeSKU."' ";
					$result_SizeSKU = mysql_query($sql_SizeSKU);
					$row_SizeSKU = mysql_fetch_assoc($result_SizeSKU);
					$SizeSKU = $row_SizeSKU["Size"];
					$sql_ColorSKU= "SELECT Color FROM product_options WHERE ColorSKU='".$ColorSKU."' ";
					$result_ColorSKU= mysql_query($sql_ColorSKU);
					$row_ColorSKU= mysql_fetch_assoc($result_ColorSKU);
					$ColorSKU=$row_ColorSKU["Color"];
				?>
				<!-- single bundle items listing -->
            <tr>
                <td class="cartitem">&nbsp;</td>
                <td class="cartitem"><!-- product subimg --><img class="cartthumb" style="width: 27px; float: left;" src="images/productImages/<?php echo $row_bimage["ColorImage"];?>" /><p style="margin-top:8px">&nbsp;<?php echo $row_bitems["ProductName"];?></p></td>
                <td colspan="2" style="text-align:left;vertical-align:center;" class="cartitem"><!-- SKU --><?php echo $row_bitems["RootSKU"]."-".$row_bitems["ColorSKU"]."-".$row_bitems["SizeSKU"]." x ".$row_bitems["Qty"];?></td>
            <?php if($isvip == "yes"): ?>
				<td class="cartitem"></td>
			<?php endif; ?>
                <td class="cartitem"></td>
                <td class="cartitem"></td>
                <td class="cartitem"></td>
            </tr>
			<?php
				}
			}	

			$freeitemHtml = $couponCalc->getSkuFreeItem(true, '', $row_cart['RootSKU'], $num, $row_cart["Qty"]);
			foreach($freeitemHtml as $html) { 
                if ($isConFiguredFreeItem == 1) {
					$isConFiguredFreeItem = $html[1]==true ? 1 : 0;
				}
				echo $html[0]; 
			}
			$num++;
			?>
			<!-- ============================Imprint Option=============================================-->
			<?php if ($num_imp>0): ?>
			<tr><td colspan="8"></td></tr>		
			<?php endif; ?>
			<?php
				if($isvip == "yes") {
					$cols = "8";
				} else {
					$cols = "7";
				}
			?>
			<?php if ($num_imp > 0): ?>
			<tr> 
				<td colspan="8" class="cartitem"><?php echo $table_data;?></td>
			</tr>	
			<tr> 
				<td colspan="<?=$cols;?>" class="cartitem" style="border:none"><div style='background: none repeat scroll 0% 0% black; width: 100%; height: 4px;'></div></td>
			</tr>	
			<?php endif; ?>
			<tr> 
				<td colspan="<?=$cols;?>" class="cartitem" style="border:none"></td>
			</tr>	
			<?php		
		} // end while loop
		
		// discounts
		$_SESSION['coupon_order_total'] = $orderTotalWithoutSpePrice;
		$couponCalc = new CouponCalculation(0, $orderTotalWithoutSpePrice, $isvip);
		$couponInfo = $couponCalc->getCoupon();
		$discount = $couponCalc->_totalDiscount;
		$_SESSION['discount'] = $discount;
		
		foreach ($couponInfo as $coupons) { 
			/* 
			if ($isConFiguredFreeItem == 1) {
				$isConFiguredFreeItem = $coupons[1] == true ? 1 : 0;
			}
			*/
			echo $coupons[0]; 
		}
		
		// add Certificate info to the Cart
		$sql_cp = "SELECT * FROM shopping_cart WHERE ".$sqlwhere." AND `Type`='Cert'";
		$result_cp = mysql_query($sql_cp);
		while($row_cp = mysql_fetch_array($result_cp)) {
		?>
		<tr>
            <td class="cartitem">&nbsp;</td>
			<td class="cartitem"><?php echo $row_cp["ProductName"];?></td>
			<td class="cartitem"><?php echo $row_cp["ProductID"];?></td>
			<td class="cartitem">-</td>
		<?php if ($isvip == "yes") : ?>
			<td class="cartitem">-</td>
		<?php endif; ?>
			<td class="cartitem">1</td>
			<td class="cartitem">$<?php echo number_format($row_cp["Price"], 2); ?></td>
			<td class="cartitem"><img class="cartremove" src="images/del_icon.png" onClick="removeItem('<?php echo $row_cp["id"]; ?>');" /></td>
        </tr>
		<?php
		}
		
		// add VIP membership info to the Cart
		$sql_vip = "SELECT * FROM shopping_cart WHERE ".$sqlwhere." AND `Type`='VIP'";
		$result_vip = mysql_query($sql_vip);
		while($row_vip = mysql_fetch_array($result_vip)) {
			$sql_vipd = "SELECT Image FROM vip LIMIT1";
			$result_vipd = mysql_query($sql_vipd);
			$row_vipd = mysql_fetch_assoc($result_vipd);
	?>
		<tr>
            <td class="cartitem"><img class="cartthumb" src="images/productImages/<?php echo $row_vipd["Image"];?>" /></td>
			<td class="cartitem"><?php echo $row_vip["ProductName"];?></td>
			<td class="cartitem"><?php echo $row_vip["ProductID"];?></td>
			<td class="cartitem">-</td>
		<?php if ($isvip == "yes") : ?>
			<td class="cartitem">-</td>
		<?php endif; ?>
			<td class="cartitem">1</td>
			<td class="cartitem">$<?php echo number_format($row_vip["Price"], 2); ?></td>
			<td class="cartitem"><img class="cartremove" src="images/del_icon.png" onClick="removeItem('<?php echo $row_vip["id"]; ?>');" /></td>
        </tr>
        <?php
        	if ($vprod == TRUE) {
        		$ordertotal = $ordertotal + $vprodPrice;
        	}
			$ordertotal .= $ordertotal + $row_vip["Price"];
			$num++;
		}
					
		$grandtotal = $ordertotal + $discount;
		if ($grandtotal < 0) {
			$grandtotal = 0;
		}
	} else {
		$sql_vip = "SELECT * FROM shopping_cart WHERE ".$sqlwhere." AND `Type`='VIP'";
		$result_vip = mysql_query($sql_vip);
		$vipnum = mysql_num_rows($result_vip);
		if ($vipnum > 0) {
			while($row_vip = mysql_fetch_array($result_vip)) {
				$sql_vipd = "SELECT Image FROM vip LIMIT1";
				$result_vipd = mysql_query($sql_vipd);
				$row_vipd = mysql_fetch_assoc($result_vipd);
		?>
		<tr>
			<td class="cartitem"><img class="cartthumb" src="images/productImages/<?php echo $row_vipd["Image"];?>" /></td>
			<td class="cartitem"><?php echo $row_vip["ProductName"];?></td>
			<td class="cartitem"><?php echo $row_vip["ProductID"];?></td>
			<td class="cartitem">-</td>
			<?php if ($isvip=="yes"): ?>
			<td class="cartitem">-</td>
			<?php endif; ?>
			<td class="cartitem">1</td>
			<td class="cartitem">$<?php echo number_format($row_vip["Price"], 2);?></td>
			<td class="cartitem"><img class="cartremove" src="images/del_icon.png" onClick="removeItem('<?php echo $row_vip["id"];?>');" /></td>
		</tr>
		<?php
			$ordertotal = $ordertotal + $row_vip["Price"];
			$num++;
			$grandtotal = $ordertotal + $discount;
			if ($grandtotal < 0) {
				$grandtotal = 0;
			}
		}
	} else {
		if ($isvip == "yes") {
			$cols = "8";
		} else {
			$cols = "7";
		}
		if ($_SESSION["email"] == '') {
			$sqlwhere = "SessionID='".session_id()."'";
		} else {
			$sqlwhere = "(EmailAddress='".$_SESSION['email']."' OR SessionID='".session_id()."') ";
		}
		$delCouponCom = "DELETE FROM shopping_cart WHERE SessionID='".session_id()."' AND `Type`='Coupon'";
		mysql_query($delCouponCom) or die("Coupon Error: " . mysql_error());
		?>
		<tr>
			<td colspan="<?php echo $cols;?>" class="cartitem" style="width: 100%">No Items in your cart.</td>
		</tr>
		<?php
		}
	}
		?>
		</table>
		<script>items=<?php echo $num;?>;</script>
		<input type="hidden" id="vipstatus" value="<?php echo $isvip;?>" />
		<div class="Row">
		<!-- Related procucts box -->
		<div class="Column" id="relatedBox">
        	<div id="relatedHeader">Based on your selection, you may be interested in the following items:</div>
			<?php
			$sql_upsales = "SELECT DISTINCT p.* FROM shopping_cart s, product_related r, products p WHERE ".$sqlwhere." AND s.`Type`='Product' AND s.ProductID=r.ProductID AND r.RelatedID=p.id AND s.id=(SELECT max(id) FROM  shopping_cart  WHERE ".$sqlwhere.") LIMIT 3";
			$result_upsales = mysql_query($sql_upsales) or die("Upsales Error: " . mysql_error());
			while ($row_upsales = mysql_fetch_array($result_upsales)) {
				$sql_upimg = "SELECT DISTINCT ColorImage FROM product_options WHERE ProductID=".$row_upsales['id']." AND ColorImage != '' LIMIT 1";
				$result_upimg = mysql_query($sql_upimg);
				$row_upimg = mysql_fetch_assoc($result_upimg);
			?>
			<div id="relatedItem"><img src="images/productImages/<?php echo $row_upimg["ColorImage"];?>" class="riImg" onClick="window.location='details.php?id=<?php echo $row_upsales["id"];?>';" /><span style="font-size: 12px; color:#555050; font-weight: bold;"><?php echo $row_upsales["ProductDetailName"];?></span><br/><span style="font-size: 12px; color:#ff0000; font-weight: bold;">$<?php echo $row_upsales["NoneMemberPrice"];?></span><br/><input type="button" class="shoppingcart" style="width: 120px; float: right;" value="View Details" onClick="window.location='details.php?id=<?php echo $row_upsales["id"];?>';" /></div>
		<?php } ?>
		</div>
		<?php 
		// is the customer a VIP member?
		// if not, offer them how much they will save by becoming one
		if ($isvip == "no") { 
		?>
    		<!-- VIP discount box -->
    		<div class="Column" id="vipBox">
    		<img class="vipBoxImg" src="images/S_soccer_card.png"><br/>Become a member and save:<br/>
			<?php 
			// VIP savings box figures
			setlocale(LC_MONETARY, 'en_US');
			$savings = $orderTotal_price - $becomevip;
			if ($savings <= 0) {
				echo "Sorry, no Savings";
			} else {
				echo money_format('%n', number_format($savings, 2)); 
			}
			?>
        	<br/>
        	<img id="addVIP" name="addVIP" src="images/add_to_cart.png" style="cursor: pointer; margin-top: 5px;" />
        	<div id="divAddVIP" style="display: none;"></div>
        	</div>
		<?php } ?>
		<!-- Totals Box -->
		<div class="Column" id="totalsBox">
		<script type="text/javascript">function continueShop() {window.history.back();}</script>
		<input class="shoppingcart" type="button" id="updatecart" name="updatecart" value="Update Shopping Cart" /> <input class="shoppingcart" type="button" id="continue" name="continue" value="Continue Shopping" onClick="return continueShop()" />
		<table cellpadding="5" cellspacing="2">
            	<tr>
                	<td class="totals" style="text-align: right;">Order Total:</td>
                <?php $_SESSION["orderTotal"] = $ordertotal; ?>
                <?php
                	if ($vprod == TRUE) {
                		$orderTotal_price += $vprodPrice;
                	}
                ?>
                	<td class="totals"><span id="ordertotal">$<?php echo number_format($orderTotal_price, 2);?></span></td>
            	</tr>
            	<tr>
                	<td class="totals" style="text-align: right;">Discount:<input type="hidden" id="discount" name="discount" value="<?php echo $discount;?>" /></td>
                	<td class="totals"><span id="totaldiscount">
			<?php 
					// display Discount amount
					setlocale(LC_MONETARY, 'en_US');
					echo money_format('%n', number_format($discount, 2));
			?></span></td>
            	</tr>
            	<tr>
			<?php 
				// display and set Grand Total amount
				$grandtotal = $orderTotal_price + $discount;
			?>
                	<td class="totals" style="text-align: right;">Grand Total:</td>
                	<td class="totals"><span id="grandtotal">$<?php echo number_format($grandtotal, 2);?></span></td>
            </tr>
        </table>
        <input type="submit" class="proceed" id="proceed" name="proceed" value="" onClick="return proceedToCheckOut('<?php echo $isConFiguredFreeItem; ?>')" />
		</div><!-- end totals -->
		</div>
        <div class="clear"></div>
		<!-- Coupon Code box -->
		<div id="cpnBox">
		<p class="coupon">Enter your Promotion Code:</p>
		<input type="text" class="couponcode" id="couponcode" name="couponcode" /><input type="button" class="applycoupon" id="applycoupon" name="applycoupon" value="" />
		<span id="couponerror" style="color: #ff0000; font-weight: bold;"><?php if(isset($_SESSION["message"])) echo $_SESSION["message"]; unset($_SESSION["message"]);?></span>
		</div><!-- end cpn -->
		<?php // print_r($_SESSION); ?>
	</div>    
    <div class="clear"></div>
</div>
<!-- Container Div ends here --> 
<!-- Footer Starts from here -->
<div class="footer">
	<div class="foot_box"><?php include_once("includes/footer.php"); ?></div>
  </div>
<!-- Footer Div ends here --> 
</div>
<div id="dialog"><p id="dcontent">Please confirm you wish to remove this item from your cart.</p></div>
</body>
</html>
<?php 
// close the database connection
mysql_close($conn); 
?>