<?php
class CouponCalculation {
	protected $_totalPrice = 0;
	protected $_qty = 0;
	protected $_isVip = false;
	protected $_couponFreeProduct = array();
	protected $_couponInfo = array();
	public $_totalDiscount = 0; /** Calculation Discount **/
	
	public function __construct($qty =0, $totalPrice = 0, $isVip = false) {
		$this->_isVip = $isVip;
		$this->_totalPrice = $totalPrice;
		$this->_qty = $qty;
	}
	
	private function isVip() {
		if ($_SESSION["email"] != '') {
			$sql_status = "SELECT Status FROM customers WHERE Status='VIP' AND EmailAddress='".$_SESSION['email']."' AND VIPExpDate > current_date()";
			$result_status = mysql_query($sql_status);
			$num_status = mysql_num_rows($result_status);
			if ($num_status > 0) {
				$this->_isvip = "yes";
				$sql_remvip = "DELETE FROM shopping_cart WHERE ProductID='VIP' AND ".$this->getSessionId();
				mysql_query($sql_remvip);
			} else {
				$sql_chkcart = "SELECT id FROM shopping_cart WHERE ProductID='VIP' AND EmailAddress='".$_SESSION['email']."'";
				$result_chkcart = mysql_query($sql_chkcart);
				$num_chkcart = mysql_num_rows($result_chkcart);
				if ($num_chkcart>0) {
					$this->_isvip = "yes";
				}
			}
		}
		if ($this->_isvip == "no") {
			$sql_chkcart = "SELECT * FROM shopping_cart WHERE ProductID='VIP' AND SessionID='".session_id()."'";
			$result_chkcart = mysql_query($sql_chkcart);
			$num_chkcart = mysql_num_rows($result_chkcart);
			if ($num_chkcart>0) {
				$this->_isvip = "yes";
			}
		}
		return $this->_isvip;
	}
	
