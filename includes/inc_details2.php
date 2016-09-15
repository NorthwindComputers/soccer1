<?php
/**
 * bundled product details include file
 *
 * Updated: 17 August 2016
 * By: Richard Tuttle
 */

session_start();
require_once '../cpadmin/includes/db.php';
	
function getBundleSizeByGender($prodId, $gender, $colorsku) {
	if ($gender != "") {
		$sql_size = "SELECT DISTINCT product_options.Size, product_options.SizeSKU FROM product_options, sizes WHERE product_options.Size=sizes.Size AND product_options.ProductID=$prodId AND product_options.Gender='$gender' AND product_options.SizeSKU=sizes.SKU AND product_options.Inventory>0";
	} else {
		$sql_size = "SELECT DISTINCT product_options.Size, product_options.SizeSKU FROM product_options, sizes WHERE product_options.Size=sizes.Size AND product_options.ProductID=$prodId AND product_options.SizeSKU=sizes.SKU AND product_options.Inventory>0";
	}		
	if (isset($colorsku) != '') {
		$sql_size .= " AND product_options.ColorSKU='$colorsku'";			
	}
	$sql_size .= " ORDER BY sizes.Rank";
	//echo "Bundle By Gender SQL: " . $sql_size; exit(); // TESTING ONLY
	$result_size = mysql_query($sql_size) or die("ERROR: Bundle By Gender - " . mysql_error());
	return $result_size;
}

function getBundleSize($prodId, $colorsku) {
	$sql_size = "SELECT DISTINCT Size, SizeSKU FROM product_options WHERE ProductID='$prodId' AND Inventory>0 AND ColorSKU='$colorsku' ORDER BY Size";
	$result_size = mysql_query($sql_size) or die("Bundle Sizing SQL Error: ". mysql_error());
	// echo "Bundle By Size SQL: " . $sql_size; exit(); // TESTING ONLY
	return $result_size;
}
	
function inArrayCheck($value2, $array_value) {
	$isgot = 0;
	foreach ($array_value as $key=>$value) {
		if ($value == $value2) {
			$isgot = 1;
			break;
		}	    
	}
	return $isgot;
}

if ($_POST["type"] == "initImprint") {
		$gender = $_POST["gender"];
		$size = $_POST["size"];
		$colorsku = $_POST["colorsku"];
		$qty = $_POST["qty"];
		$_SESSION["imprintConfig"] = array();
		$_SESSION["imprintConfig"]["color"]= $colorsku;
		$_SESSION["imprintConfig"]["size"]= $size;
		$_SESSION["imprintConfig"]["qty"]= $qty;
		$_SESSION["imprintConfig"]["gendersku"] = $_POST["gendersku"];
		$_SESSION["imprintConfig"]["gender"]= $qty;
		$_SESSION["imprintConfig"]["productname"]= isset($_POST["productname"])?$_POST["productname"]:'';
		echo "init.succeed";		
	}

if($_POST["type"]=="initImprint_single") {
		$gender = $_POST["gender"];
		$size = $_POST["size"];
		$colorsku = $_POST["colorsku"];
		$qty = $_POST["qty"];
		$_SESSION["imprintConfig_single"] = array();
		$_SESSION["imprintConfig_single"]["color"]= $colorsku;
		$_SESSION["imprintConfig_single"]["size"]= $size;
		$_SESSION["imprintConfig_single"]["qty"]= $qty;
		$_SESSION["imprintConfig_single"]["gendersku"] = $_POST["gendersku"];
		$_SESSION["imprintConfig_single"]["gender"]= $gender;
		$_SESSION["imprintConfig_single"]["productname"]= isset($_POST["productname"])?$_POST["productname"]:'';
		echo "init.succeed";	
	}

if ($_POST["type"] == "initColor") {
	$idBundle = $_POST["idBundle"];
	$color = $_POST["color"];
	$_SESSION["bundleItems"][$idBundle]["color"] = $color;
	echo $_SESSION["bundleItems"][$idBundle]["color"];
}
	
if ($_POST["type"] == "initSizeB") {
	$idBundle = $_POST["idBundle"];
	$size = $_POST["size"];
	$set = $_POST["set"];
	$_SESSION["bundleItems"]["items"][$idBundle]["size"][$set] = $size;			
	$_SESSION["bundleItems"]["items"][$idBundle]["bid"] = $idBundle;
}

if ($_POST["type"]=="initImprintCustomRadioButtonSelect") {
		/*
		* here we have to make an array of parent_tab=>childtab=>child_option=>readiobutton_option_sub
		* first relation at colors_images and images 
		* then using $idoption get id_tab from  options_tab table
		* then using $id_option_tab get tab_parent from  imp_category_tabs table
		*/
		$id = $_POST["id"];
		$splitArray=explode("_",$id);
		$idoption =$splitArray[0];
		$colors_images_id =$splitArray[1];
		$session_id=session_id();
		
		//get id_option_tab
		$sql_id_tab = "SELECT id_tab FROM options_tab WHERE id_option='".$idoption."' ";
		$result_id_tab = mysql_query($sql_id_tab);
		$row_id_tab = mysql_fetch_assoc($result_id_tab);
		$id_tab =$row_id_tab['id_tab'];
		
		//get id_option_tab
		$sql_tab_parent = "SELECT tab_parent FROM imp_category_tabs WHERE id_tab='".$id_tab."' ";
		$result_tab_parent = mysql_query($sql_tab_parent);
		$row_tab_parent = mysql_fetch_assoc($result_tab_parent);
		$tab_parent =$row_tab_parent['tab_parent'];
		$_SESSION["radioButtonSelected"][$session_id][$tab_parent][$id_tab]["colors_images_id"] = $colors_images_id;
	}

