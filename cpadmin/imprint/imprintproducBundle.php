<?php
session_start();
$_SESSION["currentImprint"] = array();
	if($_GET["id"] == '') {
		header("location: browser.php");
	}

	require 'cpadmin/includes/db.php';
	session_start();

	$prodid = $_GET["id"];

	if($prodid == 'VIP') {
		$sql_details = "SELECT * FROM vip LIMIT 1";
		$result_details = mysql_query($sql_details);
		$row_details = mysql_fetch_assoc($result_details);
		
		foreach($row_details as $key=>$value) {
			$$key = stripslashes($value);

		}
	
	} else {
	
		$sql_details = "SELECT * FROM products WHERE id=$prodid LIMIT 1";
		$result_details = mysql_query($sql_details);
		$row_details = mysql_fetch_assoc($result_details);
	
		foreach($row_details as $key=>$value) {
			$$key = stripslashes($value);
		}
		
		$sql_des = "SELECT * FROM product_descriptions WHERE ProductID=$prodid LIMIT 1";
		$result_des = mysql_query($sql_des);
		$row_des = mysql_fetch_assoc($result_des);
		
		foreach($row_des as $key=>$value) {
			$$key = stripslashes($value);
		}
		
	}
	
	if(!isset($_SESSION["email"]) || $_SESSION["email"] == '') {
		$isvip='no';
	} else {
		
		$sql_chkvip = "SELECT Status FROM customers WHERE EmailAddress='$_SESSION[email]' AND current_date < DATE_ADD(VIPDATE, INTERVAL 1 YEAR) LIMIT 1";

		$result_chkvip = mysql_query($sql_chkvip);
		$row_chkvip = mysql_fetch_assoc($result_chkvip);
		
		if($row_chkvip["Status"] == "VIP") {
			$isvip='yes';
		} else {
			$isvip='no';
		}
	}
	
	if($isvip=='no') {
		if($_SESSION["email"] == '') {
			$sqlwhere = "SessionID='".session_id()."'";
		} else {
			$sqlwhere = "(EmailAddress='$_SESSION[email]' OR SessionID='".session_id()."') ";
		}
		
		$sql_chkcart = "SELECT * FROM shopping_cart WHERE ProductID='VIP' AND $sqlwhere";
		$result_chkcart = mysql_query($sql_chkcart);
		$num_chkcart = mysql_num_rows($result_chkcart);
		
		if($num_chkcart>0) {
			$isvip='yes';
		}
	}
	
	include("./cpadmin/imprint/Database.class.php");
	include("./cpadmin/imprint/imprint_tabs.class.php");
	include("./cpadmin/imprint/imp_category_tabs.class.php");
	include("./cpadmin/imprint/cimprint_category.class.php");
	include("./cpadmin/imprint/options_tab.class.php");
	include("./cpadmin/imprint/impcategory_option.class.php");

	
	$sql_prod_imprint = "select ID_IMPRINT_CATEGORY from products where id=".$_GET['id'];
	$result_imprint = mysql_query($sql_prod_imprint);
	$row_sql_imprint = mysql_fetch_assoc($result_imprint);
	$imprint_category_id = $row_sql_imprint["ID_IMPRINT_CATEGORY"];
	
    $imp_category_tabs = new imp_category_tabs();
	$array = array();
	$array["imprint_categ_id"] = $imprint_category_id;
	$list = array();
	$list = $imp_category_tabs->readArray($array);
	
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<title><?=$PageTitle;?></title>

<meta name="description" content="<?=$MetaDescription;?>" />

<meta name="keywords" content="<?=$MetaKeywords;?>" />

<link rel="stylesheet" href="css/css_styles.css" type="text/css" />

<link rel="stylesheet" href="jqtransformplugin/jqtransform.css" type="text/css"  media="all" />

<link rel="stylesheet" href="./imprint/css/css_styles.css" type="text/css" />

<style>
.separator {
	background-image : url("./images/separator.png");
	width:936px;
	height:6px;
	float:left;
	margin-left: 11px;
}

.overview{
	float:left;
	margin-left: 11px;
	
}
</style>
<script type="text/javascript" src="js/jquery-1.4.4.min.js"></script>

<script type="text/javascript" src="jqtransformplugin/jquery.jqtransform.js"></script>

<script language="javascript" type="text/javascript"></script>

<script type="text/javascript">
currentIndex = "t1";
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

