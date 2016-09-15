<?php
    session_start();
	include("includes/header.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Untitled Document</title>
<link rel="stylesheet" href="css/styles.css" type="text/css" />
<style type="text/css">
body, td, th {
	font-family: Arial;
	font-size: 12px;
	color: #333333;
}
.style1 {
	color: #FFFFFF
}
.smallbox {
	width: 35px;
}
.melbox {
	width: 120px;
}
.lglbox {
	width: 230px;
}
.100 {
	width:100%
}
.pricebox {
	width: 50px;
}
h1, h2, h3, h4, h5, h6 {
	font-family: Arial, Helvetica, sans-serif;
}
h1 {
	font-family:Arial !important;
	color: #000 !important;
}
.border {
	border:1px;
	border-color:#999999;
	padding:3px;
}
.style2 {
	color: #000000;
	font-weight: bold;
}
.style4 {
	color: #FFFFFF;
	font-weight: bold;
}
#menu_nav li {
	color: #000000;
	line-height: 20px;
	margin-left: 10px;
	padding: 5px;
	list-style:none outside none;
}
a {
	color:#fff;
}
</style>
<style type="text/css">
<!--
body, td, th {
	font-family: Arial;
	font-size: 12px;
}
.style1 {
	color: #FFFFFF;
	font-weight: bold;
}
.style3 {
	color: #000000;
	font-weight: bold;
}
.style9 {
	font-size: 14;
	font-weight: bold;
	color: #FFFFFF;
}
-->
</style>
<?php
include("./imprint/Database.class.php");
include("./imprint/cimprint_category.class.php");
include("./imprint/impcategory_option.class.php");
include("./imprint/impoption_settings.class.php");
include("./imprint/images.class.php");
include("./imprint/pricing.class.php");
$impoption_settings = new impoption_settings();
$optionSetting = new impoption_settings();
$pricing = new pricing();


// update option name
if(isset($_POST["optionNameUpdate"])){
	$idop = mysql_real_escape_string($_POST["idop"]);
	$optionName = mysql_real_escape_string($_POST["optionNameUpdate"]);
	$impcategory_option = new impcategory_option();
	$opParam = array();
	$opParam["IDOPTION"]= $idop;
	
	$impcategory_option->readObject($opParam);
    $impcategory_option->setOPTION_NAME($optionName);
	$impcategory_option->insert();
	
}

if(isset($_GET["idop"]) || isset($_SESSION["idop"])){
       	if(isset($_GET["idop"]))
	      $_SESSION["idop"] = mysql_real_escape_string($_GET['idop']);

	$idp = $_SESSION["idop"];
	$impcategory_option = new impcategory_option();
	$array = array();
	$array["IDOPTION"] = $idp;
	$imprintOption = $impcategory_option->readObject($array);
	$impoption_settings->readObject($array);
	$pricing->readObject($array);
	
	$myFile = "testFile.txt";
	$fh = fopen($myFile, 'w') or die("can't open file");
	$stringData = $idp;
	fwrite($fh, $stringData);
	fclose($fh);
	
}

//non sequence price

if(isset($_POST["sqType"])){
	include_once("./imprint/impcategory_option.class.php");
	$idop = mysql_real_escape_string($_POST["sqType"]);
	$impcategory_option = new impcategory_option();
	$params["IDOPTION"]= $idop;
	$impcategory_option->readObject($params);

	if(isset($_POST["sequence"])){
		$impcategory_option->setNONSEQUENCE("1");
		$impcategory_option->insert();
	}else{
		$impcategory_option->setNONSEQUENCE("0");
		$impcategory_option->insert();
	}
}

// Add color-image
if(isset($_POST["colorsList"])){
	
 	include_once("./imprint/colors_images.class.php");
  
  $colorid = mysql_real_escape_string($_POST["colorsList"]);
  $idop = mysql_real_escape_string($_POST["idop"]);
  if(!empty($_FILES["imgColor"])){
  	$imgName = $_FILES["imgColor"]["name"];
	$target_path = "./imprint_files/";
	$target_path = $target_path . basename( $_FILES['imgColor']['name']); 
	if(move_uploaded_file($_FILES['imgColor']['tmp_name'], $target_path)) {
    	$images = new images();
		$images->setIDOPTION($_POST["idop"]);
		$images->setIMAGEURL($imgName);
		$images->insert();
		$styleName = mysql_real_escape_string($_POST["styleName"]);
		$idimages =  $images->getIDIMAGE();
		$colors_images = new colors_images();
		$colors_images->setidcolor($colorid);
		$colors_images->setidimages($idimages);
		$colors_images->setidoption(mysql_real_escape_string($_POST["idop"]));
		$colors_images->setstyleName($styleName);
		$colors_images->insert();

	} else{
    	echo "There was an error uploading the file, please try again!";
	}
	
  }
}
////

