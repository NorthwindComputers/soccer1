<?php
/**********************************
 * Product Admin Detail page
 *
 * Version: 1.0
 * By: Richard Tuttle
 * Updated: 12 August 2013
 **********************************/
 
// initial view of product items
if ($_POST["type"] == "view") {
	require_once 'db.php';
	$items = mysql_real_escape_string($_POST["totalview"]);
	$pager = mysql_real_escape_string($_POST["pager"]);
	if($items == ''){ $items = 100; }
	if ($pager == ''){ $pager = 1; }
	$offset = ($pager - 1) * $items;
	
	// pager ==========================================================
	$sql_total = "SELECT id AS totalProducts FROM products";
	$result_total = mysql_query($sql_total);
	$num_total = mysql_num_rows($result_total);
	$maxpage = @ceil($num_total/$items);
	if ($num_total > 0) {
		$pgr_text = '<ul class="PV_cen_ul">';
		if ($pager > 1) {
			$pgr_text .= '<li><a href="#" onClick="qtyLoad('.($pager-1).'); return false;">[prev]</a></li>';
			$pgr_text .= '<li>|</li>';
		}
		for ($pgnum = 1; $pgnum <= $maxpage; $pgnum++) {
			$pgr_text .= '<li><a href="#" onClick="qtyLoad('.$pgnum.'); return false;">'.$pgnum.'</a></li>';
			$pgr_text .= '<li>|</li>';
		}
		if ($pager < $maxpage) {
			$pgr_text .= '<li><a href="#" onClick="qtyLoad('.($pager+1).'); return false;">[next]</a></li>';
		}
		$pgr_text .= '</ul>';
	}
	echo $pgr_text;
?>
    <table cellpadding="5" cellspacing="1" width="980px" style="margin-top: 5px;">
    <tr>
        <td class="headersmain" colspan="6" style="text-align: left; padding-left: 20px;">Product Manager<input type="button" style="float: right; border: 1px solid #bebebe; background-color: #ff6600; width: 120px; height: 25px; color: #fff;" onClick="window.location='product_detail.php'" id="btnNew" name="btnNew" value="Add New" /></td>
    </tr>
    <tr>
        <td class="headers" style="width:100px;">ID</td>
        <td class="headers" style="width:420px; padding-left: 13px; text-align: left;">Product Name</td>
        <td class="headers" style="width:150px;">N.M Price</td>
        <td class="headers" style="width:100px;">Stock</td>
        <td class="headers" style="width:100px;">Sold</td>
        <td class="headers" style="width:110px; padding-left: 20px;">View</td>
    </tr>
<?php
		$sql_products = "SELECT id, ProductDetailName, NoneMemberPrice, RootSKU FROM products ORDER BY id LIMIT $offset, $items";
		$result_products = mysql_query($sql_products);
		$c = 1;
		while ($row_products = mysql_fetch_array($result_products)) {
			$prodid = $row_products["id"];
			if ($c == 1) {
				$bg = "row1";
				$c++;
			} else {
				$bg = "row2";
				$c = 1;
			}
			
			$sql_stock = "SELECT SUM(Inventory) AS TotalInv FROM product_options WHERE ProductID=$row_products[id]";
			$result_stock = mysql_query($sql_stock);
			$row_stock = mysql_fetch_assoc($result_stock);
			$sql_sold = "SELECT SUM(Qty) AS TotalSold FROM orders_items WHERE ProductID=$row_products[id]";
			$result_sold = mysql_query($sql_sold);
			$row_sold = mysql_fetch_assoc($result_sold);
?>
	<tr>
		<td class="<?=$bg;?>" style="width: 100px;"><?=$row_products["RootSKU"];?></td>
		<td class="<?=$bg;?>" style="width: 420px; text-align: left; padding-left: 13px;"><?=$row_products["ProductDetailName"];?></td>
		<td class="<?=$bg;?>" style="width: 150px;">$<?=number_format($row_products["NoneMemberPrice"],2);?></td>
		<td class="<?=$bg;?>" style="width: 100px;"><?=$row_stock["TotalInv"];?></td>
		<td class="<?=$bg;?>" style="width: 100px;"><?=$row_sold["TotalSold"];?></td>
		<td class="<?=$bg;?>" style="width: 110px; text-align: center; padding-left: 35px;"><div class="delete"><a href="#" onclick="deleteprod('<?=$prodid;?>');">&nbsp;</a></div><div class="copy"><a href="#" onclick="copyprod('<?=$prodid;?>');">&nbsp;</a></div><div class="view"><a href="product_detail.php?id=<?=$prodid;?>">&nbsp;</a></div></td>
	</tr>
<?php
		}
		
		if ($result_products) { 
			mysql_free_result($result_products); 
		}
?>
	</table>
<?php
	echo $pgr_text;
	mysql_close($conn);
	exit(); 
}
	
	if($_POST["type"] == "productsearch") {
		require 'db.php';
		$filter = mysql_real_escape_string($_POST["filter"]);
		$search = mysql_real_escape_string($_POST["search"]);
		
		?>
        
        <table cellpadding="5" cellspacing="1" width="980px" style="margin-top: 5px;">
            	<tr>
                	<td class="headersmain" colspan="6" style="text-align: left; padding-left: 20px;">Product Manager
                    	<input type="button" style="float: right; border: 1px solid #bebebe; background-color: #ff6600; width: 120px; height: 25px; color: #fff;" onClick="window.location='product_detail.php'" id="btnNew" name="btnNew" value="Add New" />
                    </td>
                </tr>
                <tr>
                      <td class="headers" style="width:100px;">ID</td>
                      <td class="headers" style="width:420px; padding-left: 13px; text-align: left;">Product Name</td>
                      <td class="headers" style="width:150px;">N.M Price</td>
                      <td class="headers" style="width:100px;">Stock</td>
                      <td class="headers" style="width:100px;">Sold</td>
                      <td class="headers" style="width:110px; padding-left: 20px;">View</td>
                </tr>
        <?php
		
			$sql_products = "SELECT id, ProductDetailName, NoneMemberPrice, RootSKU FROM products WHERE ";
			if($filter == "Name") {
				$sql_products .= " ProductDetailName LIKE '%$search%' ";
			} else {
				$sql_products .= " RootSKU LIKE '%$search%' ";
			}
			$result_products = mysql_query($sql_products);
			
			$c = 1;
			while($row_products = mysql_fetch_array($result_products)) {
				$prodid = $row_products["id"];
				if($c==1){
					$bg = "row1";
					$c++;
				} else {
					$bg = "row2";
					$c = 1;
				}
				
				$sql_stock = "SELECT SUM(Inventory) AS TotalInv FROM product_options WHERE ProductID=$prodid";
				$result_stock = mysql_query($sql_stock);
				$row_stock = mysql_fetch_assoc($result_stock);
				
				$sql_sold = "SELECT SUM(Qty) AS TotalSold FROM orders_items WHERE ProductID=$prodid";
				$result_sold = mysql_query($sql_sold);
				$row_sold = mysql_fetch_assoc($result_sold);
				
				?>
                	<tr>
                      <td class="<?=$bg;?>" style="width: 100px;"><?=$row_products["RootSKU"];?></td>
                      <td class="<?=$bg;?>" style="width: 420px; text-align: left; padding-left: 13px;"><?=$row_products["ProductDetailName"];?></td>
                      <td class="<?=$bg;?>" style="width: 150px;">$<?=number_format($row_products["NoneMemberPrice"],2);?></td>
                      <td class="<?=$bg;?>" style="width: 100px;"><?=$row_stock["TotalInv"];?></td>
                      <td class="<?=$bg;?>" style="width: 100px;"><?=$row_sold["TotalSold"];?></td>
                      <td class="<?=$bg;?>" style="width: 110px; text-align: center; padding-left: 35px;">
                        <div class="delete"><a href="#" onclick="deleteprod('<?=$prodid;?>');">&nbsp;</a></div>
                        <div class="copy"><a href="#" onclick="copyprod('<?=$prodid;?>');">&nbsp;</a></div>
                        <div class="view"><a href="product_detail.php?id=<?=$prodid;?>">&nbsp;</a></div>
                      </td>
                	</tr>
                
                <?php
			} ?>
            
            </table>
            <?php
		exit();
	}
	
	if($_POST["type"] == "copy") {
		require 'db.php';
		$prodid = mysql_real_escape_string($_POST["prodid"]);
		
		$field_prod = "BrowserName, NoneMemberPrice, VIPPrice, BrowserAddInfo, MetaTitle, ProductDetailName, RootSKU, ManufacturerNum, Brand, Material, Size, MadeIn, Taxable, ManagableStock, AvailableQTY, ProductURL, Status, CustomerGroupAvailability, NewFromDate, NewToDate";
		$field_des  = "MetaTag, MetaDescription, ProductDescription";
		
		$sql_copy = "SELECT $field_prod FROM products WHERE id=$prodid LIMIT 1";
		$result_copy = mysql_query($sql_copy);
		$row_copy = mysql_fetch_assoc($result_copy);
		
		$sql_add  = "INSERT INTO products($field_prod) VALUES('$row_copy[BrowserName]', '$row_copy[NoneMemberPrice]', '$row_copy[VIPPrice]', '$row_copy[BrowserAddInfo]', '$row_copy[MetaTitle]', '$row_copy[ProductDetailName]', '$row_copy[RootSKU]', '$row_copy[ManufacturerNum]', '$row_copy[Brand]', ";
		$sql_add .= "'$row_copy[Material]', '$row_copy[Size]', '$row_copy[MadeIn]', '$row_copy[Taxable]', '$row_copy[ManagableStock]', $row_copy[AvailableQTY], '$row_copy[ProductURL]', '$row_copy[Status]', '$row_copy[CustomerGroupAvailability]', '$row_copy[NewFromDate]', '$row_copy[NewToDate]')";
		if(!mysql_query($sql_add)) {
			echo "Error copy product: ".mysql_error();
		} else {
			
			$sql_id = "SELECT MAX(id) AS ProductID FROM products";
			$result_id = mysql_query($sql_id);
			$row_id = mysql_fetch_assoc($result_id);
			
			$sql_des = "SELECT $field_des FROM product_descriptions WHERE ProductID=$prodid LIMIT 1";
			$result_des = mysql_query($sql_des);
			$row_des = mysql_fetch_assoc($result_des);
			
			$sql_addDes = "INSERT INTO product_descriptions(ProductID, MetaTag, MetaDescription, ProductDescription) ";
			$sql_addDes .= "VALUES($row_id[ProductID], '$row_des[MetaTag]', '$row_des[MetaDescription]', '$row_des[ProductDescription]')";
			mysql_query($sql_addDes);
			
		}
		
		echo "Product Copied";
		mysql_close($conn);
		exit();
	}
	
	if($_POST["type"] == "delete") {
	
		require 'db.php';
		$prodid = mysql_real_escape_string($_POST["prodid"]);
		
		$sql_del = "DELETE FROM products WHERE id=$prodid LIMIT 1";
		mysql_query($sql_del);
		
		$sql_delDes = "DELETE FROM products_descriptions WHERE ProductID=$prodid";
		mysql_query($sql_delDes);
		
		echo "Product Deleted";
		mysql_close($conn);
		exit();
	}
?>