$(document).ready(function() {

	//Default Action
	$(".tab_content").hide(); //Hide all content
	$("ul.tabs li:first").addClass("active").show(); //Activate first tab
	$(".tab_content:first").show(); //Show first tab content
	
	//On Click Event
	$("ul.tabs li").click(function() {
		$("ul.tabs li").removeClass("active"); //Remove any "active" class
		$(this).addClass("active"); //Add "active" class to selected tab
		$(".tab_content").hide(); //Hide all tab content
		var activeTab = $(this).find("a").attr("href"); //Find the rel attribute value to identify the active tab + content
		$(activeTab).fadeIn(); //Fade in the active content
		currentIndex = $(this)[0].id;
		return false;
	});

});

function getPreview(el){
	var id = el.id;
	var idOption = el.value;
	var str = new String(currentIndex);
	var cind = str.substring(1,str.length);
$.ajax({
  type: 'POST',
  url: "./cpadmin/imprint/inc_tabs.php",
  data: "type=previewOption&idoption="+idOption+"&idtab="+cind,
  success: previewsuccess
  
});

$.ajax({
  type: 'POST',
  url: "./cpadmin/imprint/inc_tabs.php",
  data: "type=previewshirt&idoption="+idOption,
  success: previewShirtSuccess
  
});

	
}

function previewsuccess(data){
	var str = new String(currentIndex);
	var cind = str.substring(1,str.length);
	$("#optionsTabPreview"+cind).html(data);
	
}



function previewShirtSuccess(data){
	var str = new String(currentIndex);
	var cind = str.substring(1,str.length);
	var strData = new String(data);
	var imgsrc = strData.substring(0,strData.indexOf("|"));
	var lowas = strData.substring(strData.indexOf("|")+1,strData.length);
	$("#previewShirt"+cind).html(imgsrc);
	$("#lows"+cind).html(lowas);
	getTabOverview(cind);
}

function getTabOverview(idtab){
	$.ajax({
  type: 'POST',
  url: "./cpadmin/imprint/inc_tabs.php",
  data: "type=overview&idtab="+idtab,
  success: getoverviewsuccess
  
});
}
function getoverviewsuccess(data){
	var str = new String(currentIndex);
	var cind = str.substring(1,str.length);
$('#tbOption'+cind).html(data);

}

var currentLightId="";
function showPricing(el){
	var id = new String(el.id);
	id = id.substring(1,id.length);
	$.ajax({
  			type: 'POST',
  			url: "./cpadmin/imprint/inc_tabs.php",
  			data: "type=previewPrice&idoption="+id,
  			success: previewPriceSuccess
  
	});
	
	var str = new String(currentIndex);
	var cind = str.substring(1,str.length);
	document.getElementById('light'+cind).style.display='block';
	document.getElementById('fade').style.display='block';
	currentLightId = "lightContent"+cind;
}

function previewPriceSuccess(data){
	$("#"+currentLightId).html(data);
}
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
    <!-- :: Product Images :::::::::::::::::::::::::::::::::::::::::::::::::: -->
<div class="detailed_L" style="margin-left: 5px; width: 231px;">

<?php
				
						$sql_colorimg = "SELECT DISTINCT ColorImage, ColorSKU FROM product_options WHERE ProductID=$prodid and ColorSKU='".$_SESSION["imprintConfig"]["color"]."'";
						
						
						$result_colorimg = mysql_query($sql_colorimg);
						
						if($ProductType=='Bundle') {
							$imgPath = 'images/productView/';
						} else {
							$imgPath = 'images/productImages/';
						}
						
						while($row_colorimg = mysql_fetch_array($result_colorimg)) {
							if(file_exists($imgPath.$row_colorimg["ColorImage"])) {
								echo '<img  id="'.$row_colorimg["ColorSKU"].'" src="'.$imgPath.$row_colorimg["ColorImage"].'" style="width: 228px;" />';
							}
						}
					
				
		
		
		
	?>
	<img id="mainimg" style="width: 228px;" src="<?php echo $imgPath;?>/<?=$Image;?>" alt="<?=$row_mainimg["AltText"];?>" />