// Delete color Image
if(isset($_POST["delColorImg"])){
	include_once("./imprint/colors_images.class.php");
	$idcimg = mysql_real_escape_string($_POST["delColorImg"]);
	$colors_images = new colors_images();
	$params = array();
	$params["id"] = $idcimg;
	$colors_images->readObject($params);
	$idimg = $colors_images->getidimages();
	$images = new images();
	$pimg = array();
	$pimg["IDIMAGE"] = $idimg;
	$images->delete($pimg);
	$colors_images->delete($params);
	
}
//
if(isset($_GET["idp"])){
	$cimprint_category = new cimprint_category();
	$array = array();
	$array["IDCATEGORY"] = mysql_real_escape_string($_GET['idp']);
	$cimprint_category->readObject($array);

}
if(isset($_GET["del"])){
  $id = mysql_real_escape_string($_GET['del']);
  $dimp_category_tabs = new imp_category_tabs();
  $dparams = array();
  $dparams["id_tab"] = $id;
  $dimp_category_tabs->delete($dparams);
}

if(isset($_POST["categoryNameEdit"])){
	$category = mysql_real_escape_string($_POST["categoryNameEdit"]);
	$adminNotes = mysql_real_escape_string($_POST["adminNotes"]);
	$idCategory = mysql_real_escape_string($_POST["categoryID"]);
	$cimprint_category = new cimprint_category();
	$cimprint_category->setIDCATEGORY($idCategory);
	$cimprint_category->setCATEGORY($category);
	$cimprint_category->setADMIN_NOTES($adminNotes);
	$cimprint_category->insert();
}

