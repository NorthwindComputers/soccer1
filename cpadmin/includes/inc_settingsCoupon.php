<?php
/***************************************
 * Coupon setup functions
 *
 * Version: 1.2.3
 * By: Richard Tuttle
 * Updated: 14 August 2014
 ***************************************/

// save function
if (isset($_POST["btnSave"])) {
	foreach($_POST as $key=>$value) {
		$$key = $value;
	}
	
	if (!empty($_POST["category"])) {
		$ApplyOption = '';
		$categories = mysql_real_escape_string($_POST["category"]);
		$c = count($categories);
		for ($i = 0; $i < $c; $i++){
			$ApplyOption .= $categories[$i]."|";
		}
		$ApplyOption = substr($ApplyOption, 0, -1);
	}
	
	if (!empty($_POST["shipcategory"])) {
		$ShippingOption = '';
		$shipcategories = mysql_real_escape_string($_POST["shipcategory"]);
		$c = count($shipcategories);
		for ($i = 0; $i < $c; $i++){
			$ShippingOption .= $shipcategories[$i]."|";
		}
		$ShippingOption = substr($ShippingOption, 0, -1);
	}
	
	$UsedWithOther = 0;
    if (isset($_POST["UsedWithOther"])) {
		$UsedWithOther = 1;
	}
	
	if ($MinimumOrder == '') { 
		$MinimumOrder = 0; 
	}
    if ($MaximumOrder == '') {
		$MaximumOrder = 0; 
	}
	if ($Amount == '') { 
		$Amount = 0; 
	}
	if ($ShipOption != '') { 
		$ApplyOption=$ShipOption; 
	}
	if($ShippingSKU != '') { 
		$ShippingOption=$ShippingSKU; 
	}

	$freeItemID ='';
	$qtfreeItem = 1;
	if (isset($_POST["freeItemID"])) {
		$freeItemID = mysql_real_escape_string($_POST["freeItemID"]);
		$qtfreeItem = mysql_real_escape_string($_POST["qtfreeItem"]);
		$skuItemQt = mysql_real_escape_string($_POST["skuItemQt"]);
	}
	
	$sql_add = "INSERT INTO coupons(Name, Code, Amount, Type, Status, StartDate, EndDate, ApplyTo, ApplyOption, MinimumOrder, ShippingOption, Method,SkuFreeItem,QuatityFreeItem,	SkuItemQuantity, MaximumOrder, UsedWithOther) ";

	if ($StartDate != '') {
		list($month, $day, $year) = explode('/', $StartDate);
		$StartDate = $year.'-'.$month.'-'.$day;
	}
		
	if ($EndDate != '') {
		list($month, $day, $year) = explode('/', $EndDate);
		$EndDate = $year.'-'.$month.'-'.$day;
	}

	$sql_add .= " VALUES('$Name', '$Code', $Amount, '$Type', '$Status', '$StartDate', '$EndDate', '$ApplyTo', '$ApplyOption', $MinimumOrder, '$ShippingOption', '$ShipMethod','$freeItemID','$qtfreeItem','$skuItemQt','$MaximumOrder','$UsedWithOther')";

	if (!mysql_query($sql_add)) {
		echo "Error adding coupon".mysql_error();
	}
	echo "<script>window.location='settings.php?p=Coupon';</script>";
}