</div>
<div id="divproducts" class="browser_product">
<div style="font-family:13px;font-weight:bold;color:#fe0000;">You have selected <?=$ProductDetailName;?> to imprint</div>
<div style="text-align: justify; width: 665px; margin-top: 25px; margin-bottom: 25px;">This product is available directly from SoccerOnce in select colors and sizes. Call(888) 297-6386 for exact inventory status or a SoccerOne Representative will confirm availability by next business day.</div>
			 <div class="header">
  		 <ul class="tabs">
         <?php 
		  for($i=0;$i<sizeof($list);$i++){
		 ?>
         
          <li id="t<?php echo $i+1;?>"><a href="#tab<?php echo $i+1; ?>"><?php echo $list[$i]->gettab_name();?></a></li>
          
          
          <?php }?>
        </ul>
        <div class="tab_container">
        
        <?php 
		    
			for($j=0;$j<sizeof($list);$j++){
			?>					 
			<div class="tab_content" id="tab<?php echo $j+1;?>">
            	<div class="options">
                
                 <?php 
				
				$options_tab = new options_tab();
				
				$array = array();
				$array["id_tab"] = $list[$j]->getid_tab();
				
				$listoptions = $options_tab->readArray($array);
				foreach( $listoptions as $key => $value ){
					$idOption = $value->getid_option();
					
					include_once("./cpadmin/imprint/impcategory_option.class.php");
					$impcategory_option = new impcategory_option();
					$array2 = array();
					$array2["IDOPTION"] = $idOption;
					$impcategory_option->readObject($array2);
					echo  '<input type="radio" value="'.$idOption.'" name="radii" id="'.$idOption.'" onclick="getPreview(this)"/>';
					
                    echo  '<label>'.$impcategory_option->getOPTION_NAME().'</label>';
					
				}
				
				?>
                
                </div>
<div class="shirt<?php echo $j+1?>" id="previewShirt<?php echo $j+1?>">
                		<img alt="" src="images/name_img1.png">
                </div>
                
                <h1>Available options</h1>
                <div style="background-color: rgb(254, 0, 0); float: right; width: 143px; color: rgb(255, 255, 255); margin-top: -21px; height: 21px; margin-left: 0px; padding-left: 6px; padding-top: 0px;"><div style="margin-top: 2px;">AS LOW AS $<div id="lows<?php echo $j+1;?>" style="float:right;width:70px;"></div></div></div>
                <div class="A_options">
                		<div class="box" id="optionsTabPreview<?php echo $j+1?>" style="width: 670px; min-height: 540px;">
                        
                        </div>  
                        <div class="white_content" id="light<?php echo $j+1?>" style="display: none;"> 
                                    <h2>Price</h2>
                                    <div id="lightContent<?php echo $j+1?>"></div>
                                      <a class="cross" onclick="document.getElementById('light<?php echo $j+1?>').style.display='none';document.getElementById('fade').style.display='none'" href="javascript:void(0)"><img alt="" src="./imprint/images/cross.png"></a>
                                </div>
                                <div class="black_overlay" id="fade" style="display: none;"></div>
                        
                </div>
            </div>
			<?php
            
			}//end for			
			?>
           
  		<div class="clear"></div>
  </div>
<!-- Header Div ends here --> 

      </div>

    <div class="clear"></div>

  </div>
  <div class="separator"></div>
  <div class="overview">
  <table width="340"  style="border:1px solid #BDBDBD;background-color:#F1F1F1">
  <tr>
    <td colspan="2" style="border:1px solid #BDBDBD;"><span style="color:#FB3D3D;font-weight:bold">Imprint Review </span><?=$ProductDetailName;?></td>
    </tr>
    <?php 
		  for($i=0;$i<sizeof($list);$i++){
		 ?>
  <tr>
    <td style="border:1px solid #BDBDBD;"><?php echo $list[$i]->gettab_name();?></td>
    <td style="border:1px solid #BDBDBD;"><div id="tbOption<?php echo $i+1?>"></div></td>
  </tr>
  <?php }?>
</table>
<table style="width:100%">
<tr>
	<th style="background-color:#FE0000">SIZE</th>
	<th style="background-color:#FE0000">PLAYER'S NAME</th>
	<th style="background-color:#FE0000">PLAYER'S NO</th>
</tr>
<?php 
$size = $_SESSION["imprintConfig"]["size"];
$qty =	$_SESSION["imprintConfig"]["qty"];
for($sz = 0;$sz<$qty;$sz++){
	?>
    <tr>
    <td style="background-color:#F1F1F1">
    <?php 
		$sql = "SELECT `Size` FROM `sizes` WHERE `SKU`='".$size."'";
		$query = mysql_query($sql);
		$result_size1 = mysql_fetch_assoc($query) or die("size doesn't exist!");
		echo $result_size1["Size"];
	
	?>
    </td>
    <td style="background-color:#F1F1F1">
    <input value="" name="pname<?php echo $sz;?>" />
    </td>
    <td style="background-color:#F1F1F1">
     <input value="" name="pnno<?php echo $sz;?>" />
    </td>
    </tr>
    <?php
}
?>
</table>
  
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



<?php mysql_close($conn); ?>