if(isset($_POST["formType"])){
	$optionType = mysql_real_escape_string($_POST["optionType"]);
	include_once("./imprint/impoption_settings.class.php");
	
	$idcategory = mysql_real_escape_string($_GET['idp']);
	if($optionType==1){
		$optionName = mysql_real_escape_string($_POST["optionName"]);
		$sequence = 0;
		if(isset($_POST["sequence"]))
			$sequence=1;
		$nbrImgToUpload = mysql_real_escape_string($_POST["nbrImg"]);
		$nbrColors = mysql_real_escape_string($_POST["nbrColors"]);
		$adminNotes = mysql_real_escape_string($_POST["adminNotes"]);
		$idop = mysql_real_escape_string($_POST["optionId"]);
		
		// update option 
		$impcategory_option = new impcategory_option();
		$impcategory_option->setIDCATEGORY($idcategory);
		$impcategory_option->setIDTYPE($optionType);
		$impcategory_option->setIDOPTION($idop);
		$impcategory_option->setOPTION_NAME($optionName);
		$impcategory_option->setNONSEQUENCE($sequence);
		$impcategory_option->setADMIN_NOTES($adminNotes);
		$impcategory_option->insert();
		
		// update settings
		
		
		$impoption_settings = new impoption_settings();
		$impoption_settings2 = new impoption_settings();
		$array= array();
		$array["IDOPTION"] = $idop;
		$impoption_settings->readObject($array);
		$impoption_settings2->setID_SETTINGS($impoption_settings->getID_SETTINGS());
		$impoption_settings2->setIDOPTION($idop);
		$impoption_settings2->setCOLORS_NBR($nbrColors);
		$impoption_settings2->setNBR_IMAGES($nbrImgToUpload);
		
		$impoption_settings2->insert();
	}
	if($optionType==3){
		$logoType = mysql_real_escape_string($_POST["logoType"]);
		$customerGroup = "";
		if(isset($_POST["CustomerGroup"]))
			$customerGroup = mysql_real_escape_string($_POST["CustomerGroup"]);
		$adminNotes = mysql_real_escape_string($_POST["adminNotes"]);
		$idop = mysql_real_escape_string($_POST["optionId"]);
		$optionName = mysql_real_escape_string($_POST["optionName"]);
		
		// update option 
		$impcategory_option = new impcategory_option();
		$impcategory_option->setIDCATEGORY($idcategory);
		$impcategory_option->setIDTYPE($optionType);
		$impcategory_option->setIDOPTION($idop);
		$impcategory_option->setOPTION_NAME($optionName);
		$impcategory_option->setADMIN_NOTES($adminNotes);
		$impcategory_option->setlogoType($logoType);
		$impcategory_option->setCustomerGroup($customerGroup);
		$impcategory_option->insert();
		
	}
}
else{

if(isset($_POST["optionType"])){
	$optionType = mysql_real_escape_string($_POST["optionType"]);
	include_once("./imprint/impoption_settings.class.php");
	
	$idcategory = mysql_real_escape_string($_GET['idp']);
	if($optionType==1){
		$optionName = mysql_real_escape_string($_POST["optionName"]);
		$sequence = 0;
		if(isset($_POST["sequence"]))
			$sequence=1;
		$nbrImgToUpload = mysql_real_escape_string($_POST["nbrImg"]);
		$nbrColors = mysql_real_escape_string($_POST["nbrColors"]);
		$adminNotes = mysql_real_escape_string($_POST["adminNotes"]);
		$idop = mysql_real_escape_string($_POST["optionId"]);
		$impcategory_option = new impcategory_option();
		$impcategory_option->setIDCATEGORY($idcategory);
		$impcategory_option->setIDTYPE($optionType);

		$impcategory_option->setOPTION_NAME($optionName);
		$impcategory_option->setNONSEQUENCE($sequence);
		$impcategory_option->setADMIN_NOTES($adminNotes);
		$idOption = $impcategory_option->insert();
		$impoption_settings = new impoption_settings();
		$impoption_settings->setIDOPTION($idop);
		$impoption_settings->setCOLORS_NBR($nbrColors);
		$impoption_settings->setNBR_IMAGES($nbrImgToUpload);
		
		$impoption_settings->insert();
	}
	if($optionType==3){
		$optionName = mysql_real_escape_string($_POST["optionName"]);
		$sequence = 0;
		if(isset($_POST["sequence"]))
			$sequence=1;
	
		$adminNotes = mysql_real_escape_string($_POST["adminNotes"]);
		$idop = mysql_real_escape_string($_POST["optionId"]);
		$logoType = mysql_real_escape_string($_POST["logoType"]);
		$customerGroup = "";
		if(isset($_POST["CustomerGroup"]))
			$customerGroup = mysql_real_escape_string($_POST["CustomerGroup"]);
		$impcategory_option = new impcategory_option();
		$impcategory_option->setIDCATEGORY($idcategory);
		$impcategory_option->setIDTYPE($optionType);

		$impcategory_option->setOPTION_NAME($optionName);
		
		$impcategory_option->setADMIN_NOTES($adminNotes);
		$impcategory_option->setlogoType($logoType);
		$impcategory_option->setCustomerGroup($customerGroup);
		$impcategory_option->insert();

	}
	
	
}
	
}
if(isset($_FILES["file"])){
	include_once("./imprint/images.class.php");
	$name =  $_FILES['file']['name'];
	$target_path = "./imprint_files/";
	$target_path = $target_path . basename( $_FILES['file']['name']); 
	if(move_uploaded_file($_FILES['file']['tmp_name'], $target_path)) {
    	$images = new images();
	
		$images->setIDOPTION(mysql_real_escape_string($_POST["idop"]));
		$images->setIMAGEURL($name);
		$images->insert();

	} else{
    	echo "There was an error uploading the file, please try again!";
	}
		
	
}

if(isset($_FILES["frontEndPreview"])){
	include_once("./imprint/impoption_settings.class.php");
	$idOption = mysql_real_escape_string($_POST["idop"]);
	$array["IDOPTION"] = $idOption;
	$imprintoption = new impoption_settings();
	$imprintoption->readObject($array);
	$name =  $_FILES['frontEndPreview']['name'];
	$target_path = "./imprint_files/";
	$target_path = $target_path . basename( $_FILES['frontEndPreview']['name']); 

 	if(move_uploaded_file($_FILES['frontEndPreview']['tmp_name'], $target_path)) {
		if(gettype($imprintoption)!="array"){
			
			
			$imprintoption->setIDOPTION($idOption);	
			$imprintoption->setFRONTEND_PREVIEW($name);
			
			$imprintoption->insert();
		}
		else{
			$imprintoption->setIDOPTION($idOption);	
			$imprintoption->setFRONTEND_PREVIEW($name);
			$imprintoption->insert();
			
		}
	}
	
	
}

