<?php
/**
 * Main product details display screen
 * 
 * Updated: 21 July 2016
 * By: Richard Tuttle
 *
 * option1_res = 1 ... simple
 * option1_res = 2 ... bundled
*/

session_start();
unset($_SESSION["bundleItems"]["items"]);
unset($_SESSION["bundleItems"]["itemcolor"]);
unset($_SESSION["bundleItems"]["itemgender"]);
unset($_SESSION["singleitems"]["items"]);
unset($_SESSION["singleitems"]["itemcolor"]);
unset($_SESSION["singleitems"]["itemgender"]);
unset($_SESSION["imprintConfig"]);
unset($_SESSION["singleitems"]);
unset($_SESSION["bundleItems"]);
$_SESSION["imprintConfig"] = array();
$_SESSION["bundleItems"] = array();
$_SESSION["singleitems"] = array();
$_SESSION["singleitems"]["items"] = array();
$_SESSION["singleitems"]["itemcolor"] = array();
$_SESSION["singleitems"]["itemgender"] = array();
$_SESSION["bundleItems"]["items"] = array();
$_SESSION["bundleItems"]["itemcolor"] = array();
$_SESSION["bundleItems"]["itemgender"] = array();
	
if ($_GET["id"] == '') {
	header("location: browser.php");
}

require_once 'cpadmin/includes/db.php';
session_start();
	
try {
	$prodid = mysql_real_escape_string($_GET["id"]);
	$sql_prod_option = "select * from products WHERE id='$prodid' LIMIT 1";
	$query_res = @mysql_query($sql_prod_option) or die("Product Option Error: " . mysql_error());
	$row_res = @mysql_fetch_assoc($query_res); 
	$option1_res = $row_res['option_seting_1'];
	if($prodid == 'VIP') {
		$sql_details = "SELECT * FROM vip LIMIT 1";
		$result_details = mysql_query($sql_details);
		$row_details = mysql_fetch_assoc($result_details);
		if (mysql_num_rows($result_details)) {
			foreach($row_details as $key=>$value) {
				$$key = stripslashes($value);
			}
		}
	} else {
		$sql_details = "SELECT * FROM products WHERE id='$prodid' LIMIT 1";
		$result_details = mysql_query($sql_details) or die("Product Retrieval error: " . mysql_error());
		$row_details = mysql_fetch_assoc($result_details);
		if (mysql_num_rows($result_details)) {
			foreach($row_details as $key=>$value) {
				$$key = stripslashes($value);
			}
		}
		$sql_des = "SELECT * FROM product_descriptions WHERE ProductID='$prodid' LIMIT 1";
		$result_des = mysql_query($sql_des) or die("Product Description error: " . mysql_error());
		$row_des = mysql_fetch_assoc($result_des);
		if (mysql_num_rows($result_des)) {
			foreach($row_des as $key=>$value) {
				$$key = stripslashes($value);
			}
		}
		
	}
	
	if(!isset($_SESSION["email"]) || $_SESSION["email"] == '') {
		$isvip = 'no';
	} else {
		$sql_chkvip = "SELECT Status FROM customers WHERE EmailAddress='$_SESSION[email]' AND current_date<DATE_ADD(VIPDATE, INTERVAL 1 YEAR) LIMIT 1";
		$result_chkvip = mysql_query($sql_chkvip) or die("Customer Check Error: " . mysql_error());
		$row_chkvip = mysql_fetch_assoc($result_chkvip);
		if(mysql_num_rows($result_chkvip) && $row_chkvip["Status"] == "VIP") {
			$isvip = 'yes';
		} else {
			$isvip = 'no';
		}
	}
	
	if($isvip == 'no') {
		if($_SESSION["email"] == '') {
			$sqlwhere = "SessionID='".session_id()."'";
		} else {
			$sqlwhere = "(EmailAddress='$_SESSION[email]' OR SessionID='".session_id()."') ";
		}
		
		// check to see if customer has added VIP memebership to their cart
		$sql_chkcart = "SELECT * FROM shopping_cart WHERE ProductID='VIP' AND $sqlwhere";
		$result_chkcart = mysql_query($sql_chkcart);
		$num_chkcart = mysql_num_rows($result_chkcart);
		if($num_chkcart > 0) {
			$isvip = 'yes';
		}
	}
} catch(Exception $ex) {
	$row_details = array();
}
	require_once("includes/mainHeader.php");
?>
<meta property="og:image" content="images/productImages/<?=$Image;?>">
<style>
.continueImprint {
	background: url("./images/continue.png") repeat scroll 0 0 transparent;
	width: 155px;
	height: 32px;
	margin-top: 10px !important;
	display: none;
	
}
.container {
  padding-bottom: 30px;
}

.quantity_box input#qty2 {
	width: 2.5em !important;
}
</style>
<script language="javascript" type="text/javascript">
var opts = new Array();
var optsname = new Array();
var configured = -1;
		
