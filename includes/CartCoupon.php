<?php
/************************
 * Coupon capture class
 *
 * By: Richard Tuttle
 * Updated: 24 August 2016
 * ***********************/

session_start();
require_once '../cpadmin/includes/db.php';

class CartCoupon {
	protected $_couponCode = null;
	protected $_message = null;
	protected $_isvip = 'no';
	protected $_orderTotal = 0;
	protected $_couponInfo = array();
	public function __construct($code = '') {
		$this->_couponCode = stripslashes($code);
		$this->_orderTotal = intval(round($_SESSION["coupon_order_total"], 2) * 100);
	}
	
	private function isVip() {
		if ($_SESSION["email"] != '') {
			$sql_status = "SELECT Status FROM customers WHERE Status='VIP' AND EmailAddress='".$_SESSION['email']."' AND VIPExpDate > current_date()";
			$result_status = mysql_query($sql_status);
			$num_status = mysql_num_rows($result_status);
			if ($num_status > 0) {
				$this->_isvip = "yes";
				$sql_remvip = "DELETE FROM shopping_cart WHERE ProductID='VIP' AND ".$this->getSessionId();
				mysql_query($sql_remvip) or die(mysql_error());
			} else {
				$sql_chkcart = "SELECT id FROM shopping_cart WHERE ProductID='VIP' AND EmailAddress='".$_SESSION['email']."'";
				$result_chkcart = mysql_query($sql_chkcart);
				$num_chkcart = mysql_num_rows($result_chkcart);
				if ($num_chkcart > 0) {
					$this->_isvip = "yes";
				}
			}
		}
		if ($this->_isvip == "no") {
			$sql_chkcart = "SELECT * FROM shopping_cart WHERE ProductID='VIP' AND SessionID='".session_id()."'";
			$result_chkcart = mysql_query($sql_chkcart);
			$num_chkcart = mysql_num_rows($result_chkcart);
			if ($num_chkcart > 0) {
				$this->_isvip = "yes";
			}
		}
		return $this->_isvip;
	}
	
	private function getSessionId() {
		$sqlwhere = '';
		if ($_SESSION["email"] == '') {
			$sqlwhere = "SessionID='".session_id()."'";
		} else {
			$sqlwhere = "(EmailAddress='".$_SESSION['email']."' OR SessionID='".session_id()."') ";
		}
		return $sqlwhere;
	}
	
	public function isSpecial($productId = 0) {
		$sql_specialsku = "SELECT SpecialPrice, isSpecial FROM products WHERE id =".$productId." AND ((DATE_FORMAT(SpecialFrom, '%Y-%m-%d') <= DATE_FORMAT(current_date, '%Y-%m-%d') OR SpecialFrom='' OR SpecialFrom='--') AND (DATE_FORMAT(current_date, '%Y-%m-%d') <= DATE_FORMAT(SpecialTo, '%Y-%m-%d') OR SpecialTo='' OR SpecialTo='--')) AND isSpecial!='' LIMIT 1";
		$result_specialsku = mysql_query($sql_specialsku);
		$row_specialsku = @mysql_fetch_assoc($result_specialsku);
        $row_specialsku = array();
        $num_special = @mysql_num_rows($result_specialsku);
        if ($num_special > 0)
		    $row_specialsku = @mysql_fetch_assoc($result_specialsku);
        if (!isset($row_specialsku) || !$row_specialsku ) {
		     return true;
		}
		return $row_specialsku["isSpecial"] != "True"? true:false;
	}
	
	/**
	* Is applicable coupon
	**/
	private function isValidOrderTotal() {
		$maximumOrder = intval(round($this->_couponInfo["MaximumOrder"], 2) * 100);
		$minimuOrder = intval(round($this->_couponInfo["MinimumOrder"], 2) * 100);
		if ((intval($this->_couponInfo["MinimumOrder"]) == 0 || $minimuOrder <= $this->_orderTotal) && (intval($this->_couponInfo["MaximumOrder"]) == 0 || $maximumOrder >= $this->_orderTotal)) {
			$this->_message = "";
		} else {
			$this->_message = "<br>Your cart does meet the total required to qualify for the coupon";
		}
	}
	
