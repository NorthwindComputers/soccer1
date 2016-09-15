<?php
/**
 * product detail page include file
 *
 * Updated: 25 February 2016
 * By: Richard Tuttle
 */

session_start();
require_once '../cpadmin/includes/db.php';

if($_POST["type"] == "initSizeS") {
	$proid = $_POST["proid"];
	$size = $_POST["size"];
	$set = $_POST["set"];
	$color = $_POST["color"];
	$_SESSION["singleitems"]["items"][$proid]["size"][$set] = $size;
	$_SESSION["singleitems"]["items"][$proid]["color"][$set] = $color;
	$_SESSION["singleitems"]["items"][$proid]["sid"] = $proid;
}
	
if($_POST["type"] == "initGenders") {
	$proid = $_POST["proid"];
	$size = $_POST["size"];
	$set = $_POST["set"];
	$_SESSION["singleitems"]["itemgender"][$proid]["gender"][$set] = $size;			
	$_SESSION["singleitems"]["itemgender"][$proid]["sid"] = $proid;
}
	
if($_POST["type"] == "initColors") {
	$proid = $_POST["proid"];
	$size = $_POST["size"];
	$set = $_POST["set"];
	$_SESSION["singleitems"]["itemcolor"][$proid]["color"][$set] = $size;			
	$_SESSION["singleitems"]["itemcolor"][$proid]["sid"] = $proid;
}

if($_POST["type"] == "initSingleColor") {
	$proid = $_POST["proid"];
	$color = $_POST["color"];
	$_SESSION["singleitems"][$proid]["color"] = $color;
	echo $_SESSION["singleitems"][$proid]["color"];
}

