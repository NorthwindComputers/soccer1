<?php 

	include('includes/header.php');
	
	if(isset($_POST["btnSave"])) {
		
		foreach($_POST as $key=>$value){
			$$key = addslashes($value);
		}

		
		if($prodid == "") {
		
			if($AvailableQTY == "") {
				$AvailableQTY = 0;
			}
			$sql_add  = "INSERT INTO products(BrowserName, BrowserName2, BrowserName3, NoneMemberPrice, VIPPrice, BrowserAddInfo, MetaTitle, ProductDetailName, RootSKU, ManufacturerNum, Brand, Material, MadeIn, Taxable, ManagableStock, AvailableQTY, ProductURL, Status, CustomerGroupAvailability, NewFromDate, NewToDate, Vendor, Style) ";
			$sql_add .= "VALUES('$BrowserName', '$BrowserName2', '$BrowserName3', '$NoneMemberPrice', '$VIPPrice', '$BrowserAddInfo', '$MetaTitle', '$ProductDetailName', '$RootSKU', '$ManufacturerNum', '$Brand', '$Material', '$MadeIn', '$Taxable', '$ManagableStock', $AvailableQTY, '$ProductURL', '$Status', '$CustomerGroupAvailability', '$NewFromDate', '$NewToDate', '$Vendor', '$Style')";
			
			if(!mysql_query($sql_add)) {
				$err = "Error adding product: ".mysql_error();
			}
			
			if($err == '') {
				$sql_id = "SELECT MAX(id) AS ProductID FROM products";
				$result_id = mysql_query($sql_id);
				$row_id = mysql_fetch_assoc($result_id);
				
				$sql_addDes = "INSERT INTO product_descriptions(ProductID, MetaTag, MetaDescription, ProductDescription, ShortDescription) ";
				$sql_addDes .= "VALUES($row_id[ProductID], '$MetaTag', '$MetaDescription', '$ProductDescription', '$ShortDescription')";
				mysql_query($sql_addDes);
				header("location:product_detail.php?id=".$row_id["ProductID"]);
			} else {
				//echo $sql_add."<br/><br/>";
				echo mysql_error();
			}

		} else {
			$sql_update  = "UPDATE products SET BrowserName='$BrowserName', BrowserName2='$BrowserName2', BrowserName3='$BrowserName3', NoneMemberPrice='$NoneMemberPrice', VIPPrice='$VIPPrice', BrowserAddInfo='$BrowserAddInfo', MetaTitle='$MetaTitle', ProductDetailName='$ProductDetailName', RootSKU='$RootSKU', ManufacturerNum='$ManufacturerNum', Brand='$Brand', Material='$Material', MadeIn='$MadeIn', ";
			$sql_update .= "Taxable='$Taxable', ManagableStock='$ManagableStock', AvailableQTY=$AvailableQTY, ProductURL='$ProductURL', Status='$Status', CustomerGroupAvailability='$CustomerGroupAvailability', NewFromDate='$NewFromDate', NewToDate='$NewToDate', Vendor='$Vendor', Style='$Style' WHERE id=$prodid LIMIT 1";

			if(!mysql_query($sql_update)) {
				$err = "Error Updating product: ".mysql_error();
			}
			
			$sql_updateDes = "UPDATE product_descriptions SET MetaTag='$MetaTag', MetaDescription='$MetaDescription', ProductDescription='$ProductDescription', ShortDescription='$ShortDescription' WHERE ProductID=$prodid LIMIT 1";
			mysql_query($sql_updateDes);
		}
	}
	
	if($_GET["id"] != '') {
	
		$prodid = $_GET['id']; 
		$sql_prod = "SELECT * FROM products WHERE id=$prodid LIMIT 1";
		$result_prod = mysql_query($sql_prod);
		$row_prod = mysql_fetch_assoc($result_prod);
		
		foreach($row_prod as $key=>$value){
			$$key = stripslashes($value);
		}
		
		$sql_des = "SELECT ProductID, MetaTag, MetaDescription, ProductDescription, ShortDescription FROM product_descriptions WHERE ProductID=$prodid LIMIT 1";
		$result_des = mysql_query($sql_des);
		$row_des = mysql_fetch_assoc($result_des);
		
		foreach($row_des as $key=>$value){
			$$key = stripslashes($value);
		}
	}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title></title>

	<link rel="stylesheet" href="css/styles.css" type="text/css" />
	<link rel="stylesheet" href="jqtransformplugin/jqtransform.css" type="text/css"  media="all" />
   	<link rel="stylesheet" href="js/jquery.wysiwyg.css" type="text/css" />
	<link rel="stylesheet" href="css/jquery.ui.datepicker.css">
	<link rel="stylesheet" href="css/jquery.ui.theme.css">
    
	<script type="text/javascript" src="js/jquery-1.4.4.min.js"></script>
	<script type="text/javascript" src="jqtransformplugin/jquery.jqtransform.js"></script>
   	<script type="text/javascript" src="js/jquery.wysiwyg.js"></script>
	<script type="text/javascript" src="js/jquery.ui.core.js"></script>
	<script type="text/javascript" src="js/jquery.ui.widget.js"></script>
	<script type="text/javascript" src="js/jquery.ui.datepicker.js"></script>
	
	<script type="text/javascript" src="ckeditor/ckeditor.js"></script>
	<script language="javascript" type="text/javascript">
		$(function(){
			$('form').jqTransform({imgPath:'jqtransformplugin/img/'});
			//$("#ProductDescription").wysiwyg();
			$("#ShortDescription").wysiwyg();
			$("#NewFromDate").datepicker();
			$("#NewToDate").datepicker();

		});
	</script>
	</head>

	<body>
    <form action="" method="post">
    	<input type="hidden" id="prodid" name="prodid" value="<?=$prodid;?>" />