// update function
if (isset($_POST["btnUpdate"])) {
	foreach($_POST as $key=>$value) {
		$$key = $value;
	}
	if (!empty($_POST["category"])) {
		$ApplyOption = '';
		$categories = $_POST["category"];
		$c = count($categories);
		for($i = 0; $i < $c; $i++){
			$ApplyOption .= $categories[$i]."|";
		}
		$ApplyOption = substr($ApplyOption,0,-1);
	}

	if (!empty($_POST["shipcategory"])) {
		$ShippingOption = '';
		$shipcategories = mysql_real_escape_string($_POST["shipcategory"]);
		$c = count($shipcategories);
		for($i = 0; $i < $c; $i++){
			$ShippingOption .= $shipcategories[$i]."|";
		}
		$ShippingOption = substr($ShippingOption,0,-1);
	}	

	$UsedWithOther = 0;
    if (isset($_POST["UsedWithOther"])) {
		$UsedWithOther=1;
	}

	if ($MinimumOrder == '') { 
		$MinimumOrder = 0; 
	}
		
    if ($MaximumOrder == '') {
		$MaximumOrder = 0; 
	}

	if ($Amount == '') { 
		$Amount = 0; 
	}

	if ($ShipOption != '') { 
		$ApplyOption=$ShipOption; 
	}

	if ($ShippingSKU != '') { 
		$ShippingOption=$ShippingSKU; 
	}

	$freeItemID ='';
	if (isset($_POST["freeItemID"])) {
		$freeItemID = mysql_real_escape_string($_POST["freeItemID"]);
		$qtfreeItem = mysql_real_escape_string($_POST["qtfreeItem"]);
		$skuItemQt = mysql_real_escape_string($_POST["skuItemQt"]);
	}
	 	  
	if ($StartDate != '') {
		list($month, $day, $year) = explode('/', $StartDate);
		$StartDate = $year.'-'.$month.'-'.$day;
	}
		
	if ($EndDate != '') {
		list($month, $day, $year) = explode('/', $EndDate);
		$EndDate = $year.'-'.$month.'-'.$day;
	}
	
	$sql_update = "UPDATE coupons SET Name='$Name', Code='$Code', Amount=$Amount, `Type`='$Type', Status='$Status', StartDate='$StartDate', EndDate='$EndDate', ApplyTo='$ApplyTo', ApplyOption='$ApplyOption', MinimumOrder=$MinimumOrder, ShippingOption='$ShippingOption', Method='$ShipMethod', NbrUse = '$nbrUse',SkuFreeItem='$freeItemID', QuatityFreeItem='$qtfreeItem', SkuItemQuantity='$skuItemQt', MaximumOrder='$MaximumOrder', UsedWithOther='$UsedWithOther' WHERE id=$id LIMIT 1";
      
    if (!mysql_query($sql_update)) {
		echo "Error Updating coupon: ".mysql_error();
	}
	echo "<script>window.location='settings.php?p=Coupon';</script>";
}

// new function
if ($_GET["type"] == "new") { ?>
<script type="text/javascript">
$(document).ready(function() {
	$('#StartDate').datepicker({dateFormat: 'mm/dd/yy'});
    $('#EndDate').datepicker({dateFormat: 'mm/dd/yy'});
});
</script>
<form action="" method="post">
<table cellpadding="5" cellspacing="2">
<tr>
	<td style="width: 200px;"><strong>Coupon Code:</strong><br/><input type="text" id="Code" name="Code" /></td>
	<td><strong>Status:</strong><br/><select id="Status" name="Status"><option value="Enabled">Enabled</option><option value="Disabled">Disabled</option></select></td>
</tr>
<tr>
	<td><strong>Coupon Name:</strong><br/><input type="text" id="Name" name="Name" /></td>
	<td><strong>Starting Date:</strong><br/><input type="text" id="StartDate" name="StartDate" /></td>
</tr>
<tr>
	<td><strong>Amount:</strong><br/><input type="text" id="Amount" name="Amount" /></td>
	<td><strong>Ending Date:</strong><br/><input type="text" id="EndDate" name="EndDate" /></td>
</tr>
<tr>
	<td style="vertical-align: top;"><strong>Type:</strong><br/><input type="radio" style="width: 13px; border: 0px; margin-right: 10px;" id="Type" name="Type" value="dollar" checked="checked" /> Dollar ($) <br/><input type="radio" style="width: 13px; border: 0px; margin-right: 10px;" id="Type" name="Type" value="percent" /> Percent (%)<!-- br><input type="radio" style="width: 13px; border: 0x; margin-right: 10px;" id="Type" name="Type" value="special" /> Special --></td>
	<td style="vertical-align: top;"><strong>Minimum Order Amount:</strong><br/><input type="text" id="MinimumOrder" name="MinimumOrder" /></td>
</tr>
<tr>
    <td><strong>Apply Coupon to:</strong><br/>
					<select id="ApplyTo" name="ApplyTo">
						<option value="">Select...</option>
						<option value="EntireOrder">Entire Order</option>
						<option value="CustomerGroup">Customer Group</option>
						<option Value="Category">Category</option>
						<option Value="SKU">SKU</option>
                        <option Value="Shipping">Shipping</option>
					</select>
					<div id="ApplyOption"></div>
                </td>
                <td style="vertical-align: top;">
					<strong>Maximum Order Amount:</strong><br/>
                    <input type="Text" id="MaximumOrder" name="MaximumOrder" value="<?=$MaximumOrder;?>" />
				</td>
           </tr>
           <tr>
                  <td>&nbsp;</td>
                  <td style="vertical-align: top;">
				  	<input type="checkbox" name="UsedWithOther" id="UsedWithOther" style="width:35px;"/> 
					  <strong>Use With Other Offers</strong>
				</td>
           </tr>
			<tr>
				<td colspan="2">
					<input style="margin-right: 5px;" type="submit" id="btnSave" name="btnSave" value="Save"/>
       				<input type="button" id="btnCancel" name="btnCancel" value="Cancel" />
				</td>
			</tr>
	</table>
</form>
<script type="text/javascript">
$("#btnCancel").click(function() {
	window.location="settings.php?p=Coupon";
});

$("#ApplyTo").change(function() {
	var specialVIP = $('input[name=Type]:checked').val();

	switch ($(this).val()) {
		case "EntireOrder":
			if (specialVIP == "special") {
				$("#ApplyOption").html('<img src="images/loader.gif" />');
				$("#ApplyOption").load("includes/inc_settingsCoupon.php", {
					"type":"vipLevel"
				});
			} else {
				$("#ApplyOption").html('<img src="images/loader.gif" />');
				$("#ApplyOption").load("includes/inc_settingsCoupon.php", {
					"type":"FreeItem"
				});
			}
			break;
		case "CustomerGroup":
			$("#ApplyOption").html('<img src="images/loader.gif" />');
			$("#ApplyOption").load("includes/inc_settingsCoupon.php", {
				"type":"customerlist"
			});
			break;
		case "Category":
			$("#ApplyOption").html('<img src="images/loader.gif" />');
			$("#ApplyOption").load("includes/inc_settingsCoupon.php", {
				"type":"categories"
			});
			break;
		case "SKU":
			$("#ApplyOption").html('<img src="images/loader.gif" />');
			$("#ApplyOption").load("includes/inc_settingsCoupon.php", {
				"type":"SKU"
			});
			break;
		case "Shipping":
			$("#ApplyOption").html('<img src="images/loader.gif" />');
			$("#ApplyOption").load("includes/inc_settingsCoupon.php", {
				"type":"Shipping"
			});
			break;
		default:
			$("#ApplyOption").html('');
		}

		if ($(this).val() == 'Shipping') {
			$("#Amount").val('100');
			$("#Amount").attr("readonly", 'readonly');
			$('input[value="percent"]').attr('checked', 'checked');
			$('input[name="Type"]').attr('disabled', true);
		} else {
			$("#Amount").removeAttr('readonly');
			$('input[name="Type"]').removeAttr('disabled');
		}
	});
</script>
<?php		
	exit();
}