$(function() {
	$('form').not('#mobileSearch').jqTransform({imgPath:'jqtransformplugin/img/'});
			
	$(".coloropt").click(function() {
		$("#mainimg").hide();
		$("#mainimg").attr("src", $(this).attr("src"));
		$("#mainimg").fadeIn("slow");
	});
	
	$(".coloropt").change(function() {
		$("#mainimg").hide();
		$("#mainimg").attr("src", $(this).attr("src"));
		$("#mainimg").fadeIn("slow");
	});
			
	$('a[name="viewmore"]').click(function(e) {
		e.preventDefault();         
		var id = $(this).attr('href');               
		var maskHeight = $(document).height();         
		var maskWidth = $(window).width();               
		$('#mask').css({
			'width':maskWidth,
			'height':maskHeight
		});                   
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
			
	$("#continueImprint").click(function() {
		imprintPagecall(1);
	});
			
	$("#continueImprint2_asad").click(function() {
		$.post("includes/inc_details2.php", {
			"type":"initImprint_single",
			"id":"<?=$_GET['id'];?>",
			"productname":$("#productname").val(),
			"producttype":"single",
			"gender":$("#gender :selected").text(),
			"gendersku":$("#gender").val(),
			"colorsku":$("#color").val(),
			"size":$("#size").val(),
			"qty":$("#qty").val()
		}, function(data) {
			<?php 
			if($ProductType != "Bundle"){
			?>
				window.location.href = "imprint.php?id=<?php echo $_GET['id']?>";
			<?php 
			} else {
			?>
	         	window.location.href = "imprint.php?id=<?php echo $_GET['id']?>";
			 <?php 
			 }
			 ?>
		});
	});
	
    $("#addCart").live('click',function() {
        window.location.hash = "details";
    });
    
	$("#addCart2").live('click', function() {
	<?php
		if ($prodid == 'VIP') {
	?>
			$("#divNote").html('<img src="images/smallloader.gif" />');
			$.post("includes/inc_details2.php", {
				"type":"VIP"
			}, function(data) {
				$("#divNote").html(data);
			});
	<?php
		} elseif ($ProductType == "Bundle") {
	?>
			window.location.hash = "details";
			configured = 1;
	<?php
			$cate_name = array();
			$id = mysql_real_escape_string($_GET["id"]);
			$sql_bundle_size = "SELECT Items FROM product_bundles WHERE ProductID='$id' ORDER BY SortOrder ASC";
			$result_bundle_size = mysql_query($sql_bundle_size) or die("Bundle Size error: " . mysql_error());
			$bnum = 1;
			$k = 0;
			while ($row_bundle1 = mysql_fetch_array($result_bundle_size)) {
				$sql_category_product = "select Category from category where id=(select CategoryID from category_items where ProductID=".$row_bundle1['Items']." LIMIT 1)";
				$resultCategory = mysql_query($sql_category_product);
				$row_categ = mysql_fetch_assoc($resultCategory);							
				$categoryAttribute = "";
				if(strpos($row_categ["Category"], "Jersey")) {
					$categoryAttribute = "Jersey";
				}
				if(strpos($row_categ["Category"], "Short")) {
					$categoryAttribute = "Short";
				}
				if(strpos($row_categ["Category"], "Sock")) {
					$categoryAttribute = "Socks";
				}
				if($k == 0) {
					$cate_name[$k] = $categoryAttribute;
				}
				if($k == 1) {
					$cate_name[$k] = $categoryAttribute;
				}
				if($k == 2) {
					$cate_name[$k]=$categoryAttribute;
				}
				$k++;			
			}
	?>		  
			var gender = $("#gender").val();
			var color1 = $("select[name='<?php echo $cate_name[0]."_color";?>']").val();
			var color2 = $("select[name='<?php echo $cate_name[1]."_color";?>']").val();
			var color3 = $("select[name='<?php echo $cate_name[2]."_color";?>']").val();
			var quantity = $("#qty").val();
			if(gender == "") {
				alert("Please Select Range?");
				$("#gender").focus();
				return false;
			} 
			if(color1 == "") {
				alert("Please Select Color of <?php echo ucfirst($cate_name[0]);?>");
				$("select[name='<?php echo $cate_name[0]."_color";?>']").focus();
				return false;
			} 
			if(color3 == "") {
				alert("Please Select Color of <?php echo ucfirst($cate_name[0]);?>");
				$("select[name='<?php echo $cate_name[1]."_color";?>']").focus();
				return false;
			} 
			if(color2 == "") {
				alert("Please Select Color <?php echo ucfirst($cate_name[0]);?>");
				$("select[name='<?php echo $cate_name[2]."_color";?>']").focus();
				return false;
			} 
			if(quantity == "") {
				alert("Please Enter Quantity");
				$("#qty").focus();
				return false;
			}
			var should_be_back = 0;
			$("#bundleitems select[name='<?php echo $cate_name[0];?>']").each(function(index) { 
				if($(this).val() == "") {
					alert("Please Select Your Sizes");
					$(this).focus();
					should_be_back = 1;
					return false;
				}						
			});
						  
			$("#bundleitems select[name='<?php echo $cate_name[1];?>']").each(function(index) { 
				if($(this).val() == "") {
					alert("Please Select Your Sizes");
					$(this).focus();
					should_be_back = 1;
					return false;
				}
			});
						  
			$("#bundleitems select[name='<?php echo  $cate_name[2];?>']").each(function(index) { 
				if($(this).val() == "") {
					alert("Please Select Your Sizes");
					$(this).focus();
					should_be_back = 1;
					return false;
				}
			});
						
			if(should_be_back == 1) {
				return false;
			}
						
			var value1 = "";
			var value2 = "";
			var value3 = "";	
			var optsval = new Array();
			for (var i = 0; i < opts.length; i++) {
				if ($("#" + opts[i]).val() == '') {
					alert("Please Select An Option For Bundle Items");
					return false;
				} else {
					optsval[i] = $("#" + opts[i]).val();
					// alert(opts[i] + " = " + optsval[i]); // testing only
				}
			}
                        
			var repeat_selection = ($("#repeat_selection:checked").attr("checked")==true) ? 1 : 0;
			$("#divNote").html('<img src="images/smallloader.gif" />');
			$.post("includes/inc_details2.php", {
				"type":"addCart",
				"id":"<?=mysql_real_escape_string($_GET["id"]);?>",
				"productname":$("#productname").val(),
				"producttype":"bundle",
				"gender":$("#gender :selected").text(),
				"gendersku":$("#gender").val(),
				"qty":$("#qty").val(),
				"repeat_selection":repeat_selection,
				"bitems": opts,
				"bvals": optsval
			}, function(data) {
				$("#divNote").html(data);
				<?php
				if ($_SESSION["email"] != "") {
				?>
					window.location.href = "cart.php";
				<?php
				} else {
				?>
					window.location.href = 'myaccount.php';
				<?php
				}
				?>
				// window.location.href = "cart.php";
			});		
	<?php
		} else {
			if ($option1_res == "2") {
	?>
				var value_size = "";
				var value_color = "";
	<?php
				$id = mysql_real_escape_string($_GET["id"]);
				$count_colors = 0;
				$sql_color = "SELECT DISTINCT Color, ColorSKU FROM product_options WHERE ProductID=$id AND Inventory>0 ORDER BY Color";
				$result_color = mysql_query($sql_color);
				while($row_color = mysql_fetch_array($result_color)) {
					$count_colors++;
				}
				$sql_color = "SELECT DISTINCT Color, ColorSKU FROM product_options WHERE ProductID=$id AND Inventory>0 ORDER BY Color";
				$result_color = mysql_query($sql_color);
				$row_color1 = mysql_fetch_assoc($result_color);
				$num_color = mysql_num_rows($result_color);
					
				if ($num_color > 0 && $row_color1["Color"] != '') {
					if ($count_colors > 0) {
	?>
			var size = $("#size").val();
			var gender = $("#gender").val();
			var color = $("select[name='single_color']").val();
			var quantity = $("#qty2").val();
						
			if(gender == "") {
				alert("Please Select Range");
				$("#gender").focus();
				return false;
			} 
			if(size == "") {
				alert("Please Select Size");
				$("#size").focus();
				return false;
			} 
			if(color == "") {
				alert("Please Select Color");
				$("select[name='single_color']").focus();
				return false;
			} 
			if(quantity == "") {
				alert("Please Enter Quantity ");
				$("#qty2").focus();
				return false;
			}
			var should_be_back = 0;
			$("#bundleitems select[name='select_size']").each(function(index) { 
				if($(this).val() == "") {
					alert("Please Select Your Sizes");
					$(this).focus();
					should_be_back = 1;
					return false;
				}	
			});
						  
			$("#bundleitems select[name='select_color']").each(function(index) { 
				if($(this).val() == "") {
					alert("Please Select Your Sizes");
					$(this).focus();
					should_be_back = 1;
					return false;
				}	
			});
						  
			if(should_be_back == 1) {
				return false;
			}	  
			var i = 0;
            $("#bundleitems select[name='select_color']").each(function(index) { 
				var index = $(this).attr("selectedIndex");
				if(index == 0) {
					// $(this).prev('ul').find('li').eq(value_color).find('a').click();
				}
			});
	<?php 
			} 
		}
	?>
    <?php   
    	$sql_size = "SELECT DISTINCT Size, SizeSKU FROM product_options WHERE ProductID=$id ORDER BY Position ASC";
        $result_size = mysql_query($sql_size);
		$row_size1 = mysql_fetch_assoc($result_size);
		$num_size = mysql_num_rows($result_size);
					
		if($num_size > 0 && $row_size1["Size"] != '') {
	?>
			var i = 0;	  
			$("#bundleitems select[name='select_size']").each(function(index) { 
				var index = $(this).attr("selectedIndex");
				if(index == 0) {
					// $(this).prev('ul').find('li').eq(value_color).find('a').click();
				}
			});
	<?php 
		}
	?>
		$("#divNote").html('<img src="images/smallloader.gif" />');
		var repeat_selection = ($("#repeat_selection:checked").attr("checked") == true) ? 1 : 0;
		$.post("includes/inc_details.php", {
			"type":"addCart", 
			"id":"<?=mysql_real_escape_string($_GET['id']);?>",
			"producttype":"single",
			"productname":$("#productname").val(),
			"gender":$("#gender :selected").text(),
			"gendersku":$("#gender").val(),
			"repeat_selection":repeat_selection,
			"size":$("#size").val(),
			"color":$("#color").val(),
		<?php if(isset($_GET["t"])){?>
			"free":"true",
			"psid":<?=$_GET["pid"]?>,
		<?php }?>
			"qty":$("#qty2").val()
		}, function(data) {
			//alert("data="+data);
			$("#divNote").html(data);
			// window.location.href = "cart.php";
		});
	<?php
		} else {	
	?>
		for (var i = 0; i < opts.length; i++) {
			if (opts[i] == "gender") {
				if ($("#" + opts[i] + " :selected").text().substring(0,6) == "Select") {
					alert("Please " + $("#" + opts[i] + " :selected").text());
					return false;
				}
			} else if ($("#" + opts[i] + " :selected").val() == '') {
				// alert("VALUE: " + opts[i] + " = " + $("select#" + opts[i] + " :selected").val()); // testing only
				alert("Please Select A " + opts[i]);
				return false;
			}
		}
						
		$("#divNote").html('<img src="images/smallloader.gif" />');
	<?php
		$sql_mngstk = "SELECT ManagableStock FROM products WHERE id='$prodid' LIMIT 1";
		$result_mngstk = mysql_query($sql_mngstk) or die("Stock error: " . mysql_error());
		$row_mngstk = mysql_fetch_assoc($result_mngstk);
		if($row_mngstk["ManagableStock"] == "No") {
	?>
		$.post("includes/inc_details.php", {
			"type":"addCart2", 
			"id":"<?=$_GET["id"];?>",
			"productname":$("#productname").val(),
			"gender":$("#gender :selected").text(),
			"gendersku":$("#gender").val(),
			"size":$("select#size :selected").val(), // $("#size").val(),
			"color":$("#color").val(),
	<?php if(isset($_GET["t"])){?>
			"free":"true",
			"psid":<?=$_GET["pid"]?>,
	<?php }?>
			"qty":$("#qty").val()
		}, function(data) {
			$("#divNote").html(data);
		});
	<?php
		} else {
	?>
			$.post("includes/inc_details2.php", {
				"type":"chkInv", 
				"id":"<?=$_GET["id"];?>", 
				"size":$("#size").val(),
				"color":$("#color").val()
			}, function(data) {
				if(parseFloat(data) < parseFloat($("#qty").val())) {
					alert('Available Inventory Is: '+data);
					$("#divNote").html('');
					return false;
				} else {
					$.post("includes/inc_details.php", {
						"type":"addCart2", 
						"id":"<?=$_GET["id"];?>",
						"productname":$("#productname").val(),
						"gender":$("#gender :selected").text(),
						"gendersku":$("#gender").val(),
						"size":$("#size").val(),
						"color":$("#color").val(),
				<?php if(isset($_GET["t"])){?>
						"free":"true",
						"psid":<?=$_GET["pid"]?>,
				<?php }?>
						"qty":$("#qty").val()
					}, function(data) {
						$("#divNote").html(data);
					});
				}
			});
	<?php } ?>
	<?php
			}
		}
	?>
	});
			
	$("#qty").blur(function() {
		$("#bundleitems").html('<img src="images/loader.gif" />');
		$("#bundleitems").load("includes/inc_details2.php", {
			"type":"bundleitems", 
			"prodid":"<?=$prodid;?>", 
			"qty":$("#qty").val(),
			"gender":$("#gender").val(),
			"jColor":$("select[name='Jersey_color']").val(),
			"shColor":$("select[name='Shorts_color']").val(),
			"skColor":$("select[name='Socks_color']").val()
		}, function() {
			$('form').jqTransform({imgPath:'jqtransformplugin/img/'});	
			if($("#checkimprintid").attr("checked") == true) {
				$("#addCart").hide();
				$("#addCart2").hide();
				$("#continueImprint").show();
				$("#continueImprint2_newver").show();
				$('#continueImprint_bt').show();
			} else {
				$("#addCart").show();
				$("#addCart2").show();
				$("#continueImprint").hide();
				$("#continueImprint2_newver").hide();
				$('#continueImprint_bt').hide();
			}		
		});
	});
				
	$("#qty2").blur(function() {
		if ($("#qty2").val() != "") {
			$("#bundleitems").html('<img src="images/loader.gif" />');
	<?php 
		if($ImprintCatID!=0 && $ImprintCatID!="") { ?>
			var checked_imprint = ($("#checkimprintid:checked").attr("checked") == true) ? 1 : 0;
			$("#bundleitems").load("includes/inc_details2.php", {
				"type":"singleitems",
				"checkedimpt":checked_imprint, 
				"proid":"<?=$prodid;?>",
				"qty":$("#qty2").val(),
				"gender":$("#gender").val(),
				"colorsku":$("#color").val(),
				"sizesku":$("#size").val()
			}, function() {
				$('form').jqTransform({imgPath:'jqtransformplugin/img/'});	
				if($("#checkimprintid").attr("checked") == false) {
					$("#addCart").hide();
					$("#addCart2").hide();
					$("#continueImprint").show();
					$("#continueImprint2_newver").show();
					$('#continueImprint_bt').show();
				} else {
					$("#addCart").show();
					$("#addCart2").show();
					$("#continueImprint").hide();
					$("#continueImprint2_newver").hide();
					$('#continueImprint_bt').hide();	
				}
			});
	<?php 
		} else { 
	?>
			$("#bundleitems").load("includes/inc_details2.php", {
				"type":"singleitems", 
				"proid":"<?=$prodid;?>", 
				"qty":$("#qty2").val(),
				"gender":$("#gender").val(),
				"colorsku":$("#color").val(),
				"sizesku":$("#size").val()
			}, function() {
				$('form').jqTransform({imgPath:'jqtransformplugin/img/'});
				if($("#checkimprintid").attr("checked") == false) {
					$("#addCart").hide();
					$("#addCart2").hide();
					$("#continueImprint").show();
					$("#continueImprint2_newver").show();
					$('#continueImprint_bt').show();
				} else {
					$("#addCart").show();
					$("#addCart2").show();
					$("#continueImprint").hide();
					$("#continueImprint2_newver").hide();
					$('#continueImprint_bt').hide();
				}	
			});
	<?php } ?>
			}
		});
			
		$("#qty").keydown(function(e) {
           	var key = e.charCode || e.keyCode || 0;
            return (key == 8 || 
	                key == 9 ||
	                key == 46 ||
	                (key >= 37 && key <= 40) ||
	                (key >= 48 && key <= 57) ||
	                (key >= 96 && key <= 105));
        });
        	
		$("#qty2").keydown(function(e) {
           	 var key = e.charCode || e.keyCode || 0;
            return (key == 8 || 
	                key == 9 ||
	                key == 46 ||
	                (key >= 37 && key <= 40) ||
	                (key >= 48 && key <= 57) ||
	                (key >= 96 && key <= 105));
        	});
});
		
function cngImage(csku) {
	if (csku == '') { 
		return; 
	}
	$("#mainimg").hide();
	$("#mainimg").attr("src", $(this).attr("src"));
	$("#mainimg").attr("src", $("#"+csku).attr("src"));
	$("#mainimg").fadeIn("slow");
}

function setSizes(gender, div, size, pid, colorSKU) {
	$("#"+div).html('<img src="images/loader.gif" />');
	$("#"+div).load("includes/inc_details.php", {
		"type":"setsizes", 
		"id":pid, 
		"gender":gender, 
		"size":size, 
		"colorsku":colorSKU
	});
}
		
function setDefaultCat1(filter) {
	var id = new String(filter.id);
	var cid = id.substring(id.indexOf(":")+1,id.length);
	var catName = id.substring(0,id.indexOf(":"))+".html";

	$.post("./includes/inc_browser.php", {
		"type":"initCategId", 
		"idCat":cid
	}, function(data) {
		var pathname = new String(window.location.pathname);
		pathname = pathname.substring(0,pathname.lastIndexOf("/")+1);
		window.location.pathname = pathname+catName;
	});
	return false;
}
		
function initBundleColor(el) {
	var id = new String(el.id);
	id = id.substring(6, id.length);
	var color = el.value;
	$.post("includes/inc_details2.php", {
		"type":"initColor", 
		"idBundle":id,
		"color":color
	}, function(data) {
	}); 
}
		
function initColor(el) {
	var id1 = new String(el.id);
	id = id1.substring(6,id1.length);
	var color = el.value;
	var color_index=$("#"+id1).attr("selectedIndex");
	$("#bundleitems select[name='select_color']").each(function(index) { 
		$(this).prev('ul').find('li').eq(color_index).find('a').click();
	});
	$.post("includes/inc_details.php", {
		"type":"initSingleColor", 
		"proid":id,
		"color":color
	}, function(data) {
	}); 
}
		
function initGender(el) {
	$("#bundleitems").html('<img src="images/loader.gif" />');
	$("#bundleitems").load("includes/inc_details2.php", {
		"type":"bundleitems", 
		"prodid":"<?=$prodid;?>", 
		"qty":$("#qty").val(),
		"gender":$("#gender").val()
	}, function() {
		$('form').jqTransform({imgPath:'jqtransformplugin/img/'});	
		if($("#checkimprintid").attr("checked")) {
			$("#addCart").hide();
			$("#addCart2").hide();
			$("#continueImprint").show();
			$("#continueImprint2_newver").show();
			$('#continueImprint_bt').hide();
			$("#continueImprint").removeAttr("disabled");
			$("#continueImprint2_newver").removeAttr("disabled");
		} else {
			$("#continueImprint").hide();
			$("#continueImprint2_newver").hide();
			$('#continueImprint_bt').hide();
			$("#addCart").show();
			$("#addCart2").show();	
			$("#addCart").removeAttr("disabled");
			$("#addCart2").removeAttr("disabled");
		}
	});
}
		
function initGenderSingle(el) {
	$("#bundleitems").html('<img src="images/loader.gif" />');
	$("#bundleitems").load("includes/inc_details2.php", {
		"type":"singleitems", 
		"proid":"<?=$prodid;?>", 
		"qty":$("#qty2").val(),
		"gender":$("#gender").val(),
		"sizesku":$("#size").val()
	}, function() {
		$('form').jqTransform({imgPath:'jqtransformplugin/img/'});	
		if($("#checkimprintid").attr("checked")) {
			$("#addCart").hide();
			$("#addCart2").hide();
			$("#continueImprint").show();
			$("#continueImprint2_newver").show();
			$('#continueImprint_bt').hide();
			$("#continueImprint").removeAttr("disabled");
			$("#continueImprint2_newver").removeAttr("disabled");
		} else {
			$("#continueImprint").hide();
			$("#continueImprint2_newver").hide();
			$('#continueImprint_bt').hide();
			$("#addCart").show();
			$("#addCart2").show();	
			$("#addCart").removeAttr("disabled");
			$("#addCart2").removeAttr("disabled");
		}
	});
<?php if($option1_res == "2") { ?>
<?php } else { ?>
	$("#size_of_product").html('<img src="images/loader.gif" />');
	$("#size_of_product").load("includes/inc_details2.php", {
		"type":"singleitems_size", 
		"proid":"<?=$prodid;?>", 
		"gender":$("#gender").val(),
		"sizesku":$("#size").val()
	}, function(){
		$('form').jqTransform({imgPath:'jqtransformplugin/img/'});	
	});
<?php } ?>
}
		
function setsize_below() {
	var size_index=$("#size").attr("selectedIndex"); 
	$("#bundleitems select[name='select_size']").each(function(index) { 
		$(this).prev('ul').find('li').eq(size_index).find('a').click();
	});
}

function setSizeBundle(el) {
	var id = new String(el.id);
	idB = id.substring(id.indexOf("size:") + 5, id.length);
	var set =  id.substring(3, id.indexOf(":size")); 
	var size = el.value;
	// alert("hello "+size+'-'+set+'-'+id+'-'+idB); // testing use only
	$.post("./includes/inc_details2.php", {
		"type":"initSizeB", 
		"idBundle":idB,
		"size":size,
		"set":set
	}, function(data) {
	});
}
		
function setSizeSingle(el) {
	var id = new String(el.id);
	var idB = id.substring(id.indexOf("size:")+5,id.length);
	var set =  id.substring(3,id.indexOf(":size")); 
	var size = el.value;
	var color = id.replace("size","color");
	
	$.post("./includes/inc_details.php", {
		"type":"initSizeS", 
		"proid":idB,
		"size":size,
		"set":set,
		"color":document.getElementById(color).value
	}, function(data) {
	});
}
		
function initGenders(el) {
	var id = new String(el.id);
	var idB = id.substring(id.indexOf("size:")+5,id.length);
	var set =  id.substring(3,id.indexOf(":size")); 
	var size = el.value;
	$.post("./includes/inc_details.php", {
		"type":"initGenders", 
		"proid":idB,
		"size":size,"set":set
	}, function(data) {
	});
}
		
function initColors(el) {
	var id = new String(el.id);
	var idB = id.substring(id.indexOf("color:")+6,id.length);
	var set =  id.substring(3,id.indexOf(":color")); 
	var color = el.value;
	var size = id.replace("color","size");			
	$.post("./includes/inc_details.php", {
		"type":"initColors", 
		"proid":idB,
		"size":color,
		"set":set
	}, function(data) {});
	$.post("./includes/inc_details.php", {
		"type":"initSizeS", 
		"proid":idB,
		"size":document.getElementById(size).value,
		"set":set,"color":color
	}, function(data) {
	});
}
		
function imprintCheckValid(el) {
	$("#continueImprint").attr("disabled", "disabled");
	$("#continueImprint2_newver").attr("disabled", "disabled");
	$("#addCart").attr("disabled", "disabled");
	$("#addCart2").attr("disabled", "disabled");
	if(el.checked) {
		$("#continueImprint").hide();
		$("#continueImprint2_newver").hide();
		$('#continueImprint_bt').hide();
		$("#addCart").show();
		$("#addCart2").show();	
		$("#addCart").removeAttr("disabled");
		$("#addCart2").removeAttr("disabled");
	} else {
		$("#addCart").hide();
		$("#addCart2").hide();
		$("#continueImprint").show();
		$("#continueImprint2_newver").show();
		$('#continueImprint_bt').hide();
		$("#continueImprint").removeAttr("disabled");
		$("#continueImprint2_newver").removeAttr("disabled");
	}
}
		
function imprintCheckValid2(el) {
	if(el.checked) {
		$("#continueImprint_asad").hide();
		$("#continueImprint2_asad").hide();
		$("#addCart").show();
		$("#addCart2").show();
	} else {
		$("#continueImprint_asad").show();
		$("#continueImprint2_asad").show();
		$("#addCart").hide();
		$("#addCart2").hide();
	}
}
		
function imprintPagecall(continuecheck) {
	<?php
		if ($ProductType == "Bundle") {
		$cate_name = array();
		$id = mysql_real_escape_string($_GET["id"]);
		$sql_bundle_size = "SELECT Items FROM product_bundles WHERE ProductID=$id ORDER BY SortOrder ASC";
		$result_bundle_size= mysql_query($sql_bundle_size);
		$bnum = 1;
		$k = 0;
		while ($row_bundle1=mysql_fetch_array($result_bundle_size)) {
			$sql_category_product = "select Category from category where id=(select CategoryID from category_items where ProductID = ".$row_bundle1['Items']." LIMIT 1)  ";
			$resultCategory = mysql_query($sql_category_product);
			$row_categ = mysql_fetch_assoc($resultCategory);
		
			$categoryAttribute = "";

			if (strpos($row_categ["Category"],"Jersey")) {
				$categoryAttribute = "Jersey";
			}
			if (strpos($row_categ["Category"],"Short")) {
				$categoryAttribute = "Short";
			}
			if (strpos($row_categ["Category"],"Sock")) {
				$categoryAttribute = "Socks";
			}

			if ($k == 0) {
				$cate_name[$k]=$categoryAttribute;
			}
			if ($k == 1) {
				$cate_name[$k]=$categoryAttribute;
			}
			if ($k == 2) {
				$cate_name[$k]=$categoryAttribute;
			}
			$k++;			
		}
		?>
			  
		 var gender=$("#gender").val();
		 var color1=$("select[name='<?php echo  $cate_name[0]."_color";?>']").val();
		 var color2=$("select[name='<?php echo  $cate_name[1]."_color";?>']").val();
		 var color3=$("select[name='<?php echo  $cate_name[2]."_color";?>']").val();
		 var quantity=$("#qty").val();
		 
		 if (gender=="") {
			   alert("Please Select Range");
			   $("#gender").focus();
				return false;
		 } 
	 
		 if(color1==""){
			   alert("Please Select Color Of <?php echo  ucfirst($cate_name[0]);?>");
			   $("select[name='<?php echo  $cate_name[0]."_color";?>']").focus();
				return false;
		  } 
		 
		   if(color3==""){
			   alert("Please Select Color Of <?php echo  $cate_name[0]?>");
				$("select[name='<?php echo  $cate_name[1]."_color";?>']").focus();
				return false;
		   } 
			if(color2==""){
			   alert("Please Select Color <?php echo  $cate_name[0]?>");
				$("select[name='<?php echo  $cate_name[2]."_color";?>']").focus();
				return false;
		   } 
		   if(quantity=="" || quantity < 1){
			   alert("Please Enter Quantity");
			   $("#qty").focus();
				return false;
		   }
		   
		   var should_be_back=0;
		   $("#bundleitems select[name='<?php echo  $cate_name[0];?>']").each(function(index) { 
			  if($(this).val()=="") {
				  alert("Please Select Your Sizes");
				   $(this).focus();
				   should_be_back=1;
				   window.location.hash = 'details';
				   return false;
				}						
			});
			  
			$("#bundleitems select[name='<?php echo  $cate_name[1];?>']").each(function(index) { 
				if($(this).val()=="") {
					alert("Please Select Your Sizes");
					$(this).focus();
					should_be_back=1;
					window.location.hash = 'details';
					return false;
				}
			}); 
		
			$("#bundleitems select[name='<?php echo  $cate_name[2];?>']").each(function(index) { 
				if($(this).val()=="") {
					alert("Please select your sizes");
					$(this).focus();
					should_be_back=1;
					window.location.hash = 'details';
					return false;
				}
			});
		   
			if(should_be_back==1) {
				return false;
			}
		 
			var value1="";
			var value2="";
			var value3="";
			var optsval = new Array();
			for(var i=0; i<opts.length; i++) {
				if($("#"+opts[i]).val() == '') {
					alert("Please select an option for bundle items");
					return false;
				} else {
					optsval[i] = $("#"+opts[i]).val();
				}
			}
				
			$.post("includes/inc_details2.php", {
				"type":"initImprint",
				"id":"<?=$_GET["id"];?>",
				"productname":$("#productname").val(),
				"producttype":"bundle",
				"gender":$("#gender :selected").text(),
				"gendersku":$("#gender").val(),
				"colorsku":$("#color").val(),
				"size":$("#size").val(),
				"qty":$("#qty").val()
			}, function(data) {
				window.location.href = "imprint.php?id=<?php echo $_GET["id"]?>";
			});
		<?php
			} else {
					if($option1_res == "2") {
				?>	
					var value_size = "";
					var value_color = "";
					<?php
					$id = mysql_real_escape_string($_GET["id"]);
					$count_colors = 0;
					$sql_color = "SELECT DISTINCT Color, ColorSKU FROM product_options WHERE ProductID=$id AND Inventory>0 ORDER BY Color";
					$result_color = mysql_query($sql_color);
					while($row_color = mysql_fetch_array($result_color)) {
						$count_colors++;
					}
					$sql_color = "SELECT DISTINCT Color, ColorSKU FROM product_options WHERE ProductID=$id AND Inventory>0 ORDER BY Color";
					$result_color = mysql_query($sql_color);
					$row_color1 = mysql_fetch_assoc($result_color);
					$num_color = mysql_num_rows($result_color);

					if($num_color > 0 && $row_color1["Color"] != '') {
						if($count_colors > 0) { 
						?>	
							var size=$("#size").val();
							var gender=$("#gender").val();
							var color=$("select[name='single_color']").val();
							var quantity=$("#qty2").val();
	
							if (gender=="") {
							   alert("please select a range");
							   $("#gender").focus();
								return false;
							}
							
							if (size=="") {
							   alert("please select a size");
							   $("#size").focus();
							   return false;
							} 

							if (color=="") {
							   alert("please select a color");

							   $("select[name='single_color']").focus();
								return false;
							}
					
							if (quantity=="" || quantity < 1) {
							   alert("please put a quantity ");
							   $("#qty2").focus();
								return false;
							}
						   
							var should_be_back = 0;
							$("#bundleitems select[name='select_size']").each(function(index){ 
								if ($(this).val()=="") {
								  alert("Please Select Your Sizes");
								   $(this).focus();
								   should_be_back=1;
								   window.location.hash = 'details';
								   return false;
								}	
							});
			  
							$("#bundleitems select[name='select_color']").each(function(index) { 
								if($(this).val()=="") {
									alert("Please Select Your Color");
								   $(this).focus();
								   should_be_back=1;
								   window.location.hash = 'details';
								   return false;
								}	
							});

							if(should_be_back==1) {
							   return false;
							}	  
							
							var i=0;
				<?php   } 
					} ?>
					$.post("includes/inc_details2.php", {
						"type":"initImprint_single",
						"id":"<?=$_GET["id"];?>",
						"productname":$("#productname").val(),
						"producttype":"single",
						"gender":$("#gender :selected").text(),
						"gendersku":$("#gender").val(),
						"colorsku":$("#color").val(),
						"size":$("#size").val(),
						"qty":$("#qty2").val()
					}, function(data) {
						window.location.href = "imprint.php?id=<?php echo $_GET["id"]?>";
					});
				<?php 
				} else { ?>
					for(var i=0; i<opts.length; i++) {				
						if(opts[i] == "gender") {
							if($("#"+opts[i]+" :selected").text().substring(0,6) == "Select ") {
								alert("Please select a "+opts[i]);
								window.location.hash = 'details';
								return false;
							}
						} else if($("#"+opts[i]).val() == '') {
							alert("Please select a "+opts[i]);
							window.location.hash = 'details';
							return false;
						}
					}
			
					$.post("includes/inc_details2.php", {
						"type":"initImprint",
						"id":"<?=$_GET["id"];?>",
						"productname":$("#productname").val(),
						"producttype":"bundle",
						"gender":$("#gender :selected").text(),
						"gendersku":$("#gender").val(),
						"colorsku":$("#color").val(),
						"size":$("#size").val(),
						"qty":$("#qty").val()
					}, function(data) {
						window.location.href = "imprint.php?id=<?php echo $_GET["id"]?>";
					});
			<?php
				}
			}
		?>
	} // end imprintPageCall

function gotoImprint() {
	<?php 
	if ($ProductType != "Bundle") { 
	?>
		window.location.href="imprint.php?id=<?php echo $_GET['id']?>";
	<?php 
	} else { 
	?>
		window.location.href="imprint.php?id=<?php echo $_GET['id']?>";
	<?php 
	} 
	?>
}
</script>
<style type="text/css">
.discount_pricing ul li { list-style:none; }
</style>
</head>
<body>
<div class="wrapper">
<!-- More View -->
	<div id="moreview">
   		<div id="moreimages" class="window" style="text-align: center;">
   		<a href="#" class="close" style="float: right;">Close</a>
<?php
		$sql_moreimg = "SELECT Image FROM product_images WHERE ProductID='$prodid' ORDER BY SortOrder ASC";
		$result_moreimg = mysql_query($sql_moreimg) or die(mysql_error());
		while ($row_moreimg = mysql_fetch_array($result_moreimg)) {
			$last[] = $row_moreimg["Image"];
			$firstimg = '<img id="mainMore" src="images/productView/'.$last[0].'" style="height: 280px; padding: 10px;" />';
			$imglist .= '<td><img src="images/productView/'.$row_moreimg["Image"].'" class="thumbs" /></td>';	
		}
		echo $firstimg; // big image display choice
?>
    	<div style="width: 500px; height: 120px; text-align: center; overflow: auto; text-align:left;"><table><tr><?=$imglist;?></tr></table></div>
   		</div>
   	<div id="mask"></div> 
	</div><!-- end MoreView -->
<div class="Master_div"> 
	<?php include_once('includes/header.php'); ?>
	<div class="container container1">
    	<div class="navigation">
      		<div class="navi_L"></div>
      		<div class="navi_C">
        	<?php include_once('includes/topnav.php'); ?>
        		<div class="clear"></div>
      		</div>
      		<div class="navi_R"></div>
      		<div class="clear"></div>
    	</div><!-- end navigation -->
<?php 
	if (!empty($row_details) && count($row_details)) { 
?>
		<div class="detailed">
<?php
		if ($prodid != 'VIP') {
			$sql_mainimg = "SELECT p.ColorImage, p.AltText FROM product_options p, product_browser b WHERE p.ProductID=b.ProductID AND p.ColorImage=b.Image AND p.ProductID=$prodid LIMIT 1";
			$result_mainimg = mysql_query($sql_mainimg);
			$row_mainimg = mysql_fetch_assoc($result_mainimg);
			$num_mainimg = mysql_num_rows($result_mainimg);
			if ($num_mainimg > 0) {
				$mainimg = $row_mainimg["ColorImage"];
				$mainalt = $row_mainimg["AltText"];
			} else {
				$sql_mainimg = "SELECT ColorImage, AltText FROM product_options WHERE ProductID=$prodid LIMIT 1";
				$result_mainimg = mysql_query($sql_mainimg);
				$row_mainimg = mysql_fetch_assoc($result_mainimg);
			}
			$Image = $row_mainimg["ColorImage"];
			if ($row_mainimg["AltText"] == NULL) {
				$imgAlt = $row_mainimg["Color"];
			} else {
				$imgAlt = $row_mainimg["AltText"];
			}
		}
?>
    		<div class="detailed_L">
    		<img id="mainimg" src="images/productImages/<?=$Image;?>" alt="<?=$ProductDetailName;?>">
<?php 
			if ($prodid != 'VIP') {
				$sql_more = "SELECT id FROM product_images WHERE ProductID=$prodid";
				$result_more = mysql_query($sql_more) or die("More Views Error: " . mysql_error());
				$num_more = mysql_num_rows($result_more);
				if ($num_more > 0) {
					echo '<a href="#moreimages" id="viewmore" name="viewmore">For More Views Click Here</a>';
				}
			}
?>
    		</div>
    		<div class="detailed_L_mobile"><img id="mainimg" src="images/productImages/<?=$Image;?>" alt="<?=$ProductDetailName;?>"></div>
    		<div class="detailed_R_mobile">
    		<?php 
    		if ($prodid == 'VIP') { ?>
           		<h1><?=$Name;?><br/></h1>
    			<div class="clear"></div>
            	<?=$Description;?>
            	<div class="clear"></div>
                <div class="VIP_members"><h2>Price: $<?=number_format($Price,2);?></h2></div>
            <?php } else { 
            			$sql_pricing2 = "SELECT * FROM product_pricing WHERE ProductID='$prodid'";
						$result_pricing2 = mysql_query($sql_pricing2);
						$row_pricing2 = mysql_fetch_assoc($result_pricing2);
            ?>
            	<h1 class="heading"><?=$ProductDetailName;?></h1>
            	<input type="hidden" id="productname" name="productname" value="<?=$ProductDetailName;?>"/>
            	<h3 class="mobileSKU">SoccerOne #: <?=$RootSKU;?></h3>
                <?php 
                if ($isSpecial == "True") {
					$sp = number_format($SpecialPrice,2);
					$parts = explode('.', $sp);
					echo "<div id='specialCat'><strong>$SpecialCategory</strong>";
					if ($SpecialPrice < 1.00) {
						echo "<br/><span id='cents'>" . $parts[1] . "</span><sup id='centSymbol'>&cent;</sup>";
					} else {
						echo "<br/><sup>$</sup><sub id='dollar'>" . $parts[0] . "</sub><sup><u>" . $parts[1] . "</u></sup>";
					}
				echo "</div>";
			} else {
				// echo "<div class='basePrice'>Non-member: $" . number_format($row_pricing2["NonMember"], 2) . '</div>';
				// echo "<div class='vipPrice'>VIP Price: $" . number_format($row_pricing2["Option1Price"], 2) . '</div>';
				if ($isvip == 'no' && $isSpecial != "True") {
                	echo '<div class="VIP_members"><img src="images/S_soccer_card.png" alt="VIP Membership - JOIN TODAY!"><h2>VIP MEMBERS SAVE EVEN MORE</h2><h3>Not a VIP member? <a href="details.php?id=VIP">Join Today</a></h3></div>';
            	}
            
				// mobile PRICING CHART
				$output = array();
				$c = 0;
				$sql_pricing = "SELECT * FROM product_pricing WHERE ProductID=$prodid ORDER BY id";
				$result_pricing = mysql_query($sql_pricing) or die("ERROR: vip table data error " . mysql_error());
				while ($row_pricing2 = mysql_fetch_array($result_pricing)) {
					$output[$c]['gender'] = $row_pricing2["Gender"];
					$output[$c]['msrp'] = $row_pricing2["MSRP"];
					$output[$c]['nm'] = $row_pricing2["NonMember"];
					$output[$c]['o1p'] = $row_pricing2["Option1Price"];
					$output[$c]['o2p'] = $row_pricing2["Option2Price"];
					$output[$c]['o3p'] = $row_pricing2["Option3Price"];
					$output[$c]['o4p'] = $row_pricing2["Option4Price"];
					$c++;
				}
				$sql_pricing = "SELECT * FROM product_pricing WHERE ProductID=$prodid";
				$result_pricing = mysql_query($sql_pricing) or die("ERROR: vip table data error " . mysql_error());
				$row_pricing = mysql_fetch_assoc($result_pricing);
				echo '<table id="m_priceChart">
				<tbody>';
				if ($row_pricing["Gender"] != NULL) {
					echo '<tr>
						<th colspan="2">Size</th>';
						foreach ($output as $key => $html) {
							echo '<th scope="row">' . $html['gender'] . '</th>';
						}
					echo '</tr>';
				}
				echo '<tr>
						<th colspan="2" id="msrp">MSRP/Value</th>';
						foreach ($output as $key => $html) {
							echo '<td headers="msrp"><s>$' .  number_format($html['msrp'], 2) . '</s></td>';
						}
					echo '</tr>
					<tr>
						<th colspan="2" id="non-member">Non Member</th>';
						foreach ($output as $key => $html) {
							echo '<td headers="non-member">$' . number_format($html['nm'], 2) . '</td>';
						}
					echo '</tr>
					<tr>
						<th rowspan="4" scope="row" id="VIPchartHead"><p>VIP<br>Pricing</p></th>
						<th class="VIPchartYellow" id="yellow-column" headers="VIPchartHead">' . $row_pricing["Option1"] . '</th>';
						foreach ($output as $key => $html) {
							echo '<td class="VIPchartYellow" headers="yellow-column">$' .  number_format($html['o1p'], 2) . '</td>';
						}
					echo '</tr>
					<tr>
						<th class="VIPchartBlue" id="blue-column" headers="VIPchartHead">' . $row_pricing["Option2"] . '</th>';
						foreach ($output as $key => $html) {
							echo '<td class="VIPchartBlue" headers="blue-column">$' .  number_format($html['o2p'], 2) . '</td>';
						}
					echo '</tr>
					<tr>
						<th class="VIPchartSilver" id="silver-column" headers="VIPchartHead">' . $row_pricing["Option3"] . '</th>';
						foreach ($output as $key => $html) {
							echo '<td class="VIPchartSilver" headers="silver-column">$' .  number_format($html['o3p'], 2) . '</td>';
						}
					echo '<tr>
						<th class="VIPchartGold" id="gold-column" headers="VIPchartHead">' . $row_pricing["Option4"] . '</th>';
						foreach ($output as $key => $html) {
							echo '<td class="VIPchartGold" headers="gold-column">$' .  number_format($html['o4p'], 2) .'</td>';
						}
				echo '</tbody>
			</table>';
			} // end mobile PRICING CHART
            }
?> 
            <div class="mobileViewMoreImg">
            <?php
            if ($prodid != 'VIP') {
				$sql_more = "SELECT id FROM product_images WHERE ProductID=$prodid";
				$result_more = mysql_query($sql_more) or die("More Views Error: " . mysql_error());
				$num_more = mysql_num_rows($result_more);
				if ($num_more > 0) {
					echo '<a href="#moreimages" id="viewmore" name="viewmore">For More Views Click Here</a>';
				}
			}
			?></div>
    		</div><!-- END MOBILE -->
      		<div class="detailed_R">
<?php
			if ($prodid == 'VIP') {
?>
           		<h1><?=$Name;?><br/></h1>
            	<div class="clear"></div>
            	<?=$Description;?>
            	<div class="clear"></div>
<?php
				if ($isvip == 'yes') {
					$sql_vipinfo = "SELECT Status, VIPNum, VIPDate, VIPExpDate FROM customers WHERE EmailAddress='$_SESSION[email]' LIMIT 1";
					$result_vipinfo = mysql_query($sql_vipinfo) or die("VIP Information Error: " . mysql_error());
					$row_vipinfo = mysql_fetch_assoc($result_vipinfo);
					if ($row_vipinfo["VIPExpDate"] >= date('Y-m-d')) {
						echo '<h2 style="color: #ff0000">You are already a member. <br/>Your VIP Number is: <strong>'.$row_vipinfo["VIPNum"].'</strong><br/>Your membership will expire on: <strong>';
						$date = strtotime($row_vipinfo["VIPExpDate"]);
						echo date('m/d/Y', $date);
						echo '</strong></h2>';
					}
				}
?>
                <div class="clear"></div>
                	<div class="VIP_members">
                	<h2 style="color: #FF0000">Price: $<?=number_format($Price,2);?></h2>
                	</div>
<?php
			} else {
?>
                <h1 class="heading"><?=$ProductDetailName;?><input type="hidden" id="productname" name="productname" value="<?=$ProductDetailName;?>"/></h1><br /><br />
                <div class="clear"></div>
                <h1 class="heading2"><span id="manHeading">Manufacturer #:</span>&nbsp;<?=$ManufacturerNum;?></h1>
                <h1 class="heading3"><span id="s1Heading">SoccerOne #:</span>&nbsp;<?=$RootSKU;?></h1>
                <div class="clear"></div>
                <?=$ShortDescription;?>
                <div class="clear"></div>
                <table id="vip_area" cellpadding="0" cellspacing="0" width="80%">
                <tr>
                    <td style="text-align: center;">
        <?php
        			if ($isvip == 'no' && $isSpecial != "True") {
        ?>
                        <div class="VIP_members">
                        <img src="images/S_soccer_card.png" alt="" />
                        <h2>VIP MEMBERS SAVE EVEN MORE</h2>
                        <h3>Not a VIP member? <a href="details.php?id=VIP">Join Today</a></h3>
                        </div><!-- end vip_members -->
		<?php
					} else {
						// echo "<h2></h2>";
		}
                    if ($isSpecial == "True") {
                        // echo "<h2 style=\"color: #FF0000\">$SpecialCategory: $".number_format($SpecialPrice,2)."</h2>";
                        // echo "Special Price: " . $SpecialPrice . "<br>"; // testing use only
                        $sp = number_format($SpecialPrice,2);
                        $parts = explode('.', $sp);
                        echo "<div id='specialCat'><strong>$SpecialCategory</strong>";
                        if ($SpecialPrice < 1.00) {
                        	echo "<br/><span id='cents'>" . $parts[1] . "</span><sup id='centSymbol'>&cent;</sup>";
                        } else {
                        	echo "<br/><sup>$</sup><sub id='dollar'>" . $parts[0] . "</sub><sup><u>" . $parts[1] . "</u></sup>";
                        }
                        echo "</div>";
                    }
        ?>
                    </td>
                </tr>
                </table>
        <?php
                if ($isSpecial != "True") {
                	$sql_pricing = "SELECT * FROM product_pricing WHERE ProductID=$prodid";
					$result_pricing = mysql_query($sql_pricing) or die("PRICING CHART ERROR: " . mysql_error());
					$row_pricing = mysql_fetch_assoc($result_pricing);
        ?>
                	<!-- VIP pricing chart -->
                	<table id="priceChart">
					<thead>
						<tr>
							<?php if ($row_pricing["Gender"] != NULL) { ?>
							<th rowspan="2" id="size">Size</th>
							<?php } ?>
							<th rowspan="2" id="msrp">MSRP/Value</th>
							<th rowspan="2" id="non-member">Non Member</th>
							<th colspan="4" scope="col" id="VIPchartHead"><p>VIP Member Discount Pricing</p></th>
						</tr>
						<tr>
							<th class="VIPchartYellow" headers="VIPchartHead" id="yellow-column"><?=$row_pricing["Option1"];?></th>
							<th class="VIPchartBlue" headers="VIPchartHead" id="blue-column"><?=$row_pricing["Option2"];?></th>
							<th class="VIPchartSilver" headers="VIPchartHead" id="silver-column"><?=$row_pricing["Option3"];?></th>
							<th class="VIPchartGold" headers="VIPchartHead" id="gold-column"><?=$row_pricing["Option4"];?></th>
						</tr>
					</thead>
					<?php
					$sql_pricing = "SELECT * FROM product_pricing WHERE ProductID=$prodid ORDER BY id";
                    $result_pricing = mysql_query($sql_pricing);
                    while ($row_pricing = mysql_fetch_array($result_pricing)) {
                    ?>
					<tbody>
						<tr>
						<?php if ($row_pricing["Gender"] != NULL) { ?>
							<th scope="row" id="youth" headers="size"><?=$row_pricing["Gender"];?></th>
						<?php } ?>
							<td headers="msrp"><s>$<?=number_format($row_pricing["MSRP"], 2);?></s></td>
							<td headers="non-member">$<?=number_format($row_pricing["NonMember"], 2);?></td>
							<td class="VIPchartYellow" headers="yellow-column youth">$<?=number_format($row_pricing["Option1Price"], 2);?></td>
							<td class="VIPchartBlue" headers="blue-column youth">$<?=number_format($row_pricing["Option2Price"], 2);?></td>
							<td class="VIPchartSilver" headers="silver-column youth">$<?=number_format($row_pricing["Option3Price"], 2);?></td>
							<td class="VIPchartGold" headers="gold-column youth">$<?=number_format($row_pricing["Option4Price"], 2);?></td>
						</tr>
					<?php } ?>
					</tbody>
				</table>
    	<?php
				}
			}					
		?>
      		</div><!-- end detailed_R -->
      		<div class="detailed_B"> 
		<?php
			if ($prodid != 'VIP') {
				if ($ProductType == "Bundle") {
					if ($ProductType == 'Bundle') {
						$imgPath = 'images/productView/';
					} else {
						$imgPath = 'images/productImages/';
					}
					$sql_bnum = "SELECT id, items FROM product_bundles WHERE ProductID='$prodid'";
					$result_bnum = mysql_query($sql_bnum);
					$num_bnum = mysql_num_rows($result_bnum);
					if ($num_bnum == 1) {
						$row_bnum = mysql_fetch_assoc($result_bnum);
						$sql_colorimg = "SELECT DISTINCT ColorImage, ColorSKU FROM product_options WHERE ProductID='$row_bnum[items]' ORDER BY ImageSort, ColorImage";
						$result_colorimg = mysql_query($sql_colorimg);
						while ($row_colorimg = mysql_fetch_array($result_colorimg)) {
							if (file_exists($imgPath.$row_colorimg["ColorImage"])) {
								echo '<img class="coloropt" id="'.$row_colorimg["ColorSKU"].'" src="'.$imgPath.$row_colorimg["ColorImage"].'" />';
							}
						}
					} else {
						$sql_colorimg = "SELECT id, Image FROM product_images WHERE ProductID=$prodid ORDER BY SortOrder";
						$result_colorimg = mysql_query($sql_colorimg);
						while ($row_colorimg = mysql_fetch_array($result_colorimg)) {
							if (file_exists($imgPath.$row_colorimg["Image"])) {
								echo '<img class="coloropt" id="'.$row_colorimg["id"].'" src="'.$imgPath.$row_colorimg["Image"].'" />';
							}
						}
					}
				} else {
					$sql_colorimg = "SELECT DISTINCT ColorImage, ColorSKU FROM product_options WHERE ProductID=$prodid ORDER BY ImageSort, ColorImage";
					$result_colorimg = mysql_query($sql_colorimg);
					if ($ProductType == 'Bundle') {
						$imgPath = 'images/productView/';
					} else {
						$imgPath = 'images/productImages/';
					}
					while ($row_colorimg = mysql_fetch_array($result_colorimg)) {
						if (file_exists($imgPath.$row_colorimg["ColorImage"])) {
							echo '<img class="coloropt" id="'.$row_colorimg["ColorSKU"].'" src="'.$imgPath.$row_colorimg["ColorImage"].'" />';
						}
					}
				}
		?>
			<div id="shortDesc" class="mobileDesc"><?=$ShortDescription;?></div>
			<h2>Product Description</h2>
            <script type="text/javascript">
            $(document).ready(function() {
                $("#prodDesc").load("includes/inc_desc.php?type=prod&id=<?=$prodid;?>");
            });
            </script>
        	<div id="prodDesc" class="mobileDesc"></div>
			<!-- share this page -->
			<div class="share"><a href="#" onclick="window.open(
      'https://www.facebook.com/sharer/sharer.php?u='+encodeURIComponent(location.href), 
      'facebook-share-dialog', 
      'width=626,height=436'); 
    return false;"><img src="images/football_facebook.png" alt="facebook" title="facebook"></a> <a href="https://www.twitter.com/share"><img src="images/football_twitter.png" alt="twitter" title="twitter"></a> <br /><small>SHARE THIS PAGE</small><br /></div>
	<?php 
			} 
	?>        
      	</div><!-- end detailed_B -->
    </div><!-- end detailed -->
 <?php
	if ($ProductType == "Bundle") {
		$rightbarheight = ' rbh';
	} else {
		$rightbarheight = '';
	}
?>
    <div class="detailed_options<?=$rightbarheight;?>">
    <!-- Order Now Column -->
    <h1>Order Now</h1>
    <form action="" method="post" id="oForm">
<?php
		if ($prodid != 'VIP') {		
			$sql_genderprice = "SELECT ShowGender FROM product_pricing WHERE ProductID=$prodid GROUP BY ShowGender";
			$result_genderprice = mysql_query($sql_genderprice);
			$row_genderprice = mysql_fetch_assoc($result_genderprice);
			if ($row_genderprice["ShowGender"] != 'None') {
?>
    			<br />
<?php
        		if ($option1_res == "2") {
?>
					<!-- Range selection -->
					<select tabindex="33" id="gender" name="gender" <?php if($ProductType == "Bundle") { ?>onChange="initGender(this)"<?php } else { ?> onChange="initGenderSingle(this)"<?php } ?>>
<?php 
				} else {
					echo '<select tabindex="33" id="gender" name="gender" onChange="initGender(this)">';
         		}
?>
				<option value=''>Select <?=$row_genderprice["ShowGender"];?></option>
<?php
				$sql_gender = "SELECT Gender, GenderSKU FROM product_pricing WHERE ProductID=$prodid ORDER BY Gender";
				$result_gender = mysql_query($sql_gender);
				while ($row_gender = mysql_fetch_array($result_gender)) {
					echo '<option value="'.$row_gender["Gender"].'">'.$row_gender["Gender"].'</option>';
				}
?>
				</select>
				<script type="text/javascript">opts.push("gender");</script>
<?php
				// if bundle product
				if ($ProductType == "Bundle" ) {
					$sql_bundle = "SELECT Items FROM product_bundles WHERE ProductID=$prodid ORDER BY SortOrder ASC";
					$_SESSION["bundleProductId"] = $prodid;
					$_SESSION["bundleItems"] = array();
                	$_SESSION["tabsTitle"] = array();
					$result_bundle = mysql_query($sql_bundle);
					$bnum = 1;
					$i = 0;
					while ($row_bundle = mysql_fetch_array($result_bundle)) {
						$sql_bimage = "SELECT Image FROM product_browser WHERE ProductID=".$row_bundle["Items"]." LIMIT 1";
						$_SESSION["bundleItems"][$row_bundle["Items"]] = array();
						$result_bimage = mysql_query($sql_bimage);
						$row_bimage = mysql_fetch_assoc($result_bimage);
						$sql_bitem = "SELECT p.RootSKU, p.ProductDetailName, d.ShortDescription FROM products p, product_descriptions d WHERE p.id=d.ProductID AND p.id=$row_bundle[Items] LIMIT 1";
						$result_bitem = mysql_query($sql_bitem);
						$row_bitem = mysql_fetch_assoc($result_bitem);
						$sqlc1 = "select CategoryID from category_items where ProductID = ".$row_bundle['Items']." LIMIT 1";
						$resultc1 = mysql_query($sqlc1);
						$rowc1 = mysql_fetch_assoc($resultc1);
						$sql_category_product = "select Category from category where id=".$rowc1["CategoryID"];
						$resultCategory = mysql_query($sql_category_product);
						$row_categ = mysql_fetch_assoc($resultCategory);
						$categoryAttribute = "";
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
						$categoryAttribute = empty($categoryAttribute)?'Size':$categoryAttribute;
						echo "<select class='coloropt' tabindex='33' id='bundle".$row_bundle[Items]."' onchange='initBundleColor(this); cngImage(this.value); ' name='".$categoryAttribute."_color'>";
						$_SESSION["tabsTitle"][] = $categoryAttribute;
						echo "<option value=\"\">".$categoryAttribute." Color</option>";
						$sql_color = "SELECT DISTINCT Color, ColorSKU FROM product_options WHERE ProductID=$row_bundle[Items] AND Inventory>0 ORDER BY Color";
						$result_color = mysql_query($sql_color);
						$row_color = mysql_fetch_assoc($result_color);
						$num_color = mysql_num_rows($result_color);
						if ($num_color > 0 && $row_color["Color"] != '') {
							$result_citems = mysql_query($sql_color);
							while ($row_citems = mysql_fetch_array($result_citems)) {
								echo "<option value=".$row_citems["ColorSKU"].">".$row_citems["Color"]."</option>";
							}
						}
						$i++;
						echo "</select>\n";
						echo '<script type="text/javascript">opts.push("color");</script>';
					}
				} // end bundle 
	?> 
    <?php
			} // else {
	        $sql_color = "SELECT DISTINCT Color, ColorSKU FROM product_options WHERE ProductID=$prodid AND Inventory>0 ORDER BY Color";
        	$result_color = mysql_query($sql_color);
        	if (false === $result_color) {
            	echo mysql_error();
        	}
			$row_color1 = mysql_fetch_assoc($result_color);
			$num_color = mysql_num_rows($result_color);
			if ($num_color > 0 && $row_color1["Color"] != '') {
	?>
        		<div style="height: 10px;"></div>
        		<!-- color selection -->
	<?php 
				if ($option1_res == "2") { 
	?>
    <?php 
    				echo "<select tabindex='33' id='bundle".$prodid."' onchange='initColor(this); cngImage(this.value);' name='single_color'>"; 
    ?>
    <?php 
    			} else { 
    ?>
					<select id="color" name="color" onChange="cngImage(this.value); setSizes($('#gender :selected').text(), 'divSizeG', 'size', '<?=$prodid;?>', this.value);">
	<?php  
				}
	?>
                <option value="">Select Color</option>
    <?php
        		$sql_color = "SELECT DISTINCT Color, ColorSKU FROM product_options WHERE ProductID=$prodid AND Inventory>0 ORDER BY Color";
                $result_color = mysql_query($sql_color);
                while ($row_color = mysql_fetch_array($result_color)) {
                    echo '<option value="'.$row_color["ColorSKU"].'">'.$row_color["Color"].'</option>';
                }
    ?>
              	</select>
              	<script type="text/javascript">opts.push("color");</script>
    <?php
			}
			$sql_size = "SELECT DISTINCT Size, SizeSKU FROM product_options WHERE ProductID=$prodid  ORDER BY Position ASC";
            $result_size = mysql_query($sql_size);
            if (false === $result_size) {
                echo mysql_error();
            }
			$row_size1 = mysql_fetch_assoc($result_size);
			$num_size = mysql_num_rows($result_size);
			if ($num_size > 0 && $row_size1["Size"] != '') {			
		?>
            	<div id="size_of_product">
					<div id="divSizeG">
					<!-- size selection -->
		<?php 
					if ($option1_res == "2") {
        			} else {
                		echo '<select tabindex="33" id="size" name="size">';
               			echo '<option value="">Select Size</option>';
                   		$sql_gender = "SELECT Gender, GenderSKU FROM product_pricing WHERE ProductID=$prodid ORDER BY Gender";
						$result_gender = mysql_query($sql_gender);
						while ($row_gender = mysql_fetch_array($result_gender)) {
							$sql_size = "Select Distinct product_options.Size, product_options.SizeSKU From product_options, sizes Where product_options.Size = sizes.Size And product_options.ProductID=$prodid And product_options.Gender='".$row_gender["Gender"]."' And product_options.Inventory > 0 And product_options.SizeSKU = sizes.SKU order by product_options.Position ASC";
						 	$result_size = mysql_query($sql_size);
                    		while ($row_size = mysql_fetch_array($result_size)) {
                        		echo '<option value="' . $row_size["SizeSKU"] . '">' . $row_size["Size"] . '</option>';
                    		}
						 }
              			echo '</select>';
					}
		?>
                    </div><!-- end divSizeG -->
              		<script type="text/javascript">opts.push("size");</script>
              	</div><!-- end size_of_product -->
        <?php
			}
	  	} 
	  	?>
      	<div style="clear:both"></div>              
      	<div class="add_cart" style="clear:both">
    <?php
		if ($prodid == 'VIP') {
			$edit = ' readonly="readonly"';
		} else {
			$edit = '';
		}
	?>
        <label>Quantity</label>
        <style type="text/css">
		.quantity_box .jqTransformInputWrapper { background:none; }
		.quantity_box .jqTransformInputWrapper { background:none; }
		.quantity_box .jqTransformInputInner { background:none; }
		.quantity_box input#qty { padding:5px 7px; background: #fff; border: #000; }
		</style>
    <?php 
    	if ($ProductType == "Bundle") { 
    ?> 
        	<div class="quantity_box">
        	<input type="text" <?=$edit;?> value="1" id="qty" size="5" name="qty" class="quantity">
       		</div>
    <?php 
    	} else {
	?>
        	<div class="quantity_box">
    <?php 
    		if ($option1_res=="2") {
    ?>
				<input type="text" <?=$edit;?> value="1" id="qty2" size="5" name="qty2">
    <?php 
    		} else { 
    ?>
				<input type="text" <?=$edit;?> value="1" id="qty" size="5" name="qty">
    <?php 
    		}
    ?>
        </div><!-- end add_cart -->
    <?php
		}
	?>
    <div class="clear"></div>
	<div id="divNote" class="notes"></div>
	<div class="clear"></div>
<?php 
	$showButtons = "false";
	if ($Status == 'Disabled') {
		echo '<div style="text-align: center; margin-bottom: 20px;"><label>Product is currently unavailable</label></div>';
	} elseif($isvip=='yes' && $prodid == 'VIP' && $_POST['exp'] == "no") {
		echo '<div style="text-align: center; margin-bottom: 20px;"><label></label></div>';
	} elseif ($affLink != NULL) {
	    echo '<div><a href="' . $affLink .'" class="affBtn" target=_"blank">Order it!</a></div>';
	} else { 
?>
<?php  
		if (($ImprintCatID != 0 && $ImprintCatID != "")) { 
?>
        	<style type="text/css">
		 	span.jqTransformCheckboxWrapper{margin-top: -3px; margin-left:5px;}
		 	a.jqTransformCheckbox {height:15px; width:14px;}
		 	</style>
			<div style="background-color: rgb(254, 0, 0); height: 30px; width: 199px; margin-left: -16px; padding-bottom: 10px;">
			<input id="checkimprintid" type="checkbox" name="imprint" onclick="imprintCheckValid(this)" />
			<label style="color: rgb(255, 255, 255); margin-top: 12px;">View Imprint Option</label>
			</div>
			<div style="clear:both;height:20px"></div>
			<div style="text-align:center;">
    <?php 
    		if ($ProductType == "Bundle") { 
    ?>							 
				<button type="button" id="addCart" name="addCart" value="" style="margin:0px;" onclick="window.location.hash='details';" class="dfd"></button>
	<?php 
			} else {
	?>
    <?php 
    			if ($option1_res == "2") {
    ?>
					<button type="button" id="addCart" name="addCartdas" value="" style="margin:0px;" onclick="window.location.hash='details';" class="dfd"></button>
    <?php 
    			} else { 
    				// if ($_SESSION["email"] != "") {
    ?>		
					<button type="button" id="addCart2" name="addCart" value="" style="margin:0px;" class="cart"></button>     
	<?php 
					// } else {
					// 	echo '<button type="button" id="plzLogin" name="plzLogin" class="affBtn">Please Login</button>';
					// }
				}
	?>
    <?php 
    		}
	?>
			<button type="button" id="continueImprint" name="continueimprintCart" class="continueImprint" style="display:none;width: 155px; height: 32px;border:0px;cursor:pointer"></button>   
			</div>
	<?php 
		} elseif($ProductType == "Bundle") {
			// if ($_SESSION["email"] != "") {
	?>
			<div style="text-align:center;">		
        	<button type="button" id="addCart" name="addCartsd" value="" style="margin:0px;" onclick="window.location.hash='details';" class="cont"></button>
        	</div>
        	<div class="bundleText"><font color="white"><strong>INSTRUCTIONS:</strong><br/>1. Select Size Range<br/>2. Select Color(s) of Kit<br/>&nbsp;&nbsp;&nbsp;&nbsp;Components<br/><small>&nbsp;&nbsp;&nbsp;&nbsp;(if applicable)</small><br/>3. Enter total quanitity of<br/>&nbsp;&nbsp;&nbsp;&nbsp;Kits<br/>4. Click "Continue"<br/>5. Select Size(s) of Kit<br/>&nbsp;&nbsp;&nbsp;&nbsp;Components<br/><small>(if applicable)</small><br/>6. Click "Add to Cart"</font></div>
        	<div class="bundleText_mobile"><font color="white"><strong>INSTRUCTIONS:</strong><ol><li>Select Size Range</li><li>Select Color(s) of Kit Components <small>(if applicable)</small></li><li>Enter total quanitity of</li><li>Click "Continue"</li><li>Select Size(s) of Kit Components <small>(if applicable)</small></li><li>Click "Add to Cart"</li></ol></font></div>
    <?php 
    		// } else {
    		//	echo '<button type="button" id="plzLogin" name="plzLogin" class="affBtn">Please Login</button>';
    		// }
    	} else {
    ?>
    <?php 
    		if ($option1_res == "2") {
    ?>
	<?php  
				if (($ImprintCatID != 0 && $ImprintCatID != "")) { 
	?>
					<style type="text/css">
					span.jqTransformCheckboxWrapper{margin-top: -3px; margin-left:5px;}
					a.jqTransformCheckbox {height:15px; width:14px;}
					</style>
					<div style="background-color: rgb(254, 0, 0); height: 30px; width: 199px; margin-left: -16px; padding-bottom: 10px;">
					<input id="checkimprintid" type="checkbox" name="imprint2" onclick="imprintCheckValid(this)" />
					<label style="color: rgb(255, 255, 255); margin-top: 12px;">View Imprint Option</label>
					</div>
					<div style="clear:both;height:20px"></div>
	<?php 
				}
	?>
				<div style="text-align:center;">
				<button type="button" id="addCart" name="addCartdas" value="" style="margin:0px;" onclick="window.location.hash='details';" class="dfd"></button>
				<button type="button" id="continueImprint" name="continueimprintCart" class="continueImprint" style="display:none;width: 155px; height: 32px;border:0px;cursor:pointer"></button>
				</div>
    <?php 
    		} else { 
    			// if ($_SESSION["email"] != "") {
    ?>	
				<div style="text-align:center;">	
				<button type="button" id="addCart2" name="addCart" value="" style="margin:0px;" class="cart"></button>
				</div>     
	<?php 
				// } else {
				// 	echo '<button type="button" id="plzLogin" name="plzLogin" class="affBtn">Please Login</button>';
				// }
			}
    	} 
    	$showButtons = "true";
    } 
	?>
    </div><!-- end browser -->
	</form>
    <div style="clear:both;height:10px"></div>
	</div>
<div class="clear"></div>
<a name="details"></a>
<div class="clear"></div>
<?php
	if ($ProductType == "Bundle") { 
?>
        <div id="bundleitems"></div>	
<?php  
		if ($ImprintCatID != 0 && $ImprintCatID != -1) { 
?>
			<button type="button" id="continueImprint_bt" name="continueimprintCart" class="continueImprint" onclick="imprintPagecall();" style="display:none;width: 155px; height: 32px;border:0px;cursor:pointer"></button>
<?php 
		}
?>
        <script type="text/javascript">
		$("#bundleitems").load("includes/inc_details2.php", {
			"type":"bundleitems", 
			"qty":$("#qty").val(), 
			"prodid":"<?=$prodid;?>"
		}, function() {
			// $('form').jqTransform({imgPath:'jqtransformplugin/img/'});
			$('form').not('#mobileSearch').jqTransform({imgPath:'jqtransformplugin/img/'});
			$('#addCart2').show();
		});
		</script> 
    	<div class="clear"></div>
<?php
	} else {
		if ($option1_res == "2") { 
?>
			<div id="bundleitems"></div>
            <script type="text/javascript">
			$("#bundleitems").load("includes/inc_details2.php", {
				"type":"singleitems", 
				"qty":$("#qty2").val(), 
				"proid":"<?=$prodid;?>","gender":""
			}, function(data) {
				// $('form').jqTransform({imgPath:'jqtransformplugin/img/'});
				$('form').not('#mobileSearch').jqTransform({imgPath:'jqtransformplugin/img/'});
				$('#addCart2').show();
			});
			</script>
			<div class="clear"></div>
<?php 	
		} 
	} 
?>
<?php 
	} else {
?>
    	<div class="detailed"><br /><h1><span style="color: rgb(255, 0, 0);">This product is not available currently in the system.</span></h1><br /></div>
<?php 
	}
?>
    <div class="clear"></div>
</div><!-- Container Div ends here --> 
<div class="footer">
	<div class="foot_box"><?php include("includes/footer.php"); ?></div>
</div><!-- Footer Div ends here --> 
<div class="mobileFooter">&copy;2016 Youth Sports Publishing, Inc. All Rights Reserved.</div>
</div><!-- end master_div -->
</div><!-- end wrapper -->
<script type="text/javascript">
$('#repeat_selection').live('click',function() {
	var repeat_selection=($("#repeat_selection:checked").attr("checked")==true) ? 0 : 1;
	if (repeat_selection) {
	<?php 
		if ($ProductType == "Bundle") {
			$cate_name = array();
			$id = mysql_real_escape_string($_GET["id"]);
			$sql_bundle_size = "SELECT Items FROM product_bundles WHERE ProductID=$id ORDER BY SortOrder ASC";
			$result_bundle_size = mysql_query($sql_bundle_size);
			$bnum = 1;
			$k = 0;
			while($row_bundle1 = mysql_fetch_array($result_bundle_size)) {
				$sql1 = "SELECT CategoryID FROM category_items WHERE ProductID=".$row_bundle1['Items']." LIMIT 1";
				$result1 = mysql_query($sql1) or die("Preliminary Bundle Selection Error: " . mysql_error());
				$row1 = mysql_fetch_assoc($result1);
				$sql_category_product = "SELECT Category FROM category where id=".$row1["CategoryID"];
				$resultCategory = mysql_query($sql_category_product) or die("Bundle Selection Error: " . mysql_error());
				$row_categ = mysql_fetch_assoc($resultCategory);
				$categoryAttribute = "";
                if ($k == 0) {
                	if (stripos(strtolower($row_categ["Category"]), 'jerseys') !== FALSE) {
						$cate_name[$k] = "Jersey";
					} else {
						$cate_name[$k] = $row_categ["Category"]; 
					}
				}
				if ($k == 1) {
					if (stripos(strtolower($row_categ["Category"]), 'shorts') !== FALSE) {
						$cate_name[$k] = "Shorts";
					} else {
						$cate_name[$k] = $row_categ["Category"]; 
					}
				}
				if ($k == 2) {
					if (stripos(strtolower($row_categ["Category"]), 'socks') !== FALSE) {
						$cate_name[$k] = "Socks";
					} else {
						$cate_name[$k] = $row_categ["Category"]; 
					}
				}
				$k++;			
			}
	?>
		var value1 = "";
		var value2 = "";
		var value3 = "";
		$("#bundleitems select[name='<?php echo $cate_name[0];?>']").each(function(index) { 
			if (index == "0") {
				value1 = $(this).attr("selectedIndex");  
			} else {
				$(this).prev('ul').find('li').eq(value1).find('a').click();
			}
		});
		$("#bundleitems select[name='<?php echo $cate_name[1];?>']").each(function(index) { 
			if (index == "0") {
				value2 = $(this).attr("selectedIndex");
			} else {
				$(this).prev('ul').find('li').eq(value2).find('a').click();
			}
		});
		$("#bundleitems select[name='<?php echo $cate_name[2];?>']").each(function(index) { 
			if (index == "0") {
				value3 = $(this).attr("selectedIndex");
			} else {
				$(this).prev('ul').find('li').eq(value3).find('a').click();
			}
		});
	<?php 
		} else { 
	?>
		var value_size = "";
		var value_color = "";
	<?php 
			$id = mysql_real_escape_string($_GET["id"]);
			$count_colors = 0;
        	$sql_color = "SELECT DISTINCT Color, ColorSKU FROM product_options WHERE ProductID=$id AND Inventory>0 ORDER BY Color";
            $result_color = mysql_query($sql_color) or die("Error: " . mysql_error());
			if (false === $result_color) {
				echo mysql_error();
			}
			$row_color1 = mysql_fetch_assoc($result_color);
			$num_color = mysql_num_rows($result_color);
			if ($num_color > 0 && $row_color1["Color"] != '') {
				if ($num_color !== 0) { 
	?>
					var i = 0;
        			$("#bundleitems select[name='select_color']").each(function(index) { 
						if (i == 0) {
							var index = $(this).attr("selectedIndex");
							value_color = index;
						} else {
							$(this).prev('ul').find('li').eq(value_color).find('a').click();
						}
						i++
					});
	<?php
				} 
			}	
	?>
    <?php   
            $sql_size = "SELECT DISTINCT Size, SizeSKU FROM product_options WHERE ProductID=$id  ORDER BY Position ASC";
            $result_size = mysql_query($sql_size);
            if (false === $result_size) {
                echo mysql_error();
            }
			$row_size1 = mysql_fetch_assoc($result_size);
			$num_size = mysql_num_rows($result_size);
			if ($num_size>0 && $row_size1["Size"] != '') {
	?>
				var i=0;	  
				$("#bundleitems select[name='select_size']").each(function(index) { 
					if (i == 0) {
						var index = $(this).attr("selectedIndex");
						value_size = index; 
					} else {
						$(this).prev('ul').find('li').eq(value_size).find('a').click();
					}
					i++
				});	  
	<?php 
			}
	?>
	<?php
		} 
	?>
	}
});
</script>
</body>
</html>