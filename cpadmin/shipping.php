<?php
/**
 * Shipping cost module
 *
 * Version: 1.0
 * Updated: 28 Feb 2013
 * By: Richard Tuttle
 */

	include_once('includes/header.php');

	if(isset($_POST["btnSubmit"])) {
		foreach($_POST as $key=>$value){
			$$key = addslashes($value);
		}
		
			if($Weight == '') {
				$Weight = 0;
			}

			if($Width == '') {
				$Width = 0;
			}

			if($Height == '') {
				$Height = 0;
			}

			if($Lenght == '') {
				$Lenght = 0;
			}
			
			if($Ounces == '') {
				$Ounces = 0;
			}

			$sql_chk = "SELECT ID FROM product_shipping WHERE ProductID=$ProductID";
			$result_chk = mysql_query($sql_chk);
			$num_chk = mysql_num_rows($result_chk);
			
			if($num_chk > 0) {
				$sql_update  = "UPDATE product_shipping SET ShippingType='$ShippingType', UPS='$UPS', SpecificOption='$SpecificOption', DropShip='$DropShip', ";
				$sql_update .= "HandlingFee='$HandlingFee', Description='$Description', Weight=$Weight, Ounces=$Ounces, Width=$Width, Height=$Height, Lenght=$Lenght, EligibleFreeShipping='$EligibleFreeShipping' WHERE ProductID=$ProductID LIMIT 1";
				if(!mysql_query($sql_update)) {
					echo "Error Updating Shipping: ".mysql_error();
				}
			} else {
				$sql_add = "INSERT INTO product_shipping(ProductID, ShippingType, UPS, SpecificOption, DropShip, HandlingFee, Description, Weight, Ounces, Width, Height, Lenght, EligibleFreeShipping) ";
				$sql_add .= "VALUES($ProductID, '$ShippingType', '$UPS', '$SpecificOption', '$DropShip', '$HandlingFee', '$Description', $Weight, $Ounces, $Width, $Height, $Lenght, '$EligibleFreeShipping')";
		
				if(!mysql_query($sql_add)) {
					echo "Error Adding shipping: ".mysql_error();
				}
			}
	}

	if($_GET["id"] != '') {
		$id = $_GET['id'];
		$sql_prod = "SELECT BrowserName, RootSKU FROM products WHERE id='$id' LIMIT 1";
		$result_prod = mysql_query($sql_prod);
		$row_prod = mysql_fetch_assoc($result_prod);
		
		$sql_ship = "SELECT * FROM product_shipping WHERE ProductID='$id' LIMIT 1";
		$result_ship = mysql_query($sql_ship);
		$row_ship = mysql_fetch_assoc($result_ship);
		$num_ship = mysql_num_rows($result_ship);
		
		if($num_ship>0) {
			foreach($row_ship as $key=>$value){
				$$key = stripslashes($value);
			}
		}
	}

	$pgTitle = "Product Shipping";
	include_once("includes/mainHeader.php");
?>
<link rel="stylesheet" href="js/jquery.wysiwyg.css" type="text/css" />
<script src="js/menu-collapsed.js" type="text/javascript"></script>
<script type="text/javascript" src="js/jquery.wysiwyg.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$("#Description").wysiwyg();
		$('.frmLinks').click(function(){
			//alert($(this).attr("href"));
			$($(this).attr("href")).val($(this).attr("rel"))
		});

		$("#ShippingType").change(function() {
			if($(this).val() == 'PrimaryLocation') {
				$("#DropShip").fadeOut('slow');
				$("#UPS").fadeIn('slow');
				$("#SpecificOption").fadeIn('slow');
			} else if($(this).val() == 'Dropship') {
				$("#DropShip").fadeIn('slow');
				$("#UPS").fadeOut('slow');
				$("#SpecificOption").fadeOut('slow');
			} else {
				$("#DropShip").fadeOut('slow');
				$("#UPS").fadeOut('slow');
				$("SpecificOption").fadeOut('slow');
			}
		});
		
		$("#btnSubmit").click(function() {
			if(parseFloat($("#Ounces").val())>15) {
				alert("Onces cannot be more then 16");
				return false;
			}
		});
	});
</script>
<!--[if lt IE 8]>
   <style type="text/css">
   li a {display:inline-block;}
   li a {display:block;}
   </style>
   <![endif]-->
