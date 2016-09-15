<?php
/**
 * main shopping cart include file
 *
 * Version: 1.4.1
 * Updated: 05 July 2013
 * By: Ricahrd Tuttle
 */
 
session_start();
require_once '../cpadmin/includes/db.php';

// check for VIP status
function isVip() {
	$isvip = "no";
	if ($_SESSION["email"] != '') {
		$sql_status = "SELECT Status FROM customers WHERE Status='VIP' AND EmailAddress='$_SESSION[email]' AND VIPExpDate >= current_date()";
		$result_status = mysql_query($sql_status) or die(mysql_error());
		$num_status = mysql_num_rows($result_status);
		if ($num_status > 0) {
			$isvip = "yes";
			$sql_remvip = "DELETE FROM shopping_cart WHERE ProductID='VIP' AND (EmailAddress='$_SESSION[email]' OR SessionID='".session_id()."')";
			mysql_query($sql_remvip) or die(mysql_error());
		} else {
			$sql_chkcart = "SELECT id FROM shopping_cart WHERE ProductID='VIP' AND EmailAddress='$_SESSION[email]'";
			$result_chkcart = mysql_query($sql_chkcart) or die(mysql_error());
			$num_chkcart = mysql_num_rows($result_chkcart);
			if ($num_chkcart > 0) {
				$isvip = "yes";
			}
		}
	}
	
	if ($isvip == "no") {
		$sql_chkcart = "SELECT * FROM shopping_cart WHERE ProductID='VIP' AND SessionID='".session_id()."'";
		$result_chkcart = mysql_query($sql_chkcart) or die(mysql_error());
		$num_chkcart = mysql_num_rows($result_chkcart);
		if ($num_chkcart > 0) {
			$isvip = "yes";
		}
	}
	return $isvip;
} // end VIP