// edit function
if ($_GET["type"] == "edit") {
	require "db.php";
	$cid = mysql_real_escape_string($_GET["id"]);
	$sql_c = "SELECT * FROM coupons WHERE id=$cid LIMIT 1";
	$result_c = mysql_query($sql_c);
	$row_c = mysql_fetch_assoc($result_c);
	foreach($row_c as $key=>$value) {
		$$key = stripslashes($value);
	}
?>
<script type="text/javascript">
$(document).ready(function(){
	$('#StartDate').datepicker({dateFormat: 'mm/dd/yy'});
    $('#EndDate').datepicker({dateFormat: 'mm/dd/yy'});
});
</script>
<form action="" method="post">
	<table cellpadding="5" cellspacing="2">
		<tr>
			<td style="width: 200px;"><strong>Coupon Code:</strong><br/>
				<input type="hidden" id="id" name="id" value="<?=$cid;?>" />
				<input type="text" id="Code" name="Code" value="<?=$Code;?>" />
			</td>
			<td>
				<strong>Status:</strong><br/>
				<select id="Status" name="Status">
					<option <?php if($Status == "Enabled") { echo ' Selected="Selected" '; } ?> value="Enabled">Enabled</option>
					<option <?php if($Status == "Disabled") { echo ' Selected="Selected" '; } ?> value="Disabled">Disabled</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>
				<strong>Coupon Name:</strong><br/>
				<input type="text" id="Name" name="Name" value="<?=$Name;?>" />
			</td>
			<td>
				<strong>Starting Date:</strong><br/>
				<input type="text" id="StartDate" name="StartDate" value="<?php if(date('Y', strtotime($StartDate))>1972) echo date('m/d/Y', strtotime($StartDate));?>" />
			</td>
		</tr>
		<tr>
			<td>
				<strong>Amount:</strong><br/>
				<input type="text" id="Amount" name="Amount" value="<?=$Amount;?>" /> 
			</td>
			<td>
				<strong>Ending Date:</strong><br/>
				<input type="text" id="EndDate" name="EndDate" value="<?php if(date('Y', strtotime($EndDate)) > 1972) echo date('m/d/Y', strtotime($EndDate));?>" />
			</td>
		</tr>
		<tr>
			<td style="vertical-align: top;">
				<strong>Type:</strong><br/>
				<input type="radio" <?php if($Type == "dollar") { echo ' checked="checked" '; } ?> style="width: 13px; border: 0px; margin-right: 10px;" id="Type" name="Type" value="dollar" /> Dollar ($) <br/>
				<input type="radio"  <?php if($Type == "percent") { echo ' checked="checked" '; } ?> style="width: 13px; border: 0px; margin-right: 10px;" id="Type" name="Type" value="percent" /> Percent (%)<!-- br>
				<input type="radio"  <?php if($Type == "special") { echo ' checked="checked" '; } ?> style="width: 13px; border: 0px; margin-right: 10px;" id="Type" name="Type" value="special" /> Special  -->
			</td>
			<td style="vertical-align: top;">
				<strong>Minimum Order Amount:</strong><br/>
                <input type="Text" id="MinimumOrder" name="MinimumOrder" value="<?=$MinimumOrder;?>" />
			</td>
		</tr>
		<tr>
			<td>
				<input type="checkbox" name="UsedWithOther" id="UsedWithOther" style="width:35px;" <?php if($UsedWithOther==1) echo 'checked';?>/> <strong>Use With Other Offers</strong>
			</td>
			<td style="vertical-align: top;">
				<strong>Maximum Order Amount:</strong><br/>
                <input type="Text" id="MaximumOrder" name="MaximumOrder" value="<?=$MaximumOrder;?>" />
			</td>
		</tr>
        <tr>
          	<td valign="top">
				<strong>Number of use:</strong><br/>
				<select id="nbrUse" name="nbrUse">
                    <option value="1" <?php if($NbrUse==1) echo 'selected';?>>1</option>
                    <option value="2" <?php if($NbrUse==2) echo 'selected';?>>Unlimited</option>
                </select>
            </td>
            <td>
           	    <strong>Apply Coupon to:</strong><br/>
				<select id="ApplyTo" name="ApplyTo">
				   <option value="">Select...</option>
				   <option <?php if($ApplyTo == "EntireOrder") { echo ' selected="selected" '; } ?> value="EntireOrder">Entire Order</option>
				   <option <?php if($ApplyTo == "CustomerGroup") { echo ' selected="selected" '; } ?> value="CustomerGroup">Customer Group</option>
  				   <option <?php if($ApplyTo == "Category") { echo ' selected="selected" '; } ?> Value="Category">Category</option>
   				   <option <?php if($ApplyTo == "SKU") { echo ' selected="selected" '; } ?> Value="SKU">SKU</option>
                   <option <?php if($ApplyTo == "Shipping") { echo ' selected="selected" '; } ?> Value="Shipping">Shipping</option>
				</select>

				<div id="ApplyOption">
				<?php
				switch($ApplyTo) {
					case "EntireOrder":
						if ($Type == "special") {
							echo '<script>$("#ApplyOption").load("includes/inc_settingsCoupon.php", {"type":"vipLevel", "vid":"'.$ApplyOption.'"});</script>';
						} else {
							echo '<script>$("#ApplyOption").load("includes/inc_settingsCoupon.php", {"type":"FreeItem", "cid":"'.$cid.'"});</script>';
						}
						break;
					case "CustomerGroup":
								echo '<script>$("#ApplyOption").load("includes/inc_settingsCoupon.php", {"type":"customerlist", "id":"'.$cid.'"});</script>';
							break;

							case "Category":
								echo '<script>$("#ApplyOption").load("includes/inc_settingsCoupon.php", {"type":"categories", "id":"'.$cid.'"});</script>';
							break;

							case "SKU":
								echo '<script>$("#ApplyOption").load("includes/inc_settingsCoupon.php", {"type":"SKU", "id":"'.$cid.'"});</script>';
							break;

							case "Shipping":
								echo '<script>$("#ApplyOption").load("includes/inc_settingsCoupon.php", {"type":"Shipping", "id":"'.$cid.'"});</script>';
							break;

						}
					?>
				</div>
            </td>
        </tr>
		<tr>
			<td colspan="2">
				<input type="submit" style="margin-right: 5px;" id="btnUpdate" name="btnUpdate" value="Update"/>
               	<input type="button" id="btnCancel" name="btnCancel" value="Cancel" />
			</td>
		</tr>
	</table>
</form>

<script>
	$("#btnCancel").click(function() {
		//location.reload();
		window.location="settings.php?p=Coupon";
	});

	$("#ApplyTo").change(function() {
		var specialVIP = $('input[name=Type]:checked').val();
		switch ($(this).val()) {
			case "EntireOrder":
				if (specialVIP == "special") {
					$("#ApplyOption").html('<img src="images/loader.gif" />');
					$("#ApplyOption").load("includes/inc_settingsCoupon.php", {
						"type":"vipLevel"
					});
				} else {
					$("#ApplyOption").html('<img src="images/loader.gif" />');
					$("#ApplyOption").load("includes/inc_settingsCoupon.php", {"type":"FreeItem", "cid":"<?=$cid;?>"});
				}
			break;

			case "CustomerGroup":
				$("#ApplyOption").html('<img src="images/loader.gif" />');
				$("#ApplyOption").load("includes/inc_settingsCoupon.php", {"type":"customerlist"});
			break;

			case "Category":
				$("#ApplyOption").html('<img src="images/loader.gif" />');
				$("#ApplyOption").load("includes/inc_settingsCoupon.php", {"type":"categories"});
			break;

			case "SKU":
				$("#ApplyOption").html('<img src="images/loader.gif" />');
				$("#ApplyOption").load("includes/inc_settingsCoupon.php", {"type":"SKU"});
			break;

			case "Shipping":
				$("#ApplyOption").html('<img src="images/loader.gif" />');
				$("#ApplyOption").load("includes/inc_settingsCoupon.php", {"type":"Shipping"});
			break;

			default:
				$("#ApplyOption").html('');

		}

		if ($(this).val() == 'Shipping') {
	 		$("#Amount").val('100');
			$("#Amount").attr("readonly", 'readonly');
			$('input[value="percent"]').attr('checked', 'checked');
			$('input[name="Type"]').attr('disabled', true);
		} else {
			$("#Amount").removeAttr('readonly');
			$('input[name="Type"]').removeAttr('disabled');
		}
	});

</script>

<?php
	mysql_close($conn);
	exit();		
}

