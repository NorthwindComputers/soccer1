<?php
	require 'cpadmin/includes/db.php';
	session_start();

	if($_GET["cid"] == "") {
		header("location: cart.php");
	} else {
		$cartid = intval($_GET["cid"]);
	}
	
	$sql_scitem = "SELECT * FROM shopping_cart WHERE id=$cartid AND SessionID='".session_id()."' LIMIT 1";
	$result_scitem = mysql_query($sql_scitem);
	$num_scitem = mysql_num_rows($result_scitem);
	
	if($num_scitem>0) {
		$row_scitem = mysql_fetch_assoc($result_scitem);
	} else {
		header("location: cart.php");
	}

	$prodid = $row_scitem["ProductID"];
	$impQty = $row_scitem["Qty"];
	
	if(isset($_POST["btnUpdateImprint"])) {
		
		$sql_rem = "DELETE FROM imprint_shopping_cart WHERE CartID=$cartid";
		mysql_query($sql_rem);
		
		$arOpts = array("Front", "Back", "Short", "Socks");
		foreach($arOpts as $value) {
			
			if($_POST[$value] != "" && $_POST[$value] != "None") {
				
				$opt1Text = "";
				$opt2Text = "";
				$impPrice = 0;
				
				if(isset($_POST[$value."1_sizeName"])) {
					foreach($_POST[$value."1_sizeName"] as $sizekey=>$sizevalue) {
						$opt1Text .= $sizevalue." ( ".$_POST[$value."1_sizeValue"][$sizekey]." ) | ";
					}
				}
				if(isset($_POST[$value."2_sizeName"])) {
					foreach($_POST[$value."2_sizeName"] as $sizekey=>$sizevalue) {
						$opt2Text .= $sizevalue." ( ".$_POST[$value."2_sizeValue"][$sizekey]." ) | ";
					}
				}
				
				if($_POST[$value."_priceval"] != '') {
					$impPrice = $_POST[$value."_priceval"];
				}
				
				if($_POST[$value."_opt1Team"] != '') {
					$opt1Team = mysql_real_escape_string($_POST[$value."_opt1Team"]);
				}
				
				if($_POST[$value."_opt2Team"] != '') {
					$opt2Team = mysql_real_escape_string($_POST[$value."_opt2Team"]);
				}
				
				$sql_addImp  = "INSERT INTO imprint_shopping_cart (SessionID, CartID, EmailAddress, ProductID, ImprintPrice, Opt1Type, Opt1Image, Opt1Color, Opt1Loc, Opt1Text, Opt1Team, Opt2Type, Opt2Image, Opt2Color, Opt2Loc, Opt2Text, Opt2Team, CreatedDate) ";
				$sql_addImp .= "VALUES('".session_id()."','".$cartid."','".$_SESSION["email"]."', '$prodid', $impPrice, '".$_POST[$value."opt1Type"]."', '".$_POST[$value."opt1Image"]."', '".$_POST[$value."opt1Color"]."', '".$_POST[$value."opt1Loc"]."', '".$opt1Text."', '".$opt1Team."', '".$_POST[$value."opt2Type"]."', '".$_POST[$value."opt2Image"]."', '".$_POST[$value."opt2Color"]."', '".$_POST[$value."opt2Loc"]."', '".$opt2Text."', '".$opt2Team."', CURDATE())";
				
				mysql_query($sql_addImp);
			}
		}
		header("Location: cart.php");
	}
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Soccerone | Configure Your Imprint Item</title>
		<meta name="description" content="<?=$MetaDescription;?>" />
		<meta name="keywords" content="<?=$MetaKeywords;?>" />
	
		<link href="css/css_styles.css" rel="stylesheet" type="text/css" />
		<link href="css/imprint_new.css" rel="stylesheet" type="text/css" />
		<link href="css/jquery-ui.css" rel="stylesheet" type="text/css"/>
		<script type="text/javascript" src="js/jquery.min.js"></script>
		<script type="text/javascript" src="js/jquery-ui.min.js"></script>
		<script type="text/javascript">
			
		    $(document).ready(function(){
			
				<?php
					$sql_scimp = "SELECT * FROM imprint_shopping_cart WHERE CartID=$cartid AND SessionID='".session_id()."'";
					$result_scimp = mysql_query($sql_scimp);
					
					while($row_scimp = mysql_fetch_array($result_scimp)) {
						$optType = $row_scimp["Opt1Loc"];
						$opt1img = $row_scimp["Opt1Image"];
						$opt2img = $row_scimp["Opt2Image"];
						$rdoValue = $row_scimp["Opt1Type"];
						if($row_scimp["Opt2Type"] != '') {
							$rdoValue .= "_".$row_scimp["Opt2Type"];
						}
						
						switch($row_scimp["Opt1Type"]) {
							case "pocketlogo":
								$Opt1Type = "pocket";
								break;
							case "chestlogo":
								$Opt1Type = "logo";
								break;
							default:
								$Opt1Type = $row_scimp["Opt1Type"];
						}
						
						?>
							$(".<?=$optType;?> :radio[value=<?=$rdoValue;?>]").attr('checked', true);
							$("#<?=$optType;?>_options").load("includes/inc_imprint.php", {"type":"EditOptions","prodID":"<?=$prodid;?>", "optType":"<?=$rdoValue;?>", "optLoc":"<?=$row_scimp["Opt1Loc"];?>", "scid":"<?=$cartid;?>"});
							
							$("#<?=$optType;?>_pricechart").css('display', 'block');
							$("#<?=$optType;?>_pricechart").load("includes/inc_imprint.php", {"type":"impPricing", "prodID":"<?=$prodid;?>", "optType":"<?=$Opt1Type;?>",  "optLoc":"<?=$optType;?>"});
							
							<?php
								if($row_scimp["Opt2Type"] != '') {
									?>
									$("#<?=$optType;?>_pricechart2").css('display', 'block');
									$("#<?=$optType;?>_pricechart2").load("includes/inc_imprint.php", {"type":"impPricing", "prodID":"<?=$prodid;?>", "optType":"<?=$row_scimp["Opt2Type"];?>",  "optLoc":"<?=$optType;?>"});
									<?php
								}
							?>
							
							var eprice = $("#<?=$optType;?>-<?=$rdoValue;?>-price").val();
							$("#<?=$optType;?>_price").html(eprice);
							$("#<?=$optType;?>_priceval").val(eprice);
							
						<?php	
						
							switch($rdoValue) {
								case "logo":
								case "chestlogo":
								case "number":
									echo '$("#'.$optType.'_main").attr("src", "'.$opt1img.'");';
									echo '$("#'.$optType.'_main").css("display", "");';
									break;
									
								case "logo_number":
									if($row_scimp["Opt1Type"] == "number") { 
										echo '$("#'.$optType.'_main").attr("src", "'.$opt1img.'");';
										echo '$("#'.$optType.'_pocket").attr("src", "'.$opt2img.'");';
									} else {
										echo '$("#'.$optType.'_main").attr("src", "'.$opt2img.'");';
										echo '$("#'.$optType.'_pocket").attr("src", "'.$opt1img.'");';
									}
									
									echo '$("#'.$optType.'_main").css("display", "");';
									echo '$("#'.$optType.'_pocket").css("display", "");';
									break;
									
								case "pocketlogo":
									echo '$("#'.$optType.'_pocket").attr("src", "'.$opt1img.'");';
									echo '$("#'.$optType.'_pocket").css("display", "");';
									break;
									
								case "pocketlogo_number":
									if($row_scimp["Opt1Type"] == "number") { 
										echo '$("#'.$optType.'_main").attr("src", "'.$opt1img.'");';
										echo '$("#'.$optType.'_pocket").attr("src", "'.$opt2img.'");';
									} else {
										echo '$("#'.$optType.'_main").attr("src", "'.$opt2img.'");';
										echo '$("#'.$optType.'_pocket").attr("src", "'.$opt1img.'");';
									}
									
									echo '$("#'.$optType.'_main").css("display", "");';
									echo '$("#'.$optType.'_pocket").css("display", "");';
									break;
								
								case "chestlogo_number":
									if($row_scimp["Opt1Type"] == "number") { 
										echo '$("#'.$optType.'_main").attr("src", "'.$opt1img.'");';
										echo '$("#'.$optType.'_pocket").attr("src", "'.$opt2img.'");';
									} else {
										echo '$("#'.$optType.'_main").attr("src", "'.$opt2img.'");';
										echo '$("#'.$optType.'_pocket").attr("src", "'.$opt1img.'");';
									}
								
									echo '$("#'.$optType.'_main").css("display", "");';
									echo '$("#'.$optType.'_pocket").css("display", "");';
									break;
									
								case "name":
									echo '$("#'.$optType.'_name").attr("src", "'.$opt1img.'");';
									echo '$("#'.$optType.'_name").css("display", "");';
									break;
									
								case "name_number":
									if($row_scimp["Opt1Type"] == "number") { 
										echo '$("#'.$optType.'_main").attr("src", "'.$opt1img.'");';
										echo '$("#'.$optType.'_name").attr("src", "'.$opt2img.'");';
									} else {
										echo '$("#'.$optType.'_main").attr("src", "'.$opt2img.'");';
										echo '$("#'.$optType.'_name").attr("src", "'.$opt1img.'");';
									}
									
									echo '$("#'.$optType.'_main").css("display", "");';
									echo '$("#'.$optType.'_name").css("display", "");';
									break;
							}
					}
				
				?>
		    	
				$(".Front").click(function(){
					var opt = $(this).val();
					if(opt == '') {
						return;	
					}
					
					$("#Front_main").css("display", "none");
					$("#Front_pocket").css("display", "none");
					$("#Front_options").html('<img src="images/loader.gif" />');
					$("#Front_pricechart").html('');
					$("#Front_pricechart2").html('');
					$("#Front_pricechart").css('display', '');
					$("#Front_pricechart2").css('display', '');
					
					switch(opt) {
						case "None":
							$("#Front_options").html('');
							break;
							
						case "logo":
							$("#Front_main").attr('src', 'images/imprint/logo.png');
							$("#Front_main").css('display', '');
							//$("#Front_options").load("includes/inc_imprint.php", {"type":"Options","prodID":"<?=$prodid;?>", "optType":"logo", "optLoc":"Front"});
							$("#Front_options").load("includes/inc_imprint.php", {"type":"EditOptions","prodID":"<?=$prodid;?>", "optType":"logo", "optLoc":"Front", "scid":"<?=$cartid;?>"});
							$("#Front_pricechart").css('display','block');
							$("#Front_pricechart").load("includes/inc_imprint.php", {"type":"impPricing", "prodID":"<?=$prodid;?>", "optType":"logo", "optLoc":"Front"});
							break;
							
						case "logo_number":
							$("#Front_main").attr('src', 'images/imprint/logo.png');
							$("#Front_pocket").attr('src', 'images/imprint/number.png');
							$("#Front_pocket").css('display', '');
							$("#Front_main").css('display', '');
							//$("#Front_options").load("includes/inc_imprint.php", {"type":"Options","prodID":"<?=$prodid;?>", "optType":"logo_number", "optLoc":"Front"});
							$("#Front_options").load("includes/inc_imprint.php", {"type":"EditOptions","prodID":"<?=$prodid;?>", "optType":"logo_number", "optLoc":"Front", "scid":"<?=$cartid;?>"});
							$("#Front_pricechart").css('display', 'block');
							$("#Front_pricechart2").css('display', 'block');
							$("#Front_pricechart").load("includes/inc_imprint.php", {"type":"impPricing", "prodID":"<?=$prodid;?>", "optType":"logo", "optLoc":"Front"});
							$("#Front_pricechart2").load("includes/inc_imprint.php", {"type":"impPricing", "prodID":"<?=$prodid;?>", "optType":"number", "optLoc":"Front"});
							break;
							
						//::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
							
						case "pocketlogo":
							$("#Front_pocket").attr('src', 'images/imprint/logo.png');
							$("#Front_pocket").css('display', '');
							//$("#Front_options").load("includes/inc_imprint.php", {"type":"Options","prodID":"<?=$prodid;?>", "optType":"pocketlogo", "optLoc":"Front"});
							$("#Front_options").load("includes/inc_imprint.php", {"type":"EditOptions","prodID":"<?=$prodid;?>", "optType":"pocketlogo", "optLoc":"Front", "scid":"<?=$cartid;?>"});
							$("#Front_pricechart").css('display', 'block');
							$("#Front_pricechart").load("includes/inc_imprint.php", {"type":"impPricing", "prodID":"<?=$prodid;?>", "optType":"pocket", "optLoc":"Front"});
							break;
							
						case "chestlogo":
							$("#Front_main").attr('src', 'images/imprint/logo.png');
							$("#Front_main").css('display', '');
							//$("#Front_options").load("includes/inc_imprint.php", {"type":"Options","prodID":"<?=$prodid;?>", "optType":"chestlogo", "optLoc":"Front"});
							$("#Front_options").load("includes/inc_imprint.php", {"type":"EditOptions","prodID":"<?=$prodid;?>", "optType":"chestlogo", "optLoc":"Front", "scid":"<?=$cartid;?>"});
							$("#Front_pricechart").css('display', 'block');
							$("#Front_pricechart").load("includes/inc_imprint.php", {"type":"impPricing", "prodID":"<?=$prodid;?>", "optType":"logo", "optLoc":"Front"});
							break;
							
						case "pocketlogo_number":
							$("#Front_pocket").attr('src', 'images/imprint/logo.png');
							$("#Front_main").attr('src', 'images/imprint/number.png');
							$("#Front_pocket").css('display', '');
							$("#Front_main").css('display', '');
							//$("#Front_options").load("includes/inc_imprint.php", {"type":"Options","prodID":"<?=$prodid;?>", "optType":"pocketlogo_number", "optLoc":"Front"});
							$("#Front_options").load("includes/inc_imprint.php", {"type":"EditOptions","prodID":"<?=$prodid;?>", "optType":"pocketlogo_number", "optLoc":"Front", "scid":"<?=$cartid;?>"});
							$("#Front_pricechart").css('display', 'block');
							$("#Front_pricechart2").css('display', 'block');
							$("#Front_pricechart").load("includes/inc_imprint.php", {"type":"impPricing", "prodID":"<?=$prodid;?>", "optType":"logo", "optLoc":"Front"});
							$("#Front_pricechart2").load("includes/inc_imprint.php", {"type":"impPricing", "prodID":"<?=$prodid;?>", "optType":"number", "optLoc":"Front"});
							
							break;
							
						case "chestlogo_number":
							$("#Front_pocket").attr('src', 'images/imprint/number.png');
							$("#Front_main").attr('src', 'images/imprint/logo.png');
							$("#Front_pocket").css('display', '');
							$("#Front_main").css('display', '');
							//$("#Front_options").load("includes/inc_imprint.php", {"type":"Options","prodID":"<?=$prodid;?>", "optType":"chestlogo_number", "optLoc":"Front"});
							$("#Front_options").load("includes/inc_imprint.php", {"type":"EditOptions","prodID":"<?=$prodid;?>", "optType":"chestlogo_number", "optLoc":"Front", "scid":"<?=$cartid;?>"});
							$("#Front_pricechart").css('display', 'block');
							$("#Front_pricechart2").css('display', 'block');
							$("#Front_pricechart").load("includes/inc_imprint.php", {"type":"impPricing", "prodID":"<?=$prodid;?>", "optType":"logo", "optLoc":"Front"});
							$("#Front_pricechart2").load("includes/inc_imprint.php", {"type":"impPricing", "prodID":"<?=$prodid;?>", "optType":"number", "optLoc":"Front"});
							break;
					}
					
					var price = $("#Front-"+opt+"-price").val();
					$("#Front_price").html(price);
					$("#Front_priceval").val(price);
					
				});
				
				$(".Back").click(function(){
					var opt = $(this).val();
					if(opt == '') {
						return;	
					}
					
					$("#Back_main").css("display", "none");
					$("#Back_name").css("display", "none");
					$("#Back_options").html('<img src="images/loader.gif" />');
					$("#Back_pricechart").html('');
					$("#Back_pricechart2").html('');
					$("#Back_pricechart").css('display', '');
					$("#Back_pricechart2").css('display', '');
					
					switch(opt) {
						case "None":
							$("#Back_options").html('');
							break;
						
						case "name":
							$("#Back_name").attr('src', 'images/imprint/name_back.png');
							$("#Back_name").css('display', '');
							//$("#Back_options").load("includes/inc_imprint.php", {"type":"Options","prodID":"<?=$prodid;?>", "optType":"name", "optLoc":"Back"});
							$("#Back_options").load("includes/inc_imprint.php", {"type":"EditOptions","prodID":"<?=$prodid;?>", "optType":"name", "optLoc":"Back", "scid":"<?=$cartid;?>"});
							$("#Back_pricechart").css('display', 'block');
							$("#Back_pricechart").load("includes/inc_imprint.php", {"type":"impPricing", "prodID":"<?=$prodid;?>", "optType":"name", "optLoc":"Back"});
							break;
							
						case "number":
							$("#Back_main").attr('src', 'images/imprint/number.png');
							$("#Back_main").css('display', '');
							//$("#Back_options").load("includes/inc_imprint.php", {"type":"Options","prodID":"<?=$prodid;?>", "optType":"number", "optLoc":"Back"});
							$("#Back_options").load("includes/inc_imprint.php", {"type":"EditOptions","prodID":"<?=$prodid;?>", "optType":"number", "optLoc":"Back", "scid":"<?=$cartid;?>"});
							$("#Back_pricechart").css('display', 'block');
							$("#Back_pricechart").load("includes/inc_imprint.php", {"type":"impPricing", "prodID":"<?=$prodid;?>", "optType":"number", "optLoc":"Back"});
							break;
							
						case "name_number":
							$("#Back_name").attr('src', 'images/imprint/name_back.png');
							$("#Back_main").attr('src', 'images/imprint/number.png');
							$("#Back_name").css('display', '');
							$("#Back_main").css('display', '');
							//$("#Back_options").load("includes/inc_imprint.php", {"type":"Options","prodID":"<?=$prodid;?>", "optType":"name_number", "optLoc":"Back"});
							$("#Back_options").load("includes/inc_imprint.php", {"type":"EditOptions","prodID":"<?=$prodid;?>", "optType":"name_number", "optLoc":"Back", "scid":"<?=$cartid;?>"});
							$("#Back_pricechart").css('display', 'block');
							$("#Back_pricechart2").css('display', 'block');
							$("#Back_pricechart").load("includes/inc_imprint.php", {"type":"impPricing", "prodID":"<?=$prodid;?>", "optType":"name", "optLoc":"Back"});
							$("#Back_pricechart2").load("includes/inc_imprint.php", {"type":"impPricing", "prodID":"<?=$prodid;?>", "optType":"number", "optLoc":"Back"});
							break;
					}
					
					var price = $("#Back-"+opt+"-price").val();
					$("#Back_price").html(price);
					$("#Back_priceval").val(price);
					
				});
				
				$(".Short").click(function(){
					var opt = $(this).val();
					if(opt == '') {
						return;	
					}
					
					$("#Short_main").css("display", "none");
					$("#Short_options").html('<img src="images/loader.gif" />');
					$("#Short_pricechart").html('');
					$("#Short_pricechart2").html('');
					$("#Short_pricechart").css('display', '');
					$("#Short_pricechart2").css('display', '');
					
					switch(opt) {
						case "None":
							$("#Short_options").html('');
							break;
							
						case "number":
							$("#Short_main").attr('src', 'images/imprint/number.png');
							$("#Short_main").css('display', '');
							//$("#Short_options").load("includes/inc_imprint.php", {"type":"Options","prodID":"<?=$prodid;?>", "optType":"number", "optLoc":"Short"});
							$("#Short_options").load("includes/inc_imprint.php", {"type":"EditOptions","prodID":"<?=$prodid;?>", "optType":"number", "optLoc":"Short", "scid":"<?=$cartid;?>"});
							$("#Short_pricechart").css('display', 'block');
							$("#Short_pricechart").load("includes/inc_imprint.php", {"type":"impPricing", "prodID":"<?=$prodid;?>", "optType":"number", "optLoc":"Short"});
							break;
							
						case "logo":
							$("#Short_main").attr('src', 'images/imprint/logo.png');
							$("#Short_main").css('display', '');
							//$("#Short_options").load("includes/inc_imprint.php", {"type":"Options","prodID":"<?=$prodid;?>", "optType":"logo", "optLoc":"Short"});
							$("#Short_options").load("includes/inc_imprint.php", {"type":"EditOptions","prodID":"<?=$prodid;?>", "optType":"logo", "optLoc":"Short", "scid":"<?=$cartid;?>"});
							$("#Short_pricechart").css('display', 'block');
							$("#Short_pricechart").load("includes/inc_imprint.php", {"type":"impPricing", "prodID":"<?=$prodid;?>", "optType":"logo", "optLoc":"Short"});
							break;
					}
					
					var price = $("#Short-"+opt+"-price").val();
					$("#Short_price").html(price);
					$("#Short_priceval").val(price);
					
				});
				
				
				$("#frmImprint").submit(function(){
					
					var ret;
					var arOpts = new Array("Front", "Back", "Short", "Socks");
					
					$.each(arOpts, function(index, value) {
						if($("#"+value+"opt1Loc").val() != '') {
							if($("#"+value+"opt1Image").val() == "") {
								alert("Select options for "+value+" imprint");
								ret = false;
								return false;
							}
						}
					});
					
					if(ret == false) {
						return false	
					}
					
					$(".textOpt").each(function(index) {
						if($(this).val() == "") {
							alert("Enter Name/Number");
							$(this).focus();
							ret = false;
							return false;
						}
					});
					if(ret == false) {
						return false	
					}
				});
				
				$("#btnCancel").click(function(){
					window.location = "cart.php";
				});
				
				
		    });
		    
		</script>
	</head>
	
