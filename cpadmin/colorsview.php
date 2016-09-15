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
include("./imprint/options_color.class.php");

if(isset($_GET["idp"])){
	$cimprint_category = new cimprint_category();
	$array = array();
	$array["IDCATEGORY"] = $_GET["idp"];
	$cimprint_category->readObject($array);

$impcategory_option = new impcategory_option();
$optionsList = array();
$optionsList = $impcategory_option->readArray($array);

}

// update icon
if(isset($_POST["idicon"])){
	include_once("./imprint/options_color.class.php");
	$idicon = mysql_real_escape_string($_POST["idicon"]);
	$target_path = "./images/";
	$target_path = $target_path . basename( $_FILES['file']['name']); 
    $options_color2 = new options_color();
	$options_color2->setidcolor($idicon);
	$color2 = new options_color();
	$params = array();
	$params["idcolor"]= $idicon;
	$color2->readObject($params);
	$options_color2->setname($color2->getname());
	$options_color2->setidcolor($idicon);
	if(!empty($_FILES['file'])){
		move_uploaded_file($_FILES['file']['tmp_name'], $target_path);
		$options_color2->setimages($_FILES["file"]["name"]);
		
	}
	
	$options_color2->insert();
	

}
if(isset($_POST["newColor"])){
	include_once("./imprint/options_color.class.php");
	$newcolor = mysql_real_escape_string($_POST["newColor"]);
	$target_path = "./images/";
	$target_path = $target_path . basename( $_FILES['icon']['name']); 
    $options_color = new options_color();
	$options_color->setname($newcolor);
	
	
 	if(!empty($_FILES["icon"])){
	    $images = $_FILES["icon"]['name'];	
		$options_color->setimages($images);
		move_uploaded_file($_FILES['icon']['tmp_name'], $target_path);
	}
	
	$options_color->insert();
	
}

if(isset($_GET["del"])) {
	$options_color = new options_color();
	$idc = mysql_real_escape_string($_GET["del"]);
	$params = array();
	$params["idcolor"] = $idc;
	$options_color->delete($params);
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
	var rep = confirm("Confirm Delete Color");
	if(rep){
		window.location.href="colorsview.php?idp=<?php echo mysql_real_escape_string($_GET['idp']);?>&del="+id;
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

function saveOptionTab(el){
	var id = new String(el.id);
	id = id.substring(1,id.length);
	document.forms["f"+id].submit();
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
                  <td bgcolor="#666666"><span class="style1">Color Setting</span></td>
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
              <h1 align="center">Color Setting</h1>
              <div>
                <table width="100%" border="0" align="center" cellpadding="5" cellspacing="0">
      <tr>
        <td width="50%" align="left" bgcolor="#CCCCCC"><strong>Available Colors</strong></td>
        <td width="50%" align="right" bgcolor="#CCCCCC"><input type="submit" name="newbutton" id="newbutton" value="Add More Color" /></td>
      </tr>
    </table>
                <table width="100%" border="0" cellpadding="5" cellspacing="0">
        <tr>
          <td width="25%" align="left" valign="middle" bgcolor="#3399FF"><div align="center"><strong>Color Name</strong></div></td>
          <td width="8%" align="center" valign="middle" bgcolor="#3399FF"><div align="center"><strong>Icon</strong></div></td>
          <td width="42%" align="left" valign="middle" bgcolor="#3399FF"><div align="center"><strong>Replace or Upload New Icon</strong></div></td>
          <td width="25%" align="left" valign="middle" bgcolor="#3399FF"><div align="center"><strong>Action</strong></div></td>
        </tr>
        <?php 
		$options_color = new options_color();
		$params = array();
		$optionsList = array();
		$optionsList = $options_color->readArray();
		foreach($optionsList as $key => $value){
		?>
        <tr>
          <td width="25%" align="left" valign="middle" bgcolor="#F2F2F2"><?php echo $value->getname()?></td>
          <td width="8%" align="center" valign="middle" bgcolor="#F2F2F2"><a onclick="filter('Color', '15', 'Orange');"><img src="images/<?php echo $value->getimages();?>" title="<?php echo $value->getimages()?>" alt="<?php echo $value->getimages()?>" /></a></td>
          <td width="42%" align="left" valign="middle" bgcolor="#F2F2F2">
          <form name="updateIcon" method="post" action="" enctype="multipart/form-data">
          <input name="file" type="file" id="file" />
            <input type="hidden" name="idicon" value="<?php echo $value->getidcolor();?>" />
            <input type="submit" name="button5" id="button5" value="Upload" />
            </form>
            </td>
          <td width="25%" align="center" valign="middle" bgcolor="#F2F2F2"><a style="color:#333333;" href="#" onclick="confirmDeleteOption(this)" id='d<?php echo $value->getidcolor()?>' >Delete</a></td>
        </tr>
        <?php 
			}
		?>
        
        </table>
          <div id="newSubTabsDiv" style="display:none">
          <form name="newSubTabs" id="newSubTabs" action="" method="post" enctype="multipart/form-data">
          <table width="100%" border="0" cellpadding="5" cellspacing="0">
          <tr>
          <td>Color Name:</td><td><input type="text" name="newColor"/></td><td>Icon :</td><td><input type="file" name="icon" /><input type="submit" value="submit" /></td>
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