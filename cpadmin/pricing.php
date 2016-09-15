<?php
/************************************
 * Options & Pricing CPadmin page   
 *                                  
 * By: Richard Tuttle                     
 * Last updated: 03 August 2016      
 ***********************************/

include_once("includes/header.php");

// did the Save button get clicked?
if (isset($_POST["btnSave"])) {
	$statusSQL = "SELECT id FROM status WHERE current='yes' LIMIT 1";
	$statusResult = mysql_query($statusSQL) or die("Error obtaining site status! - " . mysql_error());
	$siteStatus = mysql_fetch_assoc($statusResult);
	// echo "<script>alert('Site Status: " . $siteStatus["id"] . "');</script>"; // testing use only
	$prodid = $_POST["prodid"];
	$SKUOrder = str_replace("SKU[]=",'', $_POST["SKUOrder"]);
	$SKUOrder = str_replace("&","|",$SKUOrder);
	$sql_prod_option = "UPDATE products SET option_seting_1='$_POST[RadioGroup1]' WHERE id=$prodid LIMIT 1";
	mysql_query($sql_prod_option);
		
	if ($_POST["SpecialPrice"] == '') {
		$specialprice = 0;
	} else {
		$specialprice = $_POST["SpecialPrice"];
	}
	list($sFMonth, $sFDay, $sFYear) = explode('/', $_POST['SpecialFrom']);
	list($sTMonth, $sTDay, $sTYear) = explode('/', $_POST['SpecialTo']);
                
    if ($sFMonth != '' && $sFDay != '' && $sFYear!= '')
		$_POST['SpecialFrom'] = $sFYear.'-'.$sFMonth.'-'.$sFDay;
    else  
        $_POST['SpecialFrom'] = '';

    if ($sTMonth != '' && $sTDay != '' && $sTYear != '')
		$_POST['SpecialTo'] = $sTYear.'-'.$sTMonth.'-'.$sTDay;
    else  
        $_POST['SpecialTo'] = '';

	$sql_prod = "UPDATE products SET `Type`='$_POST[GroupType]', ProductType='$_POST[ProductType]', SKUOrder='$SKUOrder', isSpecial='$_POST[isSpecial]', SpecialCategory='$_POST[SpecialCategory]', SpecialPrice=$specialprice, SpecialFrom='".$_POST['SpecialFrom']."', SpecialTo='".$_POST['SpecialTo']."', showSpecial='".$_POST['showSpecial']."' WHERE id=$prodid LIMIT 1";

	if (!mysql_query($sql_prod)) {
		echo "Error Updating Product Type: ".mysql_error();
	}

	if ($_POST["ProductType"] == "Bundle") {
				$inventory = $_POST["inventory"];
				if($inventory=='') { $inventory=0; }
				$sql_addopt = "UPDATE product_options SET Inventory=$inventory WHERE ProductID=$prodid LIMIT 1";
				mysql_query($sql_addopt);
				
				$totalb = $_POST["totalbundlenum"];
				if($totalb == '') { $totalb = 0; }
				
				for($b=1; $b<$totalb; $b++) {
					$sql_updatesort = "UPDATE product_bundles SET SortOrder=".$_POST["bundleitemsort_".$b]." WHERE id=".$_POST["bundleitem_".$b]." LIMIT 1";
					mysql_query($sql_updatesort);
				}
		} else {
			$sql_del = "DELETE FROM product_options WHERE ProductID=$prodid";
			mysql_query($sql_del);
		}

		if ($_POST["GroupType"] == "Size" && $_POST["ProductType"] != "Bundle") {
			if ($_FILES["osizefile"]["name"] == '') {
				$fileName = $_POST["osizeimage"];
			} else {
				$fileName = $_FILES["osizefile"]["name"];
				if ($siteStatus["id"] == 1) {
					$folderLoc = "/home/soccer1/public_html/images/productImages/";
				} else {
					$folderLoc = "/home/socnet/public_html/images/productImages/";
				}
				move_uploaded_file($_FILES["osizefile"]["tmp_name"], $folderLoc.$fileName);
			}
			
			$sizes=explode(",",$_POST["sizeids"]);
			if($_POST["sizeids"] != '') {
				foreach($sizes as $value_s) {
					$size_id = $_POST["size".$value_s];
					$size_addprice = $_POST["sizeaddprice".$value_s];
					$size_gender = $_POST["sizegender".$value_s];
					$size_position = intval($_POST["position".$value_s]);
					$sizeCat = $_POST["sizeCat"];
					
					$sql_size = "SELECT Size, SKU, Icon FROM sizes WHERE id=$size_id LIMIT 1";
					$result_size = mysql_query($sql_size);
					$row_size = mysql_fetch_assoc($result_size);
					
					$inventory = (isset($_POST["invsize".$value_s])?$_POST["invsize".$value_s]: '0');
					
					$sql_option  = "INSERT INTO product_options(ProductID, ColorImage, Size, Position, SizeSKU, SizeIcon, SizeAddPrice, Inventory, Gender, SizeCategory) ";
					$sql_option .= "VALUES($prodid, '$fileName', '".addslashes($row_size['Size'])."','".$size_position."', '$row_size[SKU]', '$row_size[Icon]', $size_addprice, $inventory, '$size_gender', '$sizeCat')";

					if(!mysql_query($sql_option)) {
						echo "Error Adding size: ".mysql_error();
					}
				}
			}
		}

		if ($_POST["GroupType"] == "Color" && $_POST["ProductType"] != "Bundle") {
			$colors = explode(",", $_POST["colorids"]);
			if ($_POST["colorids"] != '') {
				foreach ($colors as $value_c) {
					$color_id = $_POST["color".$value_c];
					$color_addprice = $_POST["coloraddprice".$value_c];
					$color_image = $_POST["colorimg".$value_c];
					$colorCat = $_POST["colorCat"];
					$colorTrim = $_POST["trim".$value_c];
					$sql_color = "SELECT Color, SKU, Icon FROM colors WHERE id=$color_id LIMIT 1";
					$result_color = mysql_query($sql_color);
					$row_color = mysql_fetch_assoc($result_color);
					$inventory = (isset($_POST["invcolor".$value_c])?$_POST["invcolor".$value_c]: '0');
					$sql_option  = "INSERT INTO product_options(ProductID, Color, ColorSKU, ColorIcon, ColorImage, ColorAddPrice, Inventory, ColorCategory, TrimColor) VALUES($prodid, '$row_color[Color]', '$row_color[SKU]', '$row_color[Icon]', '$color_image', '$color_addprice', $inventory, '$colorCat', '$colorTrim')";
					if (!mysql_query($sql_option)) {
						echo "Error Adding Color: " . mysql_error();
					}
				}
			}
		}
	
		if($_POST["GroupType"] == "ColorSize" && $_POST["ProductType"] != "Bundle") {
			$colors=explode(",",$_POST["colorids"]);
			$sizes=explode(",",$_POST["sizeids"]);
			$sizeCat = $_POST["sizeCat"];
			$colorCat = $_POST["colorCat"];
	
			if($_POST["colorids"] != '' && $_POST["sizeids"] != '') {
				foreach($colors as $value_c) {
					$color_id = $_POST["color".$value_c];
					$color_addprice = $_POST["coloraddprice".$value_c];
					$color_image = $_POST["colorimg".$value_c];
					$colorTrim = $_POST["trim".$value_c];
	
					$sql_color = "SELECT Color, SKU, Icon FROM colors WHERE id=$color_id LIMIT 1";
					$result_color = mysql_query($sql_color);
					$row_color = mysql_fetch_assoc($result_color);
		
					foreach($sizes as $value_s) {
						$size_id = $_POST["size".$value_s];
						$size_addprice = $_POST["sizeaddprice".$value_s];
						$size_gender = $_POST["sizegender".$value_s];
						$size_position = intval($_POST["position".$value_s]);
		
						$sql_size = "SELECT Size, SKU, Icon FROM sizes WHERE id=$size_id LIMIT 1";
						$result_size = mysql_query($sql_size);
						$row_size = mysql_fetch_assoc($result_size);
					
						$inventory = (isset($_POST["color".$value_c."_size".$value_s])?$_POST["color".$value_c."_size".$value_s]: '0');
						
						$sql_option  = "INSERT INTO product_options(ProductID, Color, ColorSKU, ColorIcon, ColorImage, ColorAddPrice, Size, Position, SizeSKU, SizeIcon, SizeAddPrice, Inventory, Gender, SizeCategory, ColorCategory, TrimColor) ";
						$sql_option .= "VAlUES($prodid, '$row_color[Color]', '$row_color[SKU]', '$row_color[Icon]', '$color_image', $color_addprice, '".addslashes($row_size['Size'])."', '".$size_position."', '$row_size[SKU]', '$row_size[Icon]', $size_addprice, $inventory, '$size_gender', '$sizeCat', '$colorCat', '$colorTrim')";
		
						if (!mysql_query($sql_option)) {
							echo "Error Add Options: " . mysql_error();
						}
					}
				}
			}
		}

		if ($_POST["GroupType"] == "None" && $_POST["ProductType"] != "Bundle") {
			if ($_POST["Inventory"] == '') {
				$inv = 0;
			} else {
				$inv = $_POST["Inventory"];
			}
			
			if ($_FILES["noneimage"]["name"] == '') {
				$fileName = $_POST["nimage"];
				if ($_FILES["noneimage"]["error"] > 0) {
					// echo "Error: " . $_FILES["noneimage"]["error"]; // testing use only
				}
			} else {
				$fileName = $_FILES["noneimage"]["name"];
				if ($siteStatus["id"] == 1) {
					$folderLoc = "/home/soccer1/public_html/images/productImages/"; // production server
				} else {
					$folderLoc = "/home/socnet/public_html/images/productImages/"; // development server
				}
				move_uploaded_file($_FILES["noneimage"]["tmp_name"], $folderLoc.$fileName);
			}
			
			$sql_option = "INSERT INTO product_options(ProductID, ColorImage, Inventory) VALUES($prodid, '$fileName', $inv)";
			if (!mysql_query($sql_option)) {
				echo "Error Adding Option: ".mysql_error();
			}
		}

		$sql_updateQty = "UPDATE products SET AvailableQty = (SELECT SUM(Inventory) AS Stock FROM product_options WHERE ProductID=$prodid) WHERE id=$prodid LIMIT 1";
		mysql_query($sql_updateQty);
		
		$sql_delPricing = "DELETE FROM product_pricing WHERE ProductID=$prodid";
		mysql_query($sql_delPricing);

		$prices = explode(",", $_POST["priceids"]);
		foreach ($prices as $value_p) {
			if (isset($_POST["NonMember_" . $value_p])) {
				$MSRP = $_POST["MSRP_" . $value_p];
				if ($MSRP == '') { $MSRP = 0; }
				
				$sql_pricing  = "INSERT INTO product_pricing(ProductID, Gender, ShowGender, GenderSKU, MSRP, NonMember, Option1, Option1Price, Option2, Option2Price, Option3, Option3Price, Option4, Option4Price) ";
				$sql_pricing .= "VALUES($prodid, '".$_POST["gender_".$value_p]."', '$_POST[ShowGender]', '".$_POST["GenderSKU_".$value_p]."' , $MSRP, ".$_POST["NonMember_".$value_p].", '$_POST[Option1]', ".$_POST["Option1_".$value_p].", '$_POST[Option2]', ".$_POST["Option2_".$value_p].", '$_POST[Option3]', ".$_POST["Option3_".$value_p].", '$_POST[Option4]', ".$_POST["Option4_".$value_p].")";
				
				if (!mysql_query($sql_pricing)) {
					echo "Error adding pricing: ".mysql_error();
				}
			}
		}
		
		$sql_delcat = "DELETE FROM category_items WHERE ProductID=$prodid";
		mysql_query($sql_delcat);

		if($_POST["category"] != '') {
			foreach($_POST["category"] as $value) {
				$sql_cat = "INSERT INTO category_items(CategoryID, ProductID) VALUES($value, $prodid)";
				mysql_query($sql_cat);
			}
		}
}