if ($_POST["type"]=="initImprintCustomRadioButton_idOption_price") {
		$idoption = $_POST["id"];		
		$session_id=session_id();
		
		//get id_option_tab
		$sql_id_tab = "SELECT id_tab FROM options_tab WHERE id_option='".$idoption."' ";
		$result_id_tab = mysql_query($sql_id_tab);
		$row_id_tab = mysql_fetch_assoc($result_id_tab);
		$id_tab =$row_id_tab['id_tab'];
		
		//get id_option_tab
		$sql_tab_parent = "SELECT tab_parent FROM imp_category_tabs WHERE id_tab='".$id_tab."' ";
		$result_tab_parent = mysql_query($sql_tab_parent);
		$row_tab_parent = mysql_fetch_assoc($result_tab_parent);
		$tab_parent =$row_tab_parent['tab_parent'];
		
		//get price
		$sql_PRICE1 = "SELECT * FROM pricing WHERE IDOPTION ='".$idoption."' ";
		$result_PRICE1 = mysql_query($sql_PRICE1);
		$row_PRICE1 = mysql_fetch_assoc($result_PRICE1);
		$PRICE1 =$row_PRICE1['PRICE1'];

		$qty=$_SESSION["imprintConfig"]["qty"];

		if($qty>=$row_PRICE1['STARTQT_1'] && $qty<=$row_PRICE1['ENDQT_1'])
			$PRICE1 =$row_PRICE1['PRICE1'];
		else if($qty>=$row_PRICE1['STARTQT_2'] && $qty<=$row_PRICE1['ENDQT_2'])
			$PRICE1 =$row_PRICE1['PRICE2'];
		else if($qty>=$row_PRICE1['STARTQT_3'] && $qty<=$row_PRICE1['ENDQT_3'])
			$PRICE1 =$row_PRICE1['PRICE3'];
		else if($qty>=$row_PRICE1['STARTQT_4'] && $qty<=$row_PRICE1['ENDQT_4'])
			$PRICE1 =$row_PRICE1['PRICE4'];
		else 
			$PRICE1 =0;

		$_SESSION["radioButtonSelected"][$session_id][$tab_parent][$id_tab]["idoption"] = $idoption;
		$_SESSION["radioButtonSelected"][$session_id][$tab_parent][$id_tab]["price"] = $PRICE1;
		$_SESSION["radioButtonSelected"][$session_id][$tab_parent][$id_tab]["setup_fee"] = $row_PRICE1['setup_fee'];
		$_SESSION["radioButtonSelected"][$session_id][$tab_parent][$id_tab]["colors_images_id"] = "";
		//print_r($_SESSION["radioButtonSelected"]); 

		echo "<table cellspacing='0'  width='100%' border='0' style='font-size:12px;padding:2px;'>";	

		foreach($_SESSION["radioButtonSelected"][$session_id] as $parent_tab_id => $value) {					
				//parent_tab_name
				$sql_name_query3 = "SELECT 	tab_name FROM imp_category_tabs WHERE id_tab='".$parent_tab_id."' ";
				$result_name_query3 = mysql_query($sql_name_query3);
				$row_name_query3 = mysql_fetch_assoc($result_name_query3);
				$parent_tab_name =$row_name_query3['tab_name'];				

				echo "<tr style='color:#FF0000'><td align='left'>".$parent_tab_name."</td><td>&nbsp;</td></tr>";		
				 $i=0;

				foreach($value as $child_tab_id=>$image_details) {
					//echo "$parent_tab_id => $child_tab_id => $selected_image_id";
					$selected_image_id=$image_details["colors_images_id"];
					$imprint_price=$image_details["price"];
					$imprint_idoption=$image_details["idoption"];
					$bgcolor=(($i++)%2==0)? "#F7F7F7":"#E9E9E9"; 
				
					//OPTION_NAME
					$sql_name_query2 = "SELECT 	OPTION_NAME FROM impcategory_option
										WHERE IDOPTION='".$imprint_idoption."' ";
					$result_name_query2 = mysql_query($sql_name_query2);
					$row_name_query2 = mysql_fetch_assoc($result_name_query2);
					$OPTION_NAME =$row_name_query2['OPTION_NAME'];

					echo "
					  <tr bgcolor='$bgcolor'>
						<td align='left'>".$OPTION_NAME."</td>
						<td align='right'>$".sprintf("%.2f", $imprint_price)."</td>
					  </tr>
					  ";	

					if($image_details["setup_fee"]>0)  

					echo "
					  <tr bgcolor='$bgcolor'>
						<td align='left'>Setup Fee</td>
						<td align='right'>$".sprintf("%.2f",$image_details["setup_fee"])."</td>
					  </tr>
					  ";	
				}

				echo "
				  <tr style='color:#FF0000'>
					<td colspan='2'>&nbsp;</td>
				  </tr>
				  ";
			}

			echo "
				  </table>
				  ";	

		echo $tableData;
	}