	private function isValidCustomerGroup() {
		if ($this->_couponInfo["ApplyTo"] == 'CustomerGroup') {
			$groupCheckSql = "SELECT * FROM `customers` WHERE EmailAddress = '".$_SESSION["email"]."' AND CustomerGroup = '".$this->_couponInfo["ApplyOption"]."'";
			$result_group_check = mysql_query($groupCheckSql);
			$num_group_coupon = @mysql_num_rows($result_group_check); 
			if (!$num_group_coupon) {
				$this->_message = "We did not recognize that code. Please try again.";
			}
		}
	}
	
	private function isValidSku() {
		$sql_product = "select * from products where RootSKU ='".$this->_couponInfo["SkuFreeItem"]."'";
		$product_query = mysql_query($sql_product);
		$num_product = @mysql_num_rows($product_query); 
		if ($num_product > 0) {
			$skuProduct = @mysql_fetch_assoc($product_query);
			
		if($this->_couponInfo["ApplyTo"] == "EntireOrder") {
			if($this->_couponInfo["MinimumOrder"] > 0) {
				$prcField = "Price";
				if($this->isVip() == "yes") {
					$prcField = "VIPPrice";
				}
				$sql_email = "";
				if($_SESSION["email"] != '') {
					$sql_email = " OR EmailAddress = '".$_SESSION["email"]."'";
				}
				$sql_chkOdrTotal = "SELECT SUM($prcField * Qty) AS OrderTotal FROM shopping_cart WHERE SessionID='".session_id()."' $sql_email";
				$result_chkOdrTotal = mysql_query($sql_chkOdrTotal);
				$row_chkOdrTotal = mysql_fetch_assoc($result_chkOdrTotal);
				if(floatval($this->_couponInfo["MinimumOrder"]) < floatval($row_chkOdrTotal["OrderTotal"])) {
					$this->saveCoupon();
					$sql_freeItem = "INSERT INTO shopping_cart(SessionID, EmailAddress, ProductID, ProductName, Qty, Price, CreatedDate, Type) ";					
					$sql_freeItem .= "VALUES('".session_id()."', '".$_SESSION['email']."','".$skuProduct['id']."', '".$skuProduct['ProductDetailName']."', '".$this->_couponInfo['QuatityFreeItem']."', 0, current_date, 'C_Product')";
					mysql_query($sql_freeItem) or die(mysql_error());
					$this->_message = 'added';
				} else {
					$this->_message = "You need a minimum order total of ".$this->_couponInfo["MinimumOrder"]." to apply this coupon.";
				}
			} else {
				$this->saveCoupon();	
				$sql_freeItem = "INSERT INTO shopping_cart(SessionID, EmailAddress, ProductID, ProductName, Qty, Price, CreatedDate, Type) ";					
				$sql_freeItem .= "VALUES('".session_id()."', '".$_SESSION['email']."','".$skuProduct['id']."', '".$skuProduct['ProductDetailName']."', '".$this->_couponInfo['QuatityFreeItem']."', 0, current_date, 'C_Product')";
				mysql_query($sql_freeItem);
				$this->_message = 'added';
			}
		} else {
			$sql_verif_product = "select count(*)as nbr,Qty  from shopping_cart where RootSKU='".$this->_couponInfo["ApplyOption"]."' and SessionID='".session_id()."'";
			$squery_exist = mysql_query($sql_verif_product);
			$skuProductExist = mysql_fetch_assoc($squery_exist);
			if ($skuProductExist["nbr"]==0){
				$this->_message = "We're sorry. The current contents in your Shopping Cart do not qualify you for this promotion.";
			} else {
				if ($skuProductExist["Qty"] < $this->_couponInfo["SkuItemQuantity"]) {
					$this->_message = "You need to order ".$this->_couponInfo["SkuItemQuantity"]." of ".$this->_couponInfo["ApplyOption"]." to qualify for this promotion.";
				} else {
					$this->saveCoupon();
					if ($this->_couponInfo["SkuItemQuantity"])
						$num = $skuProductExist["Qty"]/$this->_couponInfo["SkuItemQuantity"];
					else
						$num = 1;

					for ($j = 0;$j <(int)$num;$j++){
						$sql_freeItem = "INSERT INTO shopping_cart(SessionID, EmailAddress, ProductID, ProductName, Qty, Price, CreatedDate, Type) ";					
						$sql_freeItem .= "VALUES('".session_id()."', '".$_SESSION['email']."','".$skuProduct['id']."', '".$skuProduct['ProductDetailName']."', '".$this->_couponInfo['QuatityFreeItem']."', 0, current_date, 'C_Product')";
						mysql_query($sql_freeItem);
					}
					$this->_message = 'added';
				}
			}
		}	
	} else {
			$this->_message = "Product doesn't exist";
		}
	}
	