if ($_GET["id"] != '') {
	$sql_prod = "SELECT BrowserName, RootSKU, Type, SKUOrder, isSpecial, SpecialCategory, SpecialPrice, SpecialFrom, SpecialTo, ProductType FROM products WHERE id=$_GET[id] LIMIT 1";
	$result_prod = mysql_query($sql_prod);
	$row_prod = mysql_fetch_assoc($result_prod);
	foreach ($row_prod as $key=>$value) {
		$$key = stripslashes($value);
	}
	$sql_pricing = "SELECT DISTINCT ShowGender, Option1, Option2, Option3, Option4 FROM product_pricing WHERE ProductID=$_GET[id] LIMIT 1";
	$result_pricing = mysql_query($sql_pricing);
	$row_pricing = mysql_fetch_assoc($result_pricing);
	$p_option1 = $row_pricing["Option1"];
	$p_option2 = $row_pricing["Option2"];
	$p_option3 = $row_pricing["Option3"];
	$p_option4 = $row_pricing["Option4"];
	$ShowGender = $row_pricing["ShowGender"];
	if($p_option1 == '') { $p_option1 = '1-49'; }
	if($p_option2 == '') { $p_option2 = '50-99'; }
	if($p_option3 == '') { $p_option3 = '100-149'; }
	if($p_option4 == '') { $p_option4 = '150-200'; }
	if($ShowGender == '') { $ShowGender = 'Range'; }

	if ($ShowGender == 'None') {
		$genderview = "genderprice";
	} else {
		$genderview = "viewprice";
	}
			
	if ($SKUOrder == '') {
		$SKUOrder = "Root|Color|Size";
	}
	$SKUOrder = explode("|", $SKUOrder);
	$sql_cats = "SELECT DISTINCT ColorCategory, SizeCategory FROM product_options WHERE ProductID=$_GET[id] LIMIT 1";
	$result_cats = mysql_query($sql_cats);
	$num_cats = mysql_num_rows($result_cats);
	
	if ($num_cats>0) {
		$row_cats = mysql_fetch_assoc($result_cats);
		$colorCat = $row_cats["ColorCategory"];
		$sizeCat = $row_cats["SizeCategory"];
	}
	
	if ($Type=='None') {
		$sql_none = "SELECT ColorImage, Inventory FROM product_options WHERE ProductID=$_GET[id] LIMIT 1";
		$result_none = mysql_query($sql_none);
		$row_none = mysql_fetch_assoc($result_none);
		$ColorImage = $row_none["ColorImage"];
		$Inventory = $row_none["Inventory"];
		
	}
	
	if ($Type=="Size") {
		$sql_sizeonly = "SELECT ColorImage FROM product_options WHERE ProductID=$_GET[id] LIMIT 1";
		$result_sizeonly = mysql_query($sql_sizeonly);
		$row_sizeonly = mysql_fetch_assoc($result_sizeonly);
		$sizeimage = $row_sizeonly["ColorImage"];
	}
}