	public function getSkuFreeItem($rootSku = '', $num = 0, $qty = 0) {
		$freeItem = array();
		$sql_coupons = "SELECT * FROM shopping_cart WHERE ".$this->getSessionId()." AND `Type`='Coupon' AND RootSKU='".$rootSku."' AND RootSKU IS NOT NULL ORDER BY id DESC";
		echo $sql_coupons; exit;
		$result_coupons = mysql_query($sql_coupons);
		if (@mysql_num_rows($result_coupons)) {
			while($row_coupons = @mysql_fetch_array($result_coupons)) {
				$freeitemHtml = '';
				$sql_cdetail = "SELECT * FROM coupons WHERE Code='".$row_coupons['ProductID']."' AND Status='Enabled' AND (EndDate='0000-00-00' OR EndDate>=current_date) AND (StartDate='0000-00-00' OR StartDate>=current_date) LIMIT 1";
			  	$cresult_cdetail = mysql_query($sql_cdetail);
			  	$cnum_cdetail = @mysql_num_rows($cresult_cdetail);
			  	if ($qty >= $crow_cdetail["SkuItemQuantity"]) {
					if ($cnum_cdetail>0) {
						$crow_cdetail = mysql_fetch_assoc($cresult_cdetail);
						if ($crow_cdetail["ApplyTo"] == "SKU" && $crow_cdetail['ApplyOption']) {
							$sql_items1 = '';
							$sql_items = "SELECT p.* ";
						    $sql_items .= ",po.Color";
							$sql_items1 .= " LEFT JOIN product_options AS po ON po.ProductID=p.id 
											AND po.ColorSKU='".$row_coupons["ColorSKU"]."'";
						  
						    $sql_items .= ",po1.Size ";
							$sql_items1 .= " LEFT JOIN product_options AS po1 ON po1.ProductID=p.id 
											AND po1.SizeSKU='".$row_coupons["SizeSKU"]."'";
						    
							$sql_items .= " FROM products AS p ".$sql_items1;
							$sql_items .= " WHERE p.RootSKU='".$crow_cdetail['ApplyOption']."' GROUP BY p.id LIMIT 1";
			
							$cresult_items = mysql_query($sql_items);
							$crow_items = mysql_fetch_assoc($cresult_items);
						    
							//$freeitemSku = $crow_cdetail['ApplyOption'];
							$freeitemHtml = '<tr style="border:none"><td class="cartitem">&nbsp;</td>
										     <td class="cartitem" colspan="5">';
							$freeitemHtml .= '<div style="width:500px;color:#fff;background:red;padding:5px;margin:5px;">
											 <a style="color:#fff;text-decoration:underline;" href="changeOption.php?id='.$crow_items['id'].'&t=free&cid='.$row_coupons["ProductID"].'&pid='.$row_coupons["id"].'">Select your color, size, etc, for your free item ('.$crow_items['ProductDetailName'].')</a>'.(isset($crow_items['Color'])?',&nbsp;'.$crow_items['Color']:'').(isset($crow_items['Size'])?',&nbsp;'.$crow_items['Size']:'').(isset($crow_items['Gender'])?',&nbsp;'.$crow_items['Gender']:'');
							
							$freeitemHtml .= '<input type="hidden" id="id_'.$num.'" name="id_'.$num.'" value="'.$row_coupons["id"].'" />';
						    $freeitemHtml .= '</div></td>';
							$freeitemHtml .= '<td class="cartitem">
											 <img class="cartremove" src="images/del_icon.png" onClick="removeItem(\''.$row_coupons["id"].'\');" />
											</td>
											</tr>';
							$freeItem[] = $freeitemHtml;
									
						}
					}
				} else {
					$deleteInvalidCoupon = "DELETE FROM shopping_cart WHERE ".$this->getSessionId()." AND `Type`='Coupon' 
											AND RootSKU='".$rootSku."'";
					mysql_query($deleteInvalidCoupon);
					
					$deleteInvalidCoupon = "DELETE FROM shopping_cart WHERE ".$this->getSessionId()." AND `Type`='C_Product' 
											AND ProductID='".$prodId."'";
					mysql_query($deleteInvalidCoupon);
					
					$sql_product = "select * from products where RootSKU ='".$crow_cdetail["SkuFreeItem"]."'";
					$product_query = mysql_query($sql_product);
					$num_product = @mysql_num_rows($product_query);
					if ($num_product > 0) {
						$skuProduct = @mysql_fetch_assoc($product_query);
						if ($crow_cdetail["SkuItemQuantity"])
							$num = $qty/$crow_cdetail["SkuItemQuantity"];
						else
							$num = 0;
						
						for ($j = 0;$j <= (int)$num;$j++){
							$sql_freeItem = "INSERT INTO shopping_cart(SessionID, EmailAddress, ProductID, ProductName, Qty, Price, CreatedDate, Type) ";					
							$sql_freeItem .= "VALUES('".session_id()."', '".$_SESSION['email']."','".$skuProduct['id']."', '".$skuProduct['ProductDetailName']."', '".$crow_cdetail['QuatityFreeItem']."', 0, current_date, 'C_Product')";
							mysql_query($sql_freeItem);
						}
					}
				}
			}
		}
		return $freeItem;
		//$this->_couponFreeProduct
	}
	
	public function getCoupon() {
		$sql_coupons = "SELECT * FROM shopping_cart WHERE ".$this->getSessionId()." AND Type='Coupon' AND RootSKU IS NULL";
		$result_coupons = mysql_query($sql_coupons);
		while($row_coupons = mysql_fetch_array($result_coupons)) {
			if ($row_coupons["ProductID"]) {
				if (is_null($row_coupons['RootSKU']) && empty($row_coupons['RootSKU'])) {
					$amount = $this->isApplicable($row_coupons['ProductID']);
					if ($amount != '') {
						$this->_couponInfo[$row_coupons["ProductID"]] = array(
												'ProductName' => $row_coupons["ProductName"],
												'ProductID' => $row_coupons["ProductID"],
												'amount' => $amount,
												'id' => $row_coupons["id"],
												'vip' => $this->_isVip,
												'nfreeqty' => 1
										 );
					}
			     }
			}
		}
		return $this->_couponInfo;
	}
	