if ($_POST["type"] == "delete") {
	require "db.php";
	$sql_delete = "DELETE FROM coupons WHERE id=$_POST[id] LIMIT 1";
	if (!mysql_query($sql_delete)) {
		echo "Error removing coupon: ".mysql_error();
	} else {
		echo "Coupon Removed";
	}
	mysql_close($conn);
	exit();
}

if ($_POST["type"] == "customerlist") {
	require 'db.php';
	if ($_POST["id"] != '') {
		$id = mysql_real_escape_string($_POST["id"]);
		$sql_current = "SELECT ApplyOption FROM coupons WHERE id=$id LIMIT 1";
		$result_current = mysql_query($sql_current);
		$row_current = mysql_fetch_assoc($result_current);
		$customer = $row_current["ApplyOption"];
	}

	$sql_customer = "SELECT GroupName FROM customer_group ORDER BY GroupName";
	$result_customer = mysql_query($sql_customer);
	echo "<br/><strong>Select Customer Group:</strong><br/>";		
	echo '<select id="ApplyOption" name="ApplyOption">';
	while($row_customer = mysql_fetch_array($result_customer)) {
		if ($customer == $row_customer["GroupName"]) {
			$selected = ' selected="selected" ';
		} else {
			$selected = '';
		}
		echo "<option value=\"$row_customer[GroupName]\" $selected>$row_customer[GroupName]</option>";
	}

	echo '</select>';		

	mysql_close($conn);
	exit();
}