$pgTitle = "Product Options &amp; Pricing";
include_once("includes/mainHeader.php");
?>
<script type="text/javascript" src="js/jquery.treeview.js"></script>
<link rel="stylesheet" href="css/jquery.treeview.css" type="text/css" />
<script language="javascript" type="text/javascript">
var arrcolors = new Array();
var arrsizes = new Array();
var arrprices = new Array();
$(document).ready(function() {
	$("#categories").treeview();
	$("#SpecialFrom").datepicker();
	$("#SpecialTo").datepicker();
		
	if ($("#GroupType").val() == "Color" || $("#GroupType").val() == "ColorSize") {
		$.post("includes/inc_pricing.php", {
			"num":"0",
			"type":"viewcolor",
			"id":"<?=$_GET['id'];?>"
		}, function(data) {
			$("#divColor").append(data);
		});
	} else {
		$("#divColorOptions").hide('fast');
	}
			
	if ($("#GroupType").val() == "Size" || $("#GroupType").val() == "ColorSize") {
		$.post("includes/inc_pricing.php", {
			"num":"0", 
			"type":"viewsize", 
			"id":"<?=$_GET["id"];?>", 
			"grouptype":$("#GroupType").val()
		}, function(data) {
			$("#divSize").append(data);
		});
	} else {
		$("#divSizeOptions").hide('fast');
	}
		
	if ($("#GroupType").val() == "Size") {
		$("#sizeimage").show('fast');
	} else {
		$("#sizeimage").hide('fast');
	}
	
	if ($("#GroupType").val() == "None") {
		$("#divSKU").hide('fast');
		$(".generate").hide('fast');
		$("#divBlank").show('fast');
	}

	$.post("includes/inc_pricing.php", {
		"num":"0", 
		"type":"viewinventory"+$("#GroupType").val(), 
		"id":"<?=$_GET["id"];?>"
	}, function(data){
		$("#divInventory").append(data);
	});	
	
	$.post("includes/inc_pricing.php", {
		"num":"0", 
		"type":"<?=$genderview;?>", 
		"id":"<?=$_GET["id"];?>"
	}, function(data){
		$("#divPrice").append(data);
	});

	if ('<?=$ShowGender;?>' == 'None') {
		$("#addPrice").hide('fast');
	}

	$("#addColor").click(function() {
		var num = $("#colornum").val();
		num = parseInt(num) + 1;
		arrcolors.push(num);
		$("#colornum").val(num);
	
		$.post("includes/inc_pricing.php", {
			"num": num, 
			"type": "color", 
			"colorCat":$("#colorCat").val()
		}, function(data) {
			$("#divColor").append(data);
		});
	});

	$("#addSize").click(function() {
		var num = $("#sizenum").val();
		num = parseInt(num) + 1;
		arrsizes.push(num);
		$("#sizenum").val(num);
	
		$.post("includes/inc_pricing.php", {
			"num": num, 
			"type": "size", 
			"sizeCat":$("#sizeCat").val()
		}, function(data) {
			$("#divSize").append(data);	
		});
	});

			$("#addPrice").click(function() {
				var num = $("#pricenum").val();
				num = parseInt(num)+1;
				arrprices.push(num);
				$("#pricenum").val(num);
				
				$.post("includes/inc_pricing.php", {
					"num":num, 
					"type":"pricing"
				},
					function(data) {
						$("#divPrice").append(data);
					});
			});
			
			$(".generate").click(function() {
				$("#divInventory").html('<img src="../images/loader.gif" />');
				if($("#GroupType").val() == 'Size' || $("#GroupType").val() == 'ColorSize') {
					var arrSizeVal = new Array();
					for(i=0;i<arrsizes.length; i++) {
						arrSizeVal.push($("#size"+arrsizes[i]).val()+"_"+arrsizes[i]);
					}

					if(arrsizes.lenght<=0) {
						alert("Please add size options before generating inventory");
						$("#divInventory").html('');
						return false;
					}
				}
			
				if($("#GroupType").val() == 'Color' || $("#GroupType").val() == 'ColorSize') {
					var arrColorVal = new Array();
					for(var i=0; i<arrcolors.length; i++) {
						arrColorVal.push($("#color"+arrcolors[i]).val()+"_"+arrcolors[i]);
					}
	
					if(arrcolors.length<= 0) {
						alert("Please add color options before generating inventory");
						$("#divInventory").html('');
						return false;
					}	
				}
				$("#divInventory").load("includes/inc_pricing.php", {
					"type":"inventory"+$("#GroupType").val(), 
					"size[]":arrSizeVal, "color[]":arrColorVal
				});
				
			});
			
			$("#btnSave").click(function() {
				$("#colorids").val(arrcolors);
				$("#sizeids").val(arrsizes);
				$("#priceids").val(arrprices);
				$("#SKUOrder").val($("#divSKU").sortable('serialize'));
			});

			$("#divSKU").sortable();

			$("#GroupType").change(function(){
				if($(this).val() == 'Size') {
					$("#divColorOptions").hide('fast');
					$("#divSizeOptions").show('fast');
					$("#divInventory").html('');
					$(".generate").show('fast');
					$("#divSKU").show('fast');
					$("#sizeimage").show('fast');
					$("#divBlank").hide('fast');
				}

				if($(this).val() == 'Color') {
					$("#divSizeOptions").hide('fast');
					$("#divColorOptions").show('fast');
					$("#divInventory").html('');
					$(".generate").show('fast');
					$("#divSKU").show('fast');
					$("#divBlank").hide('fast');
					$("#sizeimage").hide('fast');
				}

				if($(this).val() == 'ColorSize') {
					$("#divSizeOptions").show('fast');
					$("#divColorOptions").show('fast');
					$("#divInventory").html('');
					$(".generate").show('fast');
					$("#divSKU").show('fast');
					$("#divBlank").hide('fast');
					$("#sizeimage").hide('fast');
				}
				
				if($(this).val() == 'None') {
					$("#divSizeOptions").hide('fast');
					$("#divColorOptions").hide('fast');
					$("#divInventory").hide('');
					$(".generate").hide('fast');
					$("#divSKU").hide('fast');
					$("#sizeimage").hide('fast');			
					$("#divBlank").show('fast');
				}
			});

			$("#ShowGender").change(function(){
				if($(this).val() == "None") {
					$("#divPrice").load("includes/inc_pricing.php", {
						"type":"hidegender", 
						"num":1}
						);
					$("#addPrice").hide('fast');
				} else {
					$("#divPrice").load("includes/inc_pricing.php", {
						"type":"viewprice", 
						"id":"<?=$_GET["id"];?>"
					});
					$("#addPrice").show('fast');
				}
			});			

			$('th[id^="options"]').click(function(){
				var id = $(this).attr("id");
				
				$(this).html('<input type="text" onblur="setOption('+id.substring(7)+')" id="optiontemp" value="'+$("#Option"+id.substring(7)).val()+'">');
				$("#optiontemp").focus();	
			});
			
			$("#ProductType").change(function(){
				if($("#ProductType").val() == "Bundle") {
					$("#divProductOptions").hide('fast');
					$("#divColorOptions").hide('fast');
					$("#divSizeOptions").hide('fast');
					$("#divInventory").hide('fast');
					$(".generate").hide('fast');
					$("#divSKU").hide('fast');
					$("#divBlank").hide('fast');
					$("#divBundleItems").show('fast');
					$("#divBItems").html('<img src="../images/loader.gif" />');
					$("#divBItems").load("includes/inc_pricing.php", {
						"type":"bundleitems", 
						"id":"<?=$_GET["id"];?>"
					});
					$("#divAddBItems").show('fast');	
				} else {
					$("#divBundleItems").hide('fast');
					$("#divAddBItems").hide('fast');
					$("#divProductOptions").show('fast');
					$("#divInventory").show('fast');
					
					if($("#GroupType").val() != "None") {
						$(".generate").show('fast');
						$("#divSKU").show('fast');
					}
					
					if($("#GroupType").val() == "Color" || $("#GroupType").val() == "ColorSize") {
						$("#divColorOptions").show('fast');	
						$("#sizeimage").hide('fast');
					}
					
					if($("#GroupType").val() == "Size" || $("#GroupType").val() == "ColorSize") {
						$("#divSizeOptions").show('fast');
					}
					
					if($("#GroupType").val() == "Size") {
						$("#sizeimage").show('fast');
					}
					
					if($("#GroupType").val() == "None") {
						$("#divBlank").show('fast');
					}
				}		
			});

				if($("#ProductType").val() == "Bundle") {
					$("#divProductOptions").hide('fast');
					$("#divColorOptions").hide('fast');
					$("#divSizeOptions").hide('fast');
					$("#divInventory").hide('fast');
					$(".generate").hide('fast');
					$("#divSKU").hide('fast');
					$("#divBlank").hide('fast');
					$("#divBundleItems").show('fast');
					$("#divBItems").html('<img src="../images/loader.gif" />');
					$("#divBItems").load("includes/inc_pricing.php", {
						"type":"bundleitems", 
						"id":"<?=$_GET["id"];?>"
					});
					$("#divAddBItems").show('fast');	
				} else {
					$("#divBundleItems").hide('fast');
					$("#divAddBItems").hide('fast');
					$("#divProductOptions").show('fast');
					$("#divInventory").show('fast');
					
					if($("#GroupType").val() != "None") {
						$(".generate").show('fast');
						$("#divSKU").show('fast');
					}
					
					if($("#GroupType").val() == "Color" || $("#GroupType").val() == "ColorSize") {
						$("#divColorOptions").show('fast');	
					}
					
					if($("#GroupType").val() == "Size" || $("#GroupType").val() == "ColorSize") {
						$("#divSizeOptions").show('fast');
					}
					
					if($("#GroupType").val() == "None") {
						$("#divBlank").show('fast');
					}
				}
		});
		
		function setOption(id){
			var data = $("#optiontemp").val();
			$("#options"+id).html(data);
			$("#Option"+id).val(data);
		}
		
		function setSKU(box, id, type) {
			$("#"+box).load("includes/inc_pricing.php", {
				"num":id, 
				"type":"sku", 
				"name":type
			});

			$.post("includes/inc_pricing.php", {
				"num":id, 
				"type":"icon", 
				"name":type
			}, 
				function(data) {
					$("#"+box.replace("sku", "icon")).attr("src",data);
				});
		}
		
		function setInv() {
			$(".inv").val($("#defaultInv").val());
		}

		function removeItem(cat, num) {
			var del = confirm('Delete Item?\nYou will have to regenerate the inventory qty.');
			
			if(del) {
				$('div').remove("#div"+cat+num);
				$("#divInventory").html('');
				if(cat=="Color") {
					for(var i=0; i<arrcolors.length; i++) {
						if(arrcolors[i] == num) {
							arrcolors.splice(i,1);
						}
					}
				}

				if(cat=="Size") {
					for(var i=0; i<arrsizes.length; i++) {
						if(arrsizes[i] == num) {
							arrsizes.splice(i,1);
						}
					}
				}
			}
		}
		
		function remPrice(id) {
			var del=confirm('Delete this price option from product?');
			if(del) {
				$('div').remove('#price_'+id);
				for(var i=0; i<arrprices.length; i++) {
					if(arrprices[i] == id) {
						arrprices.splice(i, 1);
					}
				}
			}
		}

		function remBundleItem(id) {
			var del=confirm('Delete this item from the bundle?');
			if(del) {
				$("#divBItems").html('<img src="images/loader.gif" />');
				$.post("includes/inc_pricing.php", {
					"type":"rembundleitem", 
					"id":id
				}, 
					function(data){
					alert(data);
					$("#divBItems").load("includes/inc_pricing.php", {
						"type":"bundleitems", 
						"id":"<?=$_GET["id"];?>"
					});
				});
			}
		}
		
	function searchBundle() {
		$("#divBSearch").html('<img src="images/loader.gif" />');
		$("#divBSearch").load("includes/inc_pricing.php", {
			"type":"bundlesearch", 
			"sku":$("#searchsku").val() ,
			"name":$("#searchname").val()
		});
	}

	function AddBundleItems(id) {
		$("#divBItems").html('<img src="images/loader.gif" />');
		$.post("includes/inc_pricing.php", {
			"type":"addbundleitem", 
			"id":"<?=$_GET["id"];?>", "bid":id
		}, 
			function(data) {
				$("#divBItems").load("includes/inc_pricing.php", {
					"type":"bundleitems", 
					"id":"<?=$_GET["id"];?>"
				});	
		});
}
</script>
</head>
<body>
<form method="post" action="" enctype="multipart/form-data">
<div class="Master_div"> 
    <div class="PD_header">
    	<div class="upper_head"></div>
    	<div class="navi"><?php include_once('includes/menu.php'); ?>
    	<div class="clear"></div>
    </div>