<!-- Master Div starts from here -->
<div class="Master_div"> 
      <!-- Header Div starts from here -->
      <div class="PD_header">
    <div class="upper_head"></div>
    	<div class="navi">
          <?php include('includes/menu.php'); ?>
          <div class="clear"></div>
        </div>
  	</div>
      <!-- Header Div ends here --> 
      <!-- Product Detail Div starts from here -->
      <div class="PD_main_form">
    <div class="PD_box1">
          <h1 style="margin-top:0px;">Browser Page Setup<br />
        <span>----------------------------------------------------------</span></h1>
        <label>Browser Name</label>
        <input type="text" id="BrowserName" name="BrowserName" value="<?=$BrowserName;?>"/>
         <label>Browser Name Line 2</label>
        <input type="text" id="BrowserName2" name="BrowserName2" value="<?=$BrowserName2;?>"/>
         <label>Browser Name Line 3</label>
        <input type="text" id="BrowserName3" name="BrowserName3" value="<?=$BrowserName3;?>"/>
        <div class="clear"></div>
        <label>Non Member Price</label>
        <input type="text" id="NoneMemberPrice" name="NoneMemberPrice" value="<?=$NoneMemberPrice;?>" class="my_width"/>
        <label>VIP Price</label>
        <input type="text" id="VIPPrice" name="VIPPrice" value="<?=$VIPPrice;?>" class="my_width"/>
        <label>Browser Additional Information</label>
        <input type="text" id="BrowserAddInfo" name="BrowserAddInfo" value="<?=$BrowserAddInfo;?>" class="my_width" />
        <h1>Search Engine Optimization<br />
              <span>----------------------------------------------------------</span></h1>
        <label>Meta Title</label>
        <input type="text" id="MetaTitle" name="MetaTitle" value="<?=$MetaTitle;?>" class="my_width"/>
        <label>Meta Tag</label>
        <div class="clear"></div>
        <textarea id="MetaTag" name="MetaTag" cols="5" rows="5"><?=$MetaTag;?>&nbsp;</textarea>
        <label>Meta Description</label>
        <div class="clear"></div>
        <textarea id="MetaDescription" name="MetaDescription" cols="2" rows="5"><?=$MetaDescription;?>&nbsp;</textarea>
        <h6>Maximum 255 chars</h6>
        </div>
    <div class="PD_box1 PD_box2">
          <h1 style="margin-top:0px;">Product Identifier<br />
        <span>-----------------------------------------------------------</span></h1>
        <label>Product Detailed Name</label>
        <input type="text" id="ProductDetailName" name="ProductDetailName" value="<?=$ProductDetailName;?>" class="my_width"/>
        <label>Root SKU</label>
        <input type="text" id="RootSKU" name="RootSKU" value="<?=$RootSKU;?>" class="my_width"/>
        <label>Vendor</label>
        <select id="Vendor" name="Vendor">
              <option value="">Select One...</option>
				<?php
                    $sql_vendor = "SELECT Vendor FROM vendors ORDER BY Vendor";
                    $result_vendor = mysql_query($sql_vendor);
                    
                    while($row_vendor = mysql_fetch_array($result_vendor)) {
                        if($row_vendor["Vendor"] == $Vendor) {
                            $selected = ' Selected="Selected" ';
                        } else {
                            $selected = '';
                        }
                        echo "<option value=\"$row_vendor[Vendor]\" $selected>$row_vendor[Vendor]</option>";
                    }
                ?>
            </select>
        
        <label>Brand</label>
        <select id="Brand" name="Brand">
              <option value="">Select One...</option>
		<?php
			$sql_brand = "SELECT Manufacturer FROM manufacturers ORDER BY Manufacturer";
			$result_brand = mysql_query($sql_brand);
			
			while($row_brand = mysql_fetch_array($result_brand)) {
				if($row_brand["Manufacturer"] == $Brand) {
					$selected = ' Selected="Selected" ';
				} else {
					$selected = '';
				}
				echo "<option value=\"$row_brand[Manufacturer]\" $selected>$row_brand[Manufacturer]</option>";
			}

		?>
            </select>
	<label>Manufacturer #</label>
	<input type="text" id="ManufacturerNum" name="ManufacturerNum" value="<?=$ManufacturerNum;?>" class="my_width" />
        <label>Material</label>
        <input type="text" id="Material" name="Material" value="<?=$Material;?>" class="my_width"/>
        <label>Made In</label>
        <input type="text" id="MadeIn" name="MadeIn" value="<?=$MadeIn;?>" class="my_width"/>
        <label>Is it taxable?</label>
        <select id="Taxable" name="Taxable">
              <option <?php if($Taxable=='Yes') { echo ' Selected="Selected"'; } ?> value="Yes">Yes</option>
              <option <?php if($Taxable=='No') { echo ' Selected="Selected"'; } ?> value="No">No</option>
            </select>
		<label>Style</label>
        <select id="Style" name="Style">
        	<option value="">Select One...</option>
        	<?php
			
				$selected='';
				$sql_style = "SELECT Style FROM styles ORDER BY Style";
				$result_style = mysql_query($sql_style);
				
				while($row_style=mysql_fetch_array($result_style)) {
					if($row_style["Style"] == $Style) {
						$selected = ' Selected="Selected" ';
					} else {
						$selected = '';
					}
					echo "<option value=\"$row_style[Style]\" $selected>$row_style[Style]</option>";
				}
			
			?>
        </select>
        <label> </label>
    	<label>Short Description</label>
        <textarea name="ShortDescription" id="ShortDescription" style="width: 600px; height: 150px; border: 1px solid #000;"><?=$ShortDescription;?></textarea>
        <label>Product Description</label>
        <textarea name="ProductDescription" id="ProductDescription" style="width: 600px; height: 200px; border: 1px solid #000;"><?=$ProductDescription;?></textarea>
        <script>
		    CKEDITOR.config.width = 609;
			CKEDITOR.config.contentsCss = './css/styles1.css' ;
			CKEDITOR.replace('ProductDescription', { fullPage : true });
		</script>
        <div class="clear"></div>
        <input type="submit" id="btnSave" name="btnSave" value="" class="submit"/>
        <input type="reset" value="" class="cancel"/>
        <div class="clear"></div>
        </div>
    <div class="PD_box1 PD_box2" style="padding-left:0px;">
          <h1 style="margin-top:0px; height:25px; width:200px;">&nbsp;</h1>
        <label>Managable Stock</label>
        <select id="ManagableStock" name="ManagableStock">
              <option <?php if($ManagableStock == 'Yes') { echo ' Selected="Selected"'; } ?> value="Yes">Yes</option>
              <option <?php if($ManagableStock == 'No') { echo ' Selected="Selected"'; } ?> value="No">No</option>
            </select>
        <label>Available Qty <span style="font-size: 10px;">(edit on options & pricing page)</span></label>
        <?php
		
			if($prodid != '') {
				$sql_stock = "SELECT SUM(Inventory) AS Stock FROM product_options WHERE ProductID=$prodid";
				$result_stock = mysql_query($sql_stock);
				$row_stock = mysql_fetch_assoc($result_stock);
			}		
		?>
        <input type="text" id="AvailableQTY" name="AvailableQTY" value="<?=$row_stock["Stock"];?>" class="my_width" readonly="readonly"/>
        <label>Visibility</label>
        <select id="Visibility" name="Visibility">
              <option>Visibility 1</option>
              <option>Visibility 2</option>
              <option>Visibility 3</option>
              <option>Visibility 4</option>
            </select>
        <label>Product URL</label>
        <input type="text" id="ProductURL" name="ProductURL" value="<?=$ProductURL;?>" class="my_width"/>
        <label>Status</label>
        <select id="Status" name="Status">
              <option <?php if($Status == 'Enabled') { echo ' Selected="Selected"'; } ?> value="Enabled">Enabled</option>
              <option <?php if($Status == 'Disabled') { echo ' Selected="Selected"'; } ?> value="Disabled">Disabled</option>
            </select>
        <label>Customer Group Availability</label>
        <select id="CustomerGroupAvailability" name="CustomerGroupAvailability">
	      <option value="">Select One...</option>
              <?php
			$sql_customer = "SELECT GroupName FROM customer_group ORDER BY GroupName";
			$result_customer = mysql_query($sql_customer);
			
			while($row_customer = mysql_fetch_array($result_customer)) {
				if($row_customer["GroupName"] == $CustomerGroupAvailability) {
					$selected = ' Selected="Selected" ';
				} else {
					$selected = '';
				}
				echo "<option value=\"$row_customer[GroupName]\" $selected>$row_customer[GroupName]</option>";
			}

		?>
            </select>
        <label style="width:160px !important;">New from Date</label>
        <input type="text" id="NewFromDate" name="NewFromDate" value="<?=$NewFromDate;?>" class="NFD" />
        <label style="width:150px !important;">New to Date</label>
        <input type="text" id="NewToDate" name="NewToDate" value="<?=$NewToDate;?>" />
          <div class="clear"></div>
        </div>
    <div class="clear"></div>
  </div>
      <!-- Product Detail Div ends here --> 
    </div>
</form>
</body>
</html>
<?php mysql_close($conn); ?>