if ($_POST["type"] == "SKU") {
	require 'db.php';
	if ($_POST["shipping"] == '') {
		$name = "ApplyOption";
		$field = "ApplyOption";
	} else {
		$name = "ShippingSKU";
		$field = "ShippingOption";
	}
	
	if ($_POST["id"] != '') {
		$id = mysql_real_escape_string($_POST["id"]);
		$sql_current = "SELECT $field FROM coupons WHERE id=$id LIMIT 1";
		$result_current = mysql_query($sql_current);
		$row_current = mysql_fetch_assoc($result_current);
		$SKU = $row_current[$field];
	}

	$sql_sku = "SELECT RootSKU FROM products ORDER BY RootSKU";
	$result_sku = mysql_query($sql_sku);
    if (isset($id)) {
       	$sql_free_item = "SELECT SkuFreeItem,QuatityFreeItem,SkuItemQuantity FROM coupons WHERE id=$id LIMIT 1";
		$result_free_item = mysql_query($sql_free_item);
		$row_item = mysql_fetch_assoc($result_free_item) or die("");
        $freeItem = $row_item["SkuFreeItem"];
		$qtfreeItem = $row_item["QuatityFreeItem"];
		$skuItemQt = $row_item["SkuItemQuantity"];
	}

	$SKU = $row_current[$field];
	echo "<br/><strong>Select Root Sku:</strong><br/>";		
	echo '<select id="'.$name.'" name="'.$name.'">';
	$productSku = array();
	while($row_sku = mysql_fetch_array($result_sku)) {
		$productSku[] = $row_sku["RootSKU"];
		if ($SKU == $row_sku["RootSKU"]) {
			$selected = ' selected="selected" ';
		} else {
			$selected = '';
		}
		echo "<option value=\"$row_sku[RootSKU]\" $selected>$row_sku[RootSKU]</option>";
	}

	echo '</select>';	
	echo "<br/><strong>Needed Quantity :</strong><br/>";		
	echo "<input type='text' name='skuItemQt' id='skuItemQt' value='$skuItemQt'/>";

    echo "<br/><strong>Free Item :</strong><br/>";		
	echo '<select id="freeItemID" name="freeItemID">';
	foreach($productSku as $value) {
		if ($freeItem == $value) {
			$selected = ' selected="selected" ';
		} else {
			$selected = '';
		}
		echo "<option value=\"$value\" $selected>$value</option>";
	}
	
	echo '</select>';
	echo "<br/><strong>Free Item Quantity:</strong><br/>";		
	echo "<input type='text' name='qtfreeItem' id='qtfreeItem' value='$qtfreeItem'/>";
		
	mysql_close($conn);
	exit();
}


