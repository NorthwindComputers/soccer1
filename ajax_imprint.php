<?php
if ($_POST) {
	require 'cpadmin/includes/db.php';
	session_start();
	
	$designId = intval($_POST['id']);
	$designType = strval($_POST['design']);
	$activeType = intval($_POST['type']);
	$prodid = intval($_POST['prodid']);
	$color = strval($_POST['color']);
	$qty = intval($_POST['qty']);
	$htmlId = strval($_POST['html_id']);
	$parent = intval($_POST['parent']);
	
	function getImprintJersey($sql = '') {
		
		$jesrseyInfo = array();
		if ($sql != '') {
			$r_shoppingCartOptionValue = mysql_query($sql);
			while ($optionData = mysql_fetch_array($r_shoppingCartOptionValue)) {
				if ($optionData['value'] && $optionData['type'] && $optionData['option_id']) {
					$jesrseyInfo[] = array(
								'qty' => 1, 
								'name' => $optionData['ProductName'],
								'size' => $optionData['SizeSKU'],
								'value' => $optionData['value'],
								'color' => ''
							);
				}
			}
			return $jesrseyInfo;
		}
		
		$b_count = sizeof($_SESSION["bundleItems"]["items"]);
		if ($b_count > 0) {
			foreach( $_SESSION["bundleItems"]["items"] as $key => $value) {								
				if (!empty($_SESSION['bundleItems']['items'][$key]['bid'])) {
					$sizeCount = array_count_values($_SESSION['bundleItems']['items'][$key]['size']);
					if (sizeof($sizeCount)>0) {
						foreach( $sizeCount as $sizeSKU => $sizeQty) {
							$bundleItem = "SELECT DISTINCT(p.RootSKU) AS RootSKU, p.ProductDetailName FROM products AS p 
										   INNER JOIN category_items AS c_i ON c_i.ProductID = p.id 
										   INNER JOIN category AS c ON c.id = c_i.CategoryID AND c.Category LIKE ( '%Jersey%' ) 
										   WHERE p.id=".$_SESSION['bundleItems']['items'][$key]['bid']." LIMIT 1";
							
					   		$r_bundleItem = mysql_query($bundleItem);					
							$row = @mysql_fetch_assoc($r_bundleItem);
							
							if ($row) {
								$jesrseyInfo[] = array(
														'qty' => $sizeQty,
														'name' => $row['ProductDetailName'],
														'size' => $sizeSKU,
														'value' => '',
														//'root' => $row['RootSKU'],
														////'gender' => $row['Gender'],
														//'gender_sku' => $row['GenderSKU'],
														'color' => $_SESSION["bundleItems"][$key]["color"]
													);
							}
						}
					}	
				}
			}
		} else {
			$jesrseyInfo[] = array(
									'qty' => $_SESSION["imprintConfig"]["qty"],
									'name' => $_SESSION["imprintConfig"]["productname"],
									'size' => $_SESSION["imprintConfig"]["size"],
									'value' => '',
									//'root' => $row['RootSKU'],
									//'gender' => $_SESSION["imprintConfig"]["gender"],
									//'gender_sku' => $_SESSION["imprintConfig"]["gendersku"],
									'color' => $_SESSION["imprintConfig"]["color"]
								);
		}
		return $jesrseyInfo;
	}
	
	/** Get Imprint Category By Product Id **/
	$catSql = "SELECT ID_IMPRINT_CATEGORY,ProductType FROM products WHERE id=".$prodid;
	$cat_result = mysql_query($catSql);
	$catInfo = @mysql_fetch_assoc($cat_result);
	$cat = $catInfo['ID_IMPRINT_CATEGORY'];
	
	
	$logoUrl = 'cpadmin/logo/';
	$active = array();
	$logoType = 0;
	/** Check Design for front or Back **/
	if ($designType == 'f') {
		$logoType = 1;
		if ($designId == 1) {
			$active = array(1 => 'logo', 2 => 'number');
			$_SESSION["imprintPrice"][$designType.'namePrice'] = 0;
			$_SESSION["imprintPrice"][$designType.'nameSetupFee'] = 0;
		} else if ($designId == 2) {
			$active = array( 1 => 'logo', 3 => 'name');
			$_SESSION["imprintPrice"][$designType.'numberPrice'] = 0;
			$_SESSION["imprintPrice"][$designType.'numberSetupFee'] = 0;
		} else if ($designId == 3) {
			$active = array( 1 => 'logo', 2 => 'number');
			$_SESSION["imprintPrice"][$designType.'namePrice'] = 0;
			$_SESSION["imprintPrice"][$designType.'nameSetupFee'] = 0;
		} else if ($designId == 4) {		
			$active = array(2 => 'number');
			$_SESSION["imprintPrice"][$designType.'namePrice'] = 0;
			$_SESSION["imprintPrice"][$designType.'logoPrice'] = 0;
			$_SESSION["imprintPrice"][$designType.'nameSetupFee'] = 0;
			$_SESSION["imprintPrice"][$designType.'logoSetupFee'] = 0;
		}
	} else if ($designType == 's') {
		$logoType = 3;
		if ($designId == 1) {
			$active = array(1 => 'logo');
			$_SESSION["imprintPrice"][$designType.'namePrice'] = 0;
			$_SESSION["imprintPrice"][$designType.'nameSetupFee'] = 0;
			$_SESSION["imprintPrice"][$designType.'numberPrice'] = 0;
			$_SESSION["imprintPrice"][$designType.'numberSetupFee'] = 0;
		} else if ($designId == 2) {
			$active = array( 1 => 'number');
			$_SESSION["imprintPrice"][$designType.'namePrice'] = 0;
			$_SESSION["imprintPrice"][$designType.'logoPrice'] = 0;
			$_SESSION["imprintPrice"][$designType.'nameSetupFee'] = 0;
			$_SESSION["imprintPrice"][$designType.'logoSetupFee'] = 0;
		} 
	}  else if ($designType == 'so') {
		$logoType = 4;
		$active = array(1 => 'logo');
		$_SESSION["imprintPrice"][$designType.'namePrice'] = 0;
		$_SESSION["imprintPrice"][$designType.'nameSetupFee'] = 0;
		$_SESSION["imprintPrice"][$designType.'numberPrice'] = 0;
		$_SESSION["imprintPrice"][$designType.'numberSetupFee'] = 0;
	} else {
		$logoType = 2;
		if ($designId == 1) {
			$active = array(3 => 'name');
			$_SESSION["imprintPrice"][$designType.'numberPrice'] = 0;
			$_SESSION["imprintPrice"][$designType.'logoPrice'] = 0;
			$_SESSION["imprintPrice"][$designType.'numberSetupFee'] = 0;
			$_SESSION["imprintPrice"][$designType.'logoSetupFee'] = 0;
		} else if ($designId == 2) {
			$active = array( 3 => 'name', 2 => 'number');
			$_SESSION["imprintPrice"][$designType.'logoPrice'] = 0;
			$_SESSION["imprintPrice"][$designType.'logoSetupFee'] = 0;
		} else if ($designId == 3) {
			$active = array(1 => 'logo', 3 => 'name');
			$_SESSION["imprintPrice"][$designType.'numberPrice'] = 0;
			$_SESSION["imprintPrice"][$designType.'numberSetupFee'] = 0;
		} else if ($designId == 4) {		
			$active = array( 1 => 'logo');
			$_SESSION["imprintPrice"][$designType.'namePrice'] = 0;
			$_SESSION["imprintPrice"][$designType.'numberPrice'] = 0;
			$_SESSION["imprintPrice"][$designType.'nameSetupFee'] = 0;
			$_SESSION["imprintPrice"][$designType.'numberSetupFee'] = 0;
		}
	}
	/** End Check Design for front or Back **/
	
	if ($active) {
		$keyList = array_keys($active);
		
		if (array_key_exists($keyList[$activeType-1], $active)) {
			$sql = "SELECT ii.* FROM imprint_cusom_options AS ico 
					INNER JOIN imprint_information AS ii ON ii.option_id=ico.id AND design_type='".$logoType."'
					WHERE ico.category_id=".$cat." AND ico.type=".$keyList[$activeType-1];
			if($parent>0) {
				$sql .= " AND (ii.parent=".$parent." OR ii.recNum=".$parent.")";
			} else {
				$sql .= " AND ii.parent=".$parent;
			}
			//if ($color != '' && $color != 'd') {
			//	$sql .= " AND ii.color_code='".$color."' ";
			//}
			$sql .= ' ORDER BY position';
			$result = mysql_query($sql);
			
			if ($color != '') {
				$accordin = '<a>'.ucfirst($active[$keyList[$activeType-1]]).'</a><div  class="content">';
				$colordiv = str_replace('accordin', 'quantity_information',$htmlId);
				if ($designType == 'f') {
					$activeHtml[1] = '<img src="%s" style="cursor:pointer;" onclick="changeFrontDesign(this.src, \'logo\',%d,%d); loadQuantity(\''.$designId.'\',\''.$designType.'\', \''.$activeType.'\',\''.$prodid.'\',\''.$qty.'\',\''.$htmlId.'\',\''.$colordiv.'\',%d);" alt="Logo" width="63" height="63" />';
					$activeHtml[2] = '<img src="%s" style="cursor:pointer;" onclick="changeFrontDesign(this.src, \'number\',%d,%d); loadQuantity(\''.$designId.'\',\''.$designType.'\', \''.$activeType.'\',\''.$prodid.'\',\''.$qty.'\',\''.$htmlId.'\',\''.$colordiv.'\',%d);" alt="Number" height="30" />';
					$activeHtml[3] = '<img src="%s" style="cursor:pointer;" onclick="changeFrontDesign(this.src, \'name\',%d,%d); loadQuantity(\''.$designId.'\',\''.$designType.'\', \''.$activeType.'\',\''.$prodid.'\',\''.$qty.'\',\''.$htmlId.'\',\''.$colordiv.'\',%d);" alt="Name" />';
				} else if ($designType == 's') {
					$activeHtml[1] = '<img src="%s" style="cursor:pointer;" onclick="changeShortDesign(this.src, \'logo\',%d,%d); loadQuantity(\''.$designId.'\',\''.$designType.'\', \''.$activeType.'\',\''.$prodid.'\',\''.$qty.'\',\''.$htmlId.'\',\''.$colordiv.'\',%d);" alt="Logo" width="63" height="63" />';
					$activeHtml[2] = '<img src="%s" style="cursor:pointer;" onclick="changeShortDesign(this.src, \'number\',%d,%d); loadQuantity(\''.$designId.'\',\''.$designType.'\', \''.$activeType.'\',\''.$prodid.'\',\''.$qty.'\',\''.$htmlId.'\',\''.$colordiv.'\',%d);" alt="Number" height="30" />';
					$activeHtml[3] = '<img src="%s" style="cursor:pointer;" onclick="changeShortDesign(this.src, \'name\',%d,%d); loadQuantity(\''.$designId.'\',\''.$designType.'\', \''.$activeType.'\',\''.$prodid.'\',\''.$qty.'\',\''.$htmlId.'\',\''.$colordiv.'\',%d);" alt="Name" />';
				} else if ($designType == 'so') {
					$activeHtml[1] = '<img src="%s" style="cursor:pointer;" onclick="changeSockDesign(this.src, \'logo\',%d,%d); loadQuantity(\''.$designId.'\',\''.$designType.'\', \''.$activeType.'\',\''.$prodid.'\',\''.$qty.'\',\''.$htmlId.'\',\''.$colordiv.'\',%d);" alt="Logo" width="63" height="63" />';
					$activeHtml[2] = '<img src="%s" style="cursor:pointer;" onclick="changeSockDesign(this.src, \'number\',%d,%d); loadQuantity(\''.$designId.'\',\''.$designType.'\', \''.$activeType.'\',\''.$prodid.'\',\''.$qty.'\',\''.$htmlId.'\',\''.$colordiv.'\',%d);" alt="Number" height="30" />';
					$activeHtml[3] = '<img src="%s" style="cursor:pointer;" onclick="changeSockDesign(this.src, \'name\',%d,%d); loadQuantity(\''.$designId.'\',\''.$designType.'\', \''.$activeType.'\',\''.$prodid.'\',\''.$qty.'\',\''.$htmlId.'\',\''.$colordiv.'\',%d);" alt="Name" />';
				} else {
					$activeHtml[1] = '<img src="%s" style="cursor:pointer;" onclick="changeBackDesign(this.src, \'logo\',%d,%d); loadQuantity(\''.$designId.'\',\''.$designType.'\', \''.$activeType.'\',\''.$prodid.'\',\''.$qty.'\',\''.$htmlId.'\',\''.$colordiv.'\',%d);" alt="Logo" width="63" height="63" />';
					$activeHtml[2] = '<img src="%s" style="cursor:pointer;" onclick="changeBackDesign(this.src, \'number\',%d,%d); loadQuantity(\''.$designId.'\',\''.$designType.'\', \''.$activeType.'\',\''.$prodid.'\',\''.$qty.'\',\''.$htmlId.'\',\''.$colordiv.'\',%d);" alt="Number" height="30" />';
					$activeHtml[3] = '<img src="%s" style="cursor:pointer;" onclick="changeBackDesign(this.src, \'name\',%d,%d); loadQuantity(\''.$designId.'\',\''.$designType.'\', \''.$activeType.'\',\''.$prodid.'\',\''.$qty.'\',\''.$htmlId.'\',\''.$colordiv.'\',%d);" alt="Name" />';
				}
				
				while ($imprint = mysql_fetch_array($result)) {
					$accordin .= sprintf($activeHtml[$keyList[$activeType-1]], $logoUrl.$imprint['image'], $imprint['option_id'], $imprint['index_seq'], $imprint['recNum']);
				}			
	        		$accordin .= '</div>';
	        		echo $accordin;
			} else {
				$priceSelect = "SELECT 
					(CASE  WHEN ".$qty." BETWEEN p.STARTQT_1 AND p.ENDQT_1 THEN p.PRICE1 
						   WHEN ".$qty." BETWEEN p.STARTQT_2 AND p.ENDQT_2 THEN p.PRICE2 
						   WHEN ".$qty." BETWEEN p.STARTQT_3 AND p.ENDQT_3 THEN p.PRICE3
						   WHEN ".$qty." BETWEEN p.STARTQT_4 AND p.ENDQT_4 THEN p.PRICE4
					   ELSE 0
					END) AS price, setup_fee
					FROM imprint_cusom_options AS ico
					INNER JOIN pricing AS p ON p.IDOPTION=ico.id
					WHERE category_id=".$cat." AND type=".$keyList[$activeType-1];
				$price_result = mysql_query($priceSelect);
				$priceInfo = @mysql_fetch_assoc($price_result);
				
				$priceGridSql = "SELECT p.* FROM imprint_cusom_options AS ico
								INNER JOIN pricing AS p ON p.IDOPTION=ico.id
								WHERE category_id=".$cat." AND type=".$keyList[$activeType-1];
				$pricegrid_result = mysql_query($priceGridSql);
				$pricegridInfo = @mysql_fetch_assoc($pricegrid_result);
				$colorHtml = '';
				if ($priceInfo) {
					$colorHtml = '<table width="96%" align="center">
								<tr>
									<th colspan="4" class="imprint_grid_header">
										QUANTITY DISCOUNTING
									</th>
								</tr>
								<tr>
									<td class="qty_col1">'.$pricegridInfo['STARTQT_1'].'-'.$pricegridInfo['ENDQT_1'].'</td>
									<td class="qty_col2">'.$pricegridInfo['STARTQT_2'].'-'.$pricegridInfo['ENDQT_2'].'</td>
									<td class="qty_col3">'.$pricegridInfo['STARTQT_3'].'-'.$pricegridInfo['ENDQT_3'].'</td>
									<td class="qty_col4">'.$pricegridInfo['STARTQT_4'].'-'.$pricegridInfo['ENDQT_4'].'</td>
								</tr>
								<tr>
									<td class="qty_col1">$'.$pricegridInfo['PRICE1'].'</td>
									<td class="qty_col2">$'.$pricegridInfo['PRICE2'].'</td>
									<td class="qty_col3">$'.$pricegridInfo['PRICE3'].'</td>
									<td class="qty_col4">$'.$pricegridInfo['PRICE4'].'</td>
								</tr>
							</table>';
							
						$colorHtml .= '<div style="clear:both;height:10px;width:100%"></div>
									   <div style="width:100%">
								 			<div class="imprint_price" id="price">$'.$priceInfo['price'].'</div>
											<div class="color_grid">
												<table width="100%" cellpadding="0" cellspacing="0">
													<tr>
														<th class="imprint_color_header">
															COLOR
														</th>
													</tr>
													<tr>
														<td>
															<div class="imprint-content">';
															
					//$colorHtml .= '<span style="padding:3px 2px;margin:0px 3px;color:#000;cursor:pointer;" 
					//					onclick="loadAccordin('.$designId.',\''.$designType.'\','.$activeType.','.$prodid.',\'d\', '.$qty.',\''.$htmlId.'\')">ALL</span>';
					//$colorHtml .= '<span style="padding:3px 2px;margin:0px 3px;color:#000;cursor:pointer;" 
					//					onclick="resetDesign(\''.$designType.'\', '.$keyList[$activeType-1].')">NONE</span>';
										
					$breakCount = 2;
					if($parent == 0) {
						echo '';
					} else {
						while ($imprint = mysql_fetch_array($result)) {
						if ($breakCount%6 == 0)
							$colorHtml .= '<div style="clear:both;height:10px"></div>';
						$breakCount++;
							
						//$colorHtml .= '<span style="padding:3px 10px;margin:0px 3px;cursor:pointer;background:#'.$imprint['color_code'].'" 
						//				onclick="loadAccordin('.$designId.',\''.$designType.'\','.$activeType.','.$prodid.',\''.$imprint['color_code'].'\', '.$qty.',\''.$htmlId.'\')">&nbsp;</span>';
						
						$colorHtml .= '<span style="padding:3px 10px;margin:0px 3px;cursor:pointer;background:#'.$imprint['color_code'].'" 
										onclick="';
						
						if ($designType == 'f') {
							$colorHtml .= "changeFrontDesign(";
						} else if ($designType == 's') {
							$colorHtml .= "changeShortDesign(";
						} else if ($designType == 'so') {
							$colorHtml .= "changeSockDesign(";
						} else {
							$colorHtml .= "changeBackDesign(";
						}
						
						$colorHtml .= '\''.$logoUrl.$imprint['image'].'\',\'';
						
						if($keyList[$activeType-1] == "1") {
							$colorHtml .= 'logo';
						} else if ($keyList[$activeType-1] == "2") {
							$colorHtml .= 'number';
						} else if ($keyList[$activeType-1] == "3") {
							$colorHtml .= 'name';
						}
						
						$colorHtml .= '\','.$imprint['option_id'].','.$imprint['index_seq'].')">&nbsp;</span>';
					}
					}
					
					$colorHtml .='</div>
								</td>
							</tr>
						<tr>
							<td style="padding:5px">
								<div style="width:150px;font-weight:bold;float:left;">SELECTED COLOR</div>
								<div style="float:right;"><span style="padding:3px 13px;background:#fff" id="selected_color">&nbsp;</span></div>
							</td>
						</tr>
					</table>
				</div>';
					if ($keyList[$activeType-1] == 1) {
						$_SESSION["imprintPrice"][$designType.'logoPrice'] = $priceInfo['price'];
						$_SESSION["imprintPrice"][$designType.'logoSetupFee'] = $priceInfo['price'];
					} else if ($keyList[$activeType-1] == 2) {
						$shoppingCartOptionValue = '';
						if (isset($_SESSION['itemid'])) {
							$shoppingCartOptionValue = "SELECT siod.*,sc.ProductName,sc.SizeSKU,sc.Qty FROM shopping_imprint_options AS sio
													INNER JOIN shopping_cart AS sc ON sc.id=sio.cart_id
													LEFT OUTER JOIN shopping_imprint_option_data AS siod ON siod.option_id=sio.id AND siod.type=2
								 					WHERE sio.cart_id=".$_SESSION['itemid'];
						}
						
						$cartProductInfo = getImprintJersey($shoppingCartOptionValue);
						$colorHtml .= '<table style="clear:both;width:100%">';
						$colorHtml .= '<tr>
											<th class="imprint_name_header">Description</th>
											<th class="imprint_name_header">Size</th>
											<th class="imprint_name_header">Name Or Sponsor</th>
										</tr>';
						$i = 1;
						foreach($cartProductInfo as $value) {
							for($j = 1; $j <= $value['qty']; $j++) {
								$colorHtml .= '<tr>';
								$colorHtml .= '<td>'.$value['name'].'</td>';
								$colorHtml .= '<td>'.$value['size'].'</td>';
								$colorHtml .= '<td>Number ['.$i.']*:<input type="text" class="required_value" name="'.$designType.'number[]" id="number" value="'.$value['value'].'" /></td>';
								$colorHtml .= '</tr>';
								$i++;
							}
						}
						$colorHtml .= '</table>';
						
						$_SESSION["imprintPrice"][$designType.'numberPrice'] = $priceInfo['price'];
						$_SESSION["imprintPrice"][$designType.'numberSetupFee'] = $priceInfo['price'];
					} else if ($keyList[$activeType-1] == 3) {
						$shoppingCartOptionValue = '';
						if (isset($_SESSION['itemid'])) {
							$shoppingCartOptionValue = "SELECT siod.*,sc.ProductName,sc.SizeSKU FROM shopping_imprint_options AS sio
													INNER JOIN shopping_cart AS sc ON sc.id=sio.cart_id
													LEFT OUTER JOIN shopping_imprint_option_data AS siod ON siod.option_id=sio.id AND siod.type=3
								 					WHERE sio.cart_id=".$_SESSION['itemid'];
						}
											
						$cartProductInfo = getImprintJersey($shoppingCartOptionValue);
						$colorHtml .= '<table style="clear:both;width:100%">';
						$colorHtml .= '<tr>
											<th class="imprint_name_header">Description</th>
											<th class="imprint_name_header">Size</th>
											<th class="imprint_name_header">Name Or Sponsor</th>
										</tr>';
						$i = 1;
						foreach($cartProductInfo as $value) {
							for($j = 1; $j <= $value['qty']; $j++) {
								$colorHtml .= '<tr>';
								$colorHtml .= '<td>'.$value['name'].'</td>';
								$colorHtml .= '<td>'.$value['size'].'</td>';
								$colorHtml .= '<td>'.'Name ['.$i.']*:<input type="text" class="required_value" name="'.$designType.'name[]" id="name" value="'.$value['value'].'"  /></td>';
								$colorHtml .= '</tr>';
								$i++;
							}
						}
						$colorHtml .= '</table>';
						
						$_SESSION["imprintPrice"][$designType.'namePrice'] = $priceInfo['price'];
						$_SESSION["imprintPrice"][$designType.'nameSetupFee'] = $priceInfo['price'];
					}
					
				$colorHtml .= '</div>';
			}
			echo $colorHtml;
		  }
       }
	}
} else {
	throw new Exception('Invalid request');
}