// type -> ADDCART
if ($_POST["type"] == "addCart") {
	foreach ($_POST as $key=>$value) {
		$$key = $value;
	}

	$gender = stripslashes($gender);
	$gender = addslashes($gender);

	$_SESSION["totalp"] = $_POST["totalp"];
	if ($_SESSION["email"] != '') {
		$where = " OR EmailAddress='$_SESSION[email]'";
		$sql_status = "SELECT Status, VIPLevel, VIPExpDate FROM customers WHERE EmailAddress='$_SESSION[email]' AND current_date()<=VIPExpDate LIMIT 1";
		$result_status = mysql_query($sql_status) or die("Customer Error: " . mysql_error());
		$row_status = mysql_fetch_assoc($result_status);
		$Status = $row_status["Status"];
		$VIPLevel = $row_status["VIPLevel"];
	} else {
		$where = "";
		$status = "NonMember";
	}
		
	$sql_chkprod = "SELECT * FROM shopping_cart WHERE ProductID=$id AND (SessionID='".session_id()."' $where)";
	$result_chkprod = mysql_query($sql_chkprod) or die("Product Check Error: " . mysql_error());
	$num_chkprod = mysql_num_rows($result_chkprod);
		
	if ($num_chkprod > 0 && $producttype == 'bundle') {
		$sql_prod = "SELECT * FROM products WHERE id=$id LIMIT 1";
		$result_prod = mysql_query($sql_prod) or die("Product Retrieval Error: " . mysql_error());
		$row_prod = mysql_fetch_assoc($result_prod);
		$isSpecial = $row_prod["isSpecial"];
		$SpecialPrice = $row_prod["SpecialPrice"];
		$RootSKU = $row_prod["RootSKU"];
		$SizeSKU = $row_prod["SizeSKU"];
		$ColorSKU = $row_prod["ColorSKU"];
		$specialFrom = $row_prod["SpecialFrom"];
		$specialTo = $row_prod["SpecialTo"];
		$todays_date = date("Y-m-d");
		$today = strtotime($todays_date);
		$startDate = strtotime($specialFrom);
		$endDate = strtotime($specialTo);

		// if (($isSpecial == "True") && ($endDate > $today)) {
		if ($isSpecial == "True") {
			$price = $SpecialPrice;
			$VIPprice = $SpecialPrice;
		} else {
			$sql_price = "SELECT * FROM product_pricing WHERE Gender='$gender' AND ProductID=$id LIMIT 1";
			$result_price = mysql_query($sql_price) or die("Pricing Error: " . mysql_error());
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
			} elseif($qty <= $opt2) {
				$VIPprice = $row_price["Option2Price"];
			} elseif($qty <= $opt3) {
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
		// print_r($_SESSION); exit; // testing only
        $b_count = sizeof($_SESSION["bundleItems"]["items"]);
		$final_quantity = 0;
		$final_price = 0;
		$binary_value = array();
		$cart_id_array = array();
		
		while ($cart_row = mysql_fetch_array($result_chkprod)) {
			$ColorSKU_array = array();
			$SizeSKU_array = array();
			$c_id = $cart_row['id'];
			array_push($cart_id_array, $c_id);
			$final_quantity = $cart_row['Qty'] + $qty;
			$final_price = $cart_row['Price'] + ($cart_row['Price'] / $cart_row['Qty']) * $final_quantity;
			 
			if ($cart_row['id'] > 0) {
				$b_sql_query = "SELECT * FROM shopping_cart WHERE BundleID=$c_id";
				$result_b_cart = mysql_query($b_sql_query);
				while ($row_b = mysql_fetch_array($result_b_cart)) {
					array_push($SizeSKU_array, $row_b['SizeSKU']);
					array_push($ColorSKU_array, $row_b['ColorSKU']);
				}
				// print_r($_SESSION); exit; // testing only
				if ($b_count > 0) {
					$is_exist_this_product = 1;
					foreach ($_SESSION["bundleItems"]["items"] as $key => $value) {								
						$check_i = 0;
						if (!empty($_SESSION['bundleItems']['items'][$key]['bid'])) {
							$sizeCount = array_count_values($_SESSION['bundleItems']['items'][$key]['size']);
							if (sizeof($sizeCount) > 0) {
								foreach($sizeCount as $sizeSKU => $sizeQty) {
									if ($check_i == 0) {
										$color = $_SESSION["bundleItems"][$key]["color"];
										if ($color != '') {
									  		if (inArrayCheck($color, $ColorSKU_array)) {
									  		} else { 
									       		$is_exist_this_product = 0;
										  		break;
									  		}
									  		$check_i = 1;
										$sizeSKU = $_SESSION['bundleItems']['items'][$key]['size'][0];
										if ($sizeSKU != '') {
									 		if (inArrayCheck($sizeSKU, $SizeSKU_array)) { 
									 		} else { 
									   			$is_exist_this_product = 0;
												break;
									 		}
									 	}
									}
								}	
							}		
						}
						
					if ($is_exist_this_product == 0) {	
						break;
					}
				}
			}
		}
		
		array_push($binary_value, $is_exist_this_product);
		unset($ColorSKU_array);
		unset($SizeSKU_array);
	}		
		
		if (in_array(1, $binary_value)) {
			$key = array_search(1, $binary_value); // $key = 2;
			$bbbbb_id = $cart_id_array[$key];
            $sql_add  = "update shopping_cart set Qty=$final_quantity, Price=$final_price, VIPPrice=$VIPprice where id=$bbbbb_id";
            //echo "SQL: " . $sql_add . "<br />"; exit; // testing use only
			mysql_query($sql_add) or die("Pricing Update Error: " . mysql_error());
			
			$sql_add2  = "update shopping_cart set Qty=$final_quantity where BundleID=$bbbbb_id";
			mysql_query($sql_add2) or die("Qty Update Error: " . mysql_error());
		} else {
			$sql_add  = "INSERT INTO shopping_cart(SessionID, EmailAddress, ProductID, ProductName, RootSKU, Qty, Gender, GenderSKU, VIPPrice, Price, CreatedDate, Type) VALUES('".session_id()."', '$_SESSION[email]', $id, '$productname', '$RootSKU', $qty, '$gender', '$gendersku', $VIPprice, $price, current_date, 'Bundle')";
			//echo "SQL2: " . $sql_add . "<br />"; exit; // testing use only
			mysql_query($sql_add) or die("Error adding product: " . mysql_error());
		}

			$lastid = mysql_insert_id();
			//add to shopping_cart_imprintdata for the imprint options			
			$session_id = session_id();
			$_SESSION["radioButtonSelected"] = array();
			// end shopping_cart_imprintdata
			
			// insert into databse ********************************
			$b_count = sizeof($_SESSION["bundleItems"]["items"]);
			$lk = 0;
			$se = "";
			 
			 if (in_array(1, $binary_value)) {
			 } else {
			 	// var_dump($_SESSION); // TESTING
				if ($b_count > 0) {
					foreach ($_SESSION["bundleItems"]["items"] as $key => $value) {								
						if (!empty($_SESSION['bundleItems']['items'][$key]['bid'])) {
							$sizeCount = array_count_values($_SESSION['bundleItems']['items'][$key]['size']); // count each value
							if (sizeof($sizeCount) > 0) {
								foreach ($sizeCount as $sizeSKU => $sizeQty) {
									$sql_bitem = "SELECT ProductDetailName, RootSKU FROM products WHERE id=".$_SESSION['bundleItems']['items'][$key]['bid'] ." LIMIT 1";
									$result_bitem = mysql_query($sql_bitem) or die("Bundle Product error: " . mysql_error());					
									$row_bitem = mysql_fetch_assoc($result_bitem);
									$bproductname = $row_bitem["ProductDetailName"];
									$bRootSKU = $row_bitem["RootSKU"];
									$bid = $_SESSION["bundleItems"]["items"][$key]["bid"];
									$color = $_SESSION["bundleItems"][$key]["color"];
									$sql_addb = "INSERT INTO shopping_cart SET SessionID='".session_id()."', EmailAddress='$_SESSION[email]', ProductID='".$key."', ProductName='$bproductname', RootSKU='".$bRootSKU."', SizeSKU='".$sizeSKU."', ColorSKU='".$color."', Qty='".$sizeQty."', CreatedDate=current_date, Type='Bundle', BundleID=$lastid";
									if (!mysql_query($sql_addb)) {
										echo "error adding bundle item: " . $sql_addb;
									} // end if		
								}
							} // end if	
					 } // end if
				} // foreach
				 die();
			}
		}
		echo "Item Added!";
		// echo "<script type='text/javascript'>window.location='cart.php';</script>";
		if ($_SESSION["email"] != "") {
			echo "<script>window.location.reload();</script>";
		} else {
			echo "<script>window.location='myaccount.php';</script>";
		}
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

		// if (($isSpecial == "True") && ($endDate > $today)) {
		if ($isSpecial == "True") {
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
			if($qty <= $opt1) {
				$VIPprice = $row_price["Option1Price"];
			} elseif($qty <= $opt2) {
				$VIPprice = $row_price["Option2Price"];
			} elseif($qty <= $opt3) {
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
		if($price == '') { $price = 0; }
		if($VIPprice == '') { $VIPprice = 0; }
		$sql_coloradd = "SELECT DISTINCT ColorSKU, ColorAddPrice FROM product_options WHERE ProductID=$id AND ColorSKU='$color' LIMIT 1";
		$result_coloradd = mysql_query($sql_coloradd);
		$num_coloradd = mysql_num_rows($result_coloradd);

		if ($num_coloradd > 0) {
			$row_coloradd = mysql_fetch_assoc($result_coloradd);
			$price = $price + ($row_coloradd["ColorAddPrice"]*$qty);
			$VIPprice = $VIPprice + ($row_coloradd["ColorAddPrice"]*$qty);
		}

		$sql_sizeadd = "SELECT DISTINCT SizeSKU, SizeAddPrice FROM product_options WHERE ProductID=$id AND SizeSKU='$size' LIMIT 1";
		$result_sizeadd = mysql_query($sql_sizeadd);
		$num_sizeadd = mysql_num_rows($result_sizeadd);

		if ($num_sizeadd > 0) {
			$row_sizeadd = mysql_fetch_assoc($result_sizeadd);
			$price = $price + ($row_sizeadd["SizeAddPrice"]*$qty);
			$VIPprice = $VIPprice + ($row_sizeadd["SizeAddPrice"]*$qty);
		}

		$sql_add = "INSERT INTO shopping_cart(SessionID, EmailAddress, ProductID, ProductName, RootSKU, SizeSKU, ColorSKU, Qty, Gender, GenderSKU, VIPPrice, Price, CreatedDate, Type) ";
		$sql_add .= "VALUES('".session_id()."','$_SESSION[email]', $id, '$productname', '$RootSKU', '$size', '$color', $qty, '$gender', '$gendersku', $VIPprice, $price, current_date, 'Product')";

		if(!mysql_query($sql_add)) {
			echo "Error adding item: ".mysql_error();
		} else {
			echo "Item Added!";
			$lastid = mysql_insert_id();						
			//add to shopping_cart_imprintdata for the imprint options			
			$session_id=session_id();
			$_SESSION["radioButtonSelected"]=array();
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

		// if (($isSpecial == "True") && ($endDate > $today)) {
		if ($isSpecial == "True") {
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
			if($qty <= $opt1) {
				$VIPprice = $row_price["Option1Price"];
			} elseif($qty <= $opt2) {
				$VIPprice = $row_price["Option2Price"];
			} elseif($qty <= $opt3) {
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
		if ($price == '') { $price = 0; }
		if ($VIPprice == '') { $VIPprice = 0; }
		if ($qty == '') { $qty = 0; }

		// Add Main Bundle Item :::::::::::::::::::::::::::::::::
		// $price = $_SESSION["totalp"];
		if ($price == '') { $price = 0; }
		$sql_add  = "INSERT INTO shopping_cart(SessionID, EmailAddress, ProductID, ProductName, RootSKU, Qty, Gender, GenderSKU, VIPPrice, Price, CreatedDate, Type) VALUES('".session_id()."', '$_SESSION[email]', $id, '$productname', '$RootSKU', $qty, '$gender', '$gendersku', $VIPprice, $price, current_date, 'Bundle')";
		// echo "SQL: " . $sql_add . "<br />"; exit; // testing use only
		mysql_query($sql_add) or die("error adding product2: " . mysql_error());
		$lastid = mysql_insert_id();			

		//add to shopping_cart_imprintdata for the imprint options			
		$session_id = session_id();
		if (sizeof($_SESSION["radioButtonSelected"][$session_id]) > 0) {
			foreach($_SESSION["radioButtonSelected"][$session_id] as $parent_tab_id => $value) {
				if (sizeof($value) > 0) {
					foreach($value as $child_tab_id=>$image_details) {
						//echo "$parent_tab_id => $child_tab_id => $selected_image_id";
						$selected_image_id=$image_details["colors_images_id"];
						$imprint_price=$image_details["price"];
						$imprint_idoption=$image_details["idoption"];
						$sql_addfor_imprint  = "INSERT INTO shopping_cart_imprintdata(cart_id, parentid, childid, optionid, selected_image_id, price)  ";
						$sql_addfor_imprint .= "VALUES('$lastid', '$parent_tab_id', '$child_tab_id', '$imprint_idoption', '$selected_image_id', '$imprint_price' )";
						if(!empty($selected_image_id))
						mysql_query($sql_addfor_imprint);
					}
				}	
			}
		}
		$_SESSION["radioButtonSelected"]=array();
		// end shopping_cart_imprintdata

		// insert into databse ********************************
		$b_count = sizeof($_SESSION["bundleItems"]["items"]);
		$lk = 0;
		$se = "";

		if ($b_count > 0) {
			foreach ($_SESSION["bundleItems"]["items"] as $key => $value) {								
				// echo $_SESSION["bundleItems"]["items"][$key]["color"].":".$_SESSION["bundleItems"]["items"][$lk][$key]["size"];	exit; // TESTING
				if (!empty($_SESSION['bundleItems']['items'][$key]['bid'])) {
					$sizeCount = array_count_values($_SESSION['bundleItems']['items'][$key]['size']);// count each value
					if (sizeof($sizeCount) > 0) {
						foreach ($sizeCount as $sizeSKU => $sizeQty) {
							$sql_bitem = "SELECT ProductDetailName, RootSKU FROM products WHERE id=".$_SESSION['bundleItems']['items'][$key]['bid'] ." LIMIT 1";
							$result_bitem = mysql_query($sql_bitem);					
							$row_bitem = mysql_fetch_assoc($result_bitem) or die(mysql_error());
							$bproductname = $row_bitem["ProductDetailName"];
							$bRootSKU = $row_bitem["RootSKU"];
							$bid = $_SESSION["bundleItems"]["items"][$key]["bid"];
							$color = $_SESSION["bundleItems"][$key]["color"];
							$sql_addb = "INSERT INTO shopping_cart SET SessionID='".session_id()."', EmailAddress='$_SESSION[email]', ProductID='".$key."', ProductName='$bproductname', RootSKU='".$bRootSKU."', SizeSKU='".$sizeSKU."', ColorSKU='".$color."', Qty='".$sizeQty."', CreatedDate=current_date, Type='Bundle', BundleID=$lastid";
							if (!mysql_query($sql_addb)) {
								echo "error adding bundle item - spot 2: " . mysql_error();
							}//end if		
						}
					}//end if	
				} //end if
			} //foreach
			die();
		} //if

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
// end ADD TO CART

if($_POST["type"] == 'VIP') {
	$sql_vip = "SELECT `Name`, Price FROM vip LIMIT 1";
	$result_vip = mysql_query($sql_vip);
	$row_vip = mysql_fetch_assoc($result_vip);
	$sql_add  = "INSERT INTO shopping_cart(SessionID, EmailAddress, ProductID, ProductName, Qty, VIPPrice, Price, CreatedDate, `Type`) ";
	$sql_add .= "VALUES('".session_id()."', '$_SESSION[email]', 'VIP', '$row_vip[Name]', 1, $row_vip[Price], $row_vip[Price], current_date, 'VIP')";

	if(!mysql_query($sql_add)) {
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

if ($_POST["type"] == "remove") {
	if( isset($_SESSION["sku"])) {
		if($_SESSION["sku"]=="true") {
			$sql = "select * from shopping_cart WHERE id=$_POST[id] LIMIT 1";
			$result = mysql_query($sql);
			$row = mysql_fetch_assoc($result);
			if($row["Type"]=="Coupon"){
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
		mysql_query($sql_remove);
		$sql_rembundle = "DELETE FROM shopping_cart WHERE BundleID=$_POST[id]";
		mysql_query($sql_rembundle);

		//reset item pricing
		if($_SESSION["email"] != '') {
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
		while($row_items = mysql_fetch_array($result_items)) {
			//////////////////////////////////////////////////////////////////////////////
				$sql_prod = "SELECT * FROM products WHERE id=$row_items[ProductID] LIMIT 1";
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

				// if (($isSpecial == "True") && ($endDate > $today)) {
				if ($isSpecial == "True") {
					$price = $SpecialPrice;
					$VIPprice = $SpecialPrice;
				} else {
					$sql_price = "SELECT * FROM product_pricing WHERE Gender='".addslashes($row_items["Gender"])."' AND ProductID=$row_items[ProductID] LIMIT 1";
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

					If($price == '') { $price = 0; }
					If($VIPprice == '') { $VIPprice = 0; }
			}

		$sql_update = "UPDATE shopping_cart SET Price=$price, VIPPrice=$VIPprice WHERE id=$row_items[id] LIMIT 1";
		mysql_query($sql_update);
		//////////////////////////////////////////////////////////////////////////////
	}

echo "Item has been removed";
mysql_close($conn);
exit();
}

if($_POST["type"] == "updateqty") {
$id  = $_POST["id"];
		$qty = $_POST["qty"];
		$sql_qtyup = "UPDATE shopping_cart SET Qty=$qty WHERE id=$id LIMIT 1";
		mysql_query($sql_qtyup);	
		$sql_prodid = "SELECT ProductID FROM shopping_cart WHERE id=$id LIMIT 1";
		$result_prodid = mysql_query($sql_prodid);
		$row_prodid = mysql_fetch_assoc($result_prodid);
		$prodid = $row_prodid["ProductID"];

		if($_SESSION["email"] != '') {
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

		while($row_chkprod = mysql_fetch_array($result_chkprod)) {
			////////////////////////////////////////////////////////////////////
			$RootSKU = $row_chkprod["RootSKU"];
			// if(($isSpecial == "True") && ($endDate > $today)) {
			if ($isSpecial == "True") {
				$price = $SpecialPrice;
				$VIPprice = $SpecialPrice;
			} else {
				$gender = stripslashes($row_chkprod["Gender"]);
				$gender = addslashes($row_chkprod["Gender"]);
				$sql_price = "SELECT * FROM product_pricing WHERE Gender='$gender' AND ProductID=$prodid LIMIT 1";
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
				if($totalqty <= $opt1) {
					$VIPprice = $row_price["Option1Price"];
				} elseif($totalqty<=$opt2) {
					$VIPprice = $row_price["Option2Price"];
				} elseif($totalqty<=$opt3) {
					$VIPprice = $row_price["Option3Price"];
				} else {
					$VIPprice = $row_price["Option4Price"];
				}

				if($VIPLevel != 0 && $VIPLevel != '') {
					if($VIPprice > $row_price["Option".$VIPLevel."Price"]) {
						$VIPprice = $row_price["Option".$VIPLevel."Price"];
					}
				}

				If($price == '') { $price = 0; }
				If($VIPprice == '') { $VIPprice = 0; }
			}

			$sql_update = "UPDATE shopping_cart SET Price=$price, VIPPrice=$VIPprice WHERE id=$row_chkprod[id] LIMIT 1";
			mysql_query($sql_update);
			/////////////////////////////////////////////////////////////////////
		}
		mysql_close($conn);
		exit();
	}

if($_POST["type"] == "setsizes") {
		$size = $_POST["size"];
		$colorsku ="";
		if($_POST["colorsku"]!="undefined")
			$colorsku = $_POST["colorsku"];
		?>
		<form class="mysize">
		<select id="<?=$size;?>" name="<?=$size;?>">
            <option value="">Select Size</option>
        <?php				
			$sql_size = "Select Distinct product_options.Size, product_options.SizeSKU From product_options ,sizes Where product_options.Size = sizes.Size  And product_options.ProductID=$_POST[id] And product_options.Gender='$_POST[gender]' And product_options.Inventory > 0 And product_options.SizeSKU = sizes.SKU ";		
			if ($colorsku != '') {
				$sql_size .= " AND product_options.ColorSKU='$colorsku' ";			
			}
			$sql_size .= "Order By sizes.Rank";
            $result_size = mysql_query($sql_size) or die(mysql_error());
			while ($row_size = mysql_fetch_array($result_size)) {
                echo '<option value="'.$row_size["SizeSKU"].'">'.$row_size["Size"].'</option>';
            }
        ?>
		</select>
		</form>
		<script> $('form.mysize').jqTransform({imgPath:'jqtransformplugin/img/'});</script>
		<?php
		mysql_close($conn);
		exit();
	}

if($_POST["type"] == "setcolors") {
	$color = $_POST["color"];
	$gendersku = $_POST["gendersku"];
?>
	<form class="mycolor">
	<select id="<?=$color;?>" name="<?=$color;?>" onChange="showSize();cngImage(this.value); setSizes($('#gender :selected').text(), 'divSizeG', 'size', '<?=$_POST['id'];?>', this.value);">
    	<option value="">Select Color</option>
    <?php
		$sql_size = "SELECT DISTINCT Color, ColorSKU FROM product_options WHERE ProductID=$_POST[id] AND Inventory>0 AND Gender='$_POST[gender]' ";
		if ($gendersku != '') :
			$sql_size .= " AND GenderSKU='$gendersku' ";		
		endif;
		$sql_size .= ' ORDER BY Color';
        $result_size = mysql_query($sql_size);
		while ($row_size = mysql_fetch_array($result_size)) :
            echo '<option value="'.$row_size["ColorSKU"].'">'.$row_size["Color"].'</option>';
        endwhile;
    ?>
	</select>
	</form>
	<script type="text/javascript"> $('form.mycolor').jqTransform({imgPath:'jqtransformplugin/img/'});</script>
<?php
	mysql_close($conn);
	exit();
}

// bundle items listing function
if ($_POST["type"] == "bundleitems") {		
	$qty = $_POST["qty"];
	$prodid = $_POST["prodid"];
	$gender = $_POST["gender"];		
	$_SESSION["productGender"] = $gender;
	$_SESSION["mainProdId"] = $prodid;
	$_SESSION["bundleItems"]["qty"] = $qty;
	$_SESSION["bundleItems"]["items"] = array();
	$color1 = $_POST["jColor"];
	$color2 = $_POST["shColor"];
	$color3 = $_POST["skColor"];

	if ($qty == '') { 
		$qty = 1; 
	}
	?>
	<!-- bundle selection box section -->
	<table width="95%">
	<tr>
		<td style="background-color: #3c3c3c; padding: 10px; color: #fff; font-weight: bold;">Select Sizes:</td>
	</tr>
	</table>
	<?php	
	for ($l = 0; $l < $qty; $l++) {
	?>
	<form action="" method="post">
	<script type="text/javascript">opts = new Array();</script>
	<table cellpading="5" class="bundleBox" cellspacing="3">
	<tr>
		<td><table width="100%">
			<tr>
				<td class="kit"><?php 
				$sql_prod = "SELECT ProductDetailName FROM products WHERE id=".$prodid;
				$query = mysql_query($sql_prod) or die("Product Details Error: " . mysql_error());
				$result = mysql_fetch_assoc($query);
				$set = $l + 1;
				echo "Kit " . $set;
				?></td>
			<td width="95%"><?php
			$sql_bundle_size = "SELECT Items FROM product_bundles WHERE ProductID=$prodid ORDER BY SortOrder ASC";
			$result_bundle_size = mysql_query($sql_bundle_size) or die("Bundle Item Error: " . mysql_error());
			$bnum = 1;
			$i = 0;
			$cx = 1; 
			while ($row_bundle1 = mysql_fetch_array($result_bundle_size)) {
				$result_size2 = getBundleSizeByGender($row_bundle1['Items'], $gender, ${'color'.$cx});
				$result_size3 = getBundleSize($row_bundle1['Items'], $color3); 
				$sqlCat = "SELECT CategoryID FROM category_items WHERE ProductID=".$row_bundle1['Items']." LIMIT 1";
				$resultCat = mysql_query($sqlCat);
				$rowCat = mysql_fetch_assoc($resultCat);
				$sql_category_product = "SELECT Category FROM category WHERE id=".$rowCat["CategoryID"];
				$resultCategory = mysql_query($sql_category_product) or die("Bundle Category Error: " . mysql_error());
				$row_categ = mysql_fetch_assoc($resultCategory);
				?>
            	<div id="divSize<?=$bnum."_".$i;?>" name="divSize<?=$bnum."_".$i;?>" style="width:190px; float:left;">
				<?php 
				if ($i == 0) {
					if (stripos(strtolower($row_categ["Category"]), 'jerseys') !== FALSE) {
						$categoryAttribute = "Jersey";
					} else {
						$categoryAttribute = $row_categ["Category"]; 
					}
				}
				if ($i == 1) {
					if (stripos(strtolower($row_categ["Category"]), 'shorts') !== FALSE) {
						$categoryAttribute = "Shorts";
					} else {
						$categoryAttribute = $row_categ["Category"]; 
					}
				}
				if ($i == 2) {
					if (stripos(strtolower($row_categ["Category"]), 'socks') !== FALSE) {
						$categoryAttribute = "Socks";
					} else {
						$categoryAttribute = $row_categ["Category"]; 
					}
				}
				echo $categoryAttribute; 
				$categoryAttribute = empty($categoryAttribute)?'Size':$categoryAttribute;
				?>
				<select name="<?php echo $categoryAttribute;?>" id="set<?php echo $l;?>:size:<?=$row_bundle1['Items']?>" onchange="setSizeBundle(this)"> 
					<option value=""><?php echo $categoryAttribute;?> Size</option>
					<?php
					if (mysql_num_rows($result_size2) != 0) {
						while ($row_sitems = mysql_fetch_array($result_size2)) {
							echo "<option value=" . $row_sitems["SizeSKU"] . ">" . $row_sitems["Size"] . "</option>"; // bundle item selections
						}
					} else {
						while ($row_sitems2 = mysql_fetch_array($result_size3)) {
							echo "<option value=\"$row_sitems2[SizeSKU]\">$row_sitems2[Size]</option>";
						}
					}
					?>
				</select>
                </div>
				<script type="text/javascript">
				opts.push("size<?=$bnum."_".$i;?>");
				optsname.push("Product <?=$bnum."_".$i;?> Size");
				</script>
				<?php	
				$k++;
				$i++;
				$cx++;
			}

            if ($l == ($qty - 1)) {
			?>
				<!-- Bundle Add to Cart button -->
                <div style="text-align:left;float:right;width:170px;margin-right:95px;">
                    <div style="height:19px;"></div>
                 	<button type="button" id="addCart2" name="addCart" value="" class="cart" style="display:none;float:none;"></button>
        			<button type="button" id="continueImprint2_newver" name="continueimprintCart" onclick="imprintPagecall()" class="continueImprint" style="display:none;width: 155px; height: 32px;border:0px;cursor:pointer"></button>
                </div>
            	<?php 
            }
            ?>
            <?php
            	//**** Repeat Selection coding ***/
				// if (($l == 0) && ($qty >= 2)) {
			?>       
                    <!-- div style="float:left; width:100%; margin-top:7px;">
                        <div id="divSize<?=$bnum."_".$i;?>" name="divSize<?=$bnum."_".$i;?>" style="width:190px; display:inline-block; margin-left:10px;">
                            <div style="float:left;"><input type="checkbox" name="repeat_selection" id="repeat_selection" value="repeatselection" /></div>
                            <div style="float:left; font-weight:bold; margin-top:-3px; margin-left:7px;">REPEAT SELECTION</div>
                        </div>
                    </div -->
        	<?php 
        		// }
        	?>
			</td>
		</tr>
		</table></td><!-- end bundleBox -->
	</tr>
    </table>
   	</form>
   	<hr class="style-two">
	<?php
	}
	mysql_close($conn);
	exit();
}

// singleitems insertion functionality
if ($_POST["type"] == "singleitems") {		
		$qty = $_POST["qty"];
		$proid = $_POST["proid"];
		$checkedimpt = $_POST["checkedimpt"];
		$gender = $_POST["gender"];	
		$color_sku = $_SESSION["singleitems"][$proid]["color"];	
		$size_sku = $_POST["sizesku"];
		$_SESSION["singleitems"]["qty"] = $qty;

		if($qty == '') { 
			$qty = 1; 
		}

		for($l=0; $l<$qty; $l++) {
			if($l==1) {
		?>
				<div class="bundleitems2">
		<?php } ?>
			<form action="" method="post">
				<script>opts = new Array();</script>
				<table cellpading="5" cellspacing="3" style="width: 955px; margin: 20px 0px 20px 20px; float: left;">
					<tr>
						<td>
							<table width="100%">
								<tr>
									<td style="background-color: #3c3c3c; padding: 10px; color: #fff; font-weight: bold;">
										<?php 
											$sql_prod = "select ProductDetailName from products where id=".$proid;
											$query = mysql_query($sql_prod);
											$result = mysql_fetch_assoc($query);
											$set = $l + 1;
											echo $result["ProductDetailName"]."&nbsp;(SET ".$set.")";
										?>
									</td>
								</tr>
								<tr>
									<td width="100%">
										<div style="float:left; width:60%;">
											<?php
												$bnum = 1;
												$k = 0;
												$count_size = 0;
												
												$sql_size = "SELECT DISTINCT Size, SizeSKU FROM product_options WHERE ProductID=$proid ORDER BY Position ASC";
												$result_size = mysql_query($sql_size);
												$row_size1 = mysql_fetch_assoc($result_size);
												$num_size = mysql_num_rows($result_size);
					
												if($num_size > 0 && $row_size1["Size"] != '') {  ?>
													<div id="divSize<?=$bnum."_".$i;?>" name="divSize<?=$bnum."_".$i;?>" style="width:190px; float:left;">
														<div style="width: 175px;margin-left:9px">Select Size</div>
															<select id="set<?php echo $l;?>:size:<?php echo $proid;?>" onChange="setSizeSingle(this)" name="select_size">
																<option value="">Select Size</option>
																<?php 
																	if(empty($gender) or ($gender=="undefined")) {
																		$sql_size = "SELECT Distinct product_options.Size, product_options.SizeSKU FROM product_options, sizes WHERE product_options.Size=sizes.Size AND product_options.ProductID=$proid AND product_options.Inventory>0 AND product_options.SizeSKU=sizes.SKU ORDER BY product_options.Position ASC";
																		$result_size = mysql_query($sql_size);
																		while($row_size = mysql_fetch_array($result_size)) { ?>
																			<option value="<?php echo $row_size["SizeSKU"];?>" <?php if($size_sku==$row_size["SizeSKU"]) echo 'selected="selected"';?>><?php echo $row_size["Size"];?></option>
																			<?php
																		}	   
																	} else {
																		$sql_size = "Select Distinct product_options.Size, product_options.SizeSKU From product_options ,sizes Where product_options.Size = sizes.Size And product_options.ProductID=$proid And product_options.Gender='".$gender."' And product_options.Inventory > 0 And product_options.SizeSKU = sizes.SKU ORDER BY product_options.Position ASC ";
																		$result_size = mysql_query($sql_size);
																		while($row_size = mysql_fetch_array($result_size)) { ?>
																			<option value="<?php echo $row_size["SizeSKU"];?>" <?php if($size_sku==$row_size["SizeSKU"]) echo 'selected="selected"';?>><?php echo $row_size["Size"];?></option>
																			<?php
																		}	
																	} ?>
															</select>
                                                        </div>
														<?php 
												}

												$sql_color = "SELECT DISTINCT Color, ColorSKU FROM product_options WHERE ProductID=$proid AND Inventory>0 ORDER BY Color";
												$result_color = mysql_query($sql_color);
												$row_color1 = mysql_fetch_assoc($result_color);
												$num_color = mysql_num_rows($result_color);
					
												if($num_color>0 && $row_color1["Color"] != '') { ?>
													<div id="divSize<?=$bnum."_".$i;?>" name="divSize<?=$bnum."_".$i;?>" style="width:190px; float:left;">
														<div style="width: 175px;margin-left:9px"> Select Colors  </div>
														<select id="set<?php echo $l;?>:color:<?php echo $proid;?>"  onchange='initColors(this)' name="select_color">
															<option value="">Select Color</option>
															
															<?php
																$sql_color = "SELECT DISTINCT Color, ColorSKU FROM product_options WHERE ProductID=$proid AND Inventory>0 ORDER BY Color";
																$result_color = mysql_query($sql_color);
			
																while($row_color = mysql_fetch_array($result_color)) { ?>
																	<option value="<?php echo $row_color["ColorSKU"];?>" <?php if($color_sku==$row_color["ColorSKU"]) echo 'selected="selected"';?>><?php echo $row_color["Color"];?></option>
																	<?php
																} ?>
														</select>
													</div>
													<?php
												} ?>
                                        </div>
                                        <?php 
										if($l==0){?>
											<div style="text-align: left;  float:right; width:170px; margin-right:95px;">
												<div style="height:19px;"></div>
												<!-- Add to Cart button -->
												<button type="button" id="addCart2" name="addCart"  value="" class="cart" style="display:none;float:none;"></button>
												<!-- Continue to Imprint button -->
												<button type="button" id="continueImprint2_newver" name="continueimprintCart" onclick="imprintPagecall()" class="continueImprint" style="display:none;width: 155px; height: 32px;border:0px;cursor:pointer"></button>
											</div>
											<?php 
										}
										
										if(($l==0)&&($qty>=2)) { ?>
											<div style="float:left; width:100%;">
												<div id="divSize<?=$bnum."_".$i;?>" name="divSize<?=$bnum."_".$i;?>" style="width:190px; display:inline-block; margin-left:10px;">
													<div style="float:left;"><input type="checkbox" name="repeat_selection" id="repeat_selection" value="repeatselection" /></div><div style="float:left; font-weight:bold; margin-top:7px; margin-left:7px;">REPEAT SELECTION</div>
												</div>
											</div>
                                            <?php 
										} ?>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</form>
			<?php
			if($l==($qty-1)) { ?>
				</div>
				<?php 
			}
	}
	mysql_close($conn);
	exit();
} // end SINGLEITEMS
	
// Repeat Selection functionality
if ($_POST["type"] == "repeatselection") {		
	$qty = $_POST["qty"];
	$proid = $_POST["proid"];
	$gender = $_POST["gender"];
	$selectedsize = $_POST["selectedsize"];
	$selectedcolor = $_POST["selectedcolor"];	
	if ($qty == '') { $qty = 1; }
	for ($l=0; $l < $qty; $l++) {
		if ($l == 0) { continue; }
	?>
<form action="" method="post">
<script>opts = new Array();</script>
<table cellpading="5" cellspacing="3" style="width: 955px; margin: 20px 0px 20px 20px; float: left;">
<tr>
<td>
<table width="100%">
<tr>
<td  style="background-color: #3c3c3c; padding: 10px; color: #fff; font-weight: bold;">
<?php 
$sql_prod = "select ProductDetailName from products where id=".$proid;
$query = mysql_query($sql_prod);
$result = mysql_fetch_assoc($query);
$set = $l + 1;
echo $result["ProductDetailName"]."&nbsp;(SET ".$set.")";
?></td>
</tr>
<tr>
<td width="100%">
<div style="float:left; width:100%;">
<?php
$bnum = 1;
$k = 0;
$count_size = 0;
if(empty($gender)) {
	$sql_gender = "SELECT Gender, GenderSKU FROM product_pricing WHERE ProductID=$proid ORDER BY Gender";
	$result_gender = mysql_query($sql_gender);
	while($row_gender = mysql_fetch_array($result_gender)) {
		$sql_size = "Select Distinct  product_options.Size, product_options.SizeSKU	From product_options ,sizes Where product_options.Size = sizes.Size And product_options.ProductID=$proid And product_options.Gender='".$row_gender["Gender"]."' And product_options.Inventory > 0 And product_options.SizeSKU = sizes.SKU";
		$result_size = mysql_query($sql_size);
         while($row_size = mysql_fetch_array($result_size)) {
			$count++;
		}
	}
} else {		  
	$sql_size = "Select Distinct product_options.Size, product_options.SizeSKUFrom product_options ,sizes 
                Where product_options.Size = sizes.Size  and product_options.ProductID=$proid And 			  
				product_options.Gender='$gender' And product_options.Inventory > 0 And product_options.SizeSKU = sizes.SKU";
	$result_size = mysql_query($sql_size);
    while($row_size = mysql_fetch_array($result_size)) {
        $count++;
    }	
}

if($countb> 0) {
	$sql_size = "SELECT DISTINCT Size, SizeSKU FROM product_options WHERE ProductID=$proid  ORDER BY Position ASC";
    $result_size = mysql_query($sql_size);
	$row_size1 = mysql_fetch_assoc($result_size);
	$num_size = mysql_num_rows($result_size);
					
	if($num_size>0 && $row_size1["Size"] != '') {
	?>
        <div id="divSize<?=$bnum."_".$i;?>" name="divSize<?=$bnum."_".$i;?>" style="width:190px; float:left;">
		<div style="width: 175px;margin-left:9px">Select Size</div>
		<select id="set<?php echo $l;?>:size:<?php echo $proid;?>" onChange="setSizeSingle(this)" name="select_size"><option value="">Select Size</option>
              <?php 
			  if(empty($gender)) {
				  $sql_gender = "SELECT Gender, GenderSKU FROM product_pricing WHERE ProductID=$proid ORDER BY Gender";
						$result_gender = mysql_query($sql_gender);
						while($row_gender = mysql_fetch_array($result_gender)) {
			    $sql_size = "Select Distinct product_options.Size, product_options.SizeSKU From product_options ,sizes Where product_options.Size = sizes.Size  And product_options.ProductID=$proid And product_options.Gender='".$row_gender["Gender"]."' And product_options.Inventory > 0 And product_options.SizeSKU = sizes.SKU";
				$result_size = mysql_query($sql_size);
                while($row_size = mysql_fetch_array($result_size)) {
                ?>
                     <option value="<?php echo $row_size["SizeSKU"];?>" <?php if($selectedsize==$row_size["SizeSKU"]) echo 'selected="selected"';?>><?php echo $row_size["Size"];?> </option>
                <?php
                    }	   
				 }			   
			  } else {
				    $sql_size = "Select Distinct product_options.Size, product_options.SizeSKU From product_options ,sizes Where product_options.Size = sizes.Size  And product_options.ProductID=$proid And product_options.Gender='$gender' And product_options.Inventory > 0 And product_options.SizeSKU = sizes.SKU";
					$result_size = mysql_query($sql_size);
                    while($row_size = mysql_fetch_array($result_size)) {
                    ?>
       					<option value="<?php echo $row_size["SizeSKU"];?>" <?php if($selectedsize==$row_size["SizeSKU"]) echo 'selected="selected"';?>><?php echo $row_size["Size"];?> </option>
                    <?php
                    }	
			  }	
			  ?>
               <?php 
                ?>
              </select></div>
             <?php 
							   
					}
			   }
			   
			        $count_colors=0;
			   
        			$sql_color = "SELECT DISTINCT Color, ColorSKU FROM product_options WHERE ProductID=$proid AND Inventory>0 ORDER BY Color";
                	$result_color = mysql_query($sql_color);
				
                    while($row_color = mysql_fetch_array($result_color)) {
						$count_colors++;
					}
					
					$sql_color = "SELECT DISTINCT Color, ColorSKU FROM product_options WHERE ProductID=$proid AND Inventory>0 ORDER BY Color";
                	$result_color = mysql_query($sql_color);
					$row_color1 = mysql_fetch_assoc($result_color);
					$num_color = mysql_num_rows($result_color);
					
					if($num_color>0 && $row_color1["Color"] != '') {
						if($count_colors>0)
						{
				?>
                  <div id="divSize<?=$bnum."_".$i;?>" name="divSize<?=$bnum."_".$i;?>" style="width:190px; float:left;"><div style="width: 175px;margin-left:9px"> Select Colors  </div>
                <select id="set<?php echo $l;?>:size:<?php echo $proid;?>"  onchange='initColors(this)' name="select_color"><option value="">Select Color</option>
                <?php
        			$sql_color = "SELECT DISTINCT Color, ColorSKU FROM product_options WHERE ProductID=$proid AND Inventory>0 ORDER BY Color";
                	$result_color = mysql_query($sql_color);
				
                    while($row_color = mysql_fetch_array($result_color)) {
						?>
                        <option value="<?php echo $row_color["ColorSKU"];?>" <?php if($selectedcolor==$row_size["ColorSKU"]) echo 'selected="selected"';?>><?php echo $row_color["Color"];?></option>
                        <?php
                    }
                ?>
              </select>
              </div>
              <?php
						}
			  		}
?>
<?php
?>
</div>                                                      
</td>
</tr>
</table>
</td>
</tr>
</table>
</form>
<?php
	}
	mysql_close($conn);
	exit();
}
	
if($_POST['type']=="singleitems_size") {
		$gender=$_POST['gender'];
		$proid=$_POST['proid'];
		$size_sku=$_POST['sizesku'];
		
		$sql_size = "Select Distinct   product_options.Size,  product_options.SizeSKU	From product_options ,sizes Where product_options.Size = sizes.Size  And product_options.ProductID=$proid And product_options.Gender='".$gender."' And product_options.Inventory > 0 And product_options.SizeSKU = sizes.SKU";
		if($gender=="")
		   {
			   $sql_size = "Select Distinct product_options.Size, product_options.SizeSKU	From product_options ,sizes Where product_options.Size = sizes.Size  And product_options.ProductID=$proid And product_options.Gender='$gender' And product_options.Inventory > 0 And product_options.SizeSKU = sizes.SKU";
		   }

                    $result_size = mysql_query($sql_size);
					$row_size1 = mysql_fetch_assoc($result_size);
					$num_size = mysql_num_rows($result_size);
					
					if($num_size>0 && $row_size1["Size"] != '') {
			  ?>
              <form action="" method="post">
		<div id="divSizeG">
                <select id="size" name="size" onchange="setsize_below()">
                <option value="">Select Size</option>
                <?php
                   $result_size = mysql_query($sql_size);
                    while($row_size = mysql_fetch_array($result_size)) {
                       ?>
                       <option value="<?php echo $row_size["SizeSKU"]?>" <?php if($size_sku==$row_size["SizeSKU"]) echo 'selected="selected"';?>><?php echo $row_size["Size"];?></option>
					   <?php
                    }
                ?>
              </select>
	     </div>
              <script>opts.push("size");</script>
              </form>
      		<?php
			}
	}	

if($_POST["type"] == 'chkInv') {
		$prodid = $_POST["id"];
		$SizeSKU = $_POST["size"];
		$ColorSKU = $_POST["color"];
		$inv = "0";
		
		//$sql_inv = "SELECT Inventory FROM product_options WHERE ProductID=$_POST[id] AND SizeSKU='$_POST[size]' AND ColorSKU='$_POST[color]' LIMIT 1";
		$sql_inv = "SELECT Inventory FROM product_options WHERE ProductID=$prodid ";
		if($SizeSKU != '') {
			$sql_inv .= "AND SizeSKU='$SizeSKU' ";
		}
		if($ColorSKU != '') {
			$sql_inv .= "AND ColorSKU='$ColorSKU' ";
		}
		$sql_inv .= "LIMIT 1";
		
		$result_inv = mysql_query($sql_inv);
		$num_inv = mysql_num_rows($result_inv);
		
		if($num_inv>0) {
			$row_inv = mysql_fetch_assoc($result_inv);
			$inv = $row_inv["Inventory"];
		}
		
		echo $inv;
		mysql_close($conn);
		exit();
	}
?>