// type --> addCart
if($_POST["type"] == "addCart") {
	foreach($_POST as $key=>$value) {
		$$key = $value;
	}
	
	$gender = stripslashes($gender);
	$gender = addslashes($gender);

	// checks whether a VIP member of not based upon Session's email address and sets additional SQL coding
	if($_SESSION["email"] != '') {
		$where = " OR EmailAddress = '$_SESSION[email]' ";
		$sql_status = "SELECT Status, VIPLevel, VIPExpDate FROM customers WHERE EmailAddress='$_SESSION[email]' AND current_date()<=VIPExpDate LIMIT 1";
		$result_status = mysql_query($sql_status) or die(mysql_error());
		$row_status = mysql_fetch_assoc($result_status);
		$Status = $row_status["Status"];
		$VIPLevel = $row_status["VIPLevel"];
	} else {
		$where = "";
		$status = "NonMember";
	}

	// checks shopping cart for number of rows based on either the session id or the session email address
	$sql_chkprod = "SELECT * FROM shopping_cart WHERE ProductID=$id AND (SessionID='".session_id()."' $where)";
	$result_chkprod = mysql_query($sql_chkprod) or die("Shopping Cart item error: " . mysql_error());
	$num_chkprod = mysql_num_rows($result_chkprod);
	
	// testing only
	// echo "num_chkprod: " . $num_chkprod . " / producttype: " . $producttype . "<br />";
				
	// if rows exist and are not bundles then execute
	if ($num_chkprod > 0 && $producttype != 'bundle') {
        $color = $_SESSION["singleitems"]["items"][$id]["color"][0];
        // $color = $_SESSION["singleitems"][$id]["color"]; // original code   
		$sql_titems = "SELECT SUM(Qty) AS TotalItems FROM shopping_cart WHERE ProductID=$id AND (SessionID='".session_id()."' $where)";
		$result_titems = mysql_query($sql_titems);
		$row_titems = mysql_fetch_assoc($result_titems);
		$found = "No";
		$totalqty = $qty + $row_titems["TotalItems"];
		while ($row_chkprod = mysql_fetch_array($result_chkprod)) {
			$sql_prod = "SELECT * FROM products WHERE id=$id LIMIT 1";
			$result_prod = mysql_query($sql_prod);
			$row_prod = mysql_fetch_assoc($result_prod);
			$isSpecial = $row_prod["isSpecial"];
			$SpecialPrice = $row_prod["SpecialPrice"];
			$RootSKU = $row_prod["RootSKU"];
			$specialFrom = $row_prod["SpecialFrom"];
			$specialTo = $row_prod["SpecialTo"];
			$todays_date = date("Y-m-d");
			$today = strtotime($todays_date);
			$startDate = strtotime($specialFrom);
			$endDate = strtotime($specialTo);

			// is it on special price?
			if (($isSpecial == "True") && ($endDate > $today)) {
				$price = $SpecialPrice;
				$VIPprice = $SpecialPrice;
			} else {
				// get correct product pricing
				$sql_price = "SELECT * FROM product_pricing WHERE Gender='".addslashes($row_chkprod["Gender"])."' AND ProductID=$id LIMIT 1";
				$result_price = mysql_query($sql_price) or die("Pricing error: " . mysql_error());
				$row_price = mysql_fetch_assoc($result_price);
				$price = $row_price["NonMember"];
				$VIPprice = 0;
					
				$pos1 = strpos($row_price["Option1"], "-");
				if ($pos1 === false) {
					$opt1 = $row_price["Option1"];
				} else {
					$opt1 = explode("-", $row_price["Option1"]);
					$opt1 = $opt1[1];
				}

				$pos2 = strpos($row_price["Option2"], "-");
				if ($pos2 === false) {
					$opt2 = $row_price["Option2"];
				} else {
					$opt2 = explode("-", $row_price["Option2"]);
					$opt2 = $opt2[1];
				}

				$pos3 = strpos($row_price["Option3"], "-");
				if ($pos3 === false) {
					$opt3 = $row_price["Option3"];
				} else {
					$opt3 = explode("-", $row_price["Option3"]);
					$opt3 = $opt3[1];
				}

				$pos4 = strpos($row_price["Option4"], "-");
				if ($pos4 === false) {
					$opt4 = $row_price["Option4"];
				} else {
					$opt4 = explode("-", $row_price["Option4"]);
					$opt4 = $opt4[1];
				}

				$price = $row_price["NonMember"];
				if ($totalqty <= $opt1) {
					$VIPprice = $row_price["Option1Price"];
				} elseif ($totalqty <= $opt2) {
					$VIPprice = $row_price["Option2Price"];
				} elseif ($totalqty <= $opt3) {
					$VIPprice = $row_price["Option3Price"];
				} else {
					$VIPprice = $row_price["Option4Price"];
				}
					
				if ($price == '') { 
					$price = 0; 
				}
				if ($VIPprice == '') { 
					$VIPprice = 0; 
				}
			}
				
			$sql_special_price = "SELECT SpecialPrice, SpecialFrom, SpecialTo, isSpecial FROM products WHERE id=".$id." AND isSpecial!='' LIMIT 1";
			$result_special_price = mysql_query($sql_special_price);
		  	$row_special_price = mysql_fetch_array($result_special_price);
	    	$specialPrice = number_format($row_special_price['SpecialPrice'], 2);
	    	$specialFrom = $row_special_price["SpecialFrom"];
			$specialTo = $row_special_price["SpecialTo"];
			$todays_date = date("Y-m-d");
			$today = strtotime($todays_date);
			$startDate = strtotime($specialFrom);
			$endDate = strtotime($specialTo);
	    	if (($row_special_price['isSpecial'] == "True") && ($endDate > $today)) {
				$price = $specialPrice;
				$VIPprice = $specialPrice;
			}
			// end Sale

			// free NOT set
			if (!isset($_POST["free"])) {
				if($row_chkprod["Gender"] == str_replace("\\","",$gender) && $row_chkprod["ColorSKU"] == $color) {
					$sql_update = "UPDATE shopping_cart SET Qty=Qty+$qty, Price=$price, VIPPrice=$VIPprice WHERE id=$row_chkprod[id] LIMIT 1";
					$found = "Yes";
				} else {
					$sql_update = "UPDATE shopping_cart SET Price=$price, VIPPrice=$VIPprice WHERE id=$row_chkprod[id] LIMIT 1";
				}
				mysql_query($sql_update);
			}
		} // end while loop

		if ($found != "Yes") {
			if (($isSpecial == "True") && ($endDate > $today)) {
				$price = $SpecialPrice;
				$VIPprice = $SpecialPrice;
			} else {
				$sql_price = "SELECT * FROM product_pricing WHERE Gender='$gender' AND ProductID=$id LIMIT 1";
				$result_price = mysql_query($sql_price);
				$row_price = mysql_fetch_assoc($result_price);
				$price = $row_price["NonMember"];
				$VIPprice = 0;
					
				$pos1 = strpos($row_price["Option1"], "-");
				if($pos1 === false) {
					$opt1 = $row_price["Option1"];
				} else {
					$opt1 = explode("-", $row_price["Option1"]);
					$opt1 = $opt1[1];
				}

				$pos2 = strpos($row_price["Option2"], "-");
				if($pos2 === false) {
					$opt2 = $row_price["Option2"];
				} else {
					$opt2 = explode("-", $row_price["Option2"]);
					$opt2 = $opt2[1];
				}

				$pos3 = strpos($row_price["Option3"], "-");
				if($pos3 === false) {
					$opt3 = $row_price["Option3"];
				} else {
					$opt3 = explode("-", $row_price["Option3"]);
					$opt3 = $opt3[1];
				}

				$pos4 = strpos($row_price["Option4"], "-");
				if($pos4 === false) {
					$opt4 = $row_price["Option4"];
				} else {
					$opt4 = explode("-", $row_price["Option4"]);
					$opt4 = $opt4[1];
				}

				$price = $row_price["NonMember"];
				if ($totalqty <= $opt1) {
					$VIPprice = $row_price["Option1Price"];
				} elseif ($totalqty <= $opt2) {
					$VIPprice = $row_price["Option2Price"];
				} elseif ($totalqty <= $opt3) {
					$VIPprice = $row_price["Option3Price"];
				} else {
					$VIPprice = $row_price["Option4Price"];
				}	
			}
			
			if($price == '') {
				$price = 0; 
			}
			if($VIPprice == '') { 
				$VIPprice = 0; 
			}
			
			$b_count2 = sizeof($_SESSION["singleitems"]["itemcolor"]);
			$lk = 0;
			$se = "";
			if ($b_count2 > 0) {
				foreach ($_SESSION["singleitems"]["itemcolor"] as $key => $value) {								
					$p_id = $_SESSION['singleitems']['itemcolor'][$key]['sid'];		
					if (!empty($p_id)) {
						$sizeCount1 = array_count_values($_SESSION['singleitems']['itemcolor'][$key]['color']); 
						if (sizeof($_SESSION['singleitems']['itemcolor'][$key]['color']) > 0) {
							foreach($sizeCount1 as $colorSKU => $colorQty) {
								$sql_coloradd = "SELECT DISTINCT ColorSKU, ColorAddPrice FROM product_options WHERE ProductID=$key AND ColorSKU='$colorSKU' LIMIT 1";
								$result_coloradd = mysql_query($sql_coloradd);
								$num_coloradd = mysql_num_rows($result_coloradd);
								if ($num_coloradd > 0) {
									$row_coloradd = mysql_fetch_assoc($result_coloradd);
									$price = $price + $row_coloradd["ColorAddPrice"];
									$VIPprice = $VIPprice + $row_coloradd["ColorAddPrice"];
								}
							}
					  	}
					}
				}
			}

			$b_count1 = sizeof($_SESSION["singleitems"]["items"]);
			$lk = 0;
			$se = "";
			if ($b_count1 > 0) {
				foreach ($_SESSION["singleitems"]["items"] as $key => $value) {								
					if (!empty($_SESSION['singleitems']['items'][$key]['sid'])) {
						$sizeCount1 = array_count_values($_SESSION['singleitems']['items'][$key]['size']);
						if (sizeof($sizeCount) > 0) {
							foreach($sizeCount1 as $sizeSKU => $sizeQty) {
								$sql_sizeadd = "SELECT DISTINCT SizeSKU, SizeAddPrice FROM product_options WHERE ProductID=$id AND SizeSKU='$sizeSKU' LIMIT 1";
								$result_sizeadd = mysql_query($sql_sizeadd);
								$num_sizeadd = mysql_num_rows($result_sizeadd);
								if($num_sizeadd > 0) {
									$row_sizeadd = mysql_fetch_assoc($result_sizeadd);
									$price = $price + $row_sizeadd["SizeAddPrice"];
									$VIPprice = $VIPprice + $row_sizeadd["SizeAddPrice"];
								}
							}
					  	}
					}
				}
			}

			$sql_prod = "SELECT * FROM products WHERE id=$id LIMIT 1";
			$result_prod = mysql_query($sql_prod);
			$row_prod = mysql_fetch_assoc($result_prod);
			$RootSKU = $row_prod["RootSKU"];
			$sql_special_price = "SELECT SpecialPrice, SpecialFrom, SpecialTo, isSpecial FROM products WHERE id =".$id." AND isSpecial!='' LIMIT 1";
			$result_special_price = mysql_query($sql_special_price);
		  	$row_special_price = mysql_fetch_array($result_special_price);
	    	$specialPrice = number_format($row_special_price['SpecialPrice'], 2);
	    	$specialFrom = $row_special_price["SpecialFrom"];
			$specialTo = $row_special_price["SpecialTo"];
			$todays_date = date("Y-m-d");
			$today = strtotime($todays_date);
			$startDate = strtotime($specialFrom);
			$endDate = strtotime($specialTo);
	    	if (($row_special_price['isSpecial'] == "True") && ($endDate > $today)) {
				$price = $specialPrice;
				$VIPprice = $specialPrice;
			}
            if ($found == "No") {
				if (!isset($_POST["free"])) {
					if ($color != '') {
						$color_sku = $color;
					} else {
						$color_sku = $_SESSION["singleitems"]["items"][$id]["color"][0];
					}
					$sql_prod_option = "select * from  products WHERE id=$id LIMIT 1";
		      		$query_res = mysql_query($sql_prod_option);
		      		$row_res = mysql_fetch_assoc($query_res); 
			  		$option1_res = $row_res['option_seting_1'];
			  		if ($option1_res == 2) {
						$sql_add = "INSERT INTO shopping_cart(SessionID, EmailAddress, ProductID, ProductName, RootSKU, ColorSKU,  Qty, Gender, GenderSKU, VIPPrice, Price, CreatedDate, Type) ";
						$sql_add .= "VALUES('".session_id()."','$_SESSION[email]', $id, '$productname', '$RootSKU', '$color_sku',$qty, '$gender', '$gendersku', $VIPprice, $price, current_date, 'Product')";
			  		} else {
						$sql_add = "INSERT INTO shopping_cart(SessionID, EmailAddress, ProductID, ProductName, RootSKU, ColorSKU, SizeSKU, Qty, Gender, GenderSKU, VIPPrice, Price, CreatedDate, Type) ";
						$sql_add .= "VALUES('".session_id()."','$_SESSION[email]', $id, '$productname', '$RootSKU', '$color_sku','$size', $qty, '$gender', '$gendersku', $VIPprice, $price, current_date, 'Product')";
			  		}
					mysql_query($sql_add);
					$lastid = mysql_insert_id();			
            		$session_id = session_id();
					$b_count = sizeof($_SESSION["singleitems"]["items"]);
					$lk = 0;
					$se = "";
             
			   		if ($b_count > 0) {
						foreach($_SESSION["singleitems"]["items"] as $key => $value) {					
							$p_id = $_SESSION['singleitems']['items'][$key]['sid'];		
							if (!empty($p_id)) {
								$sizeCount = array_count_values($_SESSION['singleitems']['items'][$key]['size']); 
								$sizeColor = $_SESSION['singleitems']['itemcolor'][$key]['color']; 
								if (sizeof($_SESSION['singleitems']['items'][$key]['size']) > 0) {
									$i = 0;
									foreach ($sizeCount as $sizeSKU => $sizeQty) {
										$sql_bitem = "SELECT ProductDetailName, RootSKU FROM products WHERE id=".$_SESSION['singleitems']['items'][$key]['sid'] ." LIMIT 1";
										$result_bitem = mysql_query($sql_bitem);					
										$row_bitem = mysql_fetch_assoc($result_bitem) or die(mysql_error());
										$bproductname = $row_bitem["ProductDetailName"];
										$bRootSKU = $row_bitem["RootSKU"];
										$bid = $_SESSION["singleitems"]["items"][$key]["sid"];
                                		if ($sizeColor[$i] != "") {
											// $color = $sizeColor[$i];
											$color = $_SESSION["singleitems"]["items"]["sid"]["color"][0];
										} else {
											// $color = $_SESSION["singleitems"]["items"]["sid"]["color"][0];
										}
										
										// executed when a single bundle already exists in db
										// echo "<script>alert('SESSION DATA 1: ".var_dump($_SESSION)."');</script>"; // testing only
                                		// $color = $_SESSION["singleitems"]["items"]["sid"]["color"][0];
                                		$color = $color_sku;
										$sql_addb = "INSERT INTO shopping_cart_single SET SessionID='".session_id()."', EmailAddress='$_SESSION[email]', ProductID='".$key."', ProductName='$bproductname', RootSKU='".$bRootSKU."', SizeSKU='".$sizeSKU."', ColorSKU='".$color."', Qty='".$sizeQty."', CreatedDate=current_date, Type='single', singleid=$lastid";
										if (!mysql_query($sql_addb)) {
											echo "error adding bundle item: ".$sql_addb();
										}	
                                		$i++; 
									}
								} 
						 	}
						} // foreach
				 		die();
					} else {
						$b_countc = sizeof($_SESSION["singleitems"]["itemcolor"]);
						$lk = 0;
						$se = "";
			
	   					if ($b_countc > 0) {
							foreach ($_SESSION["singleitems"]["itemcolor"] as $key => $value) {											
								$p_id = $_SESSION['singleitems']['itemcolor'][$key]['sid'];		
								if (!empty($p_id)) {
									$sizeCount = array_count_values($_SESSION['singleitems']['itemcolor'][$key]['color']); // count each value
									$sizeColor = $_SESSION['singleitems']['itemcolor'][$key]['color']; // count each value
									if (sizeof($_SESSION['singleitems']['itemcolor'][$key]['color']) > 0) {
										$i = 0;
										foreach ($sizeCount as $colorSKU => $colorQty) {
											if ($colorSKU == '') {
												$colorSKU = $color;
											}
											$sql_bitem = "SELECT ProductDetailName, RootSKU FROM products WHERE id=".$_SESSION['singleitems']['itemcolor'][$key]['sid'] ." LIMIT 1";
											$result_bitem = mysql_query($sql_bitem);					
											$row_bitem = mysql_fetch_assoc($result_bitem) or die(mysql_error());
											$bproductname = $row_bitem["ProductDetailName"];
											$bRootSKU = $row_bitem["RootSKU"];
											$bid = $_SESSION["singleitems"]["items"][$key]["sid"];
                                    		$sizeSKU = "";
                                    		// echo "<script>alert('SESSION DATA 2: ".var_dump($_SESSION)."');</script>"; // testing only
											$sql_addb = "INSERT INTO shopping_cart_single SET SessionID='".session_id()."', EmailAddress='$_SESSION[email]', ProductID='".$key."', ProductName='$bproductname', RootSKU='".$bRootSKU."', SizeSKU='".$sizeSKU."', ColorSKU='".$colorSKU."', Qty='".$colorQty."', CreatedDate=current_date, Type='single', singleid=$lastid";
											if (!mysql_query($sql_addb)) {
												echo "error adding bundle item: ".$sql_addb();
											}		
                                			$i++; 
										}
									}
					 			}
							}
							die();
						}
					} // end else
				}
			}
		} // end found != yes

		// if FREE is TRUE
		if (isset($_POST["free"])) {
			$sql_prod_option = "select * from products WHERE id=$id LIMIT 1";
			$query_res = mysql_query($sql_prod_option) or die(mysql_error());
			$row_res = mysql_fetch_assoc($query_res); 
			$option1_res = $row_res['option_seting_1'];
			if ($option1_res == 2) {
				$sql_update1 = "UPDATE shopping_cart set RootSKU='$RootSKU', ColorSKU='$color', Gender='$gender', GenderSKU= '$gendersku' where id=$psid and ProductID=$id and SessionID='".session_id()."'";
				mysql_query($sql_update1) or die("ERROR: " .mysql_error());
			} else {
				$sql_update1 = "UPDATE shopping_cart SET ProductID='$id', RootSKU='$RootSKU', ColorSKU='$_POST[color]', Qty='$qty', SizeSKU='$size', Gender='$gender', GenderSKU='$gendersku', VIPPrice='0.00', Price='0', Type='Product' WHERE ProductID='".$id."' AND Type='C_Product' AND SessionID='".session_id()."'";
				$sql_update2 = "UPDATE shopping_cart SET Type='CouponUsed' WHERE id='".$psid."' AND Type='Coupon' AND SessionID='".session_id()."'";
				mysql_query($sql_update1) or die("ERROR: " .mysql_error());
				mysql_query($sql_update2) or die("ERROR: " .mysql_error());
				$sql_update3 = "SELECT ProductName FROM shopping_cart WHERE id='".$psid."' AND SessionID='".session_id()."' LIMIT 1";
				// echo "SQL 3 -> " . $sql_update3 . "<br />"; exit; // testing use only
				$newName = mysql_query($sql_update3) or die("ERROR: " . mysql_error());
				$rowName = mysql_fetch_assoc($newName);
				$sql_update4 = "UPDATE shopping_cart SET ProductName='".$rowName["ProductName"]."' WHERE ProductID='".$id."'";
				// echo "SQL 4 -> " . $sql_update4 . "<br />"; exit; // testing use only
				mysql_query($sql_update4) or die("ERROR: " .mysql_error());
			}
		}
		echo "Item Added!";
		// echo "<script>window.location='cart.php';</script>";
		if ($_SESSION["email"] != "") {
			echo "<script>window.location.reload();</script>";
		} else {
			echo "<script>window.location='myaccount.php';</script>";
		}
	} elseif($producttype != 'bundle') {
		$sql_prod = "SELECT RootSKU, isSpecial, SpecialPrice, SpecialFrom, SpecialTo FROM products WHERE id=$id LIMIT 1";
		$result_prod = mysql_query($sql_prod);
		$row_prod = mysql_fetch_assoc($result_prod);
		$isSpecial = $row_prod["isSpecial"];
		$SpecialPrice = $row_prod["SpecialPrice"];
		$RootSKU = $row_prod["RootSKU"];
		$specialFrom = $row_prod["SpecialFrom"];
		$specialTo = $row_prod["SpecialTo"];
		$todays_date = date("Y-m-d");
		$today = strtotime($todays_date);
		$startDate = strtotime($specialFrom);
		$endDate = strtotime($specialTo);

		if (($isSpecial == "True") && ($endDate > $today)) {
			$price = $SpecialPrice;
			$VIPprice = $SpecialPrice;
		} else {
			$sql_price = "SELECT * FROM product_pricing WHERE Gender='$gender' AND ProductID=$id LIMIT 1";
			$result_price = mysql_query($sql_price);
			$row_price = mysql_fetch_assoc($result_price);
			$price = $row_price["NonMember"];
			$VIPprice = 0;
				
			$pos1 = strpos($row_price["Option1"], "-");
			if($pos1 === false) {
				$opt1 = $row_price["Option1"];
			} else {
				$opt1 = explode("-", $row_price["Option1"]);
				$opt1 = $opt1[1];
			}

			$pos2 = strpos($row_price["Option2"], "-");
			if($pos2 === false) {
				$opt2 = $row_price["Option2"];
			} else {
				$opt2 = explode("-", $row_price["Option2"]);
				$opt2 = $opt2[1];
			}

			$pos3 = strpos($row_price["Option3"], "-");
			if($pos3 === false) {
				$opt3 = $row_price["Option3"];
			} else {
				$opt3 = explode("-", $row_price["Option3"]);
				$opt3 = $opt3[1];
			}

			$pos4 = strpos($row_price["Option4"], "-");
			if($pos4 === false) {
				$opt4 = $row_price["Option4"];
			} else {
				$opt4 = explode("-", $row_price["Option4"]);
				$opt4 = $opt4[1];
			}

			$price = $row_price["NonMember"];
			if ($totalqty <= $opt1) {
				$VIPprice = $row_price["Option1Price"];
			} elseif ($totalqty <= $opt2) {
				$VIPprice = $row_price["Option2Price"];
			} elseif ($totalqty <= $opt3) {
				$VIPprice = $row_price["Option3Price"];
			} else {
				$VIPprice = $row_price["Option4Price"];
			}
		}

		if ($price == '') { $price = 0; }
		if ($VIPprice == '') { $VIPprice = 0; }

        $b_count2 = sizeof($_SESSION["singleitems"]["itemcolor"]);
		$lk = 0;
		$se = "";

		if ($b_count2 > 0) {
			foreach( $_SESSION["singleitems"]["itemcolor"] as $key => $value) {								
                $p_id = $_SESSION['singleitems']['itemcolor'][$key]['sid'];		
				if (!empty($p_id)) {
					$sizeCount1 = array_count_values($_SESSION['singleitems']['itemcolor'][$key]['color']);
					if (sizeof($_SESSION['singleitems']['itemcolor'][$key]['color']) > 0) {
						foreach( $sizeCount1 as $colorSKU => $colorQty) {
							$sql_coloradd = "SELECT DISTINCT ColorSKU, ColorAddPrice FROM product_options WHERE ProductID=$key AND ColorSKU='$colorSKU' LIMIT 1";
							$result_coloradd = mysql_query($sql_coloradd);
							$num_coloradd = mysql_num_rows($result_coloradd);
							if($num_coloradd > 0) {
								$row_coloradd = mysql_fetch_assoc($result_coloradd);
								$price = $price + $row_coloradd["ColorAddPrice"];
								$VIPprice = $VIPprice + $row_coloradd["ColorAddPrice"];
							}
						}
					  }
				}
			}
		}

		$b_count1 = sizeof($_SESSION["singleitems"]["items"]);
		$lk = 0;
		$se = "";

		if ($b_count1 > 0){
			foreach ($_SESSION["singleitems"]["items"] as $key => $value) {								
                $p_id=$_SESSION['singleitems']['items'][$key]['sid'];		
				if (!empty($p_id)) {
					$sizeCount1 = array_count_values($_SESSION['singleitems']['items'][$key]['size']);
					if (sizeof($_SESSION['singleitems']['items'][$key]['size']) > 0) {
						foreach($sizeCount1 as $sizeSKU => $sizeQty) {
							$sql_sizeadd = "SELECT DISTINCT SizeSKU, SizeAddPrice FROM product_options WHERE ProductID=$id AND SizeSKU='$sizeSKU' LIMIT 1";
							$result_sizeadd = mysql_query($sql_sizeadd);
							$num_sizeadd = mysql_num_rows($result_sizeadd);
							if ($num_sizeadd > 0) {
								$row_sizeadd = mysql_fetch_assoc($result_sizeadd);
								$price = $price + $row_sizeadd["SizeAddPrice"];
								$VIPprice = $VIPprice + $row_sizeadd["SizeAddPrice"];
							}
						}
					}
				}
			}
		}
	
		$sql_special_price = "SELECT SpecialPrice, SpecialFrom, SpecialTo, isSpecial FROM products WHERE id =".$id." AND isSpecial!='' LIMIT 1";
		$result_special_price = mysql_query($sql_special_price);
		$row_special_price = mysql_fetch_array($result_special_price);
	    $specialPrice = number_format($row_special_price['SpecialPrice'], 2);
	    $specialFrom = $row_special_price["SpecialFrom"];
		$specialTo = $row_special_price["SpecialTo"];
		$todays_date = date("Y-m-d");
		$today = strtotime($todays_date);
		$startDate = strtotime($specialFrom);
		$endDate = strtotime($specialTo);

	    if (($row_special_price['isSpecial'] == "True") && ($endDate > $today)) {
			$price = $specialPrice;
			$VIPprice = $specialPrice;
		}

		$color_sku = $_SESSION["singleitems"][$id]["color"];
		$sql_prod_option = "select * from  products WHERE id=$id LIMIT 1";
		$query_res = mysql_query($sql_prod_option);
		$row_res = mysql_fetch_assoc($query_res); 
		$option1_res = $row_res['option_seting_1'];
		if ($option1_res == 2) {
			$sql_add = "INSERT INTO shopping_cart(SessionID, EmailAddress, ProductID, ProductName, RootSKU, ColorSKU, Qty, Gender, GenderSKU, VIPPrice, Price, CreatedDate, Type) ";
			$sql_add .= "VALUES('".session_id()."','$_SESSION[email]', $id, '$productname', '$RootSKU', '$color_sku', $qty, '$gender', '$gendersku', $VIPprice, $price, current_date, 'Product')";
		} else {
			$sql_add = "INSERT INTO shopping_cart(SessionID, EmailAddress, ProductID, ProductName, RootSKU, ColorSKU, SizeSKU, Qty, Gender, GenderSKU, VIPPrice, Price, CreatedDate, Type) ";
			$sql_add .= "VALUES('".session_id()."','$_SESSION[email]', $id, '$productname', '$RootSKU', '$color_sku','$size', $qty, '$gender', '$gendersku', $VIPprice, $price, current_date, 'Product')";
		}

		if (!mysql_query($sql_add)) {
			echo "Error adding item: ".mysql_error();
		}

	    $lastid = mysql_insert_id();			
        $session_id = session_id();
		if (sizeof($_SESSION["radioButtonSelected"][$session_id]) > 0) {
			foreach($_SESSION["radioButtonSelected"][$session_id] as $parent_tab_id => $value) {
				if (sizeof($value) > 0) {
					foreach($value as $child_tab_id => $image_details) {
						$selected_image_id = $image_details["colors_images_id"];
						$imprint_price = $image_details["price"];
						$imprint_idoption = $image_details["idoption"];
						$sql_addfor_imprint = "INSERT INTO shopping_cart_imprintdata(cart_id, parentid, childid, optionid, selected_image_id, price)  ";
						$sql_addfor_imprint .= "VALUES('$lastid', '$parent_tab_id', '$child_tab_id', '$imprint_idoption', '$selected_image_id', '$imprint_price' )";
						if (!empty($selected_image_id))
							mysql_query($sql_addfor_imprint);
					}
				}	
			}
		}

		$_SESSION["radioButtonSelected"] = array();
		$b_count = sizeof($_SESSION["singleitems"]["items"]);
		$lk = 0;
		$se = "";
			
		// single bundle items
		if ($b_count > 0) {
			foreach ($_SESSION["singleitems"]["items"] as $key => $value) {		
				$p_id = $_SESSION['singleitems']['items'][$key]['sid'];		
				if (!empty($p_id)) {
					$sizeCount = array_count_values($_SESSION['singleitems']['items'][$key]['size']);
					$sizeColor = $_SESSION['singleitems']['itemcolor'][$key]['color'];
					if (sizeof($_SESSION['singleitems']['items'][$key]['size']) > 0) {
						$i = 0;
						foreach ($sizeCount as $sizeSKU => $sizeQty) {
							$sql_bitem = "SELECT ProductDetailName, RootSKU FROM products WHERE id=".$_SESSION['singleitems']['items'][$key]['sid'] ." LIMIT 1";
							$result_bitem = mysql_query($sql_bitem);					
							$row_bitem = mysql_fetch_assoc($result_bitem) or die(mysql_error());
							$bproductname = $row_bitem["ProductDetailName"];
							$bRootSKU = $row_bitem["RootSKU"];
							$bid = $_SESSION["singleitems"]["items"][$key]["sid"];
                             if ($sizeColor[$i] != "") {
								// $color = $sizeColor[$i];
								// $color = $_SESSION["singleitems"]["items"]["sid"]["color"][0];
							} else {
								// $color = $_SESSION["singleitems"][$id]["color"];
								//echo "<script>alert('COLOR: " . $color . "');</script>";
								//echo "<script>alert('ID: " . $id . "');</script>";
								//echo "<script>alert('KEY: " . $key . "');</script>";
								// $color = $_SESSION["singleitems"]["items"]["sid"]["color"][0];
							}
							// var_dump($_SESSION); // testing only
 							// $color = $_SESSION["singleitems"][$id]["color"];
 							$color = $_SESSION["singleitems"]["items"][$id]["color"][0];
							$sql_addb = "INSERT INTO shopping_cart_single SET SessionID='".session_id()."', EmailAddress='$_SESSION[email]', ProductID='".$key."', ProductName='$bproductname', RootSKU='".$bRootSKU."', SizeSKU='".$sizeSKU."', ColorSKU='".$color."', Qty='".$sizeQty."', CreatedDate=current_date, Type='single', singleid=$lastid";

							if (!mysql_query($sql_addb)) {
								echo "error adding bundle item: ".$sql_addb();
							} //end if		
                            $i++; 
						}
					} // end if
				} // end if
		} // foreach
		die();
	} else {
		$b_countc = sizeof($_SESSION["singleitems"]["itemcolor"]);
		$lk = 0;
		$se = "";
		if ($b_countc > 0) {
			foreach ($_SESSION["singleitems"]["itemcolor"] as $key => $value) {		
				$p_id = $_SESSION['singleitems']['itemcolor'][$key]['sid'];		
				if (!empty($p_id)) {
					$sizeCount = array_count_values($_SESSION['singleitems']['itemcolor'][$key]['color']); 
					$sizeColor = $_SESSION['singleitems']['itemcolor'][$key]['color'];
					if (sizeof($_SESSION['singleitems']['itemcolor'][$key]['color']) > 0) {
						$i = 0;
						foreach ($sizeCount as $colorSKU => $colorQty) {
							$sql_bitem = "SELECT ProductDetailName, RootSKU FROM products WHERE id=".$_SESSION['singleitems']['itemcolor'][$key]['sid'] ." LIMIT 1";
							$result_bitem = mysql_query($sql_bitem);					
							$row_bitem = mysql_fetch_assoc($result_bitem) or die(mysql_error());
							$bproductname = $row_bitem["ProductDetailName"];
							$bRootSKU = $row_bitem["RootSKU"];
							$bid = $_SESSION["singleitems"]["items"][$key]["sid"];
                            $sizeSKU = "";
                            // echo "<script>alert('SESSION DATA 4: ".var_dump($_SESSION)."');</script>"; // testing only
							$sql_addb = "INSERT INTO shopping_cart_single SET SessionID='".session_id()."', EmailAddress='$_SESSION[email]', ProductID='".$key."', ProductName='$bproductname', RootSKU='".$bRootSKU."', SizeSKU='".$sizeSKU."', ColorSKU='".$colorSKU."', Qty='".$colorQty."', CreatedDate=current_date, Type='single', singleid=$lastid";
							if (!mysql_query($sql_addb)) {
								echo "error adding bundle item: ".$sql_addb();
							} //end if		
                            $i++; 
						}
					} //end if
				} //end if
			} //foreach
			die();
		} //if
	}
	// echo "<script>window.location='cart.php';</script>";
	if ($_SESSION["email"] != "") {
			echo "<script>window.location.reload();</script>";
		} else {
			echo "<script>window.location='myaccount.php';</script>";
		}
	} elseif($producttype == 'bundle') {
		$sql_prod = "SELECT RootSKU, isSpecial, SpecialPrice, SpecialFrom, SpecialTo FROM products WHERE id=$id LIMIT 1";
		$result_prod = mysql_query($sql_prod);
		$row_prod = mysql_fetch_assoc($result_prod);
		$isSpecial = $row_prod["isSpecial"];
		$SpecialPrice = $row_prod["SpecialPrice"];
		$RootSKU = $row_prod["RootSKU"];
		$specialFrom = $row_prod["SpecialFrom"];
		$specialTo = $row_prod["SpecialTo"];
		$todays_date = date("Y-m-d");
		$today = strtotime($todays_date);
		$startDate = strtotime($specialFrom);
		$endDate = strtotime($specialTo);
		if (($isSpecial == "True") && ($endDate > $today)) {
			$price = $SpecialPrice;
			$VIPprice = $SpecialPrice;
		} else {
			$sql_price = "SELECT * FROM product_pricing WHERE Gender='$gender' AND ProductID=$id LIMIT 1";
			$result_price = mysql_query($sql_price);
			$row_price = mysql_fetch_assoc($result_price);
			$pos1 = strpos($row_price["Option1"], "-");
			if($pos1 === false) {
				$opt1 = $row_price["Option1"];
			} else {
				$opt1 = explode("-", $row_price["Option1"]);
				$opt1 = $opt1[1];
			}

			$pos2 = strpos($row_price["Option2"], "-");
			if($pos2 === false) {
				$opt2 = $row_price["Option2"];
			} else {
				$opt2 = explode("-", $row_price["Option2"]);
				$opt2 = $opt2[1];
			}

			$pos3 = strpos($row_price["Option3"], "-");
			if($pos3 === false) {
				$opt3 = $row_price["Option3"];
			} else {
				$opt3 = explode("-", $row_price["Option3"]);
				$opt3 = $opt3[1];
			}

			$pos4 = strpos($row_price["Option4"], "-");
			if($pos4 === false) {
				$opt4 = $row_price["Option4"];
			} else {
				$opt4 = explode("-", $row_price["Option4"]);
				$opt4 = $opt4[1];
			}

			$price = $row_price["NonMember"];
			if($qty <= $opt1) {
				$VIPprice = $row_price["Option1Price"];
			} elseif($qty<=$opt2) {
				$VIPprice = $row_price["Option2Price"];
			} elseif($qty<=$opt3) {
				$VIPprice = $row_price["Option3Price"];
			} else {
				$VIPprice = $row_price["Option4Price"];
			}

			if($VIPLevel != 0 && $VIPLevel != '') {
				if($VIPprice > $row_price["Option".$VIPLevel."Price"]) {
					$VIPprice = $row_price["Option".$VIPLevel."Price"];
				}
			}
		}

		if($price == '') { 
			$price = 0; 
		}
		if($VIPprice == '') { 
			$VIPprice = 0; 
		}

		$sql_special_price = "SELECT SpecialPrice, SpecialFrom, SpecialTo, isSpecial FROM products WHERE id =".$id." AND isSpecial!='' LIMIT 1";
		$result_special_price = mysql_query($sql_special_price);
		$row_special_price = mysql_fetch_array($result_special_price);
	    $specialPrice = number_format($row_special_price['SpecialPrice'], 2);
	    $specialFrom = $row_special_price["SpecialFrom"];
		$specialTo = $row_special_price["SpecialTo"];
		$todays_date = date("Y-m-d");
		$today = strtotime($todays_date);
		$startDate = strtotime($specialFrom);
		$endDate = strtotime($specialTo);

	    if (($row_special_price['isSpecial'] == "True") && ($endDate > $today)) {
			$price = $specialPrice;
			$VIPprice = $specialPrice;
		}
		$sql_add  = "INSERT INTO shopping_cart(SessionID, EmailAddress, ProductID, ProductName, RootSKU, Qty, Gender, GenderSKU, VIPPrice, Price, CreatedDate, Type) ";
		$sql_add .= "VALUES('".session_id()."', '$_SESSION[email]', $id, '$productname', '$RootSKU', $qty, '$gender', '$gendersku', $VIPprice, $price, current_date, 'Bundle')";
		if(!mysql_query($sql_add)) {
			echo "error adding product: ".mysql_error();
		}

		$lastid = mysql_insert_id();
		$b_count = count($bitems);
		for ($b=0; $b<$b_count; $b++) {
			$curitem = substr($bitems[$b], 0, 4);
			switch($curitem) {
				case "gend":
					$insert .= " Gender='$bvals[$b]', ";
					break;
				case "colo":
					$insert .= " ColorSKU='$bvals[$b]', ";
					break;
				case "size":
					$insert .= " SizeSKU='$bvals[$b]', ";
					break;
				case "bund":
					$start = substr($bitems[$b], 0, 4);
					$sql_bitem = "SELECT ProductDetailName, RootSKU FROM products WHERE id=$bvals[$b] LIMIT 1";
					$result_bitem = mysql_query($sql_bitem);
					$row_bitem = mysql_fetch_assoc($result_bitem);
					$bid = $bvals[$b];
					$bproductname = $row_bitem["ProductDetailName"];
					$bRootSKU = $row_bitem["RootSKU"];
					break;
			}

			if($start == substr($bitems[$b+1], 0, 4)) {
				$sql_addb = "INSERT INTO shopping_cart SET SessionID='".session_id()."', EmailAddress='$_SESSION[email]', ProductID=$bid, ProductName='$bproductname', RootSKU='$bRootSKU', Qty=1, $insert CreatedDate=current_date, Type='Bundle', BundleID=$lastid";
				if(!mysql_query($sql_addb)) {
					echo "error adding bundle item: ".mysql_error();
				}

				$curitem = '';
				$insert = '';
			}
		}

		//last item in bundle
		$sql_addb = "INSERT INTO shopping_cart SET SessionID='".session_id()."', EmailAddress='$_SESSION[email]', ProductID=$bid, ProductName='$bproductname', RootSKU='$bRootSKU', Qty=1, $insert CreatedDate=current_date, Type='Bundle', BundleID=$lastid";
		if (!mysql_query($sql_addb)) {
			echo "error adding bundle item: ".mysql_error();
		}
		$curitem = '';
		$insert = '';
		echo "Item Added!";
	}
	mysql_close($conn);
	exit();
} // end ADDCART