</div>
<input type="hidden" id="prodid" name="prodid" value="<?=$_GET["id"];?>">
<input type="hidden" id="colornum" name="colornum" value="1">
<input type="hidden" id="sizenum" name="sizenum" value="1">
<input type="hidden" id="pricenum" name="pricenum" value="1">
<input type="hidden" id="colorids" name="colorids" value="">
<input type="hidden" id="sizeids" name="sizeids" value="">
<input type="hidden" id="priceids" name="priceids" value="">
<input type="hidden" id="SKUOrder" name="SKUOrder" value="">
<div class="PD_main_form shipping">
	<h1>Product Options - <?=$BrowserName." - ".$RootSKU;?><br />
    <span>----------------------------------------------------------</span></h1>
    <div class="clear"></div>
    <div class="price_top" style="width: 855px;">
    	<div style="margin-bottom: 30px; float:left;">
       		<div style="width:200px; float:left;">
            Product Type: 
            <select id="ProductType" name="ProductType" style="width: 200px;">
                <option <?php if($ProductType == "Product") { echo 'selected="selected"'; } ?> value="Product">Product</option>
                <option <?php if($ProductType == "Bundle") { echo 'selected="selected"'; } ?> value="Bundle">Bundle</option>
             </select>
             </div>
             <div style="float:left; padding:7px;"></div>
		</div>
		<div class="clear"></div>
        <div id="divProductOptions">
        <div style="float:left; width:200px;">
            Option Type:
            <select id="GroupType" name="GroupType" class="type">
                <option <?php if($Type == "Color"){ echo 'selected="selected" '; } ?> value="Color">Color Only</option>
                <option <?php if($Type == "Size"){ echo 'selected="selected" '; } ?> value="Size">Size Only</option>
                <option <?php if($Type == "ColorSize") { echo 'selected="selected" ';} ?> value="ColorSize">Color & Size</option>
               <option <?php if($Type == "None") { echo 'selected="selected" ';} ?> value="None">None</option>
            </select>
            </div>
            <div >
              <div id="divSKU" class="sort" style="width:315px; float:left;">
                <div id="SKU_<?=$SKUOrder[0];?>" class="item"><?=$SKUOrder[0];?></div>
                <div id="SKU_<?=$SKUOrder[1];?>" class="item"><?=$SKUOrder[1];?></div>
                <div id="SKU_<?=$SKUOrder[2];?>" class="item"><?=$SKUOrder[2];?></div>
              </div>
              <div style="width:300px; float:left; border:2px solid #000; border-radius:2px;">
              <div style="font-size:18px; font-weight:bold; float:left; width:300px; border-bottom: 1px solid #000;"> System switching</div>
              <div style="float:left; width:300px; padding:5px;">
              <div>
              <?php
              // get all specific product details from the database
			  $prodid = $_GET['id'];
			  $sql_prod_option = "select * from  products WHERE id=$prodid LIMIT 1";
		      $query_res = mysql_query($sql_prod_option) or die("Product Retrieval Error: " . mysql_error());
		      $row = mysql_fetch_array($query_res); 
			  $option1 = $row['option_seting_1'];
			  ?>
              </div>
              <table>
              <tr><td style="font-weight:bold;">Set system</td><td>
              <p>
                <label style="float:none;">
                  <input type="radio" name="RadioGroup1" value="1" id="RadioGroup1" <?php if($option1=="1" or $option1==="" ) {?> checked="checked" <?php }?>/>
                 Simple</label>
                &nbsp;
                <label style="float:none;">
                  <input type="radio" name="RadioGroup1" value="2" id="RadioGroup1" <?php if($option1=="2") {?> checked="checked" <?php }?> />
                  Bundled</label>
               &nbsp;
              </p></td></tr>
              </table>
              </div>
              </div>
            </div>
		</div>
          <div class="clear"></div>
        </div>
    	<div class="price_cen">
    		<div id="divColorOptions">
                  <table>
                 	<tr>
                    	<th style="width: 500px; border: 0px; text-align: left;">
                        Color Category: 
                        <select id="colorCat" name="colorCat">
                            <option value="All">All Colors</option>
                            <?php
							$sql_colorcat = "SELECT Name FROM attribute_category WHERE Type='colors' ORDER BY Name";
							$result_colorcat = mysql_query($sql_colorcat);
							while($row_colorcat = mysql_fetch_assoc($result_colorcat)) {
								if($colorCat == $row_colorcat["Name"]) {
									$selected = ' selected="selected"';
								} else {
									$selected = '';
								}
								echo "<option value=\"$row_colorcat[Name]\" $selected>$row_colorcat[Name]</option>";
							}
							?>
                        </select>
                    	</th>
                    </tr>
                    <tr>
                      <th class="PC_color" style="width: 130px;">Color</th>
                      <th class="PC_SKU" style="width: 60px;">SKU</th>
                      <th class="PC_icon" style="width: 100px;">ICON</th>
                      <th class="PC_image" style="width: 150px;">Image</th>
                      <th class="PC_color" style="width: 180px;">Trim Color</th>
                      <th class="PC_add" style="width: 100px;">Additional Price</th>
                      <th class="PC_remove" style="width: 80px;">Remove</th>
                    </tr>
                  </table>
                  <div id="divColor"></div>
                <img src="../images/grey_plus2.jpg" alt="Add More Options" id="addColor">
            </div>