	public function calcSkuPrice($row_items = array(), $type = '', $amount = 0) {
		if ($this->_isVip) {
			$pricename = "VIPPrice";
		} else {
			$pricename = "Price";
		}
		$skuamount = 0;		
		$qty = $row_items['Qty'];		
		//if ($this->isSpecial($productId)) {
		if ($type == "dollar") {
			$skuamount = $skuamount+($amount*$qty);
			$skudiscount = $skudiscount-($amount*$qty);
		} else {
			$skuamount = $amount."%";
			$skutotal = ($row_items[$pricename]*$qty)*($amount/100);
			$skudiscount = $skudiscount-$skutotal;
		}

		$this->_totalDiscount = $this->_totalDiscount+$skudiscount;
	}
	
	public function calcOrderPrice($type = '', $amount = 0) {
		if ($type == "dollar") {
			$this->_totalDiscount = $this->_totalDiscount-$amount;
		} else {
			$this->_totalDiscount = $this->_totalDiscount+(($this->_totalPrice*($amount/100))*-1);
		}
	}
	
	public function formatPrice($type = '', $amount = 0) {
		if ($type == "dollar") {
			$amount = "- $".number_format($amount, 2);
		} else {
			$amount = $amount."%";
		}
		return $amount;
	}
	
	public function getOrderProd($couponInfo = array()) {
		if ((intval($couponInfo["MinimumOrder"]) ==0 || number_format($couponInfo["MinimumOrder"], 2)*100 <= $this->_totalPrice) 
		&& (intval($couponInfo["MaximumOrder"]) == 0 || number_format($couponInfo["MaximumOrder"], 2)*100 >= $this->_totalPrice)) {
			$this->calcOrderPrice($couponInfo['Type'], $couponInfo['Amount']);
			return true;
		} else {
			$deleteInvalidCoupon = "DELETE FROM shopping_cart WHERE ".$this->getSessionId()." AND `Type`='Coupon' 
									AND ProductID='".$couponInfo['ProductID']."'";
			mysql_query($deleteInvalidCoupon);
			return false;
		}
	}
	
	public function getSkuProd($aplyOption = '', $type = '', $amount = 0) {
		$sql_items = "SELECT * FROM shopping_cart WHERE ".$this->getSessionId()." AND `Type`='Product' AND RootSKU='".$aplyOption."'";
		$result_items = mysql_query($sql_items);
		while($row_items = mysql_fetch_array($result_items)) {
			$this->calcSkuPrice($row_items, $type, $amount);
		}
	}
	
	private function getCatProduct($couponInfo = array()) {
		$isvip = $this->isVip();
		$pricename = 'Price';
		if ($isvip == 'yes') {
			$pricename = 'VIPPrice';
		}
		$catids = str_replace("|", ",", $couponInfo['ApplyOption']);
		$sql_categoryCats = "SELECT DISTINCT s.* FROM shopping_cart s, category_items c WHERE s.ProductID=c.ProductID AND ".$this->getSessionId()." AND `Type`='Product' AND c.CategoryID IN ($catids)";
		$result_Categorycats = mysql_query($sql_categoryCats);
		$isCatCouponApplicable = false;
		while($row_Categorycats = mysql_fetch_array($result_Categorycats)) { 
			if ($row_Categorycats[$pricename] >= intval($couponInfo["MinimumOrder"]) && 
				$row_Categorycats[$pricename] <= intval($couponInfo["MaximumOrder"])) {
				if ($this->isSpecial($row_Categorycats['ProductID'])) {
					 $this->calcSkuPrice($row_Categorycats, $couponInfo['Type'], $couponInfo['Amount']);
					 $isCatCouponApplicable = true;
				}
			}
		}
		return $isCatCouponApplicable;
	}	
	