	private function isValidCategory() {
		$isvip = $this->isVip();
		$pricename = 'Price';
		if ($isvip == 'yes') {
			$pricename = 'VIPPrice';
		}
		$catids = str_replace("|", ",", $this->_couponInfo['ApplyOption']);
		$sql_categoryCats = "SELECT DISTINCT s.* FROM shopping_cart s, category_items c WHERE s.ProductID=c.ProductID AND ".$this->getSessionId()." AND (`Type`='Product' OR (`Type` ='Bundle' AND BundleID IS NULL)) AND c.CategoryID IN ($catids)";
		$result_Categorycats = mysql_query($sql_categoryCats);
		$totalPrice = 0;
		$isCatCouponApplicable = false;
		$maximumOrder = intval(round($this->_couponInfo["MaximumOrder"], 2) * 100);
		$minimuOrder = intval(round($this->_couponInfo["MinimumOrder"], 2) * 100);
		
		while($row_Categorycats = mysql_fetch_array($result_Categorycats)) { 
			if ($this->isSpecial($row_Categorycats['ProductID'])) {
				$totalPrice += $row_Categorycats['Qty']*$row_Categorycats[$pricename];
			}
		}
		
		$price = intval(round($totalPrice, 2) * 100);
		if ($price >= $minimuOrder &&  $price <= $maximumOrder) {
			$isCatCouponApplicable = true;
		} else {
			$isCatCouponApplicable = false;
		}
		
		$this->_message = ($isCatCouponApplicable==true?'':'We are sorry. The current contents in your Shopping Cart do not qualify you for this promotion.');
	}

    public function checkValidShippingProduct() {
		$catids = str_replace("|", ",", $this->_couponInfo["ShippingOption"]);
		$sql_categoryCats = "SELECT DISTINCT s.* FROM shopping_cart s, category_items c WHERE s.ProductID=c.ProductID AND ".$this->getSessionId()." AND (`Type` = 'Product' OR `Type` = 'Bundle') AND c.CategoryID IN ($catids)";
		$result_Categorycats = mysql_query($sql_categoryCats);
		$isCatCouponApplicable = false;
		while($row_Categorycats = mysql_fetch_array($result_Categorycats)) { 
            $isCatCouponApplicable = true;
		}
		$this->_message = ($isCatCouponApplicable==true?'':'We did not recognize that code. Please try again.');
	}
	