// type --> addCart2 
if($_POST["type"] == "addCart2") {
	foreach($_POST as $key=>$value) {
		$$key = $value;
	}
	$gender = stripslashes($gender);
	$gender = addslashes($gender);
	
	// get vip status
	if($_SESSION["email"] != '') {
		$where = " OR EmailAddress = '$_SESSION[email]' ";
		$sql_status = "SELECT Status, VIPLevel, VIPExpDate FROM customers WHERE EmailAddress='$_SESSION[email]' AND current_date()<=VIPExpDate LIMIT 1";
		$result_status = mysql_query($sql_status) or die("VIP Info Retrieval error: " . mysql_error());
		$row_status = mysql_fetch_assoc($result_status);
		$Status = $row_status["Status"];
		$VIPLevel = $row_status["VIPLevel"];
	} else {
		$where = "";
		$status = "NonMember";
	}
	
	// check products in shopping cart
	$sql_chkprod = "SELECT * FROM shopping_cart WHERE ProductID=$id AND (SessionID='".session_id()."' $where)";
	$result_chkprod = mysql_query($sql_chkprod) or die(mysql_error());
	$num_chkprod = mysql_num_rows($result_chkprod);
	// if number of products is greater than zero and NOT a bundle type
	if ($num_chkprod > 0 && $producttype != 'bundle') {
		$sql_titems = "SELECT SUM(Qty) AS TotalItems FROM shopping_cart WHERE ProductID=$id AND (SessionID='".session_id()."' $where)";
		$result_titems = mysql_query($sql_titems) or die(mysql_error());
		$row_titems = mysql_fetch_assoc($result_titems);
		$found = "No";
		$totalqty = $qty + $row_titems["TotalItems"];
		while ($row_chkprod = mysql_fetch_array($result_chkprod)) {
			$sql_prod = "SELECT * FROM products WHERE id=$id LIMIT 1";
			$result_prod = mysql_query($sql_prod) or die(mysql_error());
			$row_prod = mysql_fetch_assoc($result_prod);
			$isSpecial = $row_prod["isSpecial"];
			$SpecialPrice = $row_prod["SpecialPrice"];
			$RootSKU = $row_prod["RootSKU"];
			$specialFrom = $row_prod["SpecialFrom"];
			$specialTo = $row_prod["SpecialTo"];
			$todays_date = date("Y-m-d");
			$today = strtotime($todays_date);
			$startDate = strtotime($specialFrom);
			$endDate = strtotime($specialTo);
			if (($isSpecial == "True") && ($endDate > $today)) {
				$price = $SpecialPrice;
				$VIPprice = $SpecialPrice;
			} else {
				$sql_price = "SELECT * FROM product_pricing WHERE Gender='".addslashes($row_chkprod["Gender"])."' AND ProductID=$id LIMIT 1";
				$result_price = mysql_query($sql_price);
				$row_price = mysql_fetch_assoc($result_price);
				$price = $row_price["NonMember"];
				$VIPprice = 0;
				$pos1 = strpos($row_price["Option1"], "-");
				if($pos1 === false) {
					$opt1 = $row_price["Option1"];
				} else {
					$opt1 = explode("-", $row_price["Option1"]);
					$opt1 = $opt1[1];
				}

				$pos2 = strpos($row_price["Option2"], "-");
				if($pos2 === false) {
					$opt2 = $row_price["Option2"];
				} else {
					$opt2 = explode("-", $row_price["Option2"]);
					$opt2 = $opt2[1];
				}

				$pos3 = strpos($row_price["Option3"], "-");
				if($pos3 === false) {
					$opt3 = $row_price["Option3"];
				} else {
					$opt3 = explode("-", $row_price["Option3"]);
					$opt3 = $opt3[1];
				}

				$pos4 = strpos($row_price["Option4"], "-");
				if($pos4 === false) {
					$opt4 = $row_price["Option4"];
				} else {
					$opt4 = explode("-", $row_price["Option4"]);
					$opt4 = $opt4[1];
				}

				$price = $row_price["NonMember"];
				if ($totalqty <= $opt1) {
					$VIPprice = $row_price["Option1Price"];
				} elseif ($totalqty <= $opt2) {
					$VIPprice = $row_price["Option2Price"];
				} elseif ($totalqty <= $opt3) {
					$VIPprice = $row_price["Option3Price"];
				} else {
					$VIPprice = $row_price["Option4Price"];
				}
				if ($price == '') { 
					$price = 0; 
				}
				if ($VIPprice == '') { 
					$VIPprice = 0; 
				}
			}
	
			// product on special price?
			$sql_special_price = "SELECT SpecialPrice, SpecialFrom, SpecialTo, isSpecial FROM products WHERE id =".$id." AND isSpecial!='' LIMIT 1";
			$result_special_price = mysql_query($sql_special_price);
		  	$row_special_price = mysql_fetch_array($result_special_price);
	    	$specialPrice = number_format($row_special_price['SpecialPrice'], 2);
	    	$specialFrom = $row_special_price["SpecialFrom"];
			$specialTo = $row_special_price["SpecialTo"];
			$todays_date = date("Y-m-d");
			$today = strtotime($todays_date);
			$startDate = strtotime($specialFrom);
			$endDate = strtotime($specialTo);
	    	if (($row_special_price['isSpecial'] == "True") && ($endDate > $today)) {
				$price = $specialPrice;
				$VIPprice = $specialPrice;
			}
				
			// if free is NOT set
			if(!isset($_POST["free"])) {
				if($row_chkprod["Gender"] == str_replace("\\","",$gender) && $row_chkprod["SizeSKU"] == $size && $row_chkprod["ColorSKU"] == $color) {
					$sql_update = "UPDATE shopping_cart SET Qty=Qty+$qty, Price=$price, VIPPrice=$VIPprice WHERE id=$row_chkprod[id] LIMIT 1";
					$found = "Yes";
				} else {
					$sql_update = "UPDATE shopping_cart SET Price=$price, VIPPrice=$VIPprice WHERE id=$row_chkprod[id] LIMIT 1";
				}
				mysql_query($sql_update) or die(mysql_error());
			}
		} // end while loop

		if($found != "Yes") {
			if (($isSpecial == "True") && ($endDate > $today)) {
				$price = $SpecialPrice;
				$VIPprice = $SpecialPrice;
			} else {
				$sql_price = "SELECT * FROM product_pricing WHERE Gender='$gender' AND ProductID=$id LIMIT 1";
				$result_price = mysql_query($sql_price);
				$row_price = mysql_fetch_assoc($result_price);
				$price = $row_price["NonMember"];
				$VIPprice = 0;
				$pos1 = strpos($row_price["Option1"], "-");
				if($pos1 === false) {
					$opt1 = $row_price["Option1"];
				} else {
					$opt1 = explode("-", $row_price["Option1"]);
					$opt1 = $opt1[1];
				}

				$pos2 = strpos($row_price["Option2"], "-");
				if($pos2 === false) {
					$opt2 = $row_price["Option2"];
				} else {
					$opt2 = explode("-", $row_price["Option2"]);
					$opt2 = $opt2[1];
				}

				$pos3 = strpos($row_price["Option3"], "-");
				if($pos3 === false) {
					$opt3 = $row_price["Option3"];
				} else {
					$opt3 = explode("-", $row_price["Option3"]);
					$opt3 = $opt3[1];
				}

				$pos4 = strpos($row_price["Option4"], "-");
				if($pos4 === false) {
					$opt4 = $row_price["Option4"];
				} else {
					$opt4 = explode("-", $row_price["Option4"]);
					$opt4 = $opt4[1];
				}

				$price = $row_price["NonMember"];
				if ($totalqty <= $opt1) {
					$VIPprice = $row_price["Option1Price"];
				} elseif ($totalqty <= $opt2) {
					$VIPprice = $row_price["Option2Price"];
				} elseif ($totalqty <= $opt3) {
					$VIPprice = $row_price["Option3Price"];
				} else {
					$VIPprice = $row_price["Option4Price"];
				}
			}
			if ($price == '') { 
				$price = 0; 
			}
			if ($VIPprice == '') { 
				$VIPprice = 0; 
			}
			$sql_coloradd = "SELECT DISTINCT ColorSKU, ColorAddPrice FROM product_options WHERE ProductID=$id AND ColorSKU='$color' LIMIT 1";
			$result_coloradd = mysql_query($sql_coloradd) or die(mysql_error());
			$num_coloradd = mysql_num_rows($result_coloradd);
			if($num_coloradd > 0) {
				$row_coloradd = mysql_fetch_assoc($result_coloradd);
				$price = $price + ($row_coloradd["ColorAddPrice"]*$qty);
				$VIPprice = $VIPprice + ($row_coloradd["ColorAddPrice"]*$qty);
			}
			$sql_sizeadd = "SELECT DISTINCT SizeSKU, SizeAddPrice FROM product_options WHERE ProductID=$id AND SizeSKU='$size' LIMIT 1";
			$result_sizeadd = mysql_query($sql_sizeadd) or die(mysql_error());
			$num_sizeadd = mysql_num_rows($result_sizeadd);
			if($num_sizeadd > 0) {
				$row_sizeadd = mysql_fetch_assoc($result_sizeadd);
				$price = $price + ($row_sizeadd["SizeAddPrice"]*$qty);
				$VIPprice = $VIPprice + ($row_sizeadd["SizeAddPrice"]*$qty);
			}
			$sql_prod = "SELECT * FROM products WHERE id=$id LIMIT 1";
			$result_prod = mysql_query($sql_prod) or die(mysql_error());
			$row_prod = mysql_fetch_assoc($result_prod);
			$RootSKU = $row_prod["RootSKU"];
			$sql_special_price = "SELECT SpecialPrice, SpecialFrom, SpecialTo, isSpecial FROM products WHERE id =".$id." AND isSpecial!='' LIMIT 1";
			$result_special_price = mysql_query($sql_special_price);
		  	$row_special_price = mysql_fetch_array($result_special_price);
	    	$specialPrice = number_format($row_special_price['SpecialPrice'], 2);
	    	$specialFrom = $row_special_price["SpecialFrom"];
			$specialTo = $row_special_price["SpecialTo"];
			$todays_date = date("Y-m-d");
			$today = strtotime($todays_date);
			$startDate = strtotime($specialFrom);
			$endDate = strtotime($specialTo);
	    	if (($row_special_price['isSpecial'] == "True") && ($endDate > $today)) {
				$price = $specialPrice;
				$VIPprice = $specialPrice;
			}
            if($found == "No") {
				if(!isset($_POST["free"])){
					$sql_add = "INSERT INTO shopping_cart(SessionID, EmailAddress, ProductID, ProductName, RootSKU, SizeSKU, ColorSKU, Qty, Gender, GenderSKU, VIPPrice, Price, CreatedDate, Type) ";
					$sql_add .= "VALUES('".session_id()."','$_SESSION[email]', $id, '$productname', '$RootSKU', '$size', '$color', $qty, '$gender', '$gendersku', $VIPprice, $price, current_date, 'Product')";
					mysql_query($sql_add) or die("ERROR: " . mysql_error());
				}
			}
		}

		// if free IS set then process
		if(isset($_POST["free"])) {
			$sID = session_id();
			$sql_update1 = "UPDATE shopping_cart SET RootSKU='$RootSKU', SizeSKU='$size', ColorSKU='$color', Gender='$gender', GenderSKU= $gendersku' WHERE id=$psid and ProductID=$id and SessionID='$sID'";
			mysql_query($sql_update1) or die("Error with adding the item: " . mysql_error());
		}
		echo "Item Added!";
		// echo "<script>window.location='cart.php';</script>";
		if ($_SESSION["email"] != "") {
			echo "<script>window.location.reload();</script>";
		} else {
			echo "<script>window.location='myaccount.php';</script>";
		}
	} elseif($producttype != 'bundle') {
		$sql_prod = "SELECT RootSKU, isSpecial, SpecialPrice, SpecialTo, SpecialFrom FROM products WHERE id=$id LIMIT 1";
		$result_prod = mysql_query($sql_prod) or die(mysql_error());
		$row_prod = mysql_fetch_assoc($result_prod);
		$isSpecial = $row_prod["isSpecial"];
		$SpecialPrice = $row_prod["SpecialPrice"];
		$RootSKU = $row_prod["RootSKU"];
		$specialFrom = $row_prod["SpecialFrom"];
		$specialTo = $row_prod["SpecialTo"];
		$todays_date = date("Y-m-d");
		$today = strtotime($todays_date);
		$startDate = strtotime($specialFrom);
		$endDate = strtotime($specialTo);
		if (($isSpecial == "True") && ($endDate > $today)) {
			$price = $SpecialPrice;
			$VIPprice = $SpecialPrice;
		} else {
			$sql_price = "SELECT * FROM product_pricing WHERE Gender='$gender' AND ProductID=$id LIMIT 1";
			$result_price = mysql_query($sql_price) or die(mysql_error());
			$row_price = mysql_fetch_assoc($result_price);
			$price = $row_price["NonMember"];
			$VIPprice = 0;
			$pos1 = strpos($row_price["Option1"], "-");
				if($pos1 === false) {
					$opt1 = $row_price["Option1"];
				} else {
					$opt1 = explode("-", $row_price["Option1"]);
					$opt1 = $opt1[1];
				}

				$pos2 = strpos($row_price["Option2"], "-");
				if($pos2 === false) {
					$opt2 = $row_price["Option2"];
				} else {
					$opt2 = explode("-", $row_price["Option2"]);
					$opt2 = $opt2[1];
				}

				$pos3 = strpos($row_price["Option3"], "-");
				if($pos3 === false) {
					$opt3 = $row_price["Option3"];
				} else {
					$opt3 = explode("-", $row_price["Option3"]);
					$opt3 = $opt3[1];
				}

				$pos4 = strpos($row_price["Option4"], "-");
				if($pos4 === false) {
					$opt4 = $row_price["Option4"];
				} else {
					$opt4 = explode("-", $row_price["Option4"]);
					$opt4 = $opt4[1];
				}

				$price = $row_price["NonMember"];
				if ($totalqty <= $opt1) {
					$VIPprice = $row_price["Option1Price"];
				} elseif ($totalqty <= $opt2) {
					$VIPprice = $row_price["Option2Price"];
				} elseif ($totalqty <= $opt3) {
					$VIPprice = $row_price["Option3Price"];
				} else {
					$VIPprice = $row_price["Option4Price"];
				}
		}
		
		if($price == '') { 
			$price = 0; 
		}
		if($VIPprice == '') { 
			$VIPprice = 0; 
		}
		$sql_coloradd = "SELECT DISTINCT ColorSKU, ColorAddPrice FROM product_options WHERE ProductID=$id AND ColorSKU='$color' LIMIT 1";
		$result_coloradd = mysql_query($sql_coloradd) or die(mysql_error());
		$num_coloradd = mysql_num_rows($result_coloradd);
		if($num_coloradd > 0) {
			$row_coloradd = mysql_fetch_assoc($result_coloradd);
			$price = $price + ($row_coloradd["ColorAddPrice"]*$qty);
			$VIPprice = $VIPprice + ($row_coloradd["ColorAddPrice"]*$qty);
		}
		$sql_sizeadd = "SELECT DISTINCT SizeSKU, SizeAddPrice FROM product_options WHERE ProductID=$id AND SizeSKU='$size' LIMIT 1";
		$result_sizeadd = mysql_query($sql_sizeadd);
		$num_sizeadd = mysql_num_rows($result_sizeadd);
		if($num_sizeadd > 0) {
			$row_sizeadd = mysql_fetch_assoc($result_sizeadd);
			$price = $price + ($row_sizeadd["SizeAddPrice"]*$qty);
			$VIPprice = $VIPprice + ($row_sizeadd["SizeAddPrice"]*$qty);
		}
		$sql_special_price = "SELECT SpecialPrice, SpecialFrom, SpecialTo, isSpecial FROM products WHERE id =".$id." AND isSpecial!='' LIMIT 1";
		$result_special_price = mysql_query($sql_special_price);
		$row_special_price = mysql_fetch_array($result_special_price);
		$specialPrice = number_format($row_special_price['SpecialPrice'], 2);
	    $specialFrom = $row_special_price["SpecialFrom"];
		$specialTo = $row_special_price["SpecialTo"];
		$todays_date = date("Y-m-d");
		$today = strtotime($todays_date);
		$startDate = strtotime($specialFrom);
		$endDate = strtotime($specialTo);
	    if (($row_special_price['isSpecial'] == "True") && ($endDate > $today)) {
			$price = $specialPrice;
			$VIPprice = $specialPrice;
		}
		$sql_add = "INSERT INTO shopping_cart(SessionID, EmailAddress, ProductID, ProductName, RootSKU, SizeSKU, ColorSKU, Qty, Gender, GenderSKU, VIPPrice, Price, CreatedDate, Type) ";
		$sql_add .= "VALUES('".session_id()."','$_SESSION[email]', $id, '$productname', '$RootSKU', '$size', '$color', $qty, '$gender', '$gendersku', $VIPprice, $price, current_date, 'Product')";
		if(!mysql_query($sql_add)) {
			echo "Error adding item: ".mysql_error();
		} else {
			echo "Item Added!";
			// echo "<script>window.location='cart.php';</script>";
			if ($_SESSION["email"] != "") {
			echo "<script>window.location.reload();</script>";
		} else {
			echo "<script>window.location='myaccount.php';</script>";
		}
		}
	} elseif($producttype == 'bundle') {
		$sql_prod = "SELECT RootSKU, isSpecial, SpecialPrice, SpecialTo, SpecialFrom FROM products WHERE id=$id LIMIT 1";
		$result_prod = mysql_query($sql_prod);
		$row_prod = mysql_fetch_assoc($result_prod);
		$isSpecial = $row_prod["isSpecial"];
		$SpecialPrice = $row_prod["SpecialPrice"];
		$RootSKU = $row_prod["RootSKU"];
		$specialFrom = $row_prod["SpecialFrom"];
		$specialTo = $row_prod["SpecialTo"];
		$todays_date = date("Y-m-d");
		$today = strtotime($todays_date);
		$startDate = strtotime($specialFrom);
		$endDate = strtotime($specialTo);
		if (($isSpecial == "True") && ($endDate > $today)) {
			$price = $SpecialPrice;
			$VIPprice = $SpecialPrice;
		} else {
			$sql_price = "SELECT * FROM product_pricing WHERE Gender='$gender' AND ProductID=$id LIMIT 1";
			$result_price = mysql_query($sql_price);
			$row_price = mysql_fetch_assoc($result_price);
			$price = $row_price["NonMember"];
			$VIPprice = 0;
			$pos1 = strpos($row_price["Option1"], "-");
				if($pos1 === false) {
					$opt1 = $row_price["Option1"];
				} else {
					$opt1 = explode("-", $row_price["Option1"]);
					$opt1 = $opt1[1];
				}

				$pos2 = strpos($row_price["Option2"], "-");
				if($pos2 === false) {
					$opt2 = $row_price["Option2"];
				} else {
					$opt2 = explode("-", $row_price["Option2"]);
					$opt2 = $opt2[1];
				}

				$pos3 = strpos($row_price["Option3"], "-");
				if($pos3 === false) {
					$opt3 = $row_price["Option3"];
				} else {
					$opt3 = explode("-", $row_price["Option3"]);
					$opt3 = $opt3[1];
				}

				$pos4 = strpos($row_price["Option4"], "-");
				if($pos4 === false) {
					$opt4 = $row_price["Option4"];
				} else {
					$opt4 = explode("-", $row_price["Option4"]);
					$opt4 = $opt4[1];
				}

				$price = $row_price["NonMember"];
				if ($totalqty <= $opt1) {
					$VIPprice = $row_price["Option1Price"];
				} elseif ($totalqty <= $opt2) {
					$VIPprice = $row_price["Option2Price"];
				} elseif ($totalqty <= $opt3) {
					$VIPprice = $row_price["Option3Price"];
				} else {
					$VIPprice = $row_price["Option4Price"];
				}
		}
		if($price == '') { 
			$price = 0; 
		}
		if($VIPprice == '') { 
			$VIPprice = 0; 
		}
		$sql_special_price = "SELECT SpecialPrice, SpecialFrom, SpecialTo, isSpecial FROM products WHERE id =".$id." AND isSpecial!='' LIMIT 1";
		$result_special_price = mysql_query($sql_special_price);
		$row_special_price = mysql_fetch_array($result_special_price);
	    $specialPrice = number_format($row_special_price['SpecialPrice'], 2);
		$specialFrom = $row_special_price["SpecialFrom"];
		$specialTo = $row_special_price["SpecialTo"];
		$todays_date = date("Y-m-d");
		$today = strtotime($todays_date);
		$startDate = strtotime($specialFrom);
		$endDate = strtotime($specialTo);
	    if (($row_special_price['isSpecial'] == "True") && ($endDate > $today)) {
			$price = $specialPrice;
			$VIPprice = $specialPrice;
		}
		
		// Add Main Bundle Item :::::::::::::::::::::::::::::::::
		$sql_add  = "INSERT INTO shopping_cart(SessionID, EmailAddress, ProductID, ProductName, RootSKU, Qty, Gender, GenderSKU, VIPPrice, Price, CreatedDate, Type) ";
		$sql_add .= "VALUES('".session_id()."', '$_SESSION[email]', $id, '$productname', '$RootSKU', $qty, '$gender', '$gendersku', $VIPprice, $price, current_date, 'Bundle')";
		if(!mysql_query($sql_add)) {
			echo "error adding product: ".mysql_error();
		}
		$lastid = mysql_insert_id();
		$b_count = count($bitems);
		for($b=0; $b<$b_count; $b++) {
			$curitem = substr($bitems[$b], 0, 4);
			switch($curitem) {
				case "gend":
					$insert .= " Gender='$bvals[$b]', ";
					break;
				case "colo":
					$insert .= " ColorSKU='$bvals[$b]', ";
					break;
				case "size":
					$insert .= " SizeSKU='$bvals[$b]', ";
					break;
				case "bund":
					$start = substr($bitems[$b], 0, 4);
					$sql_bitem = "SELECT ProductDetailName, RootSKU FROM products WHERE id=$bvals[$b] LIMIT 1";
					$result_bitem = mysql_query($sql_bitem);
					$row_bitem = mysql_fetch_assoc($result_bitem);
					$bid = $bvals[$b];
					$bproductname = $row_bitem["ProductDetailName"];
					$bRootSKU = $row_bitem["RootSKU"];
					break;
			}
			if($start == substr($bitems[$b+1], 0, 4)) {
				$sql_addb = "INSERT INTO shopping_cart SET SessionID='".session_id()."', EmailAddress='$_SESSION[email]', ProductID=$bid, ProductName='$bproductname', RootSKU='$bRootSKU', Qty=1, $insert CreatedDate=current_date, Type='Bundle', BundleID=$lastid";
				if(!mysql_query($sql_addb)) {
					echo "error adding bundle item: ".mysql_error();
				}
				$curitem = '';
				$insert = '';
			}
		}

		//last item in bundle
		$sql_addb = "INSERT INTO shopping_cart SET SessionID='".session_id()."', EmailAddress='$_SESSION[email]', ProductID=$bid, ProductName='$bproductname', RootSKU='$bRootSKU', Qty=1, $insert CreatedDate=current_date, Type='Bundle', BundleID=$lastid";
		if(!mysql_query($sql_addb)) {
			echo "error adding bundle item: ".mysql_error();
		}
		$curitem = '';
		$insert = '';
		echo "Item Added!";
		// echo "<script>window.location='cart.php';</script>";
		if ($_SESSION["email"] != "") {
			echo "<script>window.location.reload();</script>";
		} else {
			echo "<script>window.location='myaccount.php';</script>";
		}
	}
	mysql_close($conn);
	exit();
}

