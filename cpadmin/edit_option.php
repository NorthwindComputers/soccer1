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
<link href="./imprint/uploadify/uploadify.css" type="text/css" rel="stylesheet" />
    <script type="text/javascript" src="./imprint/uploadify/jquery-1.4.2.min.js"></script>
    <script type="text/javascript" src="./imprint/uploadify/swfobject.js"></script>
    <script type="text/javascript" src="./imprint/uploadify/jquery.uploadify.v2.1.4.min.js"></script>
    <script type="text/javascript">
    $(document).ready(function() {
      $('#file').uploadify({
        'uploader'  : './imprint/uploadify/uploadify.swf',
        'script'    : './imprint/uploadify/uploadify.php?idop=<?php echo $_GET["idop"];?>',
        'cancelImg' : './imprint/uploadify/cancel.png',
        'folder'    : '/cpadmin/imprint_files/',
        'auto'      : true,
		'multi'       : true,
		'onAllComplete' : successUploadFiles
      });
    });
    </script>
    <script>
	function successUploadFiles(){
		var str = window.location.search;
		window.location.href = 'edit_option.php'+str;
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
		window.location.href = 'edit_option.php'+str;
	
}
	</script>
<style type="text/css">
<!--
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
	font-size: 100px;
	color: #FF0000;
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
#menu_nav li{
	 color: #000000;
    line-height: 20px;
    margin-left: 10px;
    padding: 5px;
	list-style:none outside none;
}
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
if(isset($_GET["idop"]) || isset($_SESSION["idop"])){
       	if(isset($_GET["idop"]))
	      $_SESSION["idop"] = $_GET["idop"];

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

if(isset($_FILES["file"])){
	$name =  $_FILES['file']['name'];
	$target_path = "./imprint_files/";
	echo $name;
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

if(isset($_POST["imgId"])){
	$images = new images();
	$array = array();
	$array["IDIMAGE"] = mysql_real_escape_string($_POST["imgId"]);
	$images->delete($array);
}
if(isset($_POST["formType"])){
	$optionType = mysql_real_escape_string($_POST["optionType"]);
	include_once("./imprint/impoption_settings.class.php");
	
	$idcategory = mysql_real_escape_string($_POST["categoId"]);
	if($optionType ==1 ) {
		$optionName = mysql_real_escape_string($_POST["optionName"]);
		$sequence = 0;
		if(isset($_POST["sequence"]))
			$sequence = 1;
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
	if($optionType == 3) {
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
else {

if(isset($_POST["optionType"])){
	$optionType = mysql_real_escape_string($_POST["optionType"]);
	include_once("./imprint/impoption_settings.class.php");
	
	$idcategory = mysql_real_escape_string($_GET["idop"]);
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
	$startqt1 = mysql_real_escape_string($_POST["strqt1"]);
	$memberPricing = substr($_POST["nmbprcing"],1);
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
<script>
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

function getTypeOptionsFields(el){
	var optionNumber = el.value;
	if(optionNumber==1){
		$.ajax({
  			type: 'POST',
			  url: "./imprint/inc_cimprint.php",
			  data: "type=optionNumber",
			  success: successOptionList
		});
	}
	
	if(optionNumber==3){
		$.ajax({
  			type: 'POST',
			  url: "./imprint/inc_cimprint.php",
			  data: "type=optionLogo",
			  success: successOptionList
		});
	}
	
}
function successOptionList(data){
	$("#optionDetails").html(data);
}


function getCustomerGroupLogo(el){
	var logoType = el.value;
	
		$.ajax({
  			type: 'POST',
			  url: "./imprint/inc_cimprint.php",
			  data: "type=getCustomerGroupLogo&logoType="+logoType,
			  success: successCustomerGroup
		});
	
}
function successCustomerGroup(data){
	$("#customerGroupLogo").html(data);
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
</script>
</head>
<body>
<div class="Master_div"> 
      <!-- Header Div starts from here -->
    	<div class="PD_header">
    		<div class="upper_head"></div>
    		<div class="navi">
          		<?php include('includes/menu_main.php'); ?>
          	<div class="clear"></div>
        	</div>
  		</div>
        <div class="PD_main_form">
        	<div style="width:150px;float:left">
		        <ul id="menu_nav" style="background:none repeat scroll 0 0 #E9E9E9;border:1px solid #CFCFCF;height:400px;">
        			<li><a href="imprint.php">Existing Category</a></li>
			         <li><a href="addimprint.php">Add New Category</a></li>
			         <li><a href="optionslist.php">Existing Options</a></li>
			       
         
			        </ul>
			 </div>
<div style="float:left">
  <table width="800" cellspacing="1" cellpadding="5" border="0" align="center">
    <tr>
      <td valign="top" align="left"><form action="" method="post">
          <input type="hidden" name="formType" value="editOption"/>
          <table width="800" cellspacing="3" cellpadding="3" border="0" align="center">
            <tbody>
              <tr>
                <td bgcolor="#66CCCC"><div style="width:150px;float:left;"><h2><span class="style1"><strong>Option/Edit</strong></span></h2></div>
                <div style="text-align:right;width:638px;float:left"><input type="submit" value="Save"/></div>
                </td>
              </tr>
            </tbody>
          </table>
          <table width="800" cellspacing="3" cellpadding="3" border="0" align="center">
            <tbody>
              <tr>
                <td width="50%" valign="top" bgcolor="#F2F2F2" align="left"><table width="100%" cellspacing="0" cellpadding="3" border="0">
                    <tbody>
                      <tr>
                        <td width="33%"><strong>Option Type</strong></td>
                        <td width="33%"><?php include_once("./imprint/optiontype.class.php");
			 	 $optiontype = new optiontype();
				$options = $optiontype->readArray();
			  ?>
                          <select id="optionType" class="melbox" name="optionType" onchange="getTypeOptionsFields(this)">
                            <option selected="selected" name="optionType">Select One</option>
                            <?php
                  foreach( $options as $key => $value ){
					  ?>
                            <option value="<?php echo $value->getIDTYPE();?>" <?php if($value->getIDTYPE()==$impcategory_option->getIDTYPE()) echo "selected";?>><?php echo $value->getOPTIONTYPE();?></option>
                            <?php }?>
                          </select></td>
                        <td width="33%"><input type="text" value="" id="textfield27" class="melbox" name="textfield34">
                          <input type="hidden" name="optionId" value="<?php echo $_GET['idop']; ?>"/></td>
                      </tr>
                      <tr>
                        <td width="50%" height="35"><strong>Option  Name</strong></td>
                        <td width="50%" height="35"><input type="text" value="<?php echo $impcategory_option->getOPTION_NAME(); ?>" id="optionName" class="100" name="optionName"></td>
                      </tr>
                      <tr>
                        <td width="50%" height="35"><strong>Admin Notes</strong></td>
                        <td width="50%" height="35"><input type="text" value="<?php echo $impcategory_option->getADMIN_NOTES();?>" id="adminNotes" class="100" name="adminNotes">
                        <input type="hidden" value="<?php echo $impcategory_option->getIDCATEGORY();?>" name="categoId" />
                        </td>
                      </tr>
                    </tbody>
                  </table></td>
                <td width="50%" valign="top" bgcolor="#F2F2F2" align="left"><div id="optionDetails">
                	<?php 
						if($impcategory_option->getIDTYPE()==1){
						?>	    
							<table width="100%">
							    <tr>
							   	  <td><strong><input type="checkbox" id="squence" name="sequence" style="width:35px;" <?php if($impcategory_option->getNONSEQUENCE()==1) echo "checked='checked'";?>>Non Sequence</strong></td>
	   					         <td></td>
								</tr>   
							    <tr>
								    	<td><strong>Number of Availble Colors</strong></td>
										<td><input type="text" name="nbrColors" value="<?php echo $impoption_settings->getCOLORS_NBR()?>"/></td>
								</tr>   
						    </table>
    <?php
						}
						if($impcategory_option->getIDTYPE()==3){
							?>
                            
                         <table width="100%">
   								 <tr>
   	  								 <td><strong>Logo</strong></td>
       							 <td>
       										<select name="logoType"  id="logoType" onchange="getCustomerGroupLogo(this)">
       											<option value="0">Select</option>
       											<option value="1" <?php if($impcategory_option->getlogoType()==1) echo 'selected';?>>Soccer One</option>
										        <option value="2" <?php if($impcategory_option->getlogoType()==2) echo 'selected';?>>For Any Group</option>
									       </select>
       <div id="customerGroupLogo">
       <select id="CustomerGroup" name="CustomerGroup">
                    	<option value="">Select Customer Group...</option>
       <?php 
	   
	    require './includes/db.php';
			$sql_cg = "SELECT GroupName FROM customer_group ORDER BY GroupName";
							$result_cg = mysql_query($sql_cg);
							
							while($row_cg = mysql_fetch_array($result_cg)) {
								if($row_cg["GroupName"] == $impcategory_option->getCustomerGroup()) {
									$selected = ' selected="selected" ';
								} else {
									$selected = '';
								}
								echo "<option value=\"$row_cg[GroupName]\" $selected>$row_cg[GroupName]</option>";
							}
							?>
                            </select>
	   
       </div>
       </td>
	</tr>   
   
    </table>

                            <?php
							
						}
					
					?>
                </div>
                  </td>
              </tr>
            </tbody>
          </table>
        </form>
        <div id="optionsDiv">
          <table width="800" cellspacing="3" cellpadding="3" border="0" align="center">
            <tbody>
              <tr>
                <td width="50%" valign="top" bgcolor="#000000" align="left"><span class="style4">Pricing</span></td>
              </tr>
            </tbody>
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
                  <input type="text" value="<?php  echo $pricing->getENDQT_4();?>" id="endqt4" class="smallbox" name="endqt4"><input type="hidden" name="idop" id="idOptionEdit" value="<?php echo $_GET['idop'];?>" /> <input type="hidden"  name="pricingId" value="<?php  echo $pricing->getIDPRICING();?>"/></td>
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
          <table width="800" cellspacing="3" cellpadding="3" border="0" align="center">
            <tbody>
              <tr>
                <td bgcolor="#000000"><span class="style1"><strong>Option Setting</strong></span></td>
              </tr>
            </tbody>
          </table>
          <table width="800" cellspacing="3" cellpadding="3" border="0" align="center">
            <tbody>
              <tr>
                <td valign="middle" bgcolor="#E8E8E8" align="left"><strong>Name to Display &amp; Available Colors</strong></td>
                <td valign="middle" bgcolor="#E8E8E8" align="left"><strong>Upload Image</strong></td>
                <td valign="middle" bgcolor="#E8E8E8" align="left"><strong>Frontend Preview</strong></td>
              </tr>
              <tr>
                <td width="33%" valign="top" bgcolor="#FFFFFF" align="left"><input type="text" value="<?php echo $impoption_settings->getDISPLAY_NAME(); ?>" id="nameToDisplay" class="melbox" name="nameToDisplay">
                  <input type="text" value="<?php echo $impoption_settings->getCOLORS_NBR();?>" id="nbColorAv" class="smallbox" name="nbColorAv"></td>
              <td width="33%" valign="top" bgcolor="#FFFFFF" align="center"><form name="frm<?php echo $j;?>" method="post" action="" enctype="multipart/form-data"><input type="file" name="file" id="file"/>
                <input type="hidden" name="idop" value="<?php echo $impcategory_option->getIDOPTION();?>"/>
                      </form> </td>
                <td width="33%" valign="top" bgcolor="#FFFFFF" align="left">
                <form name="frontEndPrev" action="" method="post" enctype="multipart/form-data"> 
                <input type="file" id="fileField" name="frontEndPreview" />
                <input type="hidden" name="idop" value="<?php echo $impcategory_option->getIDOPTION();?>"/>
                <input type="submit" value="submit" />
                </form>
                </td>
              </tr>
              <tr>
                <td width="33%" valign="middle" bgcolor="#FFFFFF" align="center">&nbsp;</td>
                <td width="33%" valign="middle" bgcolor="#FFFFFF" align="center">&nbsp;</td>
                <td width="33%" valign="middle" bgcolor="#FFFFFF" align="center"><img src="./imprint_files/<?php echo $impoption_settings->getFRONTEND_PREVIEW();?>" alt="http://techogroup.com/puma.jpg" style="width:288px;height:78px;"/></td>
              </tr>
            </tbody>
          </table>
     
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
  $array["IDOPTION"] = mysql_real_escape_string($_GET["idop"]);
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
               <td bgcolor="#CCCCCC" align="center"><input type="text" value="<?php echo $imagesList[$l]->getIMG_NUMBER()?>" id="img<?php echo $imagesList[$l]->getIDIMAGE();?>" class="100" name="img<?php echo $imagesList[$l]->getIDIMAGE();?>" onblur="updateImgNbr(this)"/>
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
        </div>
        </td>
      </tr>
      </table>

        </div></td>
    </tr>
  </table>
  
</div>
</div>
</div>
</body>
</html>