<iframe id="imgUpload" name="imgUpload" src="includes/imgUpload.php" style="width: 0px; height: 0px; border: 0px; visibility: hidden;"></iframe>            
		<div id="divSizeOptions">
                <table>
	               	<tr>
                    	<th style="width: 500px; border: 0px; text-align: left;">
                        Size Category: 
                        <select id="sizeCat" name="sizeCat">
                            <option value="All">All Sizes</option>
                            <?php
							$sql_sizecat = "SELECT Name FROM attribute_category WHERE Type='sizes' ORDER BY Name";
							$result_sizecat = mysql_query($sql_sizecat);
							while($row_sizecat = mysql_fetch_assoc($result_sizecat)) {
								if($sizeCat == $row_sizecat["Name"]) {
									$selected = ' selected="selected"';
								} else {
									$selected = '';
								}
								echo "<option value=\"$row_sizecat[Name]\" $selected>$row_sizecat[Name]</option>";
							}
							?>
                        </select>
                        </th>
                    </tr>
                    <tr>
                          <th class="PC_color">Size</th>
                          <th class="PC_icon" style="width: 100px;">Position</th>
                          <th class="PC_SKU" style="width: 120px;">SKU</th>
                          <th class="PC_icon" style="width: 120px;">ICON</th>
                          <th class="PC_add" style="width: 100px;">Additional Price</th>
						  <th class="PC_image" style="width: 140px;">Range</th>
                          <th class="PC_remove" style="width: 80px;">Remove</th>
                        </tr>
                  </table>
                <div id="divSize"></div>
                <img src="../images/grey_plus2.jpg" alt="" id="addSize">
            </div>
            <div class="clear"></div>
            <div id="sizeimage">
            	<table cellpadding="5"  cellspacing="1" style="float: none; width: 500px;">
                	<tr style="float: none;">
                    	<td style="float: none; border: 0px; width: 250px;">Upload image:</td>
                        <td style="float: none; border: 0px; width: 250px;"><input type="file" id="osizefile" name="osizefile"></td>
                    </tr>
                    <?php if($sizeimage != '') { ?>
                        <tr style="float: none;">
                            <td style="float: none; border: 0px; width: 250px;"></td>
                            <td style="float: none; border: 0px; width: 250px; text-align: center;">
                            	<input type="hidden" id="osizeimage" name="osizeimage" value="<?=$sizeimage;?>">
                            	<img src="../images/productImages/<?=$sizeimage;?>" style="width: 90px; height: 100px; margin: 5px; border: 1px solid #bebebe;">
                            </td>
                        </tr>
					<?php }	?>
                </table>
            </div>
          	<div class="clear"></div>
          <h3 class="generate">Generate Inventory Chart</h3>
          <h4>--------------------------------------------------------------------------------------------------------------------------------------------------</h4>
        </div>
    <div class="clear"></div>
  </div>
      <!-- Product Detail Div ends here -->
	<div class="price_btm">
    <div id="divInventory"></div>