// add VIP membership to shopping cart
if($_POST["type"] == 'VIP') {
	$sql_vip = "SELECT Name, Price FROM vip LIMIT 1";
	$result_vip = mysql_query($sql_vip) or die("VIP Error: " . mysql_error());
	$row_vip = mysql_fetch_assoc($result_vip);
	$sql_add  = "INSERT INTO shopping_cart(SessionID, EmailAddress, ProductID, ProductName, Qty, VIPPrice, Price, CreatedDate, `Type`) ";
	$sql_add .= "VALUES('".session_id()."', '$_SESSION[email]', 'VIP', '$row_vip[Name]', 1, $row_vip[Price], $row_vip[Price], current_date, 'VIP')";
	if (!mysql_query($sql_add)) {
		echo "Error adding Item: " . mysql_error();
	} else {
		echo "Item Added!";
		// echo "<script>window.location='cart.php';</script>";
		if ($_SESSION["email"] != "") {
			echo "<script>window.location.reload();</script>";
		} else {
			echo "<script>window.location='myaccount.php';</script>";
		}
	}
	mysql_close($conn);
	exit();
}

// remove items from shopping cart
if ($_POST["type"] == "remove") {
	if (isset($_SESSION["sku"])) {
		if ($_SESSION["sku"] == "true") {
			$sql = "select * from shopping_cart WHERE id=$_POST[id] LIMIT 1";
			$result = mysql_query($sql);
			$row = mysql_fetch_assoc($result);
			
			if ($row["Type"] == "CouponUsed") {
				$sql_delete_free_item = "delete from shopping_cart where ProductID=".$_SESSION["freeProductId"]." and SessionID='".session_id()."'";
				$query = mysql_query($sql_delete_free_item);
				unset($_SESSION["freeProductId"]);
			}
		}
	}
	$sql_id = "SELECT ProductID FROM shopping_cart WHERE id=$_POST[id] LIMIT 1";
	$result_id = mysql_query($sql_id);
	$row_id = mysql_fetch_assoc($result_id);
	$sql_remove = "DELETE FROM shopping_cart WHERE id=$_POST[id] LIMIT 1";
		
	if (mysql_query($sql_remove)) {
		$sql_remove2 = "DELETE FROM shopping_cart_single WHERE singleid=$_POST[id]";
		mysql_query($sql_remove2);
	}

	$sql_rembundle = "DELETE FROM shopping_cart WHERE BundleID=$_POST[id]";
	mysql_query($sql_rembundle);
		
	$sql_remImp = "DELETE FROM imprint_shopping_cart WHERE CartID=$_POST[id]";
	mysql_query($sql_remImp);

	//reset item pricing
	if ($_SESSION["email"] != '') {
		$where = " OR EmailAddress='$_SESSION[email]' ";
	} else {
		$where = "";
	}

	$sql_qty = "SELECT SUM(Qty) AS TotalQty FROM shopping_cart WHERE ProductID='$row_id[ProductID]' AND (SessionID='".session_id()."' $where)";
	$result_qty = mysql_query($sql_qty);
	$row_qty = mysql_fetch_assoc($result_qty);
	$qty = $row_qty["TotalQty"];

	$sql_items = "SELECT * FROM shopping_cart WHERE ProductID='$row_id[ProductID]' AND (SessionID='".session_id()."' $where)";
	$result_items = mysql_query($sql_items);
		
	if (@mysql_num_rows($result_items)):
		while($row_items = mysql_fetch_array($result_items)) {
			//////////////////////////////////////////////////////////////////////////////
				$sql_prod = "SELECT * FROM products WHERE id=$row_items[ProductID] LIMIT 1";
				$result_prod = mysql_query($sql_prod);

				if (@mysql_num_rows($result_prod))
					$row_prod = mysql_fetch_assoc($result_prod);

				$isSpecial = isset($row_prod["isSpecial"])?$row_prod["isSpecial"]:'';
				$SpecialPrice = isset($row_prod["SpecialPrice"])?$row_prod["SpecialPrice"]:0;
				$RootSKU = isset($row_prod["RootSKU"])?$row_prod["RootSKU"]:'';
				$specialFrom = $row_prod["SpecialFrom"];
				$specialTo = $row_prod["SpecialTo"];
				$todays_date = date("Y-m-d");
				$today = strtotime($todays_date);
				$startDate = strtotime($specialFrom);
				$endDate = strtotime($specialTo);

				if (($isSpecial == "True") && ($endDate > $today)) {
					$price = $SpecialPrice;
					$VIPprice = $SpecialPrice;
				} else {
					$sql_price = "SELECT * FROM product_pricing WHERE Gender='".addslashes($row_items["Gender"])."' AND ProductID=$row_items[ProductID] LIMIT 1";
					$result_price = mysql_query($sql_price);

					if (@mysql_num_rows($result_price)) {
						$row_price = mysql_fetch_assoc($result_price);
						$pos1 = strpos($row_price["Option1"], "-");

						if($pos1 === false) {
							$opt1 = $row_price["Option1"];
						} else {
							$opt1 = explode("-", $row_price["Option1"]);
							$opt1 = $opt1[1];
						}

						$pos2 = strpos($row_price["Option2"], "-");
						if($pos2 === false) {
							$opt2 = $row_price["Option2"];
						} else {
							$opt2 = explode("-", $row_price["Option2"]);
							$opt2 = $opt2[1];
						}

						$pos3 = strpos($row_price["Option3"], "-");
						if($pos3 === false) {
							$opt3 = $row_price["Option3"];
						} else {
							$opt3 = explode("-", $row_price["Option3"]);
							$opt3 = $opt3[1];
						}

						$pos4 = strpos($row_price["Option4"], "-");
						if($pos4 === false) {
							$opt4 = $row_price["Option4"];
						} else {
							$opt4 = explode("-", $row_price["Option4"]);
							$opt4 = $opt4[1];
						}

						$price = $row_price["NonMember"];
						if($qty <= $opt1) {
							$VIPprice = $row_price["Option1Price"];
						} elseif($qty<=$opt2) {
							$VIPprice = $row_price["Option2Price"];
						} elseif($qty<=$opt3) {
							$VIPprice = $row_price["Option3Price"];
						} else {
							$VIPprice = $row_price["Option4Price"];
						}

						If($price == '') { 
							$price = 0; 
						}
						If($VIPprice == '') { 
							$VIPprice = 0; 
						}
					}
				}

				/** New Code **/
				// $sql_special_price = "SELECT SpecialPrice, isSpecial FROM products WHERE id =".$id." AND ((DATE_FORMAT(SpecialFrom, '%Y-%m-%d') <= DATE_FORMAT(current_date, '%Y-%m-%d')  OR SpecialFrom='') AND (DATE_FORMAT(current_date, '%Y-%m-%d') <= DATE_FORMAT(SpecialTo, '%Y-%m-%d') OR SpecialTo='')) AND isSpecial!='' LIMIT 1";
				$sql_special_price = "SELECT * FROM products WHERE id='$id' AND isSpecial!='' LIMIT 1";
				$result_special_price = mysql_query($sql_special_price);
		  		$row_special_price = mysql_fetch_array($result_special_price);
	    		$specialPrice = number_format($row_special_price['SpecialPrice'], 2);

	    		$specialFrom = $row_special_price["SpecialFrom"];
				$specialTo = $row_special_price["SpecialTo"];
				$todays_date = date("Y-m-d");
				$today = strtotime($todays_date);
				$startDate = strtotime($specialFrom);
				$endDate = strtotime($specialTo);

	    		if (($row_special_price['isSpecial'] == "True") &&  ($endDate > $today)) {
						$price = $specialPrice;
						$VIPprice = $specialPrice;
				}
				/** End New Code **/

				$sql_update = "UPDATE shopping_cart SET Price=$price, VIPPrice=$VIPprice WHERE id=$row_items[id] LIMIT 1";
				mysql_query($sql_update);

				//if(!mysql_query($sql_update)) {
				//	echo " -- ".mysql_error();
				//}
			//////////////////////////////////////////////////////////////////////////////
		}
	endif;
	//	echo "Item has been removed";
	mysql_close($conn);
	exit();
}