if(isset($_POST["strqt1"])){
	include_once("./imprint/pricing.class.php");
	$startqt1 = mysql_real_escape_string($_POST["strtqt1"]);
	$memberPricing = substr(mysql_real_escape_string($_POST["nmbprcing"]),1);
	$endqt1 = mysql_real_escape_string($_POST["endqt1"]);
	$startqt1 = mysql_real_escape_string($_POST["strqt1"]);
	$price1 = substr(mysql_real_escape_string($_POST["price1"]),1);
	$startqt2 = mysql_real_escape_string($_POST["startqt2"]);
	$endqt2 = mysql_real_escape_string($_POST["endqt2"]);
	$price2 = substr(mysql_real_escape_string($_POST["price2"]),1);
	$startqt3 = mysql_real_escape_string($_POST["startqt3"]);
	$endqt3 = mysql_real_escape_string($_POST["endqt3"]);
	$price3 = substr(mysql_real_escape_string($_POST["price3"]),1);
	$startqt4 = mysql_real_escape_string($_POST["startqt4"]);
	$endqt4 = mysql_real_escape_string($_POST["endqt4"]);
	$price4 = substr(mysql_real_escape_string($_POST["price4"]),1);
	$nonsqpricing = substr(mysql_real_escape_string($_POST["nonsqpricing"]),1);
	$nsqprice1 = substr(mysql_real_escape_string($_POST["nsqprice1"]),1);
	$nsqprice2 =substr(mysql_real_escape_string($_POST["nsqprice2"]),1);
	$nsqprice3 = substr(mysql_real_escape_string($_POST["nsqprice3"]),1);
	$nsqprice4 = substr(mysql_real_escape_string($_POST["nsqprice4"]),1);
	$idOp = mysql_real_escape_string($_POST["idop"]);
	$pricingID ="";
	if(isset($_POST["pricingId"])){
		$pricingID = mysql_real_escape_string($_POST["pricingId"]);
	}
	
	$pricing = new pricing();
	$pricing->setIDOPTION($idOp);
	if($pricingID!=""){
		$pricing->setIDPRICING($pricingID);
	}
	$pricing->setNONMEMBER_PRICE($memberPricing);
	
	$pricing->setSTARTQT_1($startqt1);
	$pricing->setENDQT_1($endqt1);
	$pricing->setPRICE1($price1);
	
	$pricing->setSTARTQT_2($startqt2);
	$pricing->setENDQT_2($endqt2);
	$pricing->setPRICE2($price2);
	
	$pricing->setSTARTQT_3($startqt3);
	$pricing->setENDQT_3($endqt3);
	$pricing->setPRICE3($price3);
	
	$pricing->setSTARTQT_4($startqt4 );
	$pricing->setENDQT_4($endqt4);
	$pricing->setPRICE4($price4);
	
	if(isset($_POST["nonsqpricing"])){
		$pricing->setNONSEQUENCE_PRICE($nonsqpricing);
		$pricing->setNONSEQUENCE_PRICE1($nsqprice1);
		$pricing->setNONSEQUENCE_PRICE2($nsqprice2);
		$pricing->setNONSEQUENCE_PRICE3($nsqprice3);
		$pricing->setNONSEQUENCCE_PRICE4($nsqprice4);
	}
	
	$pricing->insert();
	
	
}
?>
<script type="text/javascript" src="./js/jquery-1.4.4.min.js"></script>
<script type="text/javascript" src="./js/jquery.cookie.js"></script>
<script type="text/javascript" src="./js/jquery.jeditable.js"></script>
<script>
function inlineedit(el){
	var id = el.id;
	$('.editable'+id).trigger('click');
}
function getDetails(el){
	$("#optionDetailsList").html("");
	var page = el.id;
	$.ajax({
  type: 'POST',
  url: "./imprint/inc_cimprint.php",
  data: "type="+page+"&idp=<?php echo mysql_real_escape_string($_GET['idp']);?>",
  success: success
  
});
	
}


function success(data){
			$("#optionsDiv").html(data);
}

function setTypeOptionsFields(el){
  var optionid = new String(el.id);
  var optionid = optionid.substring(4,optionid.length);
  var value = el.value;
  $.ajax({
  			type: 'POST',
			  url: "./imprint/inc_cimprint.php",
			  data: "type=setoptionNumber&optionId="+optionid+"&typeOp="+value,
			  success: successSetOptionList
		
	});
}
function successSetOptionList(data){
}
function confirmDeleteOption(el){
	var id = new String(el.id);
	id = id.substring(2,id.length);
	var rep = confirm("Confirm Delete Option");
	if(rep){
		$.ajax({
  			type: 'POST',
			  url: "./imprint/inc_cimprint.php",
			  data: "type=deleteOption&opid="+id,
			  success: successDeleteOption
		});
	}
}

function successDeleteOption(data){
	if(data=="delete.success"){
		window.location.href = "editimprint.php?idp=<?php echo mysql_real_escape_string($_GET['idp']);?>";
	}
}

function showEditOption(el){
	var id = new String(el.id);
	var id = id.substring(6,id.length);
	$("#optionDetailsList").load("editOption.php?idop="+id);
}

