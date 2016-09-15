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
        'script'    : "./imprint/uploadify/uploadify.php?idop=<?php $_GET['idp'];?>",
        'cancelImg' : './imprint/uploadify/cancel.png',
        'folder'    : '/development/cpadmin/imprint_files/',
        'auto'      : true,
		'multi'       : true,
		'onAllComplete' : successUploadFiles
      });
    });
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
?>
<div class="">
  <table width="100%" cellspacing="1" cellpadding="5" border="0" align="center">
    <tr>
      <td valign="top" align="left"><form action="" method="post">
          <input type="hidden" name="formType" value="editOption"/>
          <table width="800" cellspacing="3" cellpadding="3" border="0" align="center">
            <tbody>
              <tr>
                <td bgcolor="#66CCCC"><h2><span class="style1"><strong>Option/Edit</strong></span></h2></td>
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
                        <td width="50%" height="35"><input type="text" value="<?php echo $impcategory_option->getADMIN_NOTES();?>" id="adminNotes" class="100" name="adminNotes"></td>
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
							    	<td><strong>Number of Image To Upload</strong></td>
									       <td><input type="text" name="nbrImg" value="<?php echo $impoption_settings->getNBR_IMAGES()?>"/></td>
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
                  <input type="submit" value="Save"/></td>
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
                <td width="33%" valign="middle" bgcolor="#FFFFFF" align="center"><img src="./imprint_files/<?php echo $impoption_settings->getFRONTEND_PREVIEW();?>" alt="http://techogroup.com/puma.jpg" /></td>
              </tr>
            </tbody>
          </table>
          <?php if($impoption_settings->getNBR_IMAGES()!=0){?>
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
  $nbr = $impoption_settings->getNBR_IMAGES();
  $images = new images();
  $imagesList = array();
  $array = array();
  $array["IDOPTION"]= mysql_real_escape_string($_GET['idop']);
  $imagesList = $images->readArray($array);
  $j=0;
  $k=1;
   for($i=0;$i<=$nbr;$i++){
	   if($j==0)
	   echo "<tr>";
        if($j==4 || $j==$nbr) { echo "</tr><tr>";$j=0;}else {

?>
          <td width="20%" valign="middle"  bgcolor="#F2F2F2" align="center" style="height: 251px;">
                  <div style="height:130px;text-align:center">
                    <?php if(!isset($imagesList[$i])){?>
                    <h1 style="color: #FF0000;font-size: 100px;font-family:Arial,Helvetica,sans-serif;margin-top:67px;margin-left:67px;"><?php echo $k;$k++;?></h1>
                    <?php }else {
							echo "<img src='./imprint_files/".$imagesList[$i]->getIMAGEURL()."' style='max-width:100%;max-height:130px;'/>";
							//$i++;
							
					   } ?>
                </div>
                  
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
  <?php 
	
	}?>
</div>