// update quantity in shopping cart
if ($_POST["type"] == "updateqty") {
	$id  = $_POST["id"];
	$qty = $_POST["qty"];
	
	// if quantity changed to zero than remove completely from Shopping Cart, otherwise process as normal
	if ($qty == "0") {
		$sql_id = "SELECT ProductID FROM shopping_cart WHERE id=$id LIMIT 1";
		$result_id = mysql_query($sql_id) or die("Deletion Error: " . mysql_error());
		$row_id = mysql_fetch_assoc($result_id);
		$sql_remove = "DELETE FROM shopping_cart WHERE id=$id LIMIT 1";
		if (mysql_query($sql_remove)) {
			$sql_remove2 = "DELETE FROM shopping_cart_single WHERE singleid=$id";
			mysql_query($sql_remove2);
		}
		$sql_rembundle = "DELETE FROM shopping_cart WHERE BundleID=$id";
		mysql_query($sql_rembundle);
		$sql_remImp = "DELETE FROM imprint_shopping_cart WHERE CartID=$id";
		mysql_query($sql_remImp);
	} else {
		$sql_qtyup = "UPDATE shopping_cart SET Qty=$qty WHERE id=$id LIMIT 1";
		mysql_query($sql_qtyup) or die("Update error: " . mysql_error());
		$sql_prodid = "SELECT ProductID FROM shopping_cart WHERE id=$id LIMIT 1";
		$result_prodid = mysql_query($sql_prodid);
		$row_prodid = mysql_fetch_assoc($result_prodid);
		$prodid = $row_prodid["ProductID"];
		if ($_SESSION["email"] != '') {
			$where = " OR EmailAddress = '$_SESSION[email]' ";
			$sql_status = "SELECT Status, VIPLevel FROM customers WHERE EmailAddress='$_SESSION[email]' AND current_date()<DATE_ADD(VIPDate, INTERVAL 1 YEAR) LIMIT 1";
			$result_status = mysql_query($sql_status);
			$row_status = mysql_fetch_assoc($result_status);
			$Status = $row_status["Status"];
			$VIPLevel = $row_status["VIPLevel"];
		} else {
			$where = "";
			$status = "NonMember";
		}
		$sql_totalqty = "SELECT SUM(Qty) AS TotalQty FROM shopping_cart WHERE ProductID=$prodid AND (SessionID='".session_id()."' $where)";
		$result_totalqty = mysql_query($sql_totalqty);
		$row_totalqty = mysql_fetch_assoc($result_totalqty);
		$totalqty = $row_totalqty["TotalQty"];
		$sql_prod = "SELECT isSpecial, SpecialPrice, SpecialFrom, SpecialTo FROM products WHERE id=$prodid LIMIT 1";
		$result_prod = mysql_query($sql_prod);
		$row_prod = mysql_fetch_assoc($result_prod);
		$isSpecial = $row_prod["isSpecial"];
	$SpecialPrice = $row_prod["SpecialPrice"];
	$specialFrom = $row_prod["SpecialFrom"];
	$specialTo = $row_prod["SpecialTo"];
	$todays_date = date("Y-m-d");
	$today = strtotime($todays_date);
	$startDate = strtotime($specialFrom);
	$endDate = strtotime($specialTo);
	$sql_chkprod = "SELECT * FROM shopping_cart WHERE ProductID=$prodid AND (SessionID='".session_id()."' $where)";
	$result_chkprod = mysql_query($sql_chkprod);
	while ($row_chkprod = mysql_fetch_array($result_chkprod)) {
		if ($row_chkprod['Price'] != 0) {
			$RootSKU = $row_chkprod["RootSKU"];
			if (($isSpecial == "True") && ($endDate > $today)) {
				$price = $SpecialPrice;
				$VIPprice = $SpecialPrice;
			} else {
				$gender = stripslashes($row_chkprod["Gender"]);
				$gender = addslashes($row_chkprod["Gender"]);
				$sql_price = "SELECT * FROM product_pricing WHERE Gender='$gender' AND ProductID=$prodid LIMIT 1";
				$result_price = mysql_query($sql_price);
				$row_price = mysql_fetch_assoc($result_price);
				$pos1 = strpos($row_price["Option1"], "-");
				if ($pos1 === false) {
					$opt1 = $row_price["Option1"];
				} else {
					$opt1 = explode("-", $row_price["Option1"]);
					$opt1 = $opt1[1];
				}

				$pos2 = strpos($row_price["Option2"], "-");
				if ($pos2 === false) {
					$opt2 = $row_price["Option2"];
				} else {
					$opt2 = explode("-", $row_price["Option2"]);
					$opt2 = $opt2[1];
				}

				$pos3 = strpos($row_price["Option3"], "-");
				if ($pos3 === false) {
					$opt3 = $row_price["Option3"];
				} else {
					$opt3 = explode("-", $row_price["Option3"]);
					$opt3 = $opt3[1];
				}

				$pos4 = strpos($row_price["Option4"], "-");
				if ($pos4 === false) {
					$opt4 = $row_price["Option4"];
				} else {
					$opt4 = explode("-", $row_price["Option4"]);
					$opt4 = $opt4[1];
				}

				$price = $row_price["NonMember"];
				if ($totalqty <= $opt1) {
					$VIPprice = $row_price["Option1Price"];
				} elseif ($totalqty <= $opt2) {
					$VIPprice = $row_price["Option2Price"];
				} elseif ($totalqty <=$opt3) {
					$VIPprice = $row_price["Option3Price"];
				} else {
					$VIPprice = $row_price["Option4Price"];
				}

				if($VIPLevel != 0 && $VIPLevel != '') {
					if($VIPprice > $row_price["Option".$VIPLevel."Price"]) {
						$VIPprice = $row_price["Option".$VIPLevel."Price"];
					}
				}

				if ($price == '') { 
					$price = 0; 
				}
				if ($VIPprice == '') { 
					$VIPprice = 0; 
				}
			}

			/** New Code **/
			// $sql_special_price = "SELECT SpecialPrice, isSpecial FROM products WHERE id =".$id." AND ((DATE_FORMAT(SpecialFrom, '%Y-%m-%d') <= DATE_FORMAT(current_date, '%Y-%m-%d')  OR SpecialFrom='') AND (DATE_FORMAT(current_date, '%Y-%m-%d') <= DATE_FORMAT(SpecialTo, '%Y-%m-%d') OR SpecialTo='')) AND isSpecial!='' LIMIT 1";
			$sql_special_price = "SELECT SpecialPrice, SpecialFrom, SpecialTo, isSpecial FROM products WHERE id =".$id." AND isSpecial!='' LIMIT 1";
			$result_special_price = mysql_query($sql_special_price);
		  	$row_special_price = mysql_fetch_array($result_special_price);
	    	$specialPrice = number_format($row_special_price['SpecialPrice'], 2);
	    	$specialFrom = $row_special_price["SpecialFrom"];
			$specialTo = $row_special_price["SpecialTo"];
			$todays_date = date("Y-m-d");
			$today = strtotime($todays_date);
			$startDate = strtotime($specialFrom);
			$endDate = strtotime($specialTo);
	    	if (($row_special_price['isSpecial'] == "True") && ($endDate > $today)) {
				$price = $specialPrice;
				$VIPprice = $specialPrice;
			}
			/** End New Code **/

			$sql_update = "UPDATE shopping_cart SET Price=$price, VIPPrice=$VIPprice WHERE id=$row_chkprod[id] LIMIT 1";
			mysql_query($sql_update);
		} // end if
	}
}
	mysql_close($conn);
	exit();
}