</head>
<body>
<form action="" method="post" >
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
  <div class="PD_main_form shipping">
    <h1>Shipping - <?=$row_prod["BrowserName"]." - ".$row_prod["RootSKU"];?><br />
    <span>----------------------------------------------------------</span></h1>
    <div class="clear"></div>
    <div class="ship_left">
	<input type="hidden" id="ProductID" name="ProductID" value="<?=$_GET['id'];?>" />
	<select id="ShippingType" name="ShippingType">
		<option value="">Select Shipping type...</option>
		<option <?php if($ShippingType=='PrimaryLocation') { echo ' selected="selected"'; } ?> value="PrimaryLocation">Ship From Primary Location</option>
		<option <?php if($ShippingType=='Dropship') { echo ' selected="selected"'; } ?> value="Dropship">Dropship</option>
	</select>
	<?php
		if($ShippingType == "PrimaryLocation") {
			$display = "block";
		} else {
			$display = "none";
		}
	?>
	<select style="display: <?=$display;?>" class="ups" id="UPS" name="UPS">
		<option value="">Select ups option...</option>
		<option <?php if($UPS=='CustomerChoose') { echo ' selected="selected"'; } ?> value="CustomerChoose">Let Customer Choose The Option</option>
		<option <?php if($UPS=='SpecificShipping') { echo ' selected="selected"'; } ?> value="SpecificShipping">Specific Shipping</option>
	</select>
	<select style="display: <?=$display;?>" class="ups" id="SpecificOption" name="SpecificOption">
		<option value="">Select Method...</option>
		<option <?php if($SpecificOption=='Ground') { echo ' selected="selected"'; } ?> value="Ground">Ground</option>
        <option <?php if($SpecificOption=='3day') { echo ' selected="selected"'; } ?> value="3day">3 Day Air</option>
		<option <?php if($SpecificOption=='2ndDay') { echo ' selected="selected"'; } ?> value="2ndDay">2nd Day Air</option>
        <option <?php if($SpecificOption=='1stDay') { echo ' selected="selected"'; } ?> value="1stDay">Next Day Air</option>
	</select>
	<?php
		if($ShippingType == "Dropship") {
			$display = "block";
		} else {
			$display = "none";
		}
	?>
	<select style="display: <?=$display;?>" id="DropShip" name="DropShip">
		<option value="">Select Dropship vendor...</option>
		<?php
			$sql_dropship = "SELECT id, Vendor FROM vendors WHERE Dropship='Yes' ORDER BY Vendor";
			$result_dropship = mysql_query($sql_dropship);
			
			while($row_dropship=mysql_fetch_array($result_dropship)) {
				if($DropShip == $row_dropship["id"]) {
					$selected = ' selected="selected" ';
				} else {
					$selected = '';
				}
				echo "<option value=\"$row_dropship[id]\" $selected >$row_dropship[Vendor]</option>";
			}
		?>
	</select>
    </div>
    <div class="ship_right">
	<table cellpadding="2" cellspacing="1">
		<tr>
			<td style="width: 100px; font-weight: bold;">+Handling Fee</td>
			<td style="width: 100px; font-weight: bold;">Weight (lbs)</td>
			<td style="width: 100px; font-weight: bold;">Ounces</td>
		</tr>
		<tr>
			<td style="width: 100px;"><input type="text" style="width: 70px;" id="HandlingFee" name="HandlingFee" value="<?=$HandlingFee;?>" /></td>
			<td style="width: 100px;"><input type="text" style="width: 70px;" id="Weight" name="Weight" value="<?=$Weight;?>" /></td>
			<td style="width: 100px;"><input type="text" style="width: 70px;" id="Ounces" name="Ounces" value="<?=$Ounces;?>" /></td>
		</tr>
		<tr>
			<td colspan="3" style="font-weight: bold;">Package Demensions:</td>
		</tr>
		<tr>
			<td><h2>Width</h2></td>
			<td><h2>Height</h2></td>
			<td><h2>Length</h2></td>
		</tr>
		<tr>
			<td><input type="text" style="width: 70px;" id="Width" name="Width" value="<?=$Width;?>" /></td>
			<td><input type="text" style="width: 70px;" id="Height" name="Height" value="<?=$Height;?>" /></td>
			<td><input type="text" style="width: 70px;" id="Lenght" name="Lenght" value="<?=$Lenght;?>" /></td>
		</tr>
		<tr>
			<td colspan="3">
				<?php
					if($EligibleFreeShipping == 'yes') {
						$checked = ' checked="checked"';
					}
				?>
				<input type="checkbox" style="padding: 0px; margin: 0px; width: 15px;" id="EligibleFreeShipping" name="EligibleFreeShipping" value="yes" <?=$checked;?> />Eligible for free shipping
			</td>
		</tr>
	</table>
	<div class="clear"></div>
      <textarea style="width: 600px; height: 200px; float: none; margin-top: 20px 0 0 0; padding: 0px;" id="Description" name="Description"><?=$Description;?></textarea>
      <input type="submit" value="" id="btnSubmit" name="btnSubmit" class="submit"/>
      <input type="submit" value="" class="cancel" />
    </div>
    <div class="clear"></div>
  </div>
  <!-- Product Detail Div ends here --> 
</div>
</form>
</body>
</html>
<?php
	mysql_close($conn);
?>