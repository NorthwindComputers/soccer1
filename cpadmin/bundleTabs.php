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
include("./imprint/impcategory_option.class.php");
include("./imprint/bundle_tabs.class.php");

if(isset($_POST["tab"])){
  $tab = mysql_real_escape_string($_POST["tab"]);
  $idBundle = mysql_real_escape_string($_POST["idBundle"]);
  $bundle_tabs = new bundle_tabs();
  $bundle_tabs2 = new bundle_tabs();
  $params = array();
  $params["bundleId"] = $idBundle;
  $bundle_tabs->readObject($params);
  if(gettype($bundle_tabs)!="array"){
	   $bundle_tabs2->setid($bundle_tabs->getid());
  }
  $bundle_tabs2->setid_tab($tab);
  $bundle_tabs2->setbundleId($idBundle);
  $bundle_tabs2->insert();
}
?>
<script type="text/javascript" src="./js/jquery-1.4.4.min.js"></script>
<script type="text/javascript" src="./js/jquery.cookie.js"></script>
<script>
function setBundleTab(el){
  var id = new String(el.id);
  var id = id.substring(4,id.length);
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
<div class="PD_main_form">
<div class="">
    <table width="100%" cellspacing="1" cellpadding="5" border="0" align="center">
      <tbody>
        <tr>
          <td width="180" valign="top" align="left" class="setting"> <ul id="menu_nav" style="background:none repeat scroll 0 0 #E9E9E9;border:1px solid #CFCFCF;height:400px;">
              <li><a  class="menu" href="product_detail.php?id=<?php echo $_GET['id'];?>" id="productDetails"><< Back To Product</a></li>
           	 
             
               
            </ul></td>
          <td valign="top" align="left"><form action="" method="post">
              <table width="800" cellspacing="3" cellpadding="3" border="0" align="center">
                <tbody>
                  <tr>
                    <td bgcolor="#66CCCC"><div style="float:left;width:400px;">
                      <h2><span class="style1"><strong>Manage Bundle Items Tabs</strong></span></h2></div>
                    <div style="width: 200px; float: right; text-align: right;"><input type="submit" value="submit"/></div>
                    
                    </td>
                  </tr>
                </tbody>
              </table>
</form>
             <div id="optionsDiv">
             <?php
			 	$sql_bitems = "SELECT b.id, b.SortOrder, p.RootSKU, p.BrowserName FROM products p, product_bundles b WHERE b.Items=p.id AND b.ProductID=$_GET['id'] ORDER BY b.SortOrder";

					$result_bitems = mysql_query($sql_bitems);

					$num_bitems = mysql_num_rows($result_bitems);
					
					
			 ?>
             
             <table>
             <?php
			 if($num_bitems>0) {

					
						$bnum = 1;
						while($row_bitems=mysql_fetch_array($result_bitems)) {

							echo '<tr style="float: none;">
							<td style="width: 82%; float: none; border: 0px; text-align: left; padding-left: 20px;">';

							echo $row_bitems["RootSKU"]." -- ".$row_bitems["BrowserName"];
							echo "<td>";
							
							echo"<td><form name='frm".$bnum."' action='' method='post'>
							   <input type='hidden' name='idBundle' value='".$row_bitems["id"]."'/>
							   <select name='tab' id='tabs".$bnum."' onchange='setBundleTab(this)'><option value='-1'>Top Tab</option>";
							   $sql_options_tabs = "SELECT * FROM `imp_category_tabs` WHERE `is_parent`=1 and `imprint_categ_id` = (select ID_IMPRINT_CATEGORY from products where id=$_GET['id'])";
					
					$tabsquery = mysql_query($sql_options_tabs);
	   while($data=mysql_fetch_array($tabsquery)){
		   
	                               $selected="";
								    $bundle_tabs3 = new bundle_tabs();
									$params = array();
									$params["bundleId"] = $row_bitems["id"];
									$bundle_tabs3->readObject($params);
									
									if(gettype($bundle_tabs3)!="array"){
										$tabId = $bundle_tabs3->getid_tab();
										if($tabId==$data["id_tab"])
											$selected = " selected='selected'";
									}
								   echo "<option".$selected." value='".$data["id_tab"]."'>".$data["tab_name"]."</option>";
							   }
							   echo "</select>
							   </form>
							   </td>";
							echo "</tr>";
							$bnum++;
						}
			 }
			 ?>
             </table>
             </div></td>
        </tr>
      </tbody>
    </table>
    </div>
  </div>
 
  </div>
 
</body>
</html>