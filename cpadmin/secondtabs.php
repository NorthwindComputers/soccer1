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
include("./imprint/imp_category_tabs.class.php");

if(isset($_GET["idp"])){
	$cimprint_category = new cimprint_category();
	$array = array();
	$array["IDCATEGORY"] = $_GET['idp'];
	$cimprint_category->readObject($array);

$impcategory_option = new impcategory_option();
$optionsList = array();
$optionsList = $impcategory_option->readArray($array);

}
if(isset($_GET["del"])){
  $id = $_GET['del'];
  $dimp_category_tabs = new imp_category_tabs();
  $dparams = array();
  $dparams["id_tab"] = $id;
  $dimp_category_tabs->delete($dparams);
}
if(isset($_POST["newSubTab"])){
	if(!isset($_SESSION["token"])){
		$newsubtab = $_POST["newSubTab"];
		$parent_tab = $_POST["parentTab"];
  		$imprint_categ_id = $_GET['idp'];
		$nimp_category_tabs = new imp_category_tabs();
		$nimp_category_tabs->settab_name($newsubtab);
		$nimp_category_tabs->setimprint_categ_id($imprint_categ_id);
		$nimp_category_tabs->settab_parent($parent_tab);
		$_SESSION["token"] = $_POST["token"];
		$nimp_category_tabs->insert();
	}else {
		if($_POST["token"]!=$_SESSION["token"]){
			$newsubtab = $_POST["newSubTab"];
		$parent_tab = $_POST["parentTab"];
  		$imprint_categ_id = $_GET['idp'];
		$nimp_category_tabs = new imp_category_tabs();
		$nimp_category_tabs->settab_name($newsubtab);
		$nimp_category_tabs->setimprint_categ_id($imprint_categ_id);
		$nimp_category_tabs->settab_parent($parent_tab);
		$_SESSION["token"] = $_POST["token"];
		$nimp_category_tabs->insert();
		}
	}
}

if(isset($_POST["categoryNameEdit"])){
	$category = $_POST["categoryNameEdit"];
	$adminNotes = $_POST["adminNotes"];
	$idCategory = $_POST["categoryID"];
	$cimprint_category = new cimprint_category();
	$cimprint_category->setIDCATEGORY($idCategory);
	$cimprint_category->setCATEGORY($category);
	$cimprint_category->setADMIN_NOTES($adminNotes);
	$cimprint_category->insert();
}