// set sizes available for product
if ($_POST["type"] == "setsizes") {
	$size = $_POST["size"];
	$colorsku = "";
	if($_POST["colorsku"] != "undefined")
		$colorsku = $_POST["colorsku"];
?>
<form class="mysize">
<select id="<?=$size;?>" name="<?=$size;?>">
	<option value="">Select Size</option>
<?php
	$sql_size = "Select Distinct product_options.Size, product_options.SizeSKU From product_options, sizes Where product_options.Size=sizes.Size And product_options.ProductID=$_POST[id] And product_options.Gender='$_POST[gender]' And product_options.SizeSKU=sizes.SKU ";	
	if ($colorsku != '') {
		$sql_size .= " AND product_options.ColorSKU='$colorsku' AND product_options.Inventory!='0'";			
	}
	$sql_size .= "ORDER BY product_options.Position";
    $result_size = mysql_query($sql_size) or die(mysql_error());
	while ($row_size = mysql_fetch_array($result_size)) {
        echo '<option value="'.$row_size["SizeSKU"].'">'.$row_size["Size"].'</option>';
	}
?>
	</select>
</form>
<script>$('form.mysize').jqTransform({imgPath:'jqtransformplugin/img/'});</script>
<?php
	mysql_close($conn);
	exit();
}