	/**
	*
	* Check Coupon is active or not
	*
	**/
	public function isCouponActive() {	
		// main coupon checking code
		$sql_coupon = "SELECT * FROM coupons WHERE Code='".$this->_couponCode."' AND Status='Enabled' AND ((StartDate<=current_date OR StartDate ='0000-00-00' ) AND (EndDate>=current_date OR EndDate='0000-00-00' )) LIMIT 1";
		// $sql_coupon = "SELECT * FROM coupons WHERE Code='".$this->_couponCode."' AND Status='Enabled' AND (StartDate<=current_date AND EndDate>=current_date) LIMIT 1";
		$result_coupon = mysql_query($sql_coupon) or die("Coupon Error: " . mysql_error());
		$num_coupon = @mysql_num_rows($result_coupon);
		if ($num_coupon > 0) {
			$row_coupon = mysql_fetch_assoc($result_coupon);
			$this->_couponInfo = $row_coupon;
			$this->numberOfUse();
			$this->isMutipleTimes();
			$this->isCouponUsedWithOthers();
		
			if ($this->_message != "") {
				return $this->_message;
			}
			
			if ($row_coupon["ApplyTo"] == 'Category')
				$this->isValidCategory();
            else if ($row_coupon["ApplyOption"] == 'Category')
				$this->checkValidShippingProduct();
			else
				$this->isValidOrderTotal();
			
			if ($row_coupon["ApplyTo"] == 'CustomerGroup')
				$this->isValidCustomerGroup();
				
			if ($this->_message != "") {
				return $this->_message;
			}
				
			switch($row_coupon["ApplyTo"]) {
				case 'EntireOrder':
					if ($this->_couponInfo['SkuFreeItem'] != "") {
						$this->isValidSku();
					} else {
						$this->saveCoupon();
					}
				break;
				case 'CustomerGroup':
					$this->saveCoupon();
				break;
				case 'SKU':
					if ($this->_couponInfo['SkuFreeItem'] != "") {
						$this->isValidSku();
					}
				break;
				case 'Category':
					$this->saveCoupon();
				break;
				case 'Shipping':
					$this->saveCoupon();
				break;
				case 'Buy':
					if ($this->_couponInfo['SkuFreeItem'] != "") {
						$this->isValidSku();
					}
				break;
				case 'Manufacturer':
					if ($this->_couponInfo['SkuFreeItem'] != "") {
						$this->isValidSku();
					}
				break;
			}
		} else {
			// not found in coupons? check for certificate existance then
			$sql_coupon = "SELECT * FROM certificate WHERE codeNum='".$this->_couponCode."' AND used='no' LIMIT 1";
			$result_coupon = mysql_query($sql_coupon) or die("Coupon Error: " . mysql_error());
			$num_coupon = @mysql_num_rows($result_coupon);
			if ($num_coupon == 1) {
				$row_coupon = mysql_fetch_assoc($result_coupon);
				$this->_couponInfo = $row_coupon;
				$this->_couponInfo["ApplyTo"] = "EntireOrder";
				$this->_couponInfo["special"] = "gold";
				$this->saveCoupon();
			} else {
				$this->_message = "We're sorry, but that code appears to not exist.";
			}
		}
		return $this->_message;
	}
// }
	
	private function saveCoupon() {
		if ($this->_couponInfo["ApplyTo"] == 'Buy' || $this->_couponInfo["ApplyTo"] == 'SKU') {	
			$sql_add  = "INSERT INTO shopping_cart(SessionID, EmailAddress, ProductID, RootSKU, ProductName, Qty, Price, CreatedDate, Type) ";
			$sql_add .= "VALUES('".session_id()."', '".$_SESSION['email']."', '".$this->_couponInfo['Code']."',  '".$this->_couponInfo['ApplyOption']."', '".$this->_couponInfo['Name']."', '1', ".$this->_couponInfo['Amount'].", current_date, 'Coupon')";
		} elseif ($this->_couponInfo["special"] == 'gold') {
			$sql_add = "INSERT INTO shopping_cart(SessionID, EmailAddress, ProductID, ProductName, Qty, Price, CreatedDate, Type) VALUES('".session_id()."', '".$_SESSION['email']."', '".$this->_couponInfo["codeNum"]."', 'Gold VIP Certificate', '1', '0', current_date, 'Cert')";
			// echo "SQL: " . $sql_add; exit(); // testing only
		} else {
			$sql_add  = "INSERT INTO shopping_cart(SessionID, EmailAddress, ProductID, ProductName, Qty, Price, CreatedDate, Type) VALUES('".session_id()."', '".$_SESSION['email']."', '".$this->_couponInfo['Code']."', '".$this->_couponInfo['Name']."', '1', ".$this->_couponInfo['Amount'].", current_date, 'Coupon')";
		}
		if (mysql_query($sql_add)) {
			$this->_message = 'added';
		} else {
			$this->_message = "The current contents in your Shopping Cart do not qualify you for this promotion.";
		}
	}
	