if(isset($_POST["formType"])){
	$optionType = $_POST["optionType"];
	include_once("./imprint/impoption_settings.class.php");
	
	$idcategory= $_GET['idp'];
	if($optionType==1){
		$optionName=$_POST["optionName"];
		$sequence = 0;
		if(isset($_POST["sequence"]))
			$sequence=1;
		$nbrImgToUpload = $_POST["nbrImg"];
		$nbrColors = $_POST["nbrColors"];
		$adminNotes = $_POST["adminNotes"];
		$idop = $_POST["optionId"];
		
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
		$logoType = $_POST["logoType"];
		$customerGroup = "";
		if(isset($_POST["CustomerGroup"]))
			$customerGroup = $_POST["CustomerGroup"];
		$adminNotes = $_POST["adminNotes"];
		$idop = $_POST["optionId"];
		$optionName=$_POST["optionName"];
		
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
	$optionType = $_POST["optionType"];
	include_once("./imprint/impoption_settings.class.php");
	
	$idcategory= $_GET['idp'];
	if($optionType==1){
		$optionName=$_POST["optionName"];
		$sequence = 0;
		if(isset($_POST["sequence"]))
			$sequence=1;
		$nbrImgToUpload = $_POST["nbrImg"];
		$nbrColors = $_POST["nbrColors"];
		$adminNotes = $_POST["adminNotes"];
		$idop = $_POST["optionId"];
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
		$optionName=$_POST["optionName"];
		$sequence = 0;
		if(isset($_POST["sequence"]))
			$sequence=1;
	
		$adminNotes = $_POST["adminNotes"];
		$idop = $_POST["optionId"];
		$logoType = $_POST["logoType"];
		$customerGroup = "";
		if(isset($_POST["CustomerGroup"]))
			$customerGroup = $_POST["CustomerGroup"];
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
	
		$images->setIDOPTION($_POST["idop"]);
		$images->setIMAGEURL($name);
		$images->insert();

	} else{
    	echo "There was an error uploading the file, please try again!";
	}
		
	
}

if(isset($_FILES["frontEndPreview"])){
	include_once("./imprint/impoption_settings.class.php");
	$idOption = $_POST["idop"];
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
	$startqt1 = $_POST["strtqt1"];
	$memberPricing = substr($_POST["nmbprcing"],1);
	$endqt1 = $_POST["endqt1"];
	$startqt1 = $_POST["strtqt1"];
	$price1 = substr($_POST["price1"],1);
	$startqt2 = $_POST["startqt2"];
	$endqt2 = $_POST["endqt2"];
	$price2 = substr($_POST["price2"],1);
	$startqt3 = $_POST["startqt3"];
	$endqt3 = $_POST["endqt3"];
	$price3 = substr($_POST["price3"],1);
	$startqt4 = $_POST["startqt4"];
	$endqt4 = $_POST["endqt4"];
	$price4 = substr($_POST["price4"],1);
	$nonsqpricing = substr($_POST["nonsqpricing"],1);
	$nsqprice1 = substr($_POST["nsqprice1"],1);
	$nsqprice2 =substr( $_POST["nsqprice2"],1);
	$nsqprice3 = substr($_POST["nsqprice3"],1);
	$nsqprice4 = substr($_POST["nsqprice4"],1);
	$idOp = $_POST["idop"];
	$pricingID ="";
	if(isset($_POST["pricingId"])){
		$pricingID = $_POST["pricingId"];
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
  data: "type="+page+"&idp=<?php echo $_GET['idp'];?>",
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
		window.location.href = "editimprint.php?idp=<?php echo $_GET['idp'];?>";
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
                  <td bgcolor="#666666"><span class="style9">Second Tab</span></td>
                </tr>
                <tr>
                  <td bgcolor="#000000"><a href="imprint.php"><span class="style9">Category</span></a></td>
                </tr>
                <tr>
                  <td bgcolor="#000000"><a href="optionsview.php?idp=<?php echo $_GET['idp'];?>"><span class="style9">Options</span></a></td>
                </tr>
                <tr>
                  <td bgcolor="#000000"><a href="colorsview.php?idp=<?php echo $_GET['idp'];?>"><span class="style1">Color Setting</span></a></td>
                </tr>
                <tr>
                  <td bgcolor="#000000"><a href="imprinttype.php?idp=<?php echo $_GET['idp'];?>"><span class="style1">Imprint Type</span></a></td>
                </tr>
              </table></td>
            <td valign="top" align="left"><form action="" method="post">
                <table width="800" cellspacing="3" cellpadding="3" border="0" align="center">
                  <tbody>
                    <tr>
                      <td bgcolor="#66CCCC"><div style="width: 200px; float: right; text-align: right;">
                          <input type="submit" value="submit"/>
                        </div></td>
                    </tr>
                  </tbody>
                </table>
                <table width="800" cellspacing="3" cellpadding="3" border="0" align="center">
                  <tbody>
                    <tr>
                      <td width="50%" valign="top" bgcolor="#F2F2F2" align="left"><table width="100%" cellspacing="0" cellpadding="3" border="0">
                          <tbody>
                            <tr>
                              <td width="50%" height="35"><strong>Category ID</strong></td>
                              <td width="50%" height="35"><input type="text" value="<?php echo $cimprint_category->getIDCATEGORY();?>" id="textfield13" class="melbox" name="categoryID" readonly="readonly"></td>
                            </tr>
                            <tr>
                              <td width="50%" height="35"><strong>Category Name</strong></td>
                              <td width="50%" height="35"><input type="text" value="<?php echo $cimprint_category->getCATEGORY(); ?>" id="textfield17" class="100" name="categoryNameEdit"></td>
                            </tr>
                            <tr>
                              <td width="50%" height="35"><strong>Admin Notes</strong></td>
                              <td width="50%" height="35"><input type="text" value="<?php echo $cimprint_category->getADMIN_NOTES();?>" id="textfield18" class="100" name="adminNotes"></td>
                            </tr>
                          </tbody>
                        </table></td>
                      <td width="50%" valign="top" bgcolor="#F2F2F2" align="left">&nbsp;</td>
                    </tr>
                  </tbody>
                </table>
              </form>
              <h1 align="center">SECOND TAB SETTING</h1>
              <div>
                <table width="100%" border="0" align="center" cellpadding="5" cellspacing="0">
      <tr>
        <td width="50%" align="left" bgcolor="#CCCCCC"><strong>Available Sub-Tabs</strong></td>
        <td width="50%" align="right" bgcolor="#CCCCCC"><input type="submit" name="button9" id="button9" value="Save &amp; Updates"  onclick="document.forms.newSubTabs.submit()"/>
        <input  type="submit" name="button" id="newbutton" value="Add New Tab"/></td>
      </tr>
    </table>
                <table width="100%" border="0" cellpadding="5" cellspacing="0">
          <tr>
            <td width="9%" bgcolor="#FF0000"><div align="center"><strong>ID</strong></div></td>
            <td width="37%" bgcolor="#FF0000"><div align="center"><strong>Second Tab</strong></div></td>
            <td width="28%" bgcolor="#FF0000"><div align="center"><strong>Main Tab</strong></div></td>
            <td width="26%" bgcolor="#FF0000"><div align="center"><strong>Action</strong></div></td>
          </tr>
          <?php 
		     $imp_category_tabs4 = new imp_category_tabs();
			 $params1 = array();
			 $params1["imprint_categ_id"] = $_GET['idp'];
			 $params1["is_parent"]="0";
			 
			 $subTabs = array();
			 $subTabs = $imp_category_tabs4->readArray($params1);
			 $i=1;
		$bgcolor ="#F2F2F2";

		if(count($subTabs)!=0){
		foreach( $subTabs as $key4 => $value4){
			if(($i%2)==0){
				$bgcolor="#fff";
			}else{
				$bgcolor ="#F2F2F2";
			}
			
		  ?>
          
          <tr>
      <td align="center" bgcolor="<?php echo $bgcolor;?>"><?php echo $value4->getid_tab();?></td>
      <td bgcolor="<?php echo $bgcolor;?>"><div class="editable<?php echo $value4->getid_tab();?>" id="t<?php echo $value4->getid_tab();?>"><?php echo $value4->gettab_name();?></div></td>
      
      <td bgcolor="<?php echo $bgcolor;?>"><select name="parentTab"><option value="-1">Select One</option>
	  <?php 
	     $imp_category_tabs3 = new imp_category_tabs();
		 $params3 = array();
		 $params3["imprint_categ_id"] = $_GET['idp'];
		 $params3["is_parent"]=1;
		 $parentTabs = array();
		 $parentTabs = $imp_category_tabs3->readArray($params3);
		 foreach( $parentTabs as $key1 => $value2){
			 $selected ="";
			 if($value4->gettab_parent()==$value2->getid_tab())
			   $selected="selected='selected'";
		  ?>
          <option value="<?php echo $value2->getid_tab();?>" <?php echo $selected;?>><?php echo $value2->gettab_name();?></option>
          <?php 
		 }
		  
			 
	  ?></select></td>
      
      
      <td align="center" bgcolor="<?php echo $bgcolor;?>"><a href="#" onclick="inlineedit(this)" id="<?php echo $value4->getid_tab();?>" style="color:#333333">Edit</a>|<a href="secondtabs.php?idp=<?php echo $_GET['idp'];?>&del=<?php echo $value4->getid_tab();?>" style="color:#333333">Delete</a></td>      
      </tr>

          <?php $i++;
		  echo "<script> $('.editable".$value4->getid_tab()."').editable('./imprint/savesubtabs.php');</script>";
		  }}?>
          </table>
          <div id="newSubTabsDiv" style="display:none">
          <form name="newSubTabs" id="newSubTabs" action="" method="post">
          <table width="100%" border="0" cellpadding="5" cellspacing="0">
          <tr>
          <td>&nbsp;</td><td>Sub Tab</td><td><input type="hidden" name="token" value="<?php echo rand();?>" /><input type="text" name="newSubTab" /><input type="hidden" name="idp" value="<?php echo $_GET['idp'];?>"/></td><td>
          <select name="parentTab"><option value="-1">Select One</option>
	  <?php 
	     $imp_category_tabs5 = new imp_category_tabs();
		 $params3 = array();
		 $params3["imprint_categ_id"] = $_GET['idp'];
		 $params3["is_parent"]=1;
		 $parentTabs2 = array();
		 $parentTabs2 = $imp_category_tabs5->readArray($params3);
		 foreach( $parentTabs2 as $key2 => $value2){
			 $selected ="";
			
		  ?>
          <option value="<?php echo $value2->getid_tab();?>" <?php echo $selected;?>><?php echo $value2->gettab_name();?></option>
          <?php 
		 }
		  
			 
	  ?></select>
          </td><td>&nbsp;</td>
          </tr>
          </table>
          </form>
          </div>
              </div>
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