<!-- Blank item ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: -->
	<div id="divBlank" style="display: none;">
    	<h3>Product Options</h3>
        <table cellpadding="5" cellspacing="1" style="width: 500px;">
        	<tr style="float: none;">
            	<td style="border: 0px; float: none;">Available Inventory</td>
                <td style="border: 0px; float: none;"><input type="text" style="width: 200px;" id="Inventory" name="Inventory" value="<?=$Inventory;?>"></td>
            </tr>
            <tr style="float: none;">
            	<td style="border: 0px; float: none;">Image</td>
                <td style="border: 0px; float: none; text-align: center;">
                	<input type="hidden" id="nimage" name="nimage" value="<?=$ColorImage;?>">
                	<input type="file" style="width: 200px;" id="noneimage" name="noneimage"><br>
                	<?php
						if ($ColorImage != '') {
							echo '<img src="../images/productImages/'.$ColorImage.'" style="width: 90px; height: 100px; margin: 5px; border: 2px solid #bebebe;">';
						}
					?>
                </td>
            </tr>
        </table>
    </div>
<!-- End Blank Item ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: --> 
<!-- Bundle items :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: -->
	<div id="divBundleItems" style="display: none;">
		<h3>Bundle Items</h3>
		<div id="divBItems"></div>
		<div id="divAddBItems" style="margin-top: 20px;">
			<table cellpadding="5" cellspacing="1" style="width: 940px;">
				<tr style="float: none;">
					<td colspan="5" style="text-align: left; float: none; padding-left: 15px; border: 0px; font-weight: bold; font-size: 14px;">Add Products to Bundle</td>
				</tr>
				<tr style="float: none;">
					<td style="float: none; border: 0px;">Search Root Sku</td>
					<td style="float: none; border: 0px;"><input type="text" style="width: 90%;" id="searchsku" /></td>
					<td style="float: none; border: 0px;">Search Browser Name</td>
					<td style="float: none; border: 0px;"><input type="text" style="width: 90%;" id="searchname" /></td>
					<td style="float: none; border: 0px;"><input type="button" id="btnSearchItems" onClick="searchBundle()" value="Search" /></td>
				</tr>
			</table>
			<div id="divBSearch" style="height: 200px; overflow: auto; margin-top: 10px;"></div>
		</div>
	</div>