if ($_POST["type"] == "categories") {
	require 'db.php';
	if ($_POST["shipping"] == '') {
		$cattype = "category";
		$field = "ApplyOption";
	} else {
		$cattype = "shipcategory";
		$field = "ShippingOption";
	}

	if ($_POST["id"] != '') {
		$id = mysql_real_escape_string($_POST["id"]);
		$sql_current = "SELECT $field FROM coupons WHERE id=$id LIMIT 1";
		$result_current = mysql_query($sql_current);
		$row_current = mysql_fetch_assoc($result_current);
		$num_current = mysql_num_rows($result_current);
		if ($num_current>0) {
			$cats = explode("|", $row_current[$field]);
			$c = count($cats);
		}
	}
                   
    function subCategories($parent, $cats, $cattype) {
         $sql_sub = "SELECT id, Category FROM category WHERE ParentID=$parent ORDER BY Sort";
         $result_sub = mysql_query($sql_sub);
         $num_sub = mysql_num_rows($result_sub);
         if ($num_sub>0) {
             $c = count($cats);
             echo '<ul>';
             while($row_sub=mysql_fetch_array($result_sub)) {
				$checked = '';
				for($i=0; $i<$c; $i++) {
					if ($cats[$i] == $row_sub["id"]) {
						$checked = ' checked="checked" ';
					}
				}

                echo '<li><span class="folder" style="font-size: 15px;"><input type="checkbox" style="width: 12px; height: 12px; background-color: #fff; margin: 0px 5px 0px 5px;" '.$checked.' id="'.$cattype.'[]" name="'.$cattype.'[]" value="'.$row_sub["id"].'"/>'.$row_sub["Category"].'</span>';

                subCategories($row_sub["id"], $cats, $cattype);
                echo "</li>";
             }
          echo '</ul>';
        } 
    }

    $sql_cat = "SELECT id, Category FROM category WHERE ParentID=0 AND id!=13 AND id!=14 ORDER BY Sort";
    $result_cat = mysql_query($sql_cat);
    echo '<ul id="categories" class="filetree">';
    while($row_cat=mysql_fetch_array($result_cat)) {
		$checked = '';
		for($i=0; $i<$c; $i++) {
			if ($cats[$i] == $row_cat["id"]) {
				$checked = ' checked="checked" ';
			}
		}

        echo '<li><span class="folder" style="font-size: 15px;"><input type="checkbox" style="width: 12px; height: 12px; background-color: #fff; margin: 0px 5px 0px 5px;" '.$checked.' id="'.$cattype.'[]" name="'.$cattype.'[]" value="'.$row_cat["id"].'"/>'.$row_cat["Category"].'</span>';

        subCategories($row_cat["id"], $cats, $cattype);
        echo "</li>";
     }
     echo '</ul>';
 ?>

	<script> $("#categories").treeview(); </script>

	<?php
		mysql_close($conn);
		exit();
	}

	if ($_POST["type"] == "Shipping") {
		require 'db.php';
		$id = mysql_real_escape_string($_POST["id"]);
		if ($id != '') {
			$sql_ship = "SELECT ApplyOption, Method FROM coupons WHERE id=$id LIMIT 1";
			$result_ship = mysql_query($sql_ship);
			$row_ship = mysql_fetch_assoc($result_ship);
			$shipopt = $row_ship["ApplyOption"];
			$shipmeth = $row_ship["Method"];
		}
	?>

        <br/><strong>Shipping Options:</strong><br/>
        <select id="ShipOption" name="ShipOption">
        	<option value=''>Select...</option>
        	<option <? if($shipopt=='Entire Order') { echo ' selected="selected" '; } ?> value="Entire Order">Entire Order</option>
            <option <? if($shipopt=='Item') { echo ' selected="selected" '; } ?> value="Item">Item</option>
            <option <? if($shipopt=='Category') { echo ' selected="selected" '; } ?> value="Category">Category</option>
        </select>
        <br/>
        <div id="divShippingOption">
        	<?php
				switch($shipopt) {
					case "Item":
						echo '<script>$("#divShippingOption").load("includes/inc_settingsCoupon.php", {"type":"SKU", "id":"'.$id.'", "shipping":"shipping"});</script>';
					break;

					case "Category":
						echo '<script>$("#divShippingOption").load("includes/inc_settingsCoupon.php", {"type":"categories", "id":"'.$id.'", "shipping":"shipping"});</script>';

					break;
				}
			?>
        </div>
	<br/>

	<div id="divShippingMethod">
		<strong>Shipping Method</strong><br/>
		<select id="ShipMethod" name="ShipMethod">
			<option <?php if($shipmeth=='all'){ echo ' selected="selected" '; }?> value="all">All Methods Without Free Shipping</option>
			<option <?php if($shipmeth=='free'){ echo ' selected="selected" '; }?> value="free">Free Shipping</option>
			<option <?php if($shipmeth=='ground'){ echo ' selected="selected" '; }?> value="ground">Ground</option>
			<option <?php if($shipmeth=='3Day'){ echo ' selected="selected" '; }?> value="3Day">3rd Day</option>
			<option <?php if($shipmeth=='2Day'){ echo ' selected="selected" '; }?> value="2Day">2nd Day</option>
			<option <?php if($shipmeth=='1Day'){ echo ' selected="selected" '; }?> value="1Day">Next Day</option>
		</select>
	</div>

   <script>
		$("#ShipOption").change(function() {
			switch($(this).val()) {
				case "Entire Order":
					$("#divShippingOption").html('');
				break;

				case "Item":
					$("#divShippingOption").html('<img src="images/loader.gif" />');
					$("#divShippingOption").load("includes/inc_settingsCoupon.php", {"type":"SKU", "id":"<?=$id;?>", "shipping":"shipping"});
				break;

				case "Category":
					$("#divShippingOption").html('<img src="images/loader.gif" />');
					$("#divShippingOption").load("includes/inc_settingsCoupon.php", {"type":"categories", "id":"<?=$id;?>", "shipping":"shipping"});
				break;
			}
		});
	</script>

<?php
	exit();
}