	public function getManuProduct($aplyOption = '', $type = '', $amount = 0) {
		$sql_cats = "SELECT DISTINCT s.* FROM shopping_cart s, products p WHERE s.ProductID=p.id AND ".$this->getSessionId()."
				     AND Type='Product' AND p.ManufacturerNum='".$aplyOption."'";
		$result_cats = mysql_query($sql_cats);
		while($row_cats = mysql_fetch_array($result_cats)) {
			$this->calcSkuPrice($row_cats, $type, $amount);
		}
	}
	
	public function isApplicable($productId = 0) {
		$sql_cdetail = "SELECT * FROM coupons WHERE Code='".$productId."' AND Status='Enabled' AND (EndDate='0000-00-00' OR EndDate>=current_date) AND (StartDate='0000-00-00' OR StartDate>=current_date) LIMIT 1";
		$result_cdetail = mysql_query($sql_cdetail);
		$row_cdetail = @mysql_fetch_assoc($result_cdetail);
		$num_cdetail = @mysql_num_rows($result_cdetail);
		if ($num_cdetail > 0) {
			switch($row_cdetail["ApplyTo"]) {
				case 'EntireOrder':
					if (!$this->getOrderProd($row_cdetail)) {
						return '';
					}
				break;
				case 'CustomerGroup':
					if ($this->isCustGroupExist($row_cdetail['ApplyOption'])) {
						if (!$this->getOrderProd($row_cdetail)) {
							return '';
						}
					} else {
						$deleteInvalidCoupon = "DELETE FROM shopping_cart WHERE ".$this->getSessionId()." AND `Type`='Coupon' 
												AND ProductID='".$row_cdetail['ProductID']."'";
						mysql_query($deleteInvalidCoupon);
						return '';
					}
				break;
				case 'SKU':
					$this->getSkuProd($row_cdetail['ApplyOption'], $row_cdetail['Type'], $row_cdetail['Amount']);
				break;
				case 'Category':
					if (!$this->getCatProduct($row_cdetail)) {
						$deleteInvalidCoupon = "DELETE FROM shopping_cart WHERE ".$this->getSessionId()." AND `Type`='Coupon' 
												AND ProductID='".$row_cdetail['ProductID']."'";
						mysql_query($deleteInvalidCoupon);
						return '';
					}
				break;
				//case 'Shipping':
					//	$this->getCatProduct($row_cdetail['ApplyOption'], $row_cdetail['Type'], $row_cdetail['Amount']);
				//break;
				case 'Manufacturer':
					$this->getManuProduct($row_cdetail['ApplyOption'], $row_cdetail['Type'], $row_cdetail['Amount']);
				break;
			}
			return $this->formatPrice($row_cdetail['Type'], $row_cdetail['Amount']);
			
		}
	}
	
	public function isSpecial($productId = 0) {
		$sql_specialsku = "SELECT SpecialPrice, isSpecial FROM products WHERE id =".$productId." AND ((DATE_FORMAT(SpecialFrom, '%Y-%m-%d') <= DATE_FORMAT(current_date, '%Y-%m-%d')  OR SpecialFrom='') AND (DATE_FORMAT(current_date, '%Y-%m-%d') <= DATE_FORMAT(SpecialTo, '%Y-%m-%d') OR SpecialTo='')) AND isSpecial!='' LIMIT 1";
		$result_specialsku = mysql_query($sql_specialsku);
		$row_specialsku = @mysql_fetch_assoc($result_specialsku);
		return $row_specialsku["isSpecial"] != "True"? true:false;
	}
	
	public function isCustGroupExist($group = null) {
		$sql_cg = "SELECT id FROM customers WHERE EmailAddress='".$_SESSION['email']."' AND CustomerGroup='".$group."'";
		$result_cg = mysql_query($sql_cg);
		$num_cg = @mysql_num_rows($result_cg);
		return $num_cg>0 ? true:false;
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
}