// COUPON start
if ($_POST["type"] == "coupon") {
	$code = $_POST["code"];
	$sql_coupon = "SELECT * FROM coupons WHERE Code='".$code."' AND Status='Enabled' AND (StartDate<=current_date AND EndDate>=current_date) LIMIT 1";
	$result_coupon = mysql_query($sql_coupon) or die(mysql_error());
	$num_coupon = mysql_num_rows($result_coupon);
		
	// Stop Multiple times same coupon use
	$reuseCheck = "SELECT count(*) AS nbrc FROM shopping_cart WHERE ProductID='".stripslashes($code)."' AND Type='Coupon' AND SessionID='".session_id()."'";
	$reuseCheckRes = mysql_query($reuseCheck) or die(mysql_error());
	$reuseCheckData = @mysql_fetch_assoc($reuseCheckRes);
	if (isset($reuseCheckData["nbrc"]) && $reuseCheckData["nbrc"] > 0) {
		echo "<script>alert('Please don\'t try to use same coupon number more than one time')</script>";
		exit();
	}	
		
	// test if there is already another used coupon	
	$sql_existing_coupon = "SELECT count(*) AS nbrc FROM shopping_cart WHERE Type='Coupon' AND SessionID='".session_id()."'";
	$squery_existing_coupon = mysql_query($sql_existing_coupon) or die(mysql_error());		
	$sql_coupon_b = "SELECT * FROM coupons WHERE Code='$code' LIMIT 1";
	$result_coupon_b = mysql_query($sql_coupon_b);
	$res_cp = mysql_fetch_assoc($squery_existing_coupon);
	if ($res_cp["nbrc"] != 0 ) {
		$rc_row =  mysql_fetch_assoc($result_coupon_b);
		if ($rc_row["UsedWithOther"] == 0){
			echo 'We did not recognize that code. Please try again';
			exit();
		}
	} 		
	$sql_item = "false";
	$sql_freeItem = "";
	$ordertotal = number_format($_SESSION["orderTotal"], 2) * 100;
	if ($num_coupon > 0) {
		$row_coupon = mysql_fetch_assoc($result_coupon);
		if ($row_coupon["ApplyTo"] == 'CustomerGroup') {
			$groupCheckSql = "SELECT * FROM `customers` WHERE EmailAddress = '".$_SESSION["email"]."' AND CustomerGroup = '".$row_coupon["ApplyOption"]."'";
			$result_group_check = mysql_query($groupCheckSql) or die(mysql_error());
			$num_group_coupon = @mysql_num_rows($result_group_check); 
			if (!$num_group_coupon) {
				echo "We did not recognize that code. Please try again.";
				exit();
			}
		}
		if ((intval($row_coupon["MinimumOrder"]) == 0 || number_format($row_coupon["MinimumOrder"], 2)*100 <= $ordertotal) && (intval($row_coupon["MaximumOrder"]) == 0 || number_format($row_coupon["MaximumOrder"], 2)*100 >= $ordertotal)) {
				;
		} else {
			if ($row_coupon["ApplyTo"] != 'SKU' && $row_coupon["ApplyTo"] != 'Category') {	
				echo "Your coupon is not qualified for discounted rate";
				exit();
			}
		}
		$sql_verif_productqty = "SELECT count(*) AS nbr,Qty FROM shopping_cart WHERE RootSKU='".$row_coupon["ApplyOption"]."' AND SessionID='".session_id()."'";
		$squery_exist_qty = mysql_query($sql_verif_productqty) or die(mysql_error());		
		$res12 = mysql_fetch_assoc($squery_exist_qty);
		if ($row_coupon["MaximumOrder"] != "") {
			if ($row_coupon["MaximumOrder"] < $res12["Qty"] || (strtolower($row_coupon['ApplyTo']) == 'sku' && $row_coupon['SkuItemQuantity'] < $res12["Qty"])) {
				echo "<script>alert('Sorry but the quantity on this product is limited to ".$row_coupon["MaximumOrder"]." units')</script>";
				exit();
			}	
		}
			
		if ($row_coupon["NbrUse"] == 2) {
			$sql_add  = "INSERT INTO shopping_cart(SessionID, EmailAddress, ProductID, ProductName, Qty, Price, CreatedDate, Type) ";
			$sql_add .= "VALUES('".session_id()."', '$_SESSION[email]', '$row_coupon[Code]', '$row_coupon[Name]', '1', 0, current_date, 'Coupon')";
			
			if ($row_coupon['SkuFreeItem'] != "") {
				if(intval($row_coupon["MinimumOrder"]) == 0) {
					$sku_product = $row_coupon["ApplyOption"];
					$sql_verif_product = "select count(*)as nbr,Qty  from shopping_cart where RootSKU='".$row_coupon["ApplyOption"]."' and SessionID='".session_id()."'";
					$squery_exist = mysql_query($sql_verif_product) or die(mysql_error());
					$res1 = mysql_fetch_assoc($squery_exist);
					if($res1["nbr"] == 0) {
						echo "We did not recognize that code. Please try again.";
						exit();
					} else {
						$currentProductQtity = $res1["Qty"];
						if ($currentProductQtity < $row_coupon["SkuItemQuantity"]) {
							echo "You need to order ".$row_coupon["SkuItemQuantity"]." of ".$row_coupon["ApplyOption"]." to qualify for this promotion.";
							exit();
						}
					}
					$sql_product = "select * from products where RootSKU ='".$row_coupon['SkuFreeItem']."'";
					$query = mysql_query($sql_product)or die("product doesn't exisit");
					$res = mysql_fetch_assoc($query);
					$_SESSION["sku"] = "true";
					$_SESSION["freeProductId"] = $res["id"];
					if ($row_coupon["SkuItemQuantity"])
						$num = $currentProductQtity / $row_coupon["SkuItemQuantity"];
					else 
						$num = 0;
					if($num > 1) {
						for($j=0; $j<(int)$num; $j++) {
							$sql_freeItem = "INSERT INTO shopping_cart(SessionID, EmailAddress, ProductID, ProductName, Qty, Price, CreatedDate, Type) ";
							$sql_freeItem .= "VALUES('".session_id()."', '$_SESSION[email]','$res[id]', '$res[ProductDetailName]', '$row_coupon[QuatityFreeItem]', 0, current_date, 'Product')";
							mysql_query($sql_freeItem) or die(mysql_error());
							$sql_item = "true"; 
						}
					} else {
						$sql_add_freeItem = "INSERT INTO shopping_cart(SessionID, EmailAddress, ProductID, ProductName, Qty, Price, CreatedDate, Type) ";
						$sql_add_freeItem .= "VALUES('".session_id()."', '$_SESSION[email]','$res[id]', '$res[ProductDetailName]', '$row_coupon[QuatityFreeItem]', 0, current_date, 'Product')";
					}
						
					if ($row_coupon["ApplyTo"] == 'SKU') {
						$skuUpdateCoupon = "UPDATE shopping_cart SET RootSKU='".$row_coupon['ApplyOption']."' WHERE (SessionID='".session_id()."' OR EmailAddress='".$_SESSION['email']."') AND `Type`='Coupon' AND ProductID='".$row_coupon['Code']."'";
					}	
				} else if($ordertotal >= number_format($row_coupon["MinimumOrder"], 2)*100) {
					$sku_product = $row_coupon["ApplyOption"];
					$sql_verif_product = "select count(*)as nbr,Qty  from shopping_cart where RootSKU='".$row_coupon["ApplyOption"]."' and SessionID='".session_id()."'";
					$squery_exist = mysql_query($sql_verif_product) or die(mysql_error());
					$res1 = mysql_fetch_assoc($squery_exist);
					if($res1["nbr"] == 0) {
						echo "We did not recognize that code. Please try again.";
						exit();
					} else {
						$currentProductQtity = $res1["Qty"];
						if($currentProductQtity < $row_coupon["SkuItemQuantity"]) {
							$rqt = $row_coupon["SkuItemQuantity"] - $row_coupon["ApplyOption"];
							echo "You need to order ".$row_coupon["SkuItemQuantity"]." of ".$row_coupon["ApplyOption"]." to qualify for this promotion.";
							exit();
						}
					}
					$sql_product = "select * from products where RootSKU ='".$row_coupon['SkuFreeItem']."'";
					$query = mysql_query($sql_product)or die("product doesn't exisit");
					$res = mysql_fetch_assoc($query);
					$_SESSION["sku"] = "true";
					$_SESSION["freeProductId"] = $res["id"];
					if ($row_coupon["SkuItemQuantity"])
						$num = $currentProductQtity / $row_coupon["SkuItemQuantity"];
					else 
						$num = 0;
													
					if($num > 1) {
						for($j=0; $j<(int)$num; $j++) {
							$sql_freeItem = "INSERT INTO shopping_cart(SessionID, EmailAddress, ProductID, ProductName, Qty, Price, CreatedDate, Type) ";
							$sql_freeItem .= "VALUES('".session_id()."', '$_SESSION[email]','$res[id]', '$res[ProductDetailName]', '$row_coupon[QuatityFreeItem]', 0, current_date, 'Product')";
							mysql_query($sql_freeItem) or die(mysql_error());
							$sql_item= "true";
						}
					} else {
						$sql_add_freeItem = "INSERT INTO shopping_cart(SessionID, EmailAddress, ProductID, ProductName, Qty, Price, CreatedDate, Type) ";
						$sql_add_freeItem .= "VALUES('".session_id()."', '$_SESSION[email]','$res[id]', '$res[ProductDetailName]', '$row_coupon[QuatityFreeItem]', 0, current_date, 'Product')";
					}
						
					if ($row_coupon["ApplyTo"] == 'SKU') {
						$skuUpdateCoupon = "UPDATE shopping_cart SET RootSKU='".$row_coupon['ApplyOption']."' WHERE (SessionID='".session_id()."' OR EmailAddress='".$_SESSION['email']."') AND Type='Coupon' AND ProductID='".$row_coupon['Code']."'";
					}
				} else {
					$_SESSION["message"] = "You could get a Free Item when you order amount is ". $row_coupon["MinimumOrder"];
				}
			}
			
			if (!mysql_query($sql_add)) {
				echo "We did not recognize that code. Please try again.";
			} else {
				if($sql_item == "false") {					
					mysql_query($sql_add_freeItem) or die(mysql_error());
					if (isset($skuUpdateCoupon)) {
						mysql_query($skuUpdateCoupon) or die(mysql_error());
					}
				}
				echo "added";
			}
		} else {
			$sql_used_coupon = "select count(*) as nbr, Qty from shopping_cart where ProductID='".$row_coupon["Code"]."' and SessionID='".session_id()."'";
			$result = mysql_query($sql_used_coupon) or die(mysql_error());
			$row = mysql_fetch_assoc($result)or die(mysql_error());
			if ($row["nbr"] == 0) {
				if ($row_coupon["ApplyTo"] == 'EntireOrder') {
					if ($ordertotal >= number_format($row_coupon["MinimumOrder"], 2)*100 && ($ordertotal <= number_format($row_coupon["MaximumOrder"], 2)*100 || intval($row_coupon["MaximumOrder"])==0)) {
						$discountAmount = $row_coupon['Amount'];
						$sql_add  = "INSERT INTO shopping_cart(SessionID, EmailAddress, ProductID, ProductName, Qty, Price, CreatedDate, Type) ";
						$sql_add .= "VALUES('".session_id()."', '$_SESSION[email]', '$row_coupon[Code]', '$row_coupon[Name]', '1', ".$discountAmount.", current_date, 'Coupon')";
						if (mysql_query($sql_add))
							echo 'added';
						} else {
							echo "We did not recognize that code. Please try again.";
							exit();
						}
					} elseif($row_coupon["ApplyTo"] == 'Category') {
						if ($_SESSION["email"] == '') {
							$sqlwhere = "SessionID='".session_id()."'";
						} else {
							$sqlwhere = "(EmailAddress='$_SESSION[email]' OR SessionID='".session_id()."') ";
						}
						$isvip = isVip();
						$pricename = 'Price';
						if ($isvip == 'yes') {
							$pricename = 'VIPPrice';
						}
						$catids = str_replace("|", ",", $row_coupon['ApplyOption']);
						$sql_categoryCats = "SELECT DISTINCT s.* FROM shopping_cart s, category_items c WHERE s.ProductID=c.ProductID AND $sqlwhere AND `Type`='Product' AND c.CategoryID IN ($catids)";
						$result_Categorycats = mysql_query($sql_categoryCats) or die(mysql_error());
						$isCatCouponApplicable = false;
						while($row_Categorycats = mysql_fetch_array($result_Categorycats)) { 
						   if ($row_Categorycats[$pricename] >= intval($row_coupon["MinimumOrder"]) && 
						   	   $row_Categorycats[$pricename] <= intval($row_coupon["MaximumOrder"])) {
						   	   	$isCatCouponApplicable = true;
							}
						}

						if ($isCatCouponApplicable) {
							$discountAmount = $row_coupon['Amount'];
							$sql_add  = "INSERT INTO shopping_cart(SessionID, EmailAddress, ProductID, ProductName, Qty, Price, CreatedDate, Type) ";
							$sql_add .= "VALUES('".session_id()."', '$_SESSION[email]', '$row_coupon[Code]', '$row_coupon[Name]', '1', ".$discountAmount.", current_date, 'Coupon')";
							if (mysql_query($sql_add))
								echo 'added';
						} else {
							echo "We did not recognize that code. Please try again.";
							exit();
						}
					} else {
						$sql_add  = "INSERT INTO shopping_cart(SessionID, EmailAddress, ProductID, ProductName, Qty, Price, CreatedDate, Type) ";
						$sql_add .= "VALUES('".session_id()."', '$_SESSION[email]', '$row_coupon[Code]', '$row_coupon[Name]', '1', 0, current_date, 'Coupon')";
						if ($row_coupon['SkuFreeItem'] != "") {
							if (intval($row_coupon["MinimumOrder"]) == 0) {
								$sku_product = $row_coupon["ApplyOption"];
								$sql_verif_product = "select count(*)as nbr,Qty  from shopping_cart where RootSKU='".$row_coupon["ApplyOption"]."' and SessionID='".session_id()."'";
								$squery_exist = mysql_query($sql_verif_product) or die(mysql_error());
								$res1 = mysql_fetch_assoc($squery_exist);
								if ($res1["nbr"] == 0) {
									echo "We did not recognize that code. Please try again.";
									exit();
								} else {
									$currentProductQtity = $res1["Qty"];
									if ($currentProductQtity < $row_coupon["SkuItemQuantity"]) {
										$rqt = $row_coupon["SkuItemQuantity"] - $row_coupon["ApplyOption"];
										echo "You need to order ".$row_coupon["SkuItemQuantity"]." of ".$row_coupon["ApplyOption"]." to qualify for this promotion.";
										exit();
									}
								}
								$sql_product = "select * from products where RootSKU ='".$row_coupon['SkuFreeItem']."'";
								$query = mysql_query($sql_product)or die("product doesn't exisit");
								$res = mysql_fetch_assoc($query);
								$_SESSION["sku"] = "true";
								$_SESSION["freeProductId"] = $res["id"];
								if ($row_coupon["SkuItemQuantity"])
									$num = $currentProductQtity/$row_coupon["SkuItemQuantity"];
								else
									$num = 0;
							if ($num > 1) {
								for ($j=0; $j<(int)$num; $j++) {
									 $sql_freeItem = "INSERT INTO shopping_cart(SessionID, EmailAddress, ProductID, ProductName, Qty, Price, CreatedDate, Type) ";
									 $sql_freeItem .= "VALUES('".session_id()."', '$_SESSION[email]','$res[id]', '$res[ProductDetailName]', '$row_coupon[QuatityFreeItem]', 0, current_date, 'Product')";
									 mysql_query($sql_freeItem);
								   	$sql_freeitem = "true";			
								}
							} else {
								$sql_add_freeItem = "INSERT INTO shopping_cart(SessionID, EmailAddress, ProductID, ProductName, Qty, Price, CreatedDate, Type) ";
								$sql_add_freeItem .= "VALUES('".session_id()."', '$_SESSION[email]','$res[id]', '$res[ProductDetailName]', '$row_coupon[QuatityFreeItem]', 0, current_date, 'Product')";
							}
						} elseif($ordertotal >= number_format($row_coupon["MinimumOrder"], 2) * 100) {
								$sku_product = $row_coupon["ApplyOption"];
								$sql_verif_product = "select count(*)as nbr, Qty from shopping_cart where RootSKU='".$row_coupon["ApplyOption"]."' and SessionID='".session_id()."'";
								$squery_exist = mysql_query($sql_verif_product);
								$res1 = mysql_fetch_assoc($squery_exist);
								if($res1["nbr"] == 0) {
									echo "We did not recognize that code. Please try again.";
									exit();
								} else {
									$currentProductQtity = $res1["Qty"];
									if($currentProductQtity < $row_coupon["SkuItemQuantity"]){
										$rqt = $row_coupon["SkuItemQuantity"] - $row_coupon["ApplyOption"];
										echo "You need to order ".$row_coupon["SkuItemQuantity"]." of ".$row_coupon["ApplyOption"]." to qualify for this promotion.";
										exit();
									}
								}
								$sql_product = "select * from products where RootSKU ='".$row_coupon['SkuFreeItem']."'";
								$query = mysql_query($sql_product)or die("product doesn't exisit");
								$res = mysql_fetch_assoc($query);
								$_SESSION["sku"] = "true";
								$_SESSION["freeProductId"] = $res["id"];
								if ($row_coupon["SkuItemQuantity"])
									$num = $currentProductQtity/$row_coupon["SkuItemQuantity"];
								else
									$num = 0;
								if($num > 1) {
									for($j=0; $j<(int)$num; $j++) {
										$sql_freeItem = "INSERT INTO shopping_cart(SessionID, EmailAddress, ProductID, ProductName, Qty, Price, CreatedDate, Type) ";
										$sql_freeItem .= "VALUES('".session_id()."', '$_SESSION[email]','$res[id]', '$res[ProductDetailName]', '$row_coupon[QuatityFreeItem]', 0, current_date, 'Product')";
										mysql_query($sql_freeItem) or die(mysql_error());
								   		$sql_freeitem = "true";
									}
								} else {
									$sql_add_freeItem = "INSERT INTO shopping_cart(SessionID, EmailAddress, ProductID, ProductName, Qty, Price, CreatedDate, Type) ";
									$sql_add_freeItem .= "VALUES('".session_id()."', '$_SESSION[email]','$res[id]', '$res[ProductDetailName]', '$row_coupon[QuatityFreeItem]', 0, current_date, 'Product')";
								}
							} else {
								$_SESSION["message"] = "You could get a Free Item when you order amount is ". $row_coupon["MinimumOrder"];
							}
						}
						if (!mysql_query($sql_add)) {
							echo "We did not recognize that code. Please try again.";
						} else {
							mysql_query($sql_add_freeItem) or die(mysql_error());
							echo "added";
						}
					}
				} else {
					echo "Coupon code already used!";
				}
			}
		} else {
			echo "We did not recognize that code. Please try again.";
	}
	mysql_close($conn);
	exit();
} // end COUPON

if($_POST["type"] == "customercode") {
	$code = $_POST["code"];
	$sql_code = "SELECT GroupName FROM customer_group WHERE GroupCode='$code' LIMIT 1";
	$result_code = mysql_query($sql_code) or die(mysql_error());
	$row_code = mysql_fetch_assoc($result_code);
	echo $row_code["GroupName"];
	mysql_close($conn);
	exit();
}
?>