function successUploadFiles(){
	var id = document.getElementById("idOptionEdit").value;
	$("#optionDetailsList").load("editOption.php?idop="+id);
}

function updateImgNbr(el){
	var value = el.value;
	var id= new String(el.id);
	id = id.substring(3,id.length);
	$.ajax({
 		 type: 'POST',
  		 url: "./imprint/inc_options.php",
		 data: "type=updateOptionImgValue&idimg="+id+"&value="+value,
	     success: successUpdateImgNbr
  
	});
}
function successUpdateImgNbr(){
}

function updateImgSetupFee(el)
{
	var value=el.value;
	var id=new String(el.id);
	id=id.substring(4,id.length);
	$.ajax({
  		url: "./imprint/inc_options.php",
		type:"post",
		data:"type=updateImgSetupFeeValue&idop="+id+"&value="+value,
		success: function(data){
			$('#updateImgSetupFeeSuccessImg').html('<img src="images/tick_circle.png"/>');
			$('#updateImgSetupFeeSuccessImg').fadeIn("slow");
			$('#updateImgSetupFeeSuccessImg').fadeOut("slow");
		}
	});

}

function deleteImg(el){
	var value = el.value;
	var id= new String(el.id);
	id = id.substring(2,id.length);
	$.ajax({
 		 type: 'POST',
  		 url: "./imprint/inc_options.php",
		 data: "type=deleteImg&idimg="+id,
	     success: successDeleteImg
  
	});
}

function successDeleteImg(data){	
		var str = window.location.search;
		window.location.href = 'edit_option2.php'+str;	
}

function submitSqForm(){
	document.forms.sqForm.submit();
}

