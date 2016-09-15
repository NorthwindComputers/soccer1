<?php
	/**
	 * related products module include 
	 *
	 * Version: 1.0
	 * Updated: 26 Feb 2013
	 * By: Richard Tuttle
	 */
	
	require_once 'db.php';
	$prodid = mysql_real_escape_string($_POST["id"]);
	
	// search for related products 
	if($_POST["type"] == "searchrelated") {
		$sku = mysql_real_escape_string($_POST["sku"]);
		$name = mysql_real_escape_string($_POST["name"]);
		$pricelow = mysql_real_escape_string($_POST["pricelow"]);
		$pricehigh = mysql_real_escape_string($_POST["pricehigh"]);
		
		$sql_search = "SELECT id, RootSKU, ProductDetailName, NoneMemberPrice FROM products WHERE id<>$prodid";
		
		if($sku != '') { 
			$sql_search .= " AND RootSKU LIKE '$sku%'"; 
		}
		if($name != '') { 
			$sql_search .= " AND BrowserName LIKE '$name%'"; 
		}
		if($pricelow != '') { 
			$sql_search .= " AND NoneMemberPrice >= $pricelow"; 
		}
		if($pricehigh != '') { 
			$sql_search .= " AND NoneMemberPrice <= $pricehigh"; 
		}
		
		$result_search = mysql_query($sql_search);
		echo '<table cellpadding="5" cellspacing="0">';
		
		while($row_search = mysql_fetch_array($result_search)) {
			echo '<tr>';
			echo '<td class="checkbox"><input type="checkbox" id="related[]" name="related[]" value="'.$row_search["id"].'" /></td>';
			echo '<td class="sku">'.$row_search["RootSKU"].'</td>';
			echo '<td class="name">'.$row_search["ProductDetailName"].'</td>';
			echo '<td class="price">'.$row_search["NoneMemberPrice"].'</td>';
			echo '</tr>';
		}
		
		echo '</table>';
		mysql_close($conn);
		exit();
	}
	
	// search upsale products
	if($_POST["type"] == "searchupsale") {
		$sku = mysql_real_escape_string($_POST["sku"]);
		$name = mysql_real_escape_string($_POST["name"]);
		$pricelow = mysql_real_escape_string($_POST["pricelow"]);
		$pricehigh = mysql_real_escape_string($_POST["pricehigh"]);
		
		$sql_search = "SELECT id, RootSKU, ProductDetailName, NoneMemberPrice FROM products WHERE id<>$prodid";
		
		if($sku != '') { 
			$sql_search .= " AND RootSKU LIKE '$sku%'"; 
		}
		if($name != '') { 
			$sql_search .= " AND BrowserName LIKE '$name%'"; 
		}
		if($pricelow != '') { 
			$sql_search .= " AND NoneMemberPrice >= $pricelow"; 
		}
		if($pricehigh != '') { 
			$sql_search .= " AND NoneMemberPrice <= $pricehigh"; 
		}
		
		$result_search = mysql_query($sql_search);
		echo '<table cellpadding="5" cellspacing="0">';
		
		while($row_search = mysql_fetch_array($result_search)) {
			echo '<tr>';
			echo '<td class="checkbox"><input type="checkbox" id="upsale[]" name="upsale[]" value="'.$row_search["id"].'" /></td>';
			echo '<td class="sku">'.$row_search["RootSKU"].'</td>';
			echo '<td class="name">'.$row_search["ProductDetailName"].'</td>';
			echo '<td class="price">'.$row_search["NoneMemberPrice"].'</td>';
			echo '</tr>';
		}
		echo '</table>';
		mysql_close($conn);
		exit();
	}
?>