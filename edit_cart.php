<?php
session_start();
if (isset($_GET["id"]) && intval($_GET["id"])) {
	header("location: cart.php");
}
require 'cpadmin/includes/db.php';
$sqlwhere = '';
if ($_SESSION["email"] == '') {
	$sqlwhere = "SessionID='".session_id()."'";
} else {
	$sqlwhere = "(EmailAddress='$_SESSION[email]' OR SessionID='".session_id()."') ";
}
echo $sqlwhere; exit;
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?=$MetaTitle;?></title>
<meta name="keywords" content="<?=$MetaTag;?>" /> 
<meta name="description" content="<?=$MetaDescription;?>" />

<link rel="stylesheet" href="css/css_styles.css" type="text/css" />
<link rel="stylesheet" href="jqtransformplugin/jqtransform.css" type="text/css"  media="all" />
<style>
.continueImprint{
	background:url("./images/continue.png") repeat scroll 0 0 transparent;
	width:155px;
	height:32px;
	margin-top: 10px !important;
	display:none;
	
}
.container {
  padding-bottom:30px;
}
</style>
<script type="text/javascript" src="js/jquery-1.4.4.min.js"></script>
<script type="text/javascript" src="jqtransformplugin/jquery.jqtransform.js"></script>
<script language="javascript" type="text/javascript">
		var opts = new Array();
		var optsname = new Array();
		var configured = -1;
		
		$(function(){
			$("#addCart2").click(function() {
              $("#addCart").trigger('click');
	    	});
			$("#continueImprint2").click(function() {
              $("#continueImprint").trigger('click');
	    	});
	    	
			$('form').jqTransform({imgPath:'jqtransformplugin/img/'});
			
			$(".coloropt").click(function() {
				$("#mainimg").hide();
				$("#mainimg").attr("src", $(this).attr("src"));
				$("#mainimg").fadeIn("slow");
			});
			
			 $('a[name="viewmore"]').click(function(e) {
 
					  e.preventDefault();         

					  var id = $(this).attr('href');               
					  var maskHeight = $(document).height();         
					  var maskWidth = $(window).width();               

					  $('#mask').css({'width':maskWidth,'height':maskHeight});                   
					  $('#mask').fadeIn(500);             
					  $('#mask').fadeTo("slow",0.8);                 

					  var winH = $(window).height();         
					  var winW = $(window).width();                         

					  $(id).css('top',  winH/2-$(id).height()/2);         
					  $(id).css('left', winW/2-$(id).width()/2);
					  $(id).fadeIn(2000);
			});
			
			$('.window .close').click(function (e) {         
				e.preventDefault();         
				$('#mask, .window').hide();     
			});                
			
			$('#mask').click(function () {         

			$(this).hide();         
				$('.window').hide();     
			});
			$('.thumbs').click(function() {
				$("#mainMore").hide();
				$("#mainMore").attr("src", $(this).attr("src"));
				$("#mainMore").fadeIn("slow");
			});
			
			$("#continueImprint").click(function(){
				if (configured==-1){ 
				    window.location.hash = "details";
					configured = 1;
				}else{
					$.post("includes/inc_details2.php", {
							"type":"initImprint",
							"id":"<?=$_GET['id'];?>",
							"productname":$("#productname").val(),
							"producttype":"bundle",
							"gender":$("#gender :selected").text(),
							"gendersku":$("#gender").val(),
							"colorsku":$("#color").val(),
							"size":$("#size").val(),
							"qty":$("#qty").val()
						 }, 
						function(data) {
							<?php if($ProductType != "Bundle"){?>
								window.location.href = "imprintproduct.php?id=<?php echo $_GET['id'];?>";
							<?php }else {?>
			         			window.location.href = "imprintproductBundle.php?id=<?php echo $_GET['id'];?>";
					 		<?php }?>
						});
							
				}
			});

			$("#addCart").click(function() {
										
			
				<?php
					if($prodid == 'VIP') {
					?>
						$("#divNote").html('<img src="images/smallloader.gif" />');
						$.post("includes/inc_details2.php", {
							"type":"VIP"}, function(data) {
								$("#divNote").html(data);
						});
					<?php
					} elseif($ProductType == "Bundle") {
						?>
						if(configured==-1){ 
										 window.location.hash = "details";
										 configured = 1;
						}else{
							var optsval = new Array();
							for(var i=0; i<opts.length; i++) {
								if($("#"+opts[i]).val() == '') {
									alert("Please select an option for bundle items");
									return false;
								} else {
									optsval[i] = $("#"+opts[i]).val();
								}
							}

							$("#divNote").html('<img src="images/smallloader.gif" />');
							$.post("includes/inc_details2.php", {
									"type":"addCart",
									"id":"<?=$_GET['id'];?>",
									"productname":$("#productname").val(),
									"producttype":"bundle",
									"gender":$("#gender :selected").text(),
									"gendersku":$("#gender").val(),
									"qty":$("#qty").val(),
									"bitems": opts,
									"bvals": optsval}, 
										function(data) {
											$("#divNote").html(data);
											window.location.href = "cart.php";
							});
						}//else
							
						<?php
					} else {
				?>
						
						for(var i=0; i<opts.length; i++) {
							
							if(opts[i] == "gender") {
								if($("#"+opts[i]+" :selected").text().substring(0,6) == "Select ") {
									alert("Please select a "+opts[i]);
									return false;
								}
							} else if($("#"+opts[i]).val() == '') {
								alert("Please select a "+opts[i]);
								return false;
							}
						}
						
						$("#divNote").html('<img src="images/smallloader.gif" />');
						$.post("includes/inc_details2.php", {"type":"chkInv", "id":"<?=$_GET['id'];?>", "size":$("#size").val(),"color":$("#color").val()}, function(data){
							if(parseFloat(data)<parseFloat($("#qty").val())) {
								alert('Available inventory is: '+data);
								$("#divNote").html('');
								return false;
							} else {
							//////////////////////	
								$.post("includes/inc_details.php", {
									"type":"addCart", 
									"id":"<?=$_GET['id'];?>",
									"productname":$("#productname").val(),
									"gender":$("#gender :selected").text(),
									"gendersku":$("#gender").val(),
									"size":$("#size").val(),
									"color":$("#color").val(),
								<?php if(isset($_GET["t"])){?>
								  "free":"true",
								  "psid":<?=$_GET['pid'];?>,
									 <?php }?>
									"qty":$("#qty").val()}, function(data) {
										$("#divNote").html(data);
								});
							//////////////////////
							}
						});
						
						
				<?php
					}
				?>
			});
			
			$("#qty").blur(function(){
				$("#bundleitems").html('<img src="images/loader.gif" />');
				$("#bundleitems").load("includes/inc_details2.php", {
					"type":"bundleitems", 
					"prodid":"<?=$prodid;?>",
					 "qty":$("#qty").val(),
					 "gender":$("#gender").val()
					 },
				function(){
						$('form').jqTransform({imgPath:'jqtransformplugin/img/'});	
				});
			});
			
			$("#qty").keydown(function(e) {
           	 	var key = e.charCode || e.keyCode || 0;
            	// allow backspace, tab, delete, arrows, numbers and keypad numbers ONLY
            	return (
	                key == 8 || 
	                key == 9 ||
	                key == 46 ||
	                (key >= 37 && key <= 40) ||
	                (key >= 48 && key <= 57) ||
	                (key >= 96 && key <= 105));
        	});

			
		});

		function cngImage(csku) {
			if(csku=='') { return; }
			$("#mainimg").hide();
			$("#mainimg").attr("src", $("#"+csku).attr("src"));
			$("#mainimg").fadeIn("slow");
		}

		function setSizes(gender, div, size, pid, colorSKU) {
			$("#"+div).html('<img src="images/loader.gif" />');
			$("#"+div).load("includes/inc_details.php", {"type":"setsizes", "id":pid, "gender":gender, "size":size, "colorsku":colorSKU});
		}
		function setDefaultCat1(filter) {
			var id = new String(filter.id);
			var cid = id.substring(id.indexOf(":")+1,id.length);
			var catName = id.substring(0,id.indexOf(":"))+".html";


			$.post("./includes/inc_browser.php", {"type":"initCategId", "idCat":cid}, function(data) {

				if(window.location.pathname=="/development")
					window.location.pathname += catName;
				else{
					var pathname = new String(window.location.pathname);
					pathname=pathname.substring(0,pathname.lastIndexOf("/")+1);
					window.location.pathname = pathname+catName;
				}

			});


			return false;

		}
		
		function initBundleColor(el){
			var id = new String(el.id);
			id = id.substring(6,id.length);
			var color = el.value;
			$.post("./includes/inc_details2.php", {"type":"initColor", "idBundle":id,"color":color}, function(data) {

				

			}); 
		}
		
		function initGender(el){
			$("#bundleitems").html('<img src="images/loader.gif" />');
				$("#bundleitems").load("includes/inc_details2.php", {
					"type":"bundleitems", 
					"prodid":"<?=$prodid;?>", 
					"qty":$("#qty").val(),
					"gender":$("#gender").val()
					},
					function(){
						$('form').jqTransform({imgPath:'jqtransformplugin/img/'});	
				});
		}
		
		function setSizeBundle(el){
			var id = new String(el.id);
			idB = id.substring(id.indexOf("size:")+5,id.length);
			var set =  id.substring(3,id.indexOf(":size")); 
			var size = el.value;
			//alert("hello "+size+'-'+set+'-'+id+'-'+idB);
			$.post("./includes/inc_details2.php", {"type":"initSizeB", "idBundle":idB,"size":size,"set":set}, function(data) {

				

			});
		}
		
		function imprintCheckValid(el){
			if(el.checked){
				$("#continueImprint").hide();
				$("#continueImprint2").hide();
				$("#addCart").show();
				$("#addCart2").show();
			}else{
				
				$("#continueImprint").show();
				$("#continueImprint2").show();
				$("#addCart").hide();
				$("#addCart2").hide();
			}
		}
		
		function gotoImprint(){
			<?php if($ProductType != "Bundle"){?>
			window.location.href = "imprintproduct.php?id=<?php echo $_GET['id'];?>";
			<?php }else {?>
			window.location.href = "imprintproductBundle.php?id=<?php echo $_GET['id'];?>";
			<?php }?>
		}
	</script>
	<link rel="stylesheet" href="./cpadmin/assets/parsesample.css" type="text/css"  media="all" />
	<style>
		.discount_pricing ul li {
			list-style:none;
		}
	</style>