function updateOptionName(){
	document.forms.updateOptionNameForm.submit();
}
</script>
</head>
<body>
<!-- Master Div starts from here -->
<div class="Master_div">
  <!-- Header Div starts from here -->
  <div class="PD_header">
    <div class="upper_head"></div>
    <div class="navi">
      <?php include('includes/menu_main.php'); ?>
      <div class="clear"></div>
    </div>
  </div>
  <div class="PD_main_form1">
    <div class="">
      <table width="100%" cellspacing="1" cellpadding="5" border="0" align="center">
        <tbody>
          <tr>
            <td width="180" valign="top" align="left" class="setting"><table width="100%" border="0" cellpadding="5" cellspacing="1">
                <tr>
                  <td bgcolor="#000000"><a href="editimprint2.php?idp=<?php echo $_GET['idp'];?>"><span class="style9">Main Tab</span></a></td>
                </tr>
                <tr>
                  <td bgcolor="#000000"><a href="secondtabs.php?idp=<?php echo $_GET['idp'];?>"><span class="style9">Second Tab</span></a></td>
                </tr>
                <tr>
                  <td bgcolor="#000000"><a href="imprint.php"><span class="style9">Category</span></a></td>
                </tr>
                <tr>
                  <td bgcolor="#000000"><a href="optionsview.php?idp=<?php echo $_GET['idp'];?>"><span class="style9">Options</span></a></td>
                </tr>
                <tr>
                  <td bgcolor="#666666"><ul>
                      <li><span class="style1">Option Manager</span></li>
                    </ul></td>
                </tr>
                <tr>
                  <td bgcolor="#000000"><a href="colorsview.php?idp=<?php echo $_GET['idp'];?>"><span class="style1">Color Setting</span></a></td>
                </tr>
                <tr>
                  <td bgcolor="#000000"><a href="imprinttype.php?idp=<?php echo $_GET['idp'];?>"><span class="style1">Imprint Type</span></a></td>
                </tr>
              </table></td>
            <td valign="top" align="left"><table width="100%" border="0" cellpadding="3" cellspacing="0">
                <tr>
                  <td width="25%" align="left" bgcolor="#3399FF"><strong>Option Type</strong></td>
                  <td width="25%" bgcolor="#3399FF">
                  <?php include_once("./imprint/optiontype.class.php");
			 	 $optiontype = new optiontype();
				$options = $optiontype->readArray();
			  ?>
                  <select id="type<?php echo mysql_real_escape_string($_GET['idp']);?>" name="optionType" onchange="setTypeOptionsFields(this)" style="min-width:195px;">
                       <option selected="selected" name="optionType">Select One</option>
                            <?php
                  foreach( $options as $key => $value ){
					  ?>
                            <option value="<?php echo $value->getIDTYPE();?>" <?php if($value->getIDTYPE()==$impcategory_option->getIDTYPE()) echo "selected";?>><?php echo $value->getOPTIONTYPE();?></option>
                            <?php }?></select>
                  </td>
                  <td width="25%" align="right" bgcolor="#99CC00"><strong>Option Name</strong>
				  </td>
                  <td width="25%" align="left" bgcolor="#99CC00">
				  <form name="updateOptionNameForm" method="post" action="">
                  <input type="text" id="optionName" value='<?php $impcate_name=$impcategory_option->getOPTION_NAME(); echo $impcate_name; ?>'  name="optionNameUpdate" onblur="updateOptionName()"/>
                  <input type="hidden" name="idop" value="<?php echo mysql_real_escape_string($_GET['idop']);?>"/>
                  </form>
                  </td>
                </tr>
              </table>
              <table width="100%" border="0" cellpadding="10" cellspacing="0">
        <tr>
          <td width="80%" align="left" bgcolor="#CCCCCC"><span class="style3">Pricing</span></td>
          <td width="20%" align="left" bgcolor="#CCCCCC">
		  <form method="post" action="" name="sqForm">
            <input type="checkbox" id="sq<?php echo $_GET['idop'];?>" name="sequence" style="width:35px;" <?php if($impcategory_option->getNONSEQUENCE()==1) echo "checked='checked'";?> onclick="submitSqForm()" />
            <span class="style3">Non Sequence</span><input type="hidden" name="sqType" value="<?php echo mysql_real_escape_string($_GET['idop']);?>"/>
			</form>
			
			</td>
        </tr>
        <tr>
          <td>
            </td>
        </tr>
      </table>
      <form name="formPricing" method="post" action="">
          <table width="800" cellspacing="3" cellpadding="3" border="0" align="center">
            <tbody>
              <tr>
                <td width="50%" valign="top" bgcolor="#FFFFFF" align="center">&nbsp;</td>
                <td width="50%" valign="top" bgcolor="#E0E0E0" align="center"><span class="style2">Non Member</span></td>
                <td width="50%" valign="top" bgcolor="#F1F0AF" align="center"><input type="text" value="<?php  echo $pricing->getSTARTQT_1();?>" id="strqt1" class="smallbox" name="strqt1">
                  TO
                  <input type="text" value="<?php  echo $pricing->getENDQT_1();?>" id="endqt1" class="smallbox" name="endqt1"></td>
                <td width="50%" valign="top" bgcolor="#89D0FE" align="center"><input type="text" value="<?php  echo $pricing->getSTARTQT_2();?>" id="startqt2" class="smallbox" name="startqt2">
                  TO
                  <input type="text" value="<?php  echo $pricing->getENDQT_2();?>" id="endqt2" class="smallbox" name="endqt2"></td>
                <td width="50%" valign="top" bgcolor="#D1D1D1" align="center"><input type="text" value="<?php  echo $pricing->getSTARTQT_3();?>" id="startqt3" class="smallbox" name="startqt3">
                  TO
                  <input type="text" value="<?php  echo $pricing->getENDQT_3();?>" id="endqt3" class="smallbox" name="endqt3"></td>
                <td width="50%" valign="top" bgcolor="#FEDA00" align="center"><input type="text" value="<?php  echo $pricing->getSTARTQT_4();?>" id="startqt4" class="smallbox" name="startqt4">
                  TO
                  <input type="text" value="<?php  echo $pricing->getENDQT_4();?>" id="endqt4" class="smallbox" name="endqt4"><input type="hidden" name="idop" id="idOptionEdit" value="<?php echo mysql_real_escape_string($_GET['idop']);?>" /> <input type="hidden"  name="pricingId" value="<?php  echo $pricing->getIDPRICING();?>"/></td>
              </tr>
              <tr>
                <td valign="top" bgcolor="#FFFFFF" align="left">&nbsp;</td>
                <td valign="top" bgcolor="#F2F2F2" align="left"><input type="text" value="$<?php echo $pricing->getNONMEMBER_PRICE(); ?>" id="nmbprcing" class="100" name="nmbprcing"></td>
                <td valign="top" bgcolor="#F2F2F2" align="left"><input type="text" value="$<?php echo $pricing->getPRICE1(); ?>" id="price1" class="100" name="price1"></td>
                <td valign="top" bgcolor="#F2F2F2" align="left"><input type="text" value="$<?php echo $pricing->getPRICE2(); ?>" id="price2" class="100" name="price2"></td>
                <td valign="top" bgcolor="#F2F2F2" align="left"><input type="text" value="$<?php echo $pricing->getPRICE3(); ?>" id="price3" class="100" name="price3"></td>
                <td valign="top" bgcolor="#F2F2F2" align="left"><input type="text" value="$<?php echo $pricing->getPRICE4(); ?>" id="price4" class="100" name="price4"></td>
              </tr>
              <?php 
  if($impcategory_option->getNONSEQUENCE()==1){
	  ?>
              <tr>
                <td valign="middle" bgcolor="#F2F2F2" align="center"><strong>Non Sequence</strong></td>
                <td valign="top" bgcolor="#F2F2F2" align="left"><input type="text" value="$<?php echo $pricing->getNONSEQUENCE_PRICE();?>" id="nonsqpricing" class="100" name="nonsqpricing"></td>
                <td valign="top" bgcolor="#F2F2F2" align="left"><input type="text" value="$<?php echo $pricing->getNONSEQUENCE_PRICE1(); ?>" id="nsqprice1" class="100" name="nsqprice1"></td>
                <td valign="top" bgcolor="#F2F2F2" align="left"><input type="text" value="$<?php echo $pricing->getNONSEQUENCE_PRICE2(); ?>" id="nsqprice2" class="100" name="nsqprice2"></td>
                <td valign="top" bgcolor="#F2F2F2" align="left"><input type="text" value="$<?php echo $pricing->getNONSEQUENCE_PRICE3(); ?>" id="nsqprice3" class="100" name="nsqprice3"></td>
                <td valign="top" bgcolor="#F2F2F2" align="left"><input type="text" value="$<?php echo $pricing->getNONSEQUENCCE_PRICE4(); ?>" id="nsqprice4" class="100" name="nsqprice4"></td>
              </tr>
             
              <?php
  }
  ?>
   <tr>
                <td colspan="6" align="center" valign="middle" bgcolor="#F2F2F2"><input type="submit" value="Submit"/></td>
                </tr>
            </tbody>
          </table>
          </form>
              <table width="100%" border="0" cellpadding="5" cellspacing="0">
        <tr>
          <td align="left" valign="middle">&nbsp;</td>
          <td align="left" valign="middle">&nbsp;</td>
        </tr>
        <tr>
          <td align="left" valign="middle" bgcolor="#FF0000"><span class="style1">Setup Fee:</span> 
          <input  onblur="updateImgSetupFee(this)" name="textfield33" type="text" id="idop<?php echo mysql_real_escape_string($_GET['idop']);?>" value="<?php echo $pricing->getSetup_fee(); ?>" size="18" />		
		  <span id="updateImgSetupFeeSuccessImg"></span>
		  </td>
          <td align="left" valign="middle">&nbsp;</td>
        </tr>
        <tr>
          <td width="50%" align="left" valign="middle">&nbsp;</td>
          <td width="50%" align="left" valign="middle">&nbsp;</td>
        </tr>
        <tr>
          <td width="50%" align="left" valign="middle" bgcolor="#CCCCCC"><div align="left" class="style3">Colors</div></td>
          <td width="50%" align="right" valign="middle" bgcolor="#CCCCCC"><input type="submit" name="newbutton" id="newbutton" value="Add More Colors" /></td>
        </tr>
        
      </table>
      <table width="100%" border="0" cellpadding="5" cellspacing="0">
        <tr>
          <td align="left" valign="middle" bgcolor="#FF0000"><div align="center"><strong>Color</strong></div></td>
          <td width="32%" align="left" valign="middle" bgcolor="#FF0000"><div align="center"><strong>Option Image</strong></div></td>
          <td width="25%" align="left" valign="middle" bgcolor="#FF0000"><div align="center"><strong>Option Preview</strong></div></td>
          <td width="25%" align="left" valign="middle" bgcolor="#FF0000"><div align="center"><strong>Delete</strong></div></td>
        </tr>
          <?php 
		  	include_once("./imprint/options_color.class.php");
			include_once("./imprint/colors_images.class.php");
			$colors_images = new colors_images();
			$idop = mysql_real_escape_string($_GET['idop']);
			$params1 = array();
			$params1["idoption"]= $idop;
			$imgs = array();
			$imgs = $colors_images->readArray($params1);
			$ic=0;
			foreach($imgs as $keyc => $valuec){
				$cparams = array();
				$cparams["idcolor"] = $valuec->getidcolor();
				$options_color = new options_color();
				$options_color->readObject($cparams);
		
		?>
			
        <tr>
          <td width="18%" align="left" valign="middle" bgcolor="#F2F2F2"><div><?php echo $options_color->getname();?></div>
          </td>
          <td width="32%" align="left" valign="middle" bgcolor="#F2F2F2"><input name="fileField11" type="file" id="fileField34" size="5" maxlength="0" /></td>
          <td width="25%" align="left" valign="middle" bgcolor="#F2F2F2"><input name="fileField11" type="file" id="fileField35" size="5" /></td>
          <td width="25%" align="center" valign="middle" bgcolor="#F2F2F2"><form name="delcimgFrm<?php echo $ic;?>" id="delcimgFrm<?php echo $ic;?>" method="post" action=""><input type="hidden" name="delColorImg"  value="<?php echo $valuec->getid();?>"/></form><a href="#" onclick="document.forms.delcimgFrm<?php echo $ic;?>.submit()" style="color:#333333"> Delete</a></td>
        </tr>
        <?php
		$ic++;
			}
		  ?>
        </table>
      <div id="newSubTabsDiv" style="display:none">
          <form name="newSubTabs" id="newSubTabs" action="" method="post" enctype="multipart/form-data">
          <table width="100%" border="0" cellpadding="5" cellspacing="0">
          <tr>
          <?php 
		  $idoptionType=$impcategory_option->getIDTYPE();
		  $optiontype2 = new optiontype();
		  $oparam = array();
		  $oparam["IDTYPE"] = $idoptionType;
		  $optiontype2->readObject($oparam);
		  if($optiontype2->getimptype()=="Multiply Select"){
		  
		  ?>
          <td>Style Name</td><td><input type="text" name="styleName" value="" /></td>
          <?php }?>
          <td>Color</td>
          <td>
          <input type="hidden" name="idop" value="<?php echo $_GET['idop'];?>" />
          <select name="colorsList">
          <option value="-1">Select Color</option>
          <?php 
		  	include_once("./imprint/options_color.class.php");
			$params = array();
			$colorsList = array();
			$options_color = new options_color();
			$colorsList = $options_color->readArray($params);
			foreach($colorsList as $key => $value){
				echo "<option value='".$value->getidcolor()."'>".$value->getname()."</option>";
			}
			
		  ?>
          </select></td>
          <td>
          
          </td><td><input type="file" name="imgColor"/><input type="submit" value="submit"/></td>
          <td></td>
          </tr>
          </table>
          </form>
          </div>
