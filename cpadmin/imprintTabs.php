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
include("./imprint/imprint_tabs.class.php");
include("./imprint/imp_category_tabs.class.php");
include("./imprint/cimprint_category.class.php");

if(isset($_GET["idc"])){
	$imprint_tabs = new imprint_tabs();
	
	$cimprint_category = new cimprint_category();
	$array1 = array();
	$array1["IDCATEGORY"] = mysql_real_escape_string($_GET["idc"]);
	$cimprint_category->readObject($array1);
	
	$array = array();
	
	$array["imprint_categ_id"] = mysql_real_escape_string($_GET['idc']);
	
	$imprint_tabs->readObject($array);

	$imp_category_tabs = new imp_category_tabs();
	$tabsList = array();
	$tabsList = $imp_category_tabs->readArray($array);

}

if(isset($_POST["numTabs"])){
	$imprint_tabs = new imprint_tabs();
	$array = array();
	
	$array["imprint_categ_id"] = mysql_real_escape_string($_POST["categoryID"]);
	
	$imprint_tabs->readObject($array);
	
	$imprint_tabs->setnb_tabs(mysql_real_escape_string($_POST["numTabs"]));
	$imprint_tabs->setimprint_categ_id(mysql_real_escape_string($_POST["categoryID"]));
	$imprint_tabs->insert();
}

if(isset($_POST["idCateg"])){
	$idcateg = mysql_real_escape_string($_POST["idCateg"]);
	for($j=0;$j<sizeof(mysql_real_escape_string($_POST["tabName"]));$j++){
		$imp_category_tabs = new imp_category_tabs();
		$imp_category_tabs->setimprint_categ_id($idcateg);
		$imp_category_tabs->settab_name(mysql_real_escape_string($_POST["tabName"])[$j]);
		$parentTabs=1;
		if(!isset(mysql_real_escape_string($_POST["parentTabs"])[$j]))
			$parentTabs=0;
	
		$imp_category_tabs->setis_parent($parentTabs);
		$id=mysql_real_escape_string($_POST["idsTabs"])[$j];
		$imp_category_tabs->setid_tab($id);
		$imp_category_tabs->insert();
		
	}
	
}


?>
<script type="text/javascript" src="./js/jquery-1.4.4.min.js"></script>
<script type="text/javascript" src="./js/jquery.cookie.js"></script>
<script>
function getDetails(el){
	$("#optionDetailsList").html("");
	var page = el.id;
	$.ajax({
  type: 'POST',
  url: "./imprint/inc_tabs.php",
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
<div class="PD_main_form">
<div class="">
    <table width="100%" cellspacing="1" cellpadding="5" border="0" align="center">
      <tbody>
        <tr>
          <td width="180" valign="top" align="left" class="setting"> <ul id="menu_nav" style="background:none repeat scroll 0 0 #E9E9E9;border:1px solid #CFCFCF;height:400px;">
              <li><a  class="menu" href="editimprint.php?idp=<?php echo mysql_real_escape_string($_GET['idp']);?>" id="tabsList"><< Back To Category</a></li>
           	 
              <li><a href="tabsmanagement.php?idc=<?php echo mysql_real_escape_string($_GET['idc']);?>" class="menu"  id="newTab">Manage Tabs</a></li>
               
            </ul></td>
          <td valign="top" align="left"><form action="" method="post">
              <table width="800" cellspacing="3" cellpadding="3" border="0" align="center">
                <tbody>
                  <tr>
                    <td bgcolor="#66CCCC"><div style="float:left;width:400px;">
                      <h2><span class="style1"><strong>Category Tabs</strong></span></h2></div>
                    <div style="width: 200px; float: right; text-align: right;"><input type="submit" value="submit"/></div>
                    
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
                            <td width="50%" height="35"><strong>Category ID</strong></td>
                            <td width="50%" height="35"><input type="text" value="<?php echo $cimprint_category->getIDCATEGORY();?>" id="textfield13" class="melbox" name="categoryID" readonly="readonly"></td>
                          </tr>
                          <tr>
                            <td width="50%" height="35"><strong>Category Name</strong></td>
                            <td width="50%" height="35"><input name="categoryNameEdit" type="text" class="100" id="textfield17" value="<?php echo $cimprint_category->getCATEGORY(); ?>" readonly="readonly"></td>
                          </tr>
                          <tr>
                            <td width="50%" height="35"><strong>Number of Tabs</strong></td>
                            <td width="50%" height="35"><input type="text" value="<?php echo $imprint_tabs->getnb_tabs();?>" id="textfield18" class="100" name="numTabs"></td>
                          </tr>
                        </tbody>
                      </table></td>
                    <td width="50%" valign="top" bgcolor="#F2F2F2" align="left">&nbsp;</td>
                  </tr>
                </tbody>
              </table>
            </form>
             <div id="optionsDiv"></div>
             <div id="optionDetailsList"><?php if( $imprint_tabs->getnb_tabs()>0){?>
             <form name="submitTabs" method="post" action="">
             <input type="hidden" name="idCateg" value="<?php echo mysql_real_escape_string($_GET['idc']);?>" />
                          	 <table width="800" cellspacing="3" cellpadding="3" border="0" align="center">
                <tbody>
                  <tr>
                    <td bgcolor="#66CCCC"><div style="float:left;width:400px;">
                      <h2><span class="style1"><strong>Category Tabs</strong></span></h2></div>
                    <div style="width: 200px; float: right; text-align: right;"><input type="submit" value="submit"/></div>
                    
                    </td>
                  </tr>
                </tbody>
              </table>
              <table width="800" cellpadding="3" cellspacing="3" border="0" align="center">
              <?php 
			  
			  $imp_category_tabs = new imp_category_tabs();
			  $array21 = array();
			  $array21["imprint_categ_id"] = mysql_real_escape_string($_GET['idc']);
			  $list = array();
			  $list = $imp_category_tabs->readArray($array21);
			  echo $imprint_tabs->getnb_tabs();
			  
			  for($ji=0;$ji<$imprint_tabs->getnb_tabs();$ji++){
				  ?>
                  <tr><td width="200" style="width:200px;"><label>Tab Name <?php echo $ji+1;?> :</label></td><td width="192">
                  <input name="tabName[]"  type="text" value="<?php  if(isset($list[$ji])) echo $list[$ji]->gettab_name(); ?>"/>
                  <input type="hidden" name="idsTabs[]" value="<?php if(isset($list[$ji])) echo $list[$ji]->getid_tab();?>" />
                  </td>
                  <td width="378"> 
                  <?php
				  if(isset($list[$ji]))
				  $ip = $list[$ji]->getis_parent();
				  
				  ?>
                  <input type="checkbox" name="parentTabs[]" <?php  if($ip==1) echo 'checked="checked"'?>/>Top Tab
                  </td></tr>
                  <?php
			  }
			  
			  ?>
              <?php
              }
			  ?>
              </table>
              </form>
             </div>
            </td>
        </tr>
      </tbody>
    </table>
    </div>
  </div>
 
  </div>
 
</body>
</html>