// set colors function
if ($_POST["type"] == "setcolors") {
	$color = $_POST["color"];
	$gendersku = $_POST["gendersku"];
?>
<form class="mycolor">
<select id="<?=$color;?>" name="<?=$color;?>" onChange="showSize();cngImage(this.value); setSizes($('#gender :selected').text(), 'divSizeG', 'size', '<?=$_POST['id'];?>', this.value);">
    <option value="">Select Color</option>
<?php
	$sql_size = "SELECT DISTINCT Color, ColorSKU FROM product_options WHERE ProductID=$_POST[id] AND Inventory>0 AND Gender='$_POST[gender]' ";
	if ($gendersku != '') {
		$sql_size .= " AND GenderSKU='$gendersku' ";			
	}
	$sql_size .= ' ORDER BY Color';
    $result_size = mysql_query($sql_size);
	while($row_size = mysql_fetch_array($result_size)) {
        echo '<option value="'.$row_size["ColorSKU"].'">'.$row_size["Color"].'</option>';
    }
?>
</select>
</form>
<script>$('form.mycolor').jqTransform({imgPath:'jqtransformplugin/img/'});</script>
<?php
	mysql_close($conn);
	exit();
}

// bundleitems to shopping cart functionality
if($_POST["type"] == "bundleitems") {
		$qty = $_POST["qty"];
		$prodid = $_POST["prodid"];
		if($qty == '') { $qty = 1; }
		?>
        <form action="" method="post">
        <script> opts = new Array();</script>
        <table cellpading="5" cellspacing="3" style="width: 955px; margin: 20px 0px 20px 20px; float: left;">
				<?php
					$sql_bundle = "SELECT Items FROM product_bundles WHERE ProductID=$prodid ORDER BY SortOrder ASC";
					$result_bundle = mysql_query($sql_bundle);
					$bnum = 1;
					while($row_bundle=mysql_fetch_array($result_bundle)) {
						$sql_bimage = "SELECT Image FROM product_browser WHERE ProductID=$row_bundle[Items] LIMIT 1";
						$result_bimage = mysql_query($sql_bimage);
						$row_bimage = mysql_fetch_assoc($result_bimage);
						$sql_bitem = "SELECT p.RootSKU, p.ProductDetailName, d.ShortDescription FROM products p, product_descriptions d WHERE p.id=d.ProductID AND p.id=$row_bundle[Items] LIMIT 1";
						$result_bitem = mysql_query($sql_bitem);
						$row_bitem = mysql_fetch_assoc($result_bitem);
						?>
                        	<tr>
                            	<td style="background-color: #3c3c3c; padding: 10px; color: #fff; font-weight: bold;">
                                	<?=$row_bitem["ProductDetailName"];?>
                                </td>
                            </tr>
                            <tr>
                            	<td>
                                	<table cellpadding="5" cellspacing="2" style="100%;">
                                    	<tr>
                                        	<td style="width: 250px;">
                                            	<img src="cpadmin/images/productImages/<?=$row_bimage["Image"];?>" style="border: 1px solid #bebebe; width: 90px; height: 100px;" />
                                            </td>
                                            <td style="width: 700px;">
                                            	<!-- options -->
                                                <?php
													for($i=0;$i<$qty;$i++) {
														?>
														<input type="hidden" id="bundleid<?=$bnum."_".$i;?>" name="bundleid<?=$bnum."_".$i;?>" value="<?=$row_bundle["Items"];?>" />
					                                    <script>opts.push("bundleid<?=$bnum."_".$i;?>");</script>
														<?php
														$sql_gender = "SELECT ShowGender FROM product_pricing WHERE ProductID=$row_bundle[Items] LIMIT 1";
														$result_gender = mysql_query($sql_gender);
														$row_gender = mysql_fetch_assoc($result_gender);
														if($row_gender["ShowGender"] != 'None') {
?>
																<select id="gender<?=$bnum."_".$i;?>" name="gender<?=$bnum."_".$i;?>" style="border: 2px solid #878787; background-color: #ededed;" onChange="setSizes($('#gender<?=$bnum."_".$i;?> :selected').text(), 'divSize<?=$bnum."_".$i;?>', 'size<?=$bnum."_".$i;?>','<?=$row_bundle["Items"];?>');">
																	<option value="">Select <?=$row_gender["ShowGender"];?></option>
																	<?php
																		$sql_gitems = "SELECT Gender FROM product_pricing WHERE ProductID=$row_bundle[Items] ORDER BY Gender";
																		$result_gitems = mysql_query($sql_gitems);
																		while($row_gitems = mysql_fetch_array($result_gitems)) {
																			echo "<option value=\"$row_gitems[Gender]\">$row_gitems[Gender]</option>";
																		}
																	?>
																</select>
																<script>
																	opts.push("gender<?=$bnum."_".$i;?>");
																	optsname.push("Product <?=$bnum."_".$i;?> Range");
																</script>
															<?php
														}

														$sql_color = "SELECT DISTINCT Color, ColorSKU FROM product_options WHERE ProductID=$row_bundle[Items] AND Inventory>0 ORDER BY Color";
														$result_color = mysql_query($sql_color);
														$row_color = mysql_fetch_assoc($result_color);
														$num_color = mysql_num_rows($result_color);
														if($num_color>0 && $row_color["Color"] != '') {
															?>
																<select id="color<?=$bnum."_".$i;?>" name="color<?=$bnum."_".$i;?>">
																	<option value="">Select Color</option>
																	<?php
																		$result_citems = mysql_query($sql_color);
																		while($row_citems = mysql_fetch_array($result_citems)) {
																			echo "<option value=\"$row_citems[ColorSKU]\">$row_citems[Color]</options>";
																		}
																	?>
																</select>
																<script>
																	opts.push("color<?=$bnum."_".$i;?>");
																	optsname.push("Product <?=$bnum."_".$i;?> Color");
																</script>
															<?php
														}
														
														$sql_size = "SELECT DISTINCT Size, SizeSKU FROM product_options WHERE ProductID=$row_bundle[Items] AND Inventory>0 ORDER BY Position";
														$result_size = mysql_query($sql_size);
														$row_size = mysql_fetch_assoc($result_size);
														$num_size = mysql_num_rows($result_size);
												
														if($num_size>0 && $row_size["Size"] != '') {
															?>
                                                            	<div id="divSize<?=$bnum."_".$i;?>" name="divSize<?=$bnum."_".$i;?>">
																<select id="size<?=$bnum."_".$i;?>" name="size<?=$bnum."_".$i;?>">
																	<option value="">Select Size</option>
																	<?php
																		$result_csize = mysql_query($sql_size);
																		while($row_sitems = mysql_fetch_array($result_csize)) {
																			echo "<option value=\"$row_sitems[SizeSKU]\">$row_sitems[Size]</options>";
																		}
																	?>
																</select>
                                                                </div>
																<script>
																	opts.push("size<?=$bnum."_".$i;?>");
																	optsname.push("Product <?=$bnum."_".$i;?> Size");
																</script>
															<?php
														}
														?>
                                                        	<div class="clear"></div>
                                                        <?php
													}
												?>
                                                <!-- End Options -->
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
						<?php
						$bnum++;
					}
				?>
			</table>
        	</form>
    <?php
	mysql_close($conn);
	exit();
}