<body>
	
<!-- Master Div starts from here -->
<div class="Master_div"> 
  <!-- Header Div starts from here -->
  <?php include('includes/header.php'); ?>
  <!-- Header Div ends here --> 
  <!-- Container Div starts from here -->
  <div class="container container1">
    <div class="navigation">
      <div class="navi_L"></div>
      <div class="navi_C">
        <?php include('./includes/topnav.php'); ?>
        <div class="clear"></div>
      </div>
      <div class="navi_R"></div>
      <div class="clear"></div>
    </div>
    <div style="clear:both;height:10px"></div>
    	<form id="frmImprint" action="" method="post">
			<div class="imprint-design">
			
				<?php
				
					// DISPLAY BANNER
					$sql_banner = "SELECT ic.BannerImage FROM imprint_categories ic, products p WHERE p.ImprintCatID=ic.id AND p.id=$prodid LIMIT 1";
					$result_banner = mysql_query($sql_banner);
					$row_banner = mysql_fetch_assoc($result_banner);
					
					if($row_banner["BannerImage"] != '') {
						?>
							<img class="impBanner" src="images/ImprintCategory/<?=stripslashes($row_banner["BannerImage"]);?>" />
						<?php
					}
					// END DISPLAY BANNER
				
					// DISPLAY ITEM INFO FOR IMPRINT ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
					$prodType = $row_scitem["Type"];
					$prodName = $row_scitem["ProductName"];
					
					// CHECK SINGLE ITEM BUNDLE :::::::::::::::::::::::::::::::::::::::::::::
					if($prodType == "Product") {
						$sql_singleb = "SELECT option_seting_1 FROM products WHERE id=$prodid LIMIT 1";
						$result_singleb = mysql_query($sql_singleb);
						$row_singleb = mysql_fetch_assoc($result_singleb);
						$prodSingle = $row_singleb["option_seting_1"];
					}
					// END CHECK SINGLE ITEM BUNDLE ::::::::::::::::::::::::::::::::::::::::
					
					$arImage = array();
					$arColorName = array();
					$arSizeName = array();
					
					if($prodType == "Bundle") {
						
						$sql_bitems = "SELECT * FROM shopping_cart WHERE BundleID=$row_scitem[id]";
						$result_bitems = mysql_query($sql_bitems);
						
						while($row_bitems = mysql_fetch_array($result_bitems)) {
							$colorSKU = $row_bitems["ColorSKU"];
							$sizeSKU = $row_bitems["SizeSKU"];
							
							$sql_imgs = "SELECT ColorImage, Size, Color FROM product_options WHERE ProductID=$row_bitems[ProductID] AND ColorSku='$colorSKU' AND SizeSKU='$sizeSKU' LIMIT 1";
							$result_imgs = mysql_query($sql_imgs);
							$row_imgs = mysql_fetch_assoc($result_imgs);
							
							$arImage[] = $row_imgs["ColorImage"];
							$arColorName[] = $row_imgs["Color"];
							$arSizeName[] = $row_imgs["Size"];
							
						}
					} elseif($prodType == "Product" && $prodSingle == "2") {
						$sql_singleI = "SELECT * FROM shopping_cart_single WHERE singleid=$cartid";
						$result_singleI = mysql_query($sql_singleI);
						
						while($row_singleI = mysql_fetch_array($result_singleI)) {
							$colorSKU = $row_singleI["ColorSKU"];
							$sizeSKU  = $row_singleI["SizeSKU"];
							
							$sql_imgs = "SELECT ColorImage, Size, Color FROM product_options WHERE ProductID=$prodid AND ColorSKU='$colorSKU' AND SizeSKU='$sizeSKU' LIMIT 1";
							$result_imgs = mysql_query($sql_imgs);
							$row_imgs = mysql_fetch_assoc($result_imgs);
							
							$arImage[] = $row_imgs["ColorImage"];
							$arColorName[] = $row_imgs["Color"];
							$arSizeName [] = $row_imgs["Size"];
						}
					} else {
						$colorSKU = $row_scitem["ColorSKU"];
						$sizeSKU  = $row_scitem["SizeSKU"];
						
						$sql_imgs = "SELECT ColorImage, Size, Color FROM product_options WHERE ProductID=$prodid AND ColorSKU='$colorSKU' AND SizeSKU='$sizeSKU' LIMIT 1";
						$result_imgs = mysql_query($sql_imgs);
						$row_imgs = mysql_fetch_assoc($result_imgs);
						
						$arImage[] = $row_imgs["ColorImage"];
						$arColorName[] = $row_imgs["Color"];
						$arSizeName[] = $row_imgs["Size"];
					}
					
					// END DISPLAY ITEM INFO FOR IMPRINT ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
				?>
				<div class="designs">
					<div class="desHeader">Configure Imprint Options for <?=$prodName;?></div>
					<?php
					
						if($prodType == "Product" && $prodSingle == "2") {
							foreach($arImage as $key=>$value){ ?>
								<div class="desItems">
									<img src="images/productImages/<?=$value;?>" />
									<p class="desDesc"><strong>Color:</strong> <?=$arColorName[$key];?></p>
									<p class="desDesc"><strong>Size:</strong> <?=$arSizeName[$key];?></p>
								</div>
								<?php
							}
						} else {
							$i = 1;
							foreach($arImage as $key => $value) {
								if($i == 1) { ?>
									<div class="desItems">
										<img src="images/productImages/<?=$value;?>" />
										<p class="desDesc"><strong>Color:</strong> <?=$arColorName[$key];?></p>
								<?php } ?>
										<p class="desDesc"><strong>Size:</strong> <?=$arSizeName[$key];?></p>
								<?php if($i == $impQty) { ?>
									</div>
								<?php
								}
								if($i == $impQty) {
									$i = 1;
								} else {
									$i++;
								}
							}
						}
					?>
				</div>
            
            	<?php
					$arrOptions = array();
					$arrPrice   = array();
					
					// CHECK VIP STATUS :::::::::::::::::::::::::::
					$VIPStatus = '';
					$VIPLevel = '';
					
					if ($_SESSION["email"] != '') {
						$sql_vip = "SELECT Status, VIPLevel FROM customers 
									WHERE EmailAddress='".$_SESSION['email']."' AND current_date()<DATE_ADD(VIPDate, INTERVAL 1 YEAR) LIMIT 1";
						$result_vip = mysql_query($sql_status);
						$row_vip = mysql_fetch_assoc($result_status);
						$VIPStatus = $row_status["Status"];
						$VIPLevel = $row_status["VIPLevel"];
					}
					
					// SET IMPRINT OPTIONS ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
					$sql_impType = "SELECT DISTINCT o.*, i.Type AS Location FROM imprint_options o, imprint_images i, products p WHERE p.ImprintCatID = o.ImprintCategory AND o.id=i.OptionID AND p.id=$prodid ORDER BY o.Type";
					$result_impType = mysql_query($sql_impType);
					
					while($row_impType = mysql_fetch_array($result_impType)) {
						$priceType = $row_impType["Type"];
						$priceLoc  = $row_impType["Location"];
						
						if($VIPStatus == '') {
							$arrPrice[$priceLoc][$priceType] = $row_impType["DefaultPrice"];
							
						} else {
							// PRICE OPTION 1 :::::::::::::::::::::::::::::::
							$prange1 = explode("-", $row_impType["Option1"]);
							if($impQty>=$prange1[0] && $impQty<=$prange1[1]) {
								$arrPrice[$priceLoc][$priceType] = $row_impType["Option1Price"];
							} else {
								// PRICE OPTION 2 :::::::::::::::::::::::::::::::
								$prange2 = explode("-", $row_impType["Option2"]);
								if($impQty>=$prange2[0] && $impQty<=$prange2[1]) {
									$arrPrice[$priceLoc][$priceType] = $row_impType["Option2Price"];
								} else {
									// PRICE OPTION 3 :::::::::::::::::::::::::::::::
									$prange3 = explode("-", $row_impType["Option3"]);
									if($impQty>=$prange3[0] && $impQty<=$prange3[1]) {
										$arrPrice[$priceLoc][$priceType] = $row_impType["Option3Price"];
									} else {
										// PRICE OPTION 4 :::::::::::::::::::::::::::::::
										$prange4 = explode("-", $row_impType["Option4"]);
										if($impQty>=$prange4[0]) {
											$arrPrice[$priceLoc][$priceType] = $row_impType["Option4Price"];
										} else {
											$arrPrice[$priceLoc][$priceType] = $row_impType["DefaultPrice"];
										}
										
									}
								
								}
							}
						}
						
						$sql_Typesub = "SELECT DISTINCT Type FROM imprint_images WHERE OptionID = $row_impType[id] ORDER BY Type";
						$result_Typesub = mysql_query($sql_Typesub);
						
						while($row_Typesub = mysql_fetch_array($result_Typesub)) {
							// IGNOR SOCKS IMPRINT - FOR NOW :::::::::::::::::::::::::::::
							if($row_Typesub["Type"] != "Socks") {
								$arrOptions[$row_Typesub["Type"]][] = $row_impType["Type"];
							}
						}
					}
					
					//print_r($arrOptions);
					
					foreach($arrOptions as $key => $value) {
						?>
                        	<input type="hidden" id="design_<?=$key;?>" name="design_<?=$key;?>" value="" />
                        	<div id="designs" class="designs <?=$key;?>">
                        		<div class="desHeader"><?=$key;?> Design</div>
                                	<div id="opt<?=$key;?>" class="options">
                                    	<p class="optHeader">Choose One:</p>
										<input type="hidden" id="<?=$key;?>-None-price" value="0" />
                                    	<input class="<?=$key;?>" name="<?=$key;?>" type="radio" value="None" checked="checked" />None<br/>
                                        <?php
											
											//NEW OPTIONS:::::::::::::::::::::::::::::::::::::::::::::::::::::::
											$i = 0;
											$impOpt  = "";
											$impOpt2 = "";
											
											asort($value);
											foreach($value as $subKey => $subValue) {
												if($key == "Short") {
													if($subValue != "name") {
														?>
															<input type="hidden" id="<?=$key;?>-<?=$subValue;?>-price" value="<?=number_format(floatval($arrPrice[$key][$subValue])*intval($impQty),2,'.',',');?>" />
															<input class="<?=$key;?>" name="<?=$key;?>" type="radio" value="<?=$subValue;?>" /><?=$subValue;?><br/>
														<?php
													}
												} elseif($key == "Front") {
													if($subValue != "name") {
													
														if($subValue == "logo") {
															?>
															<input type="hidden" id="<?=$key;?>-chestlogo-price" value="<?=number_format(floatval($arrPrice[$key][$subValue])*intval($impQty),2,'.',',');?>" />
															<input class="<?=$key;?>" name="<?=$key;?>" type="radio" value="chestlogo" />Chest Logo<br/>
															<?php
														}
														
														if($subValue == "pocket") {
															?>
															<input type="hidden" id="<?=$key;?>-pocketlogo-price" value="<?=number_format(floatval($arrPrice[$key][$subValue])*intval($impQty),2,'.',',');?>" />
															<input class="<?=$key;?>" name="<?=$key;?>" type="radio" value="pocketlogo" />Pocket Logo<br/>
															<?php
														}
														
														if($subValue == "number") {
															?>
															<input type="hidden" id="<?=$key;?>-pocketlogo_number-price" value="<?=number_format((floatval($arrPrice[$key]["logo"])+floatval($arrPrice[$key][$subValue]))*intval($impQty),2,'.',',');?>" />
															<input class="<?=$key;?>" name="<?=$key;?>" type="radio" value="pocketlogo_number" />Pocket Logo with Number<br/>
															<input type="hidden" id="<?=$key;?>-chestlogo_number-price" value="<?=number_format((floatval($arrPrice[$key]["logo"])+floatval($arrPrice[$key][$subValue]))*intval($impQty),2,'.',',');?>" />
															<input class="<?=$key;?>" name="<?=$key;?>" type="radio" value="chestlogo_number" />Chest Logo with Number<br/>
															<?php
														}
														
													}
												} elseif($key == "Back") {
													if($subValue != "logo") {
														if($i == 1 && $subValue == "number") {
															?>
																<input type="hidden" id="<?=$key;?>-name_<?=$subValue;?>-price" value="<?=number_format((floatval($arrPrice[$key]["name"])+floatval($arrPrice[$key][$subValue]))*intval($impQty),2,'.',',');?>" />
																<input class="<?=$key;?>" name="<?=$key;?>" type="radio" value="name_<?=$subValue;?>" />Name\Sponsor & Number<br/>
															<?php
														}
														if($subValue == "name") {
															?>
															<input type="hidden" id="<?=$key;?>-<?=$subValue;?>-price" value="<?=number_format(floatval($arrPrice[$key][$subValue])*intval($impQty),2,'.',',');?>" />
															<input class="<?=$key;?>" name="<?=$key;?>" type="radio" value="<?=$subValue;?>" />Name or Sponsor<br/>
															<?php
														} else {
															?>
															<input type="hidden" id="<?=$key;?>-<?=$subValue;?>-price" value="<?=number_format(floatval($arrPrice[$key][$subValue])*intval($impQty),2,'.',',');?>" />
															<input class="<?=$key;?>" name="<?=$key;?>" type="radio" value="<?=$subValue;?>" /><?=$subValue;?><br/>
															<?php
														}
														$i++;
													}
												}
											}
											//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
										?>
                                    </div>
                                    <div id="<?=$key;?>_options" class="designOptions"></div>
                                	<div id="img<?=$key;?>" class="images">
                                    	<img class="<?=$key;?>_main" id="<?=$key;?>_main" style="display: none;" src="images/imprint/number.png" />
                                        <?php
											if($key == 'Front') {
												?>
                                                <img class="<?=$key;?>_pocket" id="<?=$key;?>_pocket" style="display: none;" src="images/imprint/number.png" />
                                                <?php	
											} else if($key == 'Back') {
												?>
                                                <img class="<?=$key;?>_name" id="<?=$key;?>_name" style="display: none;" src="images/imprint/name_back.png" />
                                                <?php
											}
										?>
                                    	<img class="outline" src="images/imprint/<?=strtolower($key);?>.png" />
                               		</div>
                                <div class="desPrice"> 
									<div class="pricechart" id="<?=$key;?>_pricechart"></div>
									<div class="pricechart" id="<?=$key;?>_pricechart2"></div>
									<input type="hidden" id="<?=$key;?>_priceval" name="<?=$key;?>_priceval" val="" />
									<?=$key;?> Design: $<p id="<?=$key;?>_price">0.00</p>
								</div>
                        	</div>
                        <?php
					}
					
				?>
                <div class="clear"></div>
                <input type="submit" class="continue" id="btnUpdateImprint" name="btnUpdateImprint" value="Update Imprint" />
                <input type="button" class="continue" id="btnCancel" name="btnCancel" value="Cancel"/>
			</div>
		</form>
	</div>
	<div style="clear:both;height:20px;"></div>	
		  <!-- Footer Starts from here -->
		  <div class="footer">
		    <div class="foot_box">
			<?php include("includes/footer.php"); ?>
		    </div>
		  </div>
		  <!-- Footer Div ends here --> 
	</div>		
	</body>
</html>