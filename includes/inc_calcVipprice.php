<?php
	/**************************************
	 * VIP Pricing functionality          
	 *                                    
	 * Version: 1.2                      
	 * Updated: 29 July 2014               
	 * By: Richard Tuttle                 
	 *************************************/

function getVipPrice($gender = '', $color = '', $size = '', $id = 0, $qty = 0) {
	$gender = addslashes($gender);	
	$color = addslashes($color);
	$size = addslashes($size);
	$qty = intval($qty);
	$sql_price = "SELECT * FROM product_pricing WHERE Gender='".$gender."' AND ProductID=".$id." LIMIT 1";
	$result_price = mysql_query($sql_price) or die("Pricing Error: " . mysql_error());
	$row_price = @mysql_fetch_assoc($result_price);
	$price = $row_price["NonMember"];
	$VIPprice = 0;
	for($i==1; $i<=4; $i++) {
		$arrOpt = explode("-", $row_price["Option".$i]);
		if (count($arrOpt) > 1) {
			if ($qty <= intval($arrOpt[1]) && $qty >= intval($arrOpt[0])) {
				$VIPprice = $row_price["Option".$i."Price"];
			}
		} else {
			if ($qty <= intval($arrOpt[0]) && $VIPprice == 0) {
				$VIPprice = $row_price["Option".$i."Price"];
			}
		}
	}

	if ($VIPprice == 0) {
		$VIPprice = $row_price["Option4Price"];
	}
				
	$VIPLevel = '';
		
	if ($_SESSION["email"] != '') {
		$where = " OR EmailAddress = '$_SESSION[email]' ";
		$sql_status = "SELECT Status, VIPLevel FROM customers WHERE EmailAddress='$_SESSION[email]' AND current_date() <= VIPExpDate LIMIT 1";
		$result_status = mysql_query($sql_status) or die("VIP Selection Error: " . mysql_error());
		$row_status = @mysql_fetch_assoc($result_status);
		$Status = $row_status["Status"];
		$VIPLevel = $row_status["VIPLevel"];
	}
		
	if ($VIPLevel != 0 && $VIPLevel != '') {
		if (floatval($VIPprice) > floatval($row_price["Option".$VIPLevel."Price"])) {
			$VIPprice = $row_price["Option".$VIPLevel."Price"];
		}
	}
	
	// check for Gold certificate code in cart
	$couponCk = "SELECT * FROM shopping_cart WHERE Type='Cert' LIMIT 1";
	$result_couponCk = mysql_query($couponCk) or die("Gold Certificate pricing check error: " . mysql_error());
	$num_couponCk = mysql_num_rows($result_couponCk);
	if ($num_couponCk > 0) {
		$VIPprice = $row_price["Option4Price"];
	}
	
	if ($price == '') { 
		$price = 0; 
	}
		
	if ($VIPprice == '') { 
		echo "VIP Price: " . $VIPprice . "<br />";
		// $VIPprice = 0; 
	}
		
	if ($qty == '') { 
		$qty = 0; 
	}
		
	$sql_coloradd = "SELECT DISTINCT ColorSKU, ColorAddPrice FROM product_options WHERE ProductID=".$id." AND ColorSKU='".$color."' LIMIT 1";
	$result_coloradd = mysql_query($sql_coloradd);
	$num_coloradd = @mysql_num_rows($result_coloradd);

	if ($num_coloradd > 0) {
		$row_coloradd = @mysql_fetch_assoc($result_coloradd);
		$price = $price + ($row_coloradd["ColorAddPrice"]*$qty);
		$VIPprice = $VIPprice + ($row_coloradd["ColorAddPrice"]*$qty);
	}

	$sql_sizeadd = "SELECT DISTINCT SizeSKU, SizeAddPrice FROM product_options WHERE ProductID=".$id." AND SizeSKU='".$size."' LIMIT 1";
	$result_sizeadd = mysql_query($sql_sizeadd);
	$num_sizeadd = @mysql_num_rows($result_sizeadd);

	if ($num_sizeadd > 0) {
		$row_sizeadd = @mysql_fetch_assoc($result_sizeadd);
		$price = $price + ($row_sizeadd["SizeAddPrice"]*$qty);
		$VIPprice = $VIPprice + ($row_sizeadd["SizeAddPrice"]*$qty);
	}
	return array('VIPPrice' => $VIPprice, 'NoneMemberPrice' => $price);
}