// chkInv functional to check the inventory
if ($_POST["type"] == 'chkInv') {
	$sql_inv = "SELECT Inventory FROM product_options WHERE ProductID='$_POST[id]' AND SizeSKU='$_POST[size]' AND ColorSKU='$_POST[color]' LIMIT 1";
	$result_inv = mysql_query($sql_inv);
	$row_inv = mysql_fetch_assoc($result_inv);
	echo $row_inv["Inventory"];
	mysql_close($conn);
	exit();
}
	
if($_POST["type"] == 'chkCartInv') {
		$scid = $_POST["scid"];
		$qty = $_POST["qty"];
		$sql_stk = "SELECT p.ManagableStock, s.ProductID, s.SizeSKU, s.ColorSKU FROM products p, shopping_cart s WHERE s.id=$scid AND s.ProductID=p.id LIMIT 1";
		$result_stk = mysql_query($sql_stk);
		$row_stk = mysql_fetch_assoc($result_stk);
		
		if($row_stk["ManagableStock"] == "No") {
			echo "true";
		} else {
			$prodid = $row_stk["ProductID"];
			$sizeSKU = $row_stk["SizeSKU"];
			$colorSKU = $row_stk["ColorSKU"];
			$sql_chk = "SELECT Inventory FROM product_options WHERE ProductID=$prodid ";
			if($sizeSKU != '') {
				$sql_chk .= "AND SizeSKU='$sizeSKU' ";
			}
			if($colorSKU != '') {
				$sql_chk .= "AND ColorSKU='$colorSKU' ";
			}
			$sql_chk .= "LIMIT 1";
			$result_chk = mysql_query($sql_chk);
			$row_chk = mysql_fetch_assoc($result_chk);
			
			if(intval($qty)>intval($row_chk["Inventory"])) {
				echo "false_".$row_chk["Inventory"];
			} else {
				echo "true";
			}
		}
	mysql_close($conn);
	exit();
}

// coupon check function	
if ($_POST["type"] == 'chkCoupons') {
	$sqlWhere = " SessionID = '".session_id()."' ";
	if ($_SESSION["email"] != '') {
		$sqlWhere .= " OR EmailAddress = '".$_SESSION["email"]."'";
	}
	$sql_coupons = "SELECT * FROM shopping_cart WHERE Type='CouponUsed' AND ($sqlWhere)";
	$result_coupons = mysql_query($sql_coupons) or die("Coupon Error: " . mysql_error());
	while ($row_coupons = mysql_fetch_array($result_coupons)) {
		$sql_cdetails = "SELECT * FROM coupons WHERE Code='$row_coupons[ProductID]' AND (EndDate='0000-00-00' OR EndDate>=current_date) LIMIT 1";
		$result_cdetails = mysql_query($sql_cdetails);
		$num_cdetails = mysql_num_rows($result_cdetails);
		// echo "NUMC --> " . $num_cdetails; // testing only
		if ($num_cdetails >= 0) {
			$sql_remc = "DELETE FROM shopping_cart WHERE id='$row_coupons[id]' AND Type='CouponUsed'";
			// echo "SQL --> " . $sql_remc; exit; // testing use only
			mysql_query($sql_remc);
		} else {
			$row_cdetails = mysql_fetch_assoc($result_cdetails);
			$sql_ototal = "SELECT SUM(Qty * Price) AS OrderTotal FROM shopping_cart WHERE Type='Product' AND ($sqlWhere) ";
			$result_ototal = mysql_query($sql_ototal);
			$row_ototal = mysql_fetch_assoc($result_ototal);
			if(intval($row_cdetails["MinimumOrder"]) > intval($row_ototal["OrderTotal"])) {
				$sql_remc = "DELETE FROM shopping_cart WHERE id=$row_coupons[id] AND Type='CouponUsed'";
				mysql_query($sql_remc);
			} else {
				switch($row_cdetails["ApplyTo"]) {
					case "SKU":
						$sql_chkcart = "SELECT SUM(QTY) AS TotalQty FROM shopping_cart WHERE (Type='Product' OR (Type='Bundle' AND BundleID IS NULL)) AND RootSKU ='$row_cdetails[ApplyOption]' AND ($sqlWhere)";
						$result_chkcart = mysql_query($sql_chkcart);
						$row_chkcart = mysql_fetch_assoc($result_chkcart);
						if($row_chkcart["TotalQty"] <= 0) {
							$sql_remc = "DELETE FROM shopping_cart WHERE id=$row_coupons[id] AND Type='CouponUsed'";
							mysql_query($sql_remc);
						} else {
							if(intval($row_cdetails["SkuItemQuantity"]) > intval($row_chkcart["TotalQty"])) {
								$sql_remc = "DELETE FROM shopping_cart WHERE id=$row_coupons[id] AND Type='CouponUsed'";
								mysql_query($sql_remc);
							}
						}
						break;	
					case "Category":
						$catids = str_replace("|", ",", $row_coupons["ApplyOption"]);
						$sql_chkcats = "SELECT s.id FROM shopping_cart s, category_items c WHERE s.ProductID=c.ProductID AND (Type='Product OR (Type='Bundle' AND BundleID IS NULL)) AND c.CategoryID IN ($catids) AND ($sqlWhere)";
						$result_chkcats = mysql_query($sql_chkcats);
						$num_chkcats = mysql_num_rows($result_chkcats);
						if($num_chkcats<=0) {
							$sql_remc = "DELETE FROM shopping_cart WHERE id=$row_coupons[id] AND Type='CouponUsed'";
							mysql_query($sql_remc);
						}
						break;
				}
			}
		}
	}
	mysql_close($conn);
	exit();
}
?>