</head>

<body>
<!-- More View -->
<div id="moreview">
   <!-- #popupwindow -->
   <div id="moreimages" class="window" style="text-align: center;">
   			<a href="#" class="close" style="float: right;">Close</a>
            <?php
				$sql_moreimg = "SELECT Image FROM product_images WHERE ProductID=$prodid ORDER BY SortOrder";
				$result_moreimg = mysql_query($sql_moreimg);
				
				$flag=0;
				while($row_moreimg = mysql_fetch_array($result_moreimg)) {
					if($fistimg == '' && $flag==0) {
						$firstimg = '<img id="mainMore" src="images/productView/'.$row_moreimg["Image"].'" style="height: 280px; padding: 10px;" />';
						$flag=1;
					}
					$imglist .= '<td><img src="images/productView/'.$row_moreimg["Image"].'" class="thumbs" /></td>';
				}
				echo $firstimg;
			?>
            <div style="width: 500px; height: 120px; text-align: center; overflow: auto; text-align:left;">
		<table>
			<tr>
            			<?=$imglist;?>
			</tr>
		</table>
            </div>
   </div>
   <div id="mask"></div> 
</div> 


<!-- End More View -->

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
        <?php include('includes/topnav.php'); ?>
        <div class="clear"></div>
      </div>
      <div class="navi_R"></div>
      <div class="clear"></div>
    </div>
    
    
    <?php 
	
	if (!empty($row_details) && count($row_details)): ?>
    <div class="detailed">
   
    <?php
		if($prodid != 'VIP') {
			$sql_mainimg = "SELECT p.ColorImage, p.AltText FROM product_options p, product_browser b WHERE p.ProductID=b.ProductID AND p.ColorImage=b.Image AND p.ProductID=$prodid LIMIT 1";
			$result_mainimg = mysql_query($sql_mainimg);
			$row_mainimg = mysql_fetch_assoc($result_mainimg);
			$num_mainimg = mysql_num_rows($result_mainimg);
			
			if($num_mainimg>0) {
				$mainimg = $row_mainimg["ColorImage"];
				$mainalt = $row_mainimg["AltText"];
			} else {
				$sql_mainimg = "SELECT ColorImage, AltText FROM product_options WHERE ProductID=$prodid LIMIT 1";
				$result_mainimg = mysql_query($sql_mainimg);
				$row_mainimg = mysql_fetch_assoc($result_mainimg);
			}
			
			$Image = $row_mainimg["ColorImage"];
		}
	?>
      <div class="detailed_L"> 
      	<img id="mainimg" style="width: 228px;" src="images/productImages/<?=$Image;?>" alt="<?=$row_mainimg["AltText"];?>" />
       <?php
	   		
			if($prodid != 'VIP') {
				$sql_more = "SELECT id FROM product_images WHERE ProductID=$prodid";
				$result_more = mysql_query($sql_more);
				$num_more = mysql_num_rows($result_more);
		
				if($num_more>0){
					echo '<a href="#moreimages" name="viewmore">For More Views Click Here</a>';
				}
			}
	   ?>
       </div>
      <div class="detailed_R">
          	<?php
			if($prodid == 'VIP') {
				?>
                <h1><?=$Name;?><br/></h1>
                <div class="clear"></div>
                <h2><?=$Description;?></h2>
                <div class="clear"></div>

                <?php
					if($isvip=='yes') {
						$sql_vipinfo = "SELECT VIPNum, VIPDate FROM customers WHERE EmailAddress='$_SESSION[email]' LIMIT 1";
						$result_vipinfo = mysql_query($sql_vipinfo);
						$row_vipinfo = mysql_fetch_assoc($result_vipinfo);
						
						echo '<h2 style="color: #ff0000">You are already a memeber. <br/>Your VIP Number is: <strong>'.$row_vipinfo["VIPNum"].'</strong>  <br/>You membership will expire on: <strong>';
	
						$date = strtotime($row_vip["VIPDate"]);
						$date = mktime(0, 0, 0, date("m", $date), date("d", $date), date("Y", $date)+1);
						echo date('m/d/Y', $date);
						
						echo '</strong></h2>';
					}
				?>
                <div class="clear"></div>
                <div class="VIP_members">
                <h2 style="color: #FF0000">Price: $<?=number_format($Price,2);?></h2>
                </div>
                <?php
			} else {
		?>
      
                     <h1 class="heading"><?=$ProductDetailName;?><input type="hidden" id="productname" name="productname" value="<?=$ProductDetailName;?>"/></h1><br />
                      <br />
                    </h1>
                    <div class="clear"></div>
                    <h1><span style="color: #ff0000;">Manufacturer #:&nbsp;</span><?=$ManufacturerNum;?></h1><h1 style="float: right;"><span style="color: #ff0000;">SoccerOne #:&nbsp;</span><?=$RootSKU;?></h1>
                    <div class="clear"></div>
                    <h2><?=$ShortDescription;?></h2>
                    <div class="clear"></div>
                    
                    <table cellpadding="0" cellspacing="0" width="90%">
                    	<tr>
                        	<td style="text-align: center;">
                            
                                <div class="VIP_members">
                                	<?php if($isvip=='no' && $isSpecial != "True") {?>
                                            <img src="images/S_soccer_card.png" alt="" />
                                            <h2>VIP MEMBERS SAVE EVEN MORE</h2>
                                            <h3>Not a VIP member? <a href="details.php?id=VIP">Join Today</a></h3>
									  <?php
									  		} else {
												echo "<h2> </h2>";
											}
										
                                        if($isSpecial == "True") {
                                            echo "<h2 style=\"color: #FF0000\">$SpecialCategory: $".number_format($SpecialPrice,2)."</h2>";
                                        }
                                      ?>
                                </div>
                    		</td>
                    	<tr>
                    </table>
                <?php
                    if($isSpecial != "True") {
                	?>
                	<table cellpadding="0" cellspacing="0" style="width: 100%;">
                    	<tr>
                        	<td style="text-align: center;">
                                <div class="discount_pricing" style="position: relative; left: -15px; z-index: -1;">
                                    <h4>VIP Member Discount Pricing</h4>
                                    <div class="clear"></div>
                                          <ul>
                                          	<?php
                                            
												$sql_pricing = "SELECT * FROM product_pricing WHERE ProductID=$prodid";
												$result_pricing = mysql_query($sql_pricing);
												$row_pricing = mysql_fetch_assoc($result_pricing);
                                            ?>
                                            <li style="background:none;">&nbsp;</li>
											<li class="non_members">MSRP/Value</li>
											<li class="non_members">Non Member</li>
                                            <li class="vip_C2"><?=$row_pricing["Option1"];?></li>
                                            <li class="vip_C3"><?=$row_pricing["Option2"];?></li>
                                            <li class="vip_C4"><?=$row_pricing["Option3"];?></li>
                                            <li class="yellow" style="margin-right:0px;"><?=$row_pricing["Option4"];?></li>
                                           
                                            <?php
                                                
                                                $sql_pricing = "SELECT * FROM product_pricing WHERE ProductID=$prodid ORDER BY id";
                                                $result_pricing = mysql_query($sql_pricing);
                                                
                                                while($row_pricing = mysql_fetch_array($result_pricing)) {
													if($row_pricing["Gender"] == '') {
														$genclass= ' style="background-color: #fff;"';
													} else {
														$genclass = 'class="red"';
													}
                                                    ?>
                                                        <li <?=$genclass;?>><?=$row_pricing["Gender"];?></li>
														<li class="non_members"><s>$<?=number_format($row_pricing["MSRP"],2);?></s></li>
    	                                                 <li class="non_members">$<?=number_format($row_pricing["NonMember"],2);?></li>
                                                        <li class="vip_C2">$<?=number_format($row_pricing["Option1Price"],2);?></li>
                                                        <li class="vip_C3">$<?=number_format($row_pricing["Option2Price"],2);?></li>
                                                        <li class="vip_C4">$<?=number_format($row_pricing["Option3Price"],2);?></li>
                                                        <li class="yellow" style="margin-right:0px;">$<?=number_format($row_pricing["Option4Price"],2);?></li>
                                                        
                                                    <?php
                                                }
                                            ?>
                                     
                                          </ul>
                                	</div>
                    			</td>
                        </tr>
                    </table>
            
                	<?php
					}
		}					
	?>
      </div>
      <div class="detailed_B"> 
		<p>Click Below to View Color</p><?php
			if($prodid != 'VIP') {
				
					if($ProductType == "Bundle") {
						$sql_colorimg = "SELECT Image FROM product_images WHERE ProductID=$prodid ORDER BY SortOrder";
						$result_colorimg = mysql_query($sql_colorimg);
						
						if($ProductType=='Bundle') {
							$imgPath = 'images/productView/';
						} else {
							$imgPath = 'images/productImages/';
						}
						
						while($row_colorimg = mysql_fetch_array($result_colorimg)) {
							if(file_exists($imgPath.$row_colorimg["Image"])) {
								echo '<img class="coloropt" style="width: 66px; border: 1px solid #b4b4b4; margin-right: 2px; cursor: pointer;" id="'.$row_colorimg["ColorSKU"].'" src="'.$imgPath.$row_colorimg["Image"].'" />';
							}
						}
					
					} else {
						$sql_colorimg = "SELECT DISTINCT ColorImage, ColorSKU FROM product_options WHERE ProductID=$prodid ORDER BY ImageSort, ColorImage";
						$result_colorimg = mysql_query($sql_colorimg);
						
						if($ProductType=='Bundle') {
							$imgPath = 'images/productView/';
						} else {
							$imgPath = 'images/productImages/';
						}
						
						while($row_colorimg = mysql_fetch_array($result_colorimg)) {
							if(file_exists($imgPath.$row_colorimg["ColorImage"])) {
								echo '<img class="coloropt" style="width: 66px; border: 1px solid #b4b4b4; margin-right: 2px; cursor: pointer;" id="'.$row_colorimg["ColorSKU"].'" src="'.$imgPath.$row_colorimg["ColorImage"].'" />';
							}
						}
					}
				?>
			
				<h2>Product Description</h2>
				<!-- <p><?=$ProductDescription;?></p> -->
                <div id="desc" class="desc">
                	<?=$ProductDescription;?>
                </div>
                <br />
                  <!-- AddThis Button BEGIN -->
<div class="addthis_toolbox addthis_default_style addthis_32x32_style">
<a class="addthis_button_preferred_1"></a>
<a class="addthis_button_preferred_2"></a>
<a class="addthis_button_preferred_3"></a>
<a class="addthis_button_compact"></a>
<a class="addthis_counter addthis_bubble_style"></a>
</div>
<script type="text/javascript">var addthis_config = {"data_track_addressbar":true};</script>
<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-5186b4f029e7d8ed"></script>
<!-- AddThis Button END -->

                
			<?php } ?>        
      </div>
    </div>