if($_POST["type"] == "FreeItem") {
	require "db.php";
	
	$cid = mysql_real_escape_string($_POST["cid"]);
	$ItemID = '';
	$ItemQty = '';
	
	if($cid != '') {
		$sql_free = "SELECT SkuFreeItem, QuatityFreeItem FROM coupons WHERE id=$cid LIMIT 1";
		$result_free = mysql_query($sql_free);
		$num_free = mysql_num_rows($result_free);
		
		if($num_free>0) {
			$row_free = mysql_fetch_assoc($result_free);
			$ItemID = $row_free["SkuFreeItem"];
			$ItemQty = $row_free["QuatityFreeItem"];
		}
	}
	?>
	<br/><strong>Free Item:</strong><br/>
	<select id="freeItemID" name="freeItemID">
		<option value="">None</option>
		
		<?php
			$sql_items = "SELECT DISTINCT RootSKU FROM products ORDER BY RootSKU";
			$result_items = mysql_query($sql_items);
			
			while($row_items = mysql_fetch_array($result_items)) {
				if($ItemID == $row_items["RootSKU"]) {
					$selected = ' selected="selected" ';
				} else {
					$selected = '';
				}
				echo '<option value="'.$row_items["RootSKU"].'"'.$selected.'>'.$row_items["RootSKU"].'</option>';
			}
		?>
		
	</select>
	<br/><br/>
	<strong>Free Item Qty:</strong><br/>
	<input type="text" id="qtfreeItem" name="qtfreeItem" value="<?=$ItemQty;?>" />
	<br/>
	<?php
	exit();
}