<table width="800" cellspacing="3" cellpadding="3" border="0" align="center">
            <tbody>
              <tr>
                <td bgcolor="#000000"><span class="style1"><strong>Available Images</strong></span></td>
              </tr>
            </tbody>
          </table>
          <table width="800" cellspacing="3" cellpadding="3" border="0" align="center">
              <tbody>
                
                <?php

  $images = new images();
  $imagesList = array();
  $array = array();
  $array["IDOPTION"] = mysql_real_escape_string($_GET['idop']);
  $imagesList = $images->readArray($array);
  $nbr = sizeof($imagesList);

  $j=0;
  $k=0;
  $l=0;
   for($i=1;$i<=$nbr+1;$i++){
	   if($j==0)
	   echo "<tr>";
        if($j==4 || $j==$nbr) { echo "</tr><tr>";$j=0;}else {

?>
          <td width="20%" valign="middle"  bgcolor="#F2F2F2" align="center" style="height: 251px;">
          <table style="height: 100%; width: 100%;">
          <tbody>
          <tr><td>
                  <div style="height:130px;text-align:center">
                    <?php if(!isset($imagesList[$l])){echo $i;?>
                    <h1 style="color: #FF0000;font-size: 100px;font-family:Arial,Helvetica,sans-serif;margin-top:67px;margin-left:67px;"><?php echo $k;$k++;?></h1> 
                    <?php }else {
							echo "<img src='./imprint_files/".$imagesList[$l]->getIMAGEURL()."' style='max-width:100%;max-height:130px;'/>";
							
							
					   } ?>
                </div>
               </td>
               </tr>
               <tr>
               <td bgcolor="#99CC00" align="center"><input type="text" value="<?php echo $imagesList[$l]->getIMG_NUMBER()?>" id="img<?php echo $imagesList[$l]->getIDIMAGE();?>" class="100" name="img<?php echo $imagesList[$l]->getIDIMAGE();?>" onblur="updateImgNbr(this)"/>
               <span><img src="./images/delete-icon.png" onclick="deleteImg(this)" id="im<?php echo $imagesList[$l]->getIDIMAGE();?>" style="cursor:pointer"/></span> <?php $l++;?>
               </td>
               </tr>
                  </table>
              </td>
                  <?php $j++;?>
                  
                  <?php }?>
                  
                  <?php 
	
	}?></tr>
              </tbody></table>
              
              <div id="optionsDiv"></div>
              <div id="optionDetailsList"> </div></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>
<script>
 $("#newbutton").click(function () { 
      $("#newSubTabsDiv").show(); 
    });
  </script>
</body>
</html>