<!-- ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: -->
 	<?php

		if($ProductType == "Bundle") {
			$rightbarheight = 'style="height: 475px !important;"';
		} else {
			$rightbarheight = '';
		}
		
	?>

    <div class="browser_color detailed_options" <?=$rightbarheight;?> >
      <h1>Order Now</h1>
      <form action="" method="post">
      <?php
	  		if($prodid != 'VIP') {		
				$sql_genderprice = "SELECT ShowGender FROM product_pricing WHERE ProductID=$prodid GROUP BY ShowGender";
				$result_genderprice = mysql_query($sql_genderprice);
				$row_genderprice = mysql_fetch_assoc($result_genderprice);
				
				if($row_genderprice["ShowGender"] != 'None') {
					?>
                    <br/>
					<select id="gender" name="gender" onChange="initGender(this)">
						<option value=''>Select <?=$row_genderprice["ShowGender"];?></option>
					<?php
						$sql_gender = "SELECT Gender, GenderSKU FROM product_pricing WHERE ProductID=$prodid ORDER BY Gender";
						$result_gender = mysql_query($sql_gender);
						
						while($row_gender = mysql_fetch_array($result_gender)) {
							echo '<option value="'.$row_gender["Gender"].'">'.$row_gender["Gender"].'</option>';
						}
					?>
				  </select>
				
                  	<?php
						// if bundle product
						if($ProductType == "Bundle") {
							$sql_bundle = "SELECT Items FROM product_bundles WHERE ProductID='$prodid' ORDER BY SortOrder ASC";
							$_SESSION["bundleProductId"]=$prodid;
							$_SESSION["bundleItems"]= array();
                            $_SESSION["tabsTitle"] = array();
							$result_bundle = mysql_query($sql_bundle);
							$bnum = 1;
							$i=0;
							while($row_bundle=mysql_fetch_array($result_bundle)) {
								$sql_bimage = "SELECT Image FROM product_browser WHERE ProductID=$row_bundle[Items] LIMIT 1";
								
								$_SESSION["bundleItems"][$row_bundle["Items"]] = array();
								

								$result_bimage = mysql_query($sql_bimage);

								$row_bimage = mysql_fetch_assoc($result_bimage);

								$sql_bitem = "SELECT p.RootSKU, p.ProductDetailName, d.ShortDescription FROM products p, product_descriptions d WHERE p.id=d.ProductID AND p.id=$row_bundle[Items] LIMIT 1";

								$result_bitem = mysql_query($sql_bitem);
		
								$row_bitem = mysql_fetch_assoc($result_bitem);
								
								$sql_category_product = "select Category from category where id=(select CategoryID from category_items where ProductID = ".$row_bundle['Items']." LIMIT 1)  ";
								
								
								$resultCategory = mysql_query($sql_category_product);
								$row_categ = mysql_fetch_assoc($resultCategory);
								echo "<select id='bundle".$row_bundle[Items]."' onchange='initBundleColor(this)'>";
								$categoryAttribute = "";
								if(strpos($row_categ["Category"],"Jersey")){
									$categoryAttribute = "Jersey";
								}
								if(strpos($row_categ["Category"],"Short")){
									$categoryAttribute = "Short";
								}
								if(strpos($row_categ["Category"],"Sock")){
									$categoryAttribute = "Socks";
								}
								$_SESSION["tabsTitle"][]=$categoryAttribute;
								echo "<option>".$categoryAttribute."&nbsp; Color<option>";
									$sql_color = "SELECT DISTINCT Color, ColorSKU FROM product_options WHERE ProductID=$row_bundle[Items] AND Inventory>0 ORDER BY Color";

														$result_color = mysql_query($sql_color);

														$row_color = mysql_fetch_assoc($result_color);

														$num_color = mysql_num_rows($result_color);

														

														if($num_color>0 && $row_color["Color"] != '') {
															$result_citems = mysql_query($sql_color);

																		while($row_citems = mysql_fetch_array($result_citems)) {

																			echo "<option value=\"$row_citems[ColorSKU]\">$row_citems[Color]</options>";

																		}

														}

								echo "</select>";
							}
						
					
					} // end bundle test ?> 
                 
                
              	<?php
				}
				
              		$sql_color = "SELECT DISTINCT Color, ColorSKU FROM product_options WHERE ProductID=$prodid AND Inventory>0 ORDER BY Color";
                	$result_color = mysql_query($sql_color);
					$row_color1 = mysql_fetch_assoc($result_color);
					$num_color = mysql_num_rows($result_color);
					
					if($num_color>0 && $row_color1["Color"] != '') {
				?>
                <div style="height: 10px;"></div>
                <select id="color" name="color" onChange="cngImage(this.value); setSizes($('#gender :selected').text(), 'divSizeG', 'size', '<?=$prodid;?>', this.value);">
                <option value="">Select Color</option>
                <?php
        			$sql_color = "SELECT DISTINCT Color, ColorSKU FROM product_options WHERE ProductID=$prodid AND Inventory>0 ORDER BY Color";
                	$result_color = mysql_query($sql_color);
				
                    while($row_color = mysql_fetch_array($result_color)) {
                        echo '<option value="'.$row_color["ColorSKU"].'">'.$row_color["Color"].'</option>';
                    }
                ?>
              </select>
              <script>opts.push("color");</script>
              <?php
			  		}
					
					//$sql_size = "SELECT DISTINCT Size, SizeSKU FROM product_options WHERE ProductID=$prodid AND Inventory>0 ORDER BY Size";
					$sql_size = "SELECT DISTINCT Size, SizeSKU FROM product_options WHERE ProductID=$prodid  ORDER BY Position ASC";
                    $result_size = mysql_query($sql_size);
					$row_size1 = mysql_fetch_assoc($result_size);
					$num_size = mysql_num_rows($result_size);
					
					if($num_size>0 && $row_size1["Size"] != '') {
			  ?>
		<div id="divSizeG">
                <select id="size" name="size">
                <option value="">Select Size</option>
                <?php
					//$sql_size = "SELECT DISTINCT Size, SizeSKU FROM product_options WHERE ProductID=$prodid AND Inventory>0 ORDER BY Size";
                 /*  $sql_size = "Select 
									Distinct   product_options.Size,  product_options.SizeSKU,   sizes.Rank
							From
									  product_options Inner Join   sizes On product_options.Size = sizes.Size 
							Where
									  product_options.ProductID = $prodid And
									  product_options.Inventory > 0
							Order By
									  sizes.Rank"; 
									  echo $sql_size;*/
                   $result_size = mysql_query($sql_size);
                    while($row_size = mysql_fetch_array($result_size)) {
                        echo '<option value="'.$row_size["SizeSKU"].'">'.$row_size["Size"].'</option>';
                    }
                ?>
              </select>
	      </div>
              <script>opts.push("size");</script>
      		<?php
			}
	  	}
	  ?>
      
       			
                    
      <div style="clear:both"></div>              
      <div class="add_cart" style="clear:both">
      <!-- <a href="#">Add items to cart</a> -->
      <?php
			if($prodid == 'VIP') {
				$edit = ' readonly="readonly"';
			} else {
				$edit = '';
			}
		?>
        <label>Quantity</label>
        <style type="text/css">
			.quantity_box .jqTransformInputWrapper {background:none;}
			.quantity_box .jqTransformInputWrapper {background:none;}
			.quantity_box .jqTransformInputInner {background:none;}
			.quantity_box input#qty {width:2.5em !important; padding:5px 7px; background: #fff;}
		</style>
        <div class="quantity_box">
        	<input type="text" <?=$edit;?> value="" id="qty" name="qty" class="quantity" onblur="this.style.border='none'" onfocus="this.style.border='2px solid red'" style="width:2.5em !important; padding:5px 7px; background: #fff;" />
        </div>
        <div class="clear"></div>
	<div id="divNote" class="notes"></div>
	<div class="clear"></div>
    	<?php 
		$showButtons = "false";
		if($Status == 'Disabled') {
			echo '<div style="text-align: center; margin-bottom: 20px;"><label>Product is currently unavailable</label></div>';
		} elseif($isvip=='yes' && $prodid == 'VIP') {
			echo '<div style="text-align: center; margin-bottom: 20px;"><label></label></div>';
		} else { ?>
		
         <?php  if($ID_IMPRINT_CATEGORY!=0 && $ID_IMPRINT_CATEGORY!=-1){ ?>
         <style type="text/css">
		 	span.jqTransformCheckboxWrapper{margin-top: -3px; margin-left:5px;}
		 	a.jqTransformCheckbox {height:15px; width:14px;}
		 </style>
			<div style="background-color: rgb(254, 0, 0); height: 30px; width: 199px; margin-left: -16px; padding-bottom: 10px;">
			<input type="checkbox" name="imprint" onclick="imprintCheckValid(this)" />
				<label  style="color: rgb(255, 255, 255); margin-top: 12px;" >Add Imprint Option</label>
			</div>
			
			<div style="clear:both;height:20px"></div>
			<div style="text-align:center;">							 
				<button type="button" id="addCart" name="addCart" value="" style="margin:0px;" onclick="window.location.hash = 'details';"   class="cart"></button>
			 
			
				<button type="button" id="continueImprint" name="continueimprintCart" class="continueImprint" style="display:none;width: 155px; height: 32px;border:0px;cursor:pointer">
			</button>   
			</div>
			
		
		<?php } elseif($ProductType == "Bundle") {?>
			<div style="text-align:center;">		
        		<button type="button" id="addCart" name="addCart" value="" style="margin:0px;"  onclick="window.location.hash = 'details';"  class="cart"></button>
        	</div>
        <?php } else {?>
			<div style="text-align:center;">		
        		<button type="button" id="addCart" name="addCart" value="" style="margin:0px;"  class="cart"></button>
        	</div>
        <?php } ?>
		
         <?php $showButtons = "true";?>
        <?php } ?>
      </div>
	</form>
	 
		
		
      <div style="clear:both;height:10px"></div>
      <!-- div class="sites"> <a href="http://www.facebook.com/pages/SoccerOne/229707679836" target="_new"><img src="images/fb_icon.png" alt="" /></a> <a href="https://twitter.com/#!/soccerone_" target="_new"><img src="images/twitter.png" alt="" /></a> </div -->
    </div>
	<?php
		//}

		if($ProductType == "Bundle") {
	?>
				
                <div id="bundleitems"></div>	
                <!--<div style="text-align: right; float: right; margin-right: 26px;">-->
                <div style="text-align: left; clear: both; margin-left: 440px;">
                <?php if($showButtons=="true"){?>
                 <button type="button" id="addCart2" name="addCart"  value="" class="cart" style="float:none;"></button>
        <button type="button" id="continueImprint2" name="continueimprintCart" class="continueImprint" style="display:none;width: 155px; height: 32px;border:0px;cursor:pointer"></button>
        <?php }?>
                </div>
                <script>
					$("#bundleitems").load("includes/inc_details2.php", {
						"type":"bundleitems", 
						"qty":$("#qty").val(), 
						"prodid":"<?=$prodid;?>"
						}, 
						function(){
								$('form').jqTransform({imgPath:'jqtransformplugin/img/'});
					});
				</script>
               
            <div class="clear"></div>
            
 <a name="details"></a>
	<?php
		}
	?>
<!-- ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: -->


    
    <?php else: ?>
    	<div class="detailed">
			<br />
			<h1><span style="color: rgb(255, 0, 0);">This product is not available in current system.</span></h1>
			<br />
		</div>
    <?php endif; ?>
    <div class="clear"></div>
  </div>
  <!-- Container Div ends here --> 
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