<!-- End Bundle Items :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: -->
    <h3>Pricing</h3>
    <div class="pricing_right" style="width: 100%;">
    	<input type="hidden" id="Option1" name="Option1" value="<?=$p_option1;?>" />
        <input type="hidden" id="Option2" name="Option2" value="<?=$p_option2;?>" />
        <input type="hidden" id="Option3" name="Option3" value="<?=$p_option3;?>" />
        <input type="hidden" id="Option4" name="Option4" value="<?=$p_option4;?>" />
		<table class="P_right_table" style="width:100%;">
        	<tr>
        	  <th style="width: 154px; background-color: #ffffff;">  
			<select id="ShowGender" name="ShowGender" style="width: 100px; height: 30px;">
				<option <?php if($ShowGender=="Range") { echo 'selected="selected"'; }?> value="Range">Range</option>
				<option <?php if($ShowGender=="None") { echo 'selected="selected"'; }?> value="None">None</option>
				<option <?php if($ShowGender=="Size") { echo 'selected="selected"'; }?> value="Size">Size</option>
				<option <?php if($ShowGender=="uSize") { echo 'selected="selected"'; }?> value="uSize">Unit Size</option>
				<option <?php if($ShowGender=="Color") { echo 'selected="selected"'; }?> value="Color">Color</option>
			</select>
		  </th>
          	  <th style="height: 26px;">MSRP/Value</th>
              <th style="height: 26px;">Non Member</th>
              <th style="height: 26px;" class="PRT_color1" id="options1"><input type="text" id="Option1" name="Option1" value="<?=$p_option1;?>" /></th>
              <th style="height: 26px;" class="PRT_color2" id="options2"><input type="type" id="Option2" name="Option2" value="<?=$p_option2;?>" /></th>
              <th style="height: 26px;" class="PRT_color3" id="options3"><input type="type" id="Option3" name="Option3" value="<?=$p_option3;?>" /></th>
              <th style="height: 26px;" class="PRT_color4" id="options4"><input type="type" id="Option4" name="Option4" value="<?=$p_option4;?>" /></th>
              <th class="edit_pen" style="width:80px; background-color: #fff; font-size: 11px;">(Click header to edit values)</th>
            </tr>
      </table>
      <div id="divPrice"></div>
      <img src="../images/grey_plus2.jpg" alt="Add More Options" id="addPrice">
        </div>
    <div class="clear"></div>
    <div class="special_price">
    	<h3>Special Price</h3>
		   	<table cellpadding="5" cellspacing="1" style="width: 850px !important;">
        	<tr>
            	<td style="height: 22px; width: 200px; background-color: #FFFFFF; border: 0px; font-size: 13px; text-align: left; padding: 0px;"><input type="checkbox" style="padding: 5px; margin: 0px; width: 20px; border: 0px; background-color:#FFFFFF;" id="isSpecial" name="isSpecial" value="True" <?php if($isSpecial == "True"){ echo 'checked'; }?> />Special Price</td>
                <td style="height: 22px; width: 140px; background-color: #FFFFFF; border: 0px; font-size: 13px; text-align: left; padding: 0px;"><label><strong>Price</strong></label></td>
                <td style="height: 22px; width: 140px; background-color: #FFFFFF; border: 0px; font-size: 13px; text-align: left; padding: 0px;"><label><strong>From</strong></label></td>
                <td style="height: 22px; width: 140px; background-color: #FFFFFF; border: 0px; font-size: 13px; text-align: left; padding: 0px;"><label><strong>To</strong></label></td>
                <td style="height: 22px; width: 140px; background-color: #FFFFFF; border: 0px; font-size: 13px; text-align: left; padding: 0px;"><label><strong>Show on Specials</strong></label></td>
            </tr>
            <tr>
            	<td style="height: 22px; width: 200px; background-color: #FFFFFF; border: 0px; font-size: 13px; text-align: left; padding: 0px; padding: 0px;"><select id="SpecialCategory" name="SpecialCategory" style="height: 25px; width: 150px; border: 1px solid #bebebe;">
				<?php
                    $sql_special = "SELECT SpecialName FROM pricing_special";
                    $result_special = mysql_query($sql_special);
                    while ($row_special = mysql_fetch_array($result_special)) {
                        echo "<option value=\"$row_special[SpecialName]\"";
                        if ($row["SpecialCategory"] == $row_special["SpecialName"]) {
                        	echo " selected='selected'";
                        }
                        echo ">$row_special[SpecialName]</option>";
                    }
                ?>
                </select></td>
                <td style="height: 22px; width: 140px; background-color: #FFFFFF; border: 0px; font-size: 13px; text-align: left; padding: 0px;"><input type="text" style="margin: 0px; padding: 3px; background-color: #FFFFFF;" id="SpecialPrice" name="SpecialPrice" value="<?=$SpecialPrice;?>" /></td>
                <td style="height: 22px; width: 140px; background-color: #FFFFFF; border: 0px; font-size: 13px; text-align: left; padding: 0px;">
				<?php
				list($sFYear, $sFMonth, $sFDay) = explode('-', $SpecialFrom);
				list($sTYear, $sTMonth, $sTDay) = explode('-', $SpecialTo);
				$specialFormDate = '';
				if ($SpecialFrom)
					$specialFormDate = $sFMonth.'/'.$sFDay.'/'.$sFYear;
				$specialToDate = '';
				if ($SpecialTo)
					$specialToDate = $sTMonth.'/'.$sTDay.'/'.$sTYear;
				?>
				<input type="text" style="margin: 0px; padding: 3px;" id="SpecialFrom" name="SpecialFrom" value="<?=$specialFormDate;?>" /></td>
                <td style="height: 22px; width: 140px; background-color: #FFFFFF; border: 0px; font-size: 13px; text-align: left; padding: 0px;"><input type="text" style="margin: 0px; padding: 3px;" id="SpecialTo" name="SpecialTo" value="<?=$specialToDate;?>" /></td>
                <td tyle="height: 22px; width: 140px; background-color: #FFFFFF; border: 0px; font-size: 13px; text-align: left; padding: 0px;"><select name="showSpecial" id="showSpecial" style="height: 25px; width: 150px; border: 1px solid #bebebe;">
                <?php
                	if (($row["showSpecial"] == NULL) || ($row["showSpecial"] == "")) {
                		echo "<option value=''>select a status</option>";
                	}
                	if ($row["showSpecial"] == "yes") {
                		echo '<option value="yes" selected="selected">Yes</option>';
                	} else {
                		echo '<option value="yes">Yes</option>';
                	}
                	if ($row["showSpecial"] == "no") {
                		echo '<option value="no" selected="selected">No</option>';
                	} else {
                		echo '<option value="no">No</option>';
                	}
                ?></select></td>
            </tr>
        </table> 
        </div>
    <div class="clear"></div>
    <h3>Category</h3>
    <div class="clear"></div>
	<div style="width: 100%; padding-left: 50px;">
	<?php
		function subCategories($parent) {
			$sql_sub = "SELECT id, Category FROM category WHERE ParentID=$parent ORDER BY Sort";
			$result_sub = mysql_query($sql_sub);
			$num_sub = mysql_num_rows($result_sub);
		
			if($num_sub>0) {
				echo '<ul>';
				while($row_sub=mysql_fetch_array($result_sub)) {
					$sql_checked = "SELECT id FROM category_items WHERE CategoryID=$row_sub[id] AND ProductID=$_GET[id] LIMIT 1";

					$result_checked = mysql_query($sql_checked);
					$num_rows = mysql_num_rows($result_checked);

					if($num_rows>0) {
						$checked = ' checked="checked" ';
					} else {
						$checked = '';
					}

					echo '<li><span class="folder" style="font-size: 15px;"><input type="checkbox" style="width: 12px; height: 12px; background-color: #fff; margin: 0px 5px 0px 5px;" '.$checked.' id="category[]" name="category[]" value="'.$row_sub["id"].'"/>'.$row_sub["Category"].'</span>';
					subCategories($row_sub["id"]);
					echo "</li>";
				}
				echo '</ul>';
			} 
		}

		$sql_cat = "SELECT id, Category FROM category WHERE ParentID=0 AND id!=13 AND id!=14 ORDER BY Sort";
		$result_cat = mysql_query($sql_cat);

		echo '<ul id="categories" class="filetree">';
		while($row_cat=mysql_fetch_array($result_cat)) {

			$sql_checked = "SELECT id FROM category_items WHERE CategoryID=$row_cat[id] AND ProductID=$_GET[id] LIMIT 1";
			$result_checked = mysql_query($sql_checked);
			$num_rows = mysql_num_rows($result_checked);

			if($num_rows>0) {
				$checked = ' checked="checked" ';
			} else {
				$checked = '';
			}

			echo '<li><span class="folder" style="font-size: 15px;"><input type="checkbox" style="width: 12px; height: 12px; background-color: #fff; margin: 0px 5px 0px 5px;" '.$checked.' id="category[]" name="category[]" value="'.$row_cat["id"].'"/>'.$row_cat["Category"].'</span>';
			subCategories($row_cat["id"]);
			echo "</li>";
		}
		echo '</ul>';
	?>
	</div>
  </div>
      <div class="clear"></div>
      <input class="save" type="submit" style="margin-top: 20px; margin-bottom: 20px;" id="btnSave" name="btnSave" value="">
    </div>
</form>
</body>
</html>