	private function numberOfUse() {
		if ($this->_couponInfo["NbrUse"] == 2) {
			$this->_message = "";
		} else {
			$sql_used_coupon = "(SELECT count(*) as nbr FROM orders, orders_items WHERE orders_items.ProductID='".$this->_couponCode."' AND orders.EmailAddress='".$_SESSION['email']."') UNION (select count(*) as nbr from shopping_cart where ProductID='".$this->_couponCode."' and SessionID='".session_id()."')";
			// echo "SQL: " . $sql_used_coupon . "<br />"; exit;
			$result = mysql_query($sql_used_coupon);
			$row = @mysql_fetch_assoc($result);
			if ($row["nbr"] > 0) {
				$this->_message = "We're sorry but you have already used this promotion and you're limited to a one-time use only.";
			} else {
				$this->_message = "";
			}
		}
	}
	
	/**
	*
	* Multiple Times check 
	*
	**/
	public function isMutipleTimes() {
		$reuseCheck = "SELECT count(*) AS nbrc FROM shopping_cart WHERE ProductID='".$this->_couponCode."' AND (Type='Coupon' OR Type='CouponUsed') AND SessionID='".session_id()."'";
		// $reuseCheck = "SELECT count(*) AS nbrc FROM shopping_cart WHERE ProductID='".$this->_couponCode."' AND Type='CouponUsed' AND SessionID='".session_id()."'";
		$reuseCheckRes = mysql_query($reuseCheck);
		$reuseCheckData = @mysql_fetch_assoc($reuseCheckRes);
		if (isset($reuseCheckData["nbrc"]) && $reuseCheckData["nbrc"] > 0 ) {
			 $this->_message = "<script>alert('We are sorry, but this promotion already exists in your Shopping Cart.')</script>";
		}	
	}
	
	/** 
	*
	* Is this coupon applicable with others coupon 
	*
	**/
	public function isCouponUsedWithOthers() {
		// $sql_existing_coupon = "SELECT count(*) AS nbrc FROM shopping_cart WHERE Type='Coupon' AND SessionID='".session_id()."'";
		$sql_existing_coupon = "SELECT count(*) AS nbrc FROM shopping_cart WHERE (Type='Coupon' OR Type='CouponUsed') AND SessionID='".session_id()."'";
	    $squery_existing_coupon= mysql_query($sql_existing_coupon);		
		$sql_coupon_b = "SELECT * FROM coupons WHERE Code='".$this->_couponCode."' LIMIT 1";
		$result_coupon_b = mysql_query($sql_coupon_b);
		$res_cp = @mysql_fetch_assoc($squery_existing_coupon);
	    if ($res_cp["nbrc"] !=0 ) {
		    $rc_row =  mysql_fetch_assoc($result_coupon_b);
		    if ($rc_row["UsedWithOther"] == 0) {
			    $this->_message = 'We\'re sorry. This promotion cannot be combined with other offers.';
			}
	    }
	}
} // end class definition

if ($_POST["type"] == "coupon") {
	$code = $_POST["code"];
	$coupoObj = new CartCoupon($code);
	$message = $coupoObj->isCouponActive();
	echo $message;
	mysql_close($conn);
	exit();
}

if ($_POST["type"] == "customercode") {
	$code = $_POST["code"];
	$sql_code = "SELECT GroupName FROM customer_group WHERE GroupCode='$code' LIMIT 1";
	$result_code = mysql_query($sql_code);
	$row_code = mysql_fetch_assoc($result_code);
	echo $row_code["GroupName"];
	mysql_close($conn);
	exit();
}
?>