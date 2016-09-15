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
include("./imprint/optiontype.class.php");

if(isset($_POST["idtype"])){
	$idtype = mysql_real_escape_string($_POST["idtype"]);
	$type = mysql_real_escape_string($_POST["typeList"]);
	$typename = mysql_real_escape_string($_POST["typename"]);
	$optiontype = new optiontype();
	$optiontype->setIDTYPE($idtype);
	$optiontype->setOPTIONTYPE($typename);
	$optiontype->setimptype($type);
	if(isset($_POST["nameFields"]))
		$optiontype->setnameFields("1");
	else
		$optiontype->setnameFields("0");
	if(isset($_POST["numberFields"]))
		$optiontype->setnumberFields("1");
	else
		$optiontype->setnumberFields("0");

	$optiontype->insert();
}

if(isset($_POST["newImpType"])){
	$newtype = mysql_real_escape_string($_POST["newImpType"]);
	$type = mysql_real_escape_string($_POST["typeList2"]);
	$optiontype = new optiontype();
	$optiontype->setOPTIONTYPE($newtype);
	$optiontype->setimptype($type);
	$optiontype->insert();
}
if(isset($_GET["del"])){
	$id = mysql_real_escape_string($_GET['del']);
	$params = array();
	$params["IDTYPE"] = $id;
	$optiontype = new optiontype();
	$optiontype->delete($params);
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

function submitImpOption(el){
	var id = new String(el.id);
	id = id.substring(1,id.length);
	document.forms[id].submit();
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
                  <td bgcolor="#000000"><a href="editimprint2.php?idp=<?php echo mysql_real_escape_string($_GET['idp']);?>"><span class="style9">Main Tab</span></a></td>
                </tr>
                <tr>
                  <td bgcolor="#000000"><a href="secondtabs.php?idp=<?php echo mysql_real_escape_string($_GET['idp']);?>"><span class="style9">Second Tab</span></a></td>
                </tr>
                <tr>
                  <td bgcolor="#000000"><a href="imprint.php"><span class="style9">Category</span></a></td>
                </tr>
                <tr>
                  <td bgcolor="#000000"><a href="optionsview.php?idp=<?php echo mysql_real_escape_string($_GET['idp']);?>"><span class="style9">Options</span></a></td>
                </tr>
                <tr>
                  <td bgcolor="#000000"><a href="colorsview.php?idp=<?php echo mysql_real_escape_string($_GET['idp']);?>"><span class="style1">Color Setting</span></a></td>
                </tr>
                <tr>
                  <td bgcolor="#666666"><a href="imprinttype.php?idp=<?php echo mysql_real_escape_string($_GET['idp']);?>"><span class="style1">Imprint Type</span></a></td>
                </tr>
              </table></td>
            <td valign="top" align="left"><h1 align="center">Imprint Type</h1>
              <div>
                <table width="100%" border="0" align="center" cellpadding="5" cellspacing="0">
      <tr>
        <td width="50%" align="left" bgcolor="#CCCCCC"><strong>Available Imprint Type</strong></td>
        <td width="50%" align="right" bgcolor="#CCCCCC"><input type="submit" name="button13" id="button13" value="Save and Updates" onclick="addNewImpType()"/>
        <input type="submit" name="button11" id="newImpType" value="Add More Types" /></td>
      </tr>
    </table>
                <table width="100%" border="0" cellpadding="5" cellspacing="0">
          <tr>
            <td width="29%" align="left" valign="middle" bgcolor="#3399FF"><div align="center"><strong> Name</strong></div></td>
            <td width="43%" align="left" valign="middle" bgcolor="#3399FF"><div align="center"><strong>Replace or Upload New Icon</strong></div></td>
            <td width="28%" align="left" valign="middle" bgcolor="#3399FF"><div align="center"><strong>Action</strong></div></td>
          </tr>
          <?php 
		  $optiontype = new optiontype();
		  $optionTypesList = array();
		  $params = array();
		  $optionTypesList = $optiontype->readArray($params);
		  $i=0;
	
		  foreach($optionTypesList as $key => $value){
			  if($i%2!=0)
			  	$bgcolor = "#fff";
			  else
			    $bgcolor = "#F2F2F2";
		  ?>
          <tr>
            <td width="29%" align="left" valign="middle" bgcolor="<?php echo $bgcolor;?>"><div id="div<?php echo $value->getIDTYPE()?>" class="editable<?php echo $value->getIDTYPE();?>"><?php echo $value->getOPTIONTYPE();?></div></td>
            <td width="50%" align="left" valign="middle" bgcolor="<?php echo $bgcolor;?>">
            <form id="f<?php echo $i;?>" name="typeList" method="post" action="">
            <input type="hidden" name="idtype" value="<?php echo $value->getIDTYPE()?>" />
            <input type="hidden" name="typename" value="<?php echo $value->getOPTIONTYPE()?>" />
            <select name="typeList" id="i<?php echo $i;?>" onchange="submitImpOption(this);"> 
              <option>Select One</option>
              <option <?php if($value->getimptype()=="Single Select") echo "selected";?> value="Single Select">Single Select</option>
              <option value="Multiply Select" <?php if($value->getimptype()=="Multiply Select") echo "selected";?>>Multiply Select</option>
              </select>
              <input type="checkbox" name="nameFields" <?php if($value->getnameFields()=="1") echo "checked='checked'"?> id="c<?php echo $i;?>" onclick="submitImpOption(this);"/>Show Name Fields <input type="checkbox" name="numberFields" <?php if($value->getnumberFields()=="1") echo "checked='checked'"?> id="c<?php echo $i;?>" onclick="submitImpOption(this);"/>Show Number Fields
              </form></td>
            <td width="28%" align="center" valign="middle" bgcolor="<?php echo $bgcolor;?>"><a href="#" id="<?php echo $value->getIDTYPE()?>" onclick="inlineedit(this)" style="color:#333333">Edit</a> | <a style="color:#333333" id="d<?php echo $value->getIDTYPE() ?>" onclick="deleteType(this)" href="#">Delete</a></td>
          </tr>
          <?php 
		     $i++;
		     echo "<script> $('.editable".$value->getIDTYPE()."').editable('./imprint/saveimprints.php');</script>";
		  }?>
          </table>
          <div id="newImpTypesDiv" style="display:none">
          <form name="newSubTabs" id="newSubTabs" action="" method="post">
          <table border="0">
          <tr>
          <td>Name</td><td><input type="text" name="newImpType" /></td>
          <td><select name="typeList2"> 
              <option>Select One</option>
              <option value="Single Select">Single Select</option>
              <option value="Multiply Select">Multiply Select</option>
              </select></td>
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
 $("#newImpType").click(function () { 
      $("#newImpTypesDiv").show(); 
    });
 function addNewImpType(){
   document.forms.newSubTabs.submit();
 }
 
 function deleteType(el){
 	var id = el.id;
	id = id.substring(1,id.length);
	cf = confirm("Delete Imprint Type ? ");
	if(cf){
		window.location.href = 'imprinttype.php?del='+id;
	}
 }
  </script>
</body>
</html>