if ($_POST["type"] == "vipLevel") {
	require "db.php";
	$vid = mysql_real_escape_string($_POST["vid"]);
	echo '<br><strong>VIP Level:</strong><br>';
	echo '<select id="vipID" name="ApplyOption"><option value="">---</option>';
	$sql_vip = "SELECT * FROM viplevels ORDER BY level";
	$result_vip = mysql_query($sql_vip);
	if ($vid) {
		while ($row_levels = mysql_fetch_array($result_vip)) {
			echo '<option value="' . $row_levels["level"] . '"';
			if ($vid == $row_levels["level"]) {
				echo ' selected="selected"';
			}
			echo '>' . $row_levels["level"] . '</option>';
		}
	} else {
		while ($row_levels = mysql_fetch_array($result_vip)) {
			echo '<option value="' . $row_levels["level"] . '">' . $row_levels["level"] . '</option>';
		}
	}
	echo '</select><br><br>';
	exit();
}

?>
<?php if ($_GET['type'] == 'list' || !isset($_GET['type']) || $_GET['type'] == '') : ?>
<div id="main">
	<table cellpadding="5" cellspacing="2">
		<tr>
       		<td colspan="5">
			   	<img src="images/plus.png" class="caddnew" style="float: right; width: 20px; cursor: pointer;" />
			</td>
       	</tr>
		<tr>
			<td class="headercg" style="width: 20%;">Code</td>
			<td class="headercg" style="width: 40%;">Name</td>
			<td class="headercg" style="width: 10%;">Amount</td>
			<td class="headercg" style="width: 10%";>Status</td>
			<td class="headercg" style="width: 10%";>Nbr.USE</td>
			<td class="headercg" style="width: 20%;">Action</td>
		</tr>
		<?php
			$sql_coupons = "SELECT * FROM coupons";
			$result_coupons = mysql_query($sql_coupons);
			while($row_coupons=mysql_fetch_array($result_coupons)) {
				if ($row_coupons["Type"] == "percent") {
					$amount = $row_coupons["Amount"]."%";
				} else {
					$amount = "$".$row_coupons["Amount"];
				}

				$ccode = stripslashes($row_coupons["Code"]);
				$cname = stripslashes($row_coupons["Name"]);
		?>
			<tr>
				<td><?=$ccode;?></td>
				<td><?=$cname;?></td>
				<td><?=$amount;?></td>
				<td><?=$row_coupons["Status"];?></td>
				<td><?php if($row_coupons["NbrUse"]==2) echo "Unlimited"; else echo $row_coupons["NbrUse"];?></td>
				<td style="text-align: center;">
					<img class="cedit" id="<?=$row_coupons["id"];?>" style="cursor: pointer; margin-right: 5px;" src="images/E.png"/>
					<img class="cdelete" style="cursor: pointer;" id="<?=$row_coupons["id"];?>" src="images/D.png" />
				</td>
			</tr>

		<?php } ?>
	</table>
</div>


<script>
	$(".caddnew").hover(function() {
		$(this).attr("src", "images/plus_hover.png");		

	}, function() {
		$(this).attr("src", "images/plus.png");
	});

	$(".cedit").hover(function() {
		$(this).attr("src", "images/E_hover.png");
	}, function() {
		$(this).attr("src", "images/E.png");
	});

	$(".cdelete").hover(function() {
		$(this).attr("src", "images/D_hover.png");
	}, function() {
		$(this).attr("src", "images/D.png");
	});

	$(".caddnew").click(function() {
		window.location = "settings.php?p=Coupon&type=new";
	});

	$(".cedit").click(function() {
		window.location = "settings.php?p=Coupon&type=edit&id="+$(this).attr("id");
	});

	$(".cdelete").click(function() {
		var del = confirm("Delete Coupon?");
		if (del) {
			$.post("includes/inc_settingsCoupon.php", {"type":"delete", "id":$(this).attr("id")}, 
			function(data){
				alert(data);
				location.reload();
			});
		}
	});
</script>
<?php endif; ?>