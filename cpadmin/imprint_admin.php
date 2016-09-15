<?php
include('includes/header.php');
session_start();

/** For Modification **/
$optionInfo = array();
$priceInfo = array();
$id = 0;
if (isset($_GET['id'])) {
	$id = intval($_GET['id']);
}

/**
* Fetch Imprint Design Data for show Information at Edit Mode
**/
$sql_details = "SELECT * FROM imprint_cusom_options WHERE id=".$id;
$result_details = mysql_query($sql_details);
$optionInfo = @mysql_fetch_assoc($result_details);



/**
* Get Imprint Category Information
**/
$catSql = "SELECT * FROM `cimprint_category` "; //WHERE enabled=0
$imprintResult = mysql_query($catSql);

/**
* Fetch Pricing Data for show Information at Edit Mode
**/

$priceSelect = "SELECT * FROM pricing WHERE IDOPTION=".$id;
$price_result = mysql_query($priceSelect);
$priceInfo = @mysql_fetch_assoc($price_result);

/** 
* Global Variable For Show Message
*/
$error = array();

/**
* Upload Image
*/
function uploadImage() {
	global $error;
	$allowedExtensions = array("jpg", "jpeg", "gif", "png");
	$destinationPath = 'logo/';
	$fileList = array();
	if (isset($_FILES['image'])) {
		$iamges = $_FILES['image'];
		foreach($iamges['name'] as $key => $filename) {
			$fileSize = $iamges['size'][$key];
			$filename = microtime(TRUE).basename($filename);
			if (in_array(end(explode(".", $iamges['name'][$key])), $allowedExtensions)) {
				if ($fileSize <= 10000) { //10 KB
					if (@move_uploaded_file($iamges['tmp_name'][$key], $destinationPath . $filename)) {
						$fileList[$key] = $filename;
					}
				} else {
					$error['Image'] = 'Maximum allowable file size 10KB';
				}
			} else {
				$error['Image'] = 'File extension is invalid';
			}
		}
		return $fileList;
	}
	$error['Image'] = "Please select design image";
	return '';
}

/**
* Process Number
**/
function processNumber($value = 0) {
	return number_format(floatval($value), 2);
}

/**
* Save Data After Post
**/
if ($_POST) {
	
	$fileList = uploadImage();
	$colorCodeList = mysql_real_escape_string($_POST['color_code']);
	$designType = mysql_real_escape_string($_POST['design_type']);
	$parent = mysql_real_escape_string($_POST['category']);
	$selSql = 'SELECT MAX(index_seq) AS lastindex FROM imprint_information AS ii
			   INNER JOIN imprint_cusom_options AS ico ON ico.id=ii.option_id
			   WHERE ico.category_id='.intval(mysql_real_escape_string($_POST['category_id']));
	$indexResult = mysql_query($selSql);
	$indexSeq = @mysql_fetch_assoc($indexResult);
	
	if ($indexSeq) {
		$lastindex = $indexSeq['lastindex']+1;
	} else {
		$lastindex = 1;
	}
	
	if ($id) {
		
		$type = intval($_POST['type']);
		$categoryId = intval($_POST['category_id']);
		$adminNote = addslashes(strip_tags($_POST['admin_note']));
		$imprintQuery = "UPDATE imprint_cusom_options SET category_id=".$categoryId.",type=".$type.",admin_note='".$adminNote."' WHERE id=".$id;
		@mysql_query($imprintQuery);
		
		if($colorCodeList != '') {
			$array = array_keys($colorCodeList);
			$existKey = rtrim(implode(',', $array), ',');
		
			$dSql = "DELETE FROM imprint_information WHERE option_id=".$id." AND index_seq NOT IN(".$existKey.")";
			mysql_query($dSql);
		
			foreach ($fileList as $key => $value) {
				$colorCode = !empty($colorCodeList[$key])?$colorCodeList[$key]:'ffffff';
				$designLogo = intval($designType[$key]);
				$parentid = intval($parent[$key]);
				$imprintInfoQuery = "INSERT INTO imprint_information(image,option_id,index_seq,color_code,design_type,parent) VALUES('".$value."',".$id.",".($lastindex++).",'".$colorCode."','".$designLogo."',".$parentid.")";
				
				mysql_query($imprintInfoQuery);
			}
		
			foreach ($colorCodeList as $key => $value) {
				$colorCode = $colorCodeList[$key];
				$parentid = intval($parent[$key]);
				$designLogo = intval($designType[$key]);
				
				$update_imprint = "UPDATE imprint_information SET color_code='$colorCode', parent=$parentid, design_type=$designLogo WHERE index_seq=$key LIMIT 1";
				mysql_query($update_imprint);
			}
		} else {
			$dSql = "DELETE FROM imprint_information WHERE option_id=".$id;
			mysql_query($dSql);
		}
		
	} else {
		$type = intval($_POST['type']);
		$categoryId = intval($_POST['category_id']);
		$adminNote = addslashes(strip_tags($_POST['admin_note']));
		$imprintQuery = "INSERT INTO imprint_cusom_options(category_id,type,admin_note) VALUES(".$categoryId.", ".$type.", '".$adminNote."');";
		
		if (mysql_query($imprintQuery)) {
			if (!$id) {
				$sql_details1 = "SELECT MAX(id) AS id FROM imprint_cusom_options";
				$result_details1 = mysql_query($sql_details1);
				$maximumId = @mysql_fetch_assoc($result_details1);
				$id = $maximumId['id'];
			}
		} else {
			$imprintQuery = "UPDATE imprint_cusom_options SET admin_note= '".$adminNote."' WHERE category_id=".$categoryId." AND type=".$type;
			@mysql_query($imprintQuery);
			
			$sql_details1 = "SELECT MAX(id) AS id FROM imprint_cusom_options WHERE category_id=".$categoryId." AND type=".$type;
			$result_details1 = mysql_query($sql_details1);
			$maximumId = @mysql_fetch_assoc($result_details1);
			$id = $maximumId['id'];
		}
		
		foreach ($fileList as $key => $value) {
			$colorCode = !empty($colorCodeList[$key])?$colorCodeList[$key]:'ffffff';
			$designLogo = intval($designType[$key]);
			$parentid = intval($parent[$key]);
			$imprintInfoQuery = "INSERT INTO imprint_information(image,option_id,index_seq,color_code,design_type,parent) VALUES('".$value."', ".$id.",".($lastindex++).",'".$colorCode."','".$designLogo."',".$parentid.")";
			mysql_query($imprintInfoQuery);
		}
	}
	
	
	if (!$priceInfo) {
		$priceQuery = "INSERT INTO pricing(
					IDOPTION,NONMEMBER_PRICE,
					STARTQT_1,ENDQT_1,PRICE1,
					STARTQT_2,ENDQT_2,PRICE2,
					STARTQT_3,ENDQT_3,PRICE3,
					STARTQT_4,ENDQT_4,PRICE4,
					setup_fee
			) VALUES(
				".$id.", '".processNumber($_POST['nmbprcing'])."',
				'".intval($_POST['strqt1'])."', '".intval($_POST['endqt1'])."', '".processNumber($_POST['price1'])."',
				'".intval($_POST['strqt2'])."', '".intval($_POST['endqt2'])."', '".processNumber($_POST['price2'])."',
				'".intval($_POST['strqt3'])."', '".intval($_POST['endqt3'])."', '".processNumber($_POST['price3'])."',
				'".intval($_POST['strqt4'])."', '".intval($_POST['endqt4'])."', '".processNumber($_POST['price4'])."',
				'".processNumber($_POST['setup_fee'])."'
			)";
	
	} else {
		$priceQuery = "UPDATE pricing
				SET NONMEMBER_PRICE='".processNumber($_POST['nmbprcing'])."', 
				STARTQT_1='".intval($_POST['strqt1'])."',ENDQT_1='".intval($_POST['endqt1'])."',PRICE1='".processNumber($_POST['price1'])."',
				STARTQT_2='".intval($_POST['strqt2'])."',ENDQT_2='".intval($_POST['endqt2'])."',PRICE2='".processNumber($_POST['price2'])."', 
				STARTQT_3='".intval($_POST['strqt3'])."',ENDQT_3='".intval($_POST['endqt3'])."',PRICE3='".processNumber($_POST['price3'])."',
				STARTQT_4='".intval($_POST['strqt4'])."',ENDQT_4='".intval($_POST['endqt4'])."',PRICE4='".processNumber($_POST['price4'])."', 				  	  				  setup_fee='".processNumber($_POST['setup_fee'])."'
				WHERE IDPRICING=".intval($_POST['pricingId'])." AND IDOPTION=".$id ;
	}
	
	@mysql_query($priceQuery);
	header("location: imprint_list.php");
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Imprint Configaration</title>

<script type="text/javascript" src="./js/jquery-1.4.4.min.js"></script>
<script type="text/javascript" src="js/jquery.validate.js"></script>
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
	font-size: 14px;
	font-weight: bold;
	color: #FFFFFF;
}

#color_image td { height: 63px; text-align: left; vertical-align: middle; }
#color_image td.header { height: 30px;padding:3px 0px; background:#89D0FE; color:#000; width:100%; text-align:center; }
#color_image td.image { width: 65px; font-weight: bold; }
#color_image td.imagewide { width: 100px; font-weight: bold; }
#color_image td.main { width: 30px; }
#color_image input { border: 1px solid #b5b5b5; padding: 5px; }
#color_image select { border: 1px solid #b5b5b5; padding: 5px; }


-->
</style>

<script type="text/javascript">

//var index = <?php //echo isset($r_imageInfo['index']) ? $r_imageInfo['index']+1 : 2; ?>;

function appendRow() {
	
	var index = parseInt($('#last_index').val());
	index++;
 	
	var newRow = '<tr><td><table cellpadding="0" cellspacing="0" style="width: 100%;"><tr><td class="image"></td><td class="imagewide">Main Design</td><td>Design Image: <br/><input type="file" name="image['+index+']" id="image" />';
	newRow = newRow+'<input type="hidden" name="category['+index+']" id="category" value="0" /></td>';
	newRow = newRow+'<td>Color Code(without #): <br/><input type="text" name="color_code['+index+']" id="color_code" /></td>';
 	newRow = newRow+ '<td>Type:<select name="design_type['+index+']"><option value="1">Front</option><option value="2">Back</option><option value="3">Short</option><option value="4">Socks</option></select></td>';
	newRow = newRow+'<td onclick="removeRow(this);" class="table_remove"><span style="text-decoration:underline;background:#e7e7e7;padding:3px 10px;cursor:pointer">Remove</span></td><td class="imagewide"></td></tr></table></td></tr>';
 	
	$("#color_image").append(newRow);
 	$('#last_index').val(index)
}

function addRow(r, cat) {
	var index = parseInt($('#last_index').val());
	index++;
 	
	var newRow = '<tr><td class="image"></td><td class="imagewide"></td><td>Design Image: <br/><input type="file" name="image['+index+']" id="image" />';
	newRow = newRow+'<input type="hidden" name="category['+index+']" id="category" value="'+cat+'" /></td>';
	newRow = newRow+'<td>Color Code(without #): <br/><input type="text" name="color_code['+index+']" id="color_code" /></td>';
 	newRow = newRow+ '<td>Type:<select name="design_type['+index+']"><option value="1">Front</option><option value="2">Back</option><option value="3">Short</option><option value="4">Socks</option></select></td>';
	newRow = newRow+'<td onclick="removeRow(this);" class="table_remove"><span style="text-decoration:underline;background:#e7e7e7;padding:3px 10px;cursor:pointer">Remove</span></td></tr>';
 	
	$(r).parent().parent().append(newRow);
	$('#last_index').val(index)
}

function removeRow(c) {
	$(c).parent().remove();
}

$(document).ready(function() {
	$('#imprint_option').validate();
});

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
    <div class="" style="width:990px;text-align:center;margin:0px auto">
<form method="post" action="" id="imprint_option" enctype="multipart/form-data">
	<table border="0" cellspacing="4" cellpadding="10" width="100%">
		<tr>
			<td align="left" bgcolor="#CCCCCC" width="100%" colspan="5">
				<span class="style3">Pricing($)</span>
			</td>
		</tr>
		<tr>
			<td align="center" bgcolor="#E0E0E0" valign="top" width="20%">
				<span class="style2">Default Price</span>
			</td>
            <td align="center" bgcolor="#F1F0AF" valign="top" width="20%">
				<input value="<?php echo isset($priceInfo['STARTQT_1'])?$priceInfo['STARTQT_1']:1; ?>" style="width:60px" id="strqt1" class="smallbox" name="strqt1" type="text">
                TO
              	<input value="<?php echo isset($priceInfo['ENDQT_1'])?$priceInfo['ENDQT_1']:149; ?>" style="width:60px" id="endqt1" class="smallbox" name="endqt1" type="text">
			</td>
            <td align="center" bgcolor="#89D0FE" valign="top" width="20%">
				<input value="<?php echo isset($priceInfo['STARTQT_2'])?$priceInfo['STARTQT_2']:150; ?>" style="width:60px" id="strqt2" class="smallbox" name="strqt2" type="text">
              	TO
              	<input value="<?php echo isset($priceInfo['ENDQT_2'])?$priceInfo['ENDQT_2']:299; ?>" style="width:60px" id="endqt2" class="smallbox" name="endqt2" type="text">
			</td>
            <td align="center" bgcolor="#D1D1D1" valign="top" width="20%">
				<input value="<?php echo isset($priceInfo['STARTQT_3'])?$priceInfo['STARTQT_3']:300; ?>" style="width:60px" id="strqt3" class="smallbox" name="strqt3" type="text">
              	TO
              	<input value="<?php echo isset($priceInfo['ENDQT_3'])?$priceInfo['ENDQT_3']:999; ?>" style="width:60px" id="endqt3" class="smallbox" name="endqt3" type="text">
			</td>
            <td align="center" bgcolor="#FEDA00" valign="top" width="20%">
				<input value="<?php echo isset($priceInfo['STARTQT_4'])?$priceInfo['STARTQT_4']:1000; ?>" style="width:60px" id="strqt4" class="smallbox" name="strqt4" type="text">
              	TO
              	<input value="<?php echo isset($priceInfo['ENDQT_4'])?$priceInfo['ENDQT_4']:5000; ?>" style="width:60px" id="endqt4" class="smallbox" name="endqt4" type="text">
			  	 
			  	<input name="pricingId" value="<?php echo isset($priceInfo['IDPRICING'])?$priceInfo['IDPRICING']:0; ?>" type="hidden">
			</td>
          </tr>
          <tr>
            <td align="center" bgcolor="#F2F2F2" valign="top">
				<input value="<?php echo isset($priceInfo['NONMEMBER_PRICE'])?$priceInfo['NONMEMBER_PRICE']:0; ?>" id="nmbprcing" class="100" name="nmbprcing" type="text">
			</td>
            <td align="center" bgcolor="#F2F2F2" valign="top">
				<input value="<?php echo isset($priceInfo['PRICE1'])?$priceInfo['PRICE1']:0; ?>" id="price1" class="100" name="price1" type="text">
			</td>
            <td align="center" bgcolor="#F2F2F2" valign="top">
				<input value="<?php echo isset($priceInfo['PRICE2'])?$priceInfo['PRICE2']:0; ?>" id="price2" class="100" name="price2" type="text">
			</td>
            <td align="center" bgcolor="#F2F2F2" valign="top">
				<input value="<?php echo isset($priceInfo['PRICE3'])?$priceInfo['PRICE3']:0; ?>" id="price3" class="100" name="price3" type="text">
			</td>
            <td align="center" bgcolor="#F2F2F2" valign="top">
				<input value="<?php echo isset($priceInfo['PRICE4'])?$priceInfo['PRICE4']:0; ?>" id="price4" class="100" name="price4" type="text">
			</td>
          </tr>
   
	</table>
	<br />
	<table>
		<tr>
          <td align="left" valign="middle">&nbsp;</td>
          <td align="left" valign="middle">&nbsp;</td>
        </tr>
        <tr>
          <td align="left" style="padding:5px 14px" valign="middle">
		  	<span class="style1" style="color:#fff;display:none">Setup Fee:</span> 
          	<input name="setup_fee" id="setup_fee" value="<?php echo isset($priceInfo['setup_fee'])?$priceInfo['setup_fee']:0; ?>" size="18" type="text" style="display:none">		
		  </td>
          <td align="left" valign="middle">&nbsp;</td>
        </tr>
        
	</table>
	<br />
	<table border="0" cellpadding="3" cellspacing="0" width="100%">
		<tr>
			<td align="left" bgcolor="#CCCCCC" width="100%" colspan="5">
				<span class="style3">Option Information</span>
			</td>
		</tr>
		<tr>
			<td height="35" width="20%">
				<strong>Admin Note<em>*</em></strong>
			</td>
			<td height="35" width="50%" align="left">
				<input name="admin_note" type="text" id="admin_note" value="<?php if (isset($optionInfo['admin_note'])) echo $optionInfo['admin_note']; ?>" class="required">
			</td>
		</tr>
		
		<tr>
			<td height="35" width="20%">
				<strong>Imprint Category<em>*</em></strong>
			</td>
			<td height="35" width="50%" align="left">
				<select name="category_id" id="category_id" class="required">
				<option value="">--Select Category--</option>
				<?php while ($category = mysql_fetch_array($imprintResult)) { ?>
					<option value="<?php echo $category['IDCATEGORY']; ?>" <?php if (isset($optionInfo['category_id']) && $optionInfo['category_id'] == $category['IDCATEGORY']) echo 'selected="selected"'; ?>><?php echo $category['CATEGORY']; ?></option>
				<?php } ?>
				</select>
			</td>
		</tr>
		<tr>
			<td height="35" width="20%">
				<strong>Type<em>*</em></strong>
			</td>
			<td height="35" width="50%"  align="left">
				<select name="type" id="type" class="required">
					<option value="1" <?php if (isset($optionInfo['type']) && $optionInfo['type'] == 1) echo 'selected="selected"'; ?>>Logo</option>
					<option value="2" <?php if (isset($optionInfo['type']) && $optionInfo['type'] == 2) echo 'selected="selected"'; ?>>Number</option>
					<option value="3" <?php if (isset($optionInfo['type']) && $optionInfo['type'] == 3) echo 'selected="selected"'; ?>>Name</option>
				</select>
			</td>
		</tr>
		<tr>
			<td colspan="2">



				<!-- :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: -->
				<table id="color_image" style="border: 1px solid #e7e7e7; padding: 5px; width: 100%;" cellspacing="5">
					<tr>
						<td class="header">
							(Best Dimension: [For Logo(63x63), For Name(100x50), For Number(63x63)])
						</td>
					</tr>
					<?php 
						$sql_imageinfo = "SELECT * FROM imprint_information WHERE option_id=".$id." AND parent=0";
						$imageInfo = mysql_query($sql_imageinfo);
						$numberImage = @mysql_num_rows($imageInfo);
						
						$lastIndex = 2;
						
						if ($numberImage): 
						//$i = 1;
						while ($value = mysql_fetch_array($imageInfo)):
						?>
							<tr>
                            	<td>
                                    <table cellpadding="0" cellspacing="0" style="width: 100%;">
                                        <tr>
                                            <td class="image"><img src="logo/<?php echo $value['image']; ?>" alt="Design Image" width="63" height="63"></td>
                                            <td class="imagewide">Main Design</td>
                                            <td>
                                            	Main Design Image:<br/>
                                                <!-- input type="file" name="image[<?php echo $value['index_seq']; ?>]" id="image"  --/>
                                                <input type="hidden" name="category[<?=$value["index_seq"];?>]" id="category" value="<?=$value["parent"];?>" />
                                            </td>
                                            <td>
                                                Color Code(without #): <br/> <input type="text" name="color_code[<?php echo $value['index_seq']; ?>]" value="<?php if (isset($value['color_code'])) echo $value['color_code']; ?>" id="color_code" />
                                            </td>
                                            <td>
                                                Type: 
                                                <select name="design_type[<?php echo $value['index_seq']; ?>]">
                                                    <option value="1" <?php if (isset($value['design_type']) && $value['design_type'] == 1) echo 'selected="selected"'; ?>>Front</option>
                                                    <option value="2" <?php if (isset($value['design_type']) && $value['design_type'] == 2) echo 'selected="selected"'; ?>>Back</option>
                                                    <option value="3" <?php if (isset($value['design_type']) && $value['design_type'] == 3) echo 'selected="selected"'; ?>>Short</option>
                                                    <option value="4" <?php if (isset($value['design_type']) && $value['design_type'] == 4) echo 'selected="selected"'; ?>>Socks</option>
                                                </select>
                                            </td>
                                            <td onclick="removeRow(this);" class="table_remove">
                                                <span style="text-decoration:underline;background:#e7e7e7;padding:5px 10px;cursor:pointer">Remove</span>
                                            </td>
                                            <td onclick="addRow(this, <?=$value["recNum"];?>);" class="table_remove">
                                                <span style="text-decoration:underline;background:#e7e7e7;padding:5px 10px;cursor:pointer">Add Design</span>
                                            </td>
                                        </tr>
                                    
                                    <?php 
                                    
                                        $sql_sub = "SELECT * FROM imprint_information WHERE option_id=".$id." AND parent=".$value["recNum"];
                                        $result_sub = mysql_query($sql_sub);
                                        
                                        while($row_sub = mysql_fetch_assoc($result_sub)) {
                                            ?>
                                                <tr>
                                                    <td class="image"></td>
                                                    <td class="imagewide"><img src="logo/<?php echo $row_sub['image']; ?>" alt="Design Image" width="63" height="63"></td>
                                                    <td>
                                                    	Design Image:<br/>
                                                        <!-- input type="file" name="image[<?php echo $row_sub['index_seq']; ?>]" id="image" / -->
                                                        <input type="hidden" name="category[<?=$row_sub["index_seq"];?>]" id="category" value="<?=$row_sub["parent"];?>" />
                                                    </td>
                                                    <td>
                                                        Color Code(without #): <br/> <input type="text" name="color_code[<?php echo $row_sub['index_seq']; ?>]" value="<?php if (isset($row_sub['color_code'])) echo $row_sub['color_code']; ?>" id="color_code" />
                                                    </td>
                                                    <td>
                                                        Type: 
                                                        <select name="design_type[<?php echo $row_sub['index_seq']; ?>]">
                                                            <option value="1" <?php if (isset($row_sub['design_type']) && $row_sub['design_type'] == 1) echo 'selected="selected"'; ?>>Front</option>
                                                            <option value="2" <?php if (isset($row_sub['design_type']) && $row_sub['design_type'] == 2) echo 'selected="selected"'; ?>>Back</option>
                                                            <option value="3" <?php if (isset($row_sub['design_type']) && $row_sub['design_type'] == 3) echo 'selected="selected"'; ?>>Short</option>
                                                            <option value="4" <?php if (isset($row_sub['design_type']) && $row_sub['design_type'] == 4) echo 'selected="selected"'; ?>>Socks</option>
                                                        </select>
                                                    </td>
                                                    <td onclick="removeRow(this);" class="table_remove">
                                                        <span style="text-decoration:underline;background:#e7e7e7;padding:5px 10px;cursor:pointer">Remove</span>
                                                    </td>
                                                    <td></td>
                                                </tr>
                                            <?php
                                        }
                                        ?>
                                    </table>
                            	</td>
                    		</tr>
                            <?php
							
							$lastIndex = $value['index_seq'];
						endwhile;
						else: ?>
                    		<!-- ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
                    		<tr>
                            	<td>
                                    <table cellpadding="0" cellspacing="0" style="width: 100%;">
                                        <tr>
                                            <td class="image"></td>
                                            <td>Design Image: <br/><input type="file" name="image[1]" id="image" /></td>
                                            <td>Color Code: <br/><input type="text" name="color_code[1]" id="color_code" /></td>
                                            <td>Type: 
                                                <select name="design_type[1]">
                                                    <option value="1">Front</option>
                                                    <option value="2">Back</option>
                                                    <option value="3">Short</option>
                                                    <option value="4">Socks</option>
                                                </select>
                                            </td>
                                            <td onclick="removeRow(this);" class="table_remove">
                                                <span style="text-decoration:underline;background:#e7e7e7;padding:3px 10px;cursor:pointer">Remove</span>
                                            </td>
                                        </tr>
                                    </table>
                            	</td>
                    		</tr>
                    		:::::::::::::::::::::::::::::::::::::::::::::::::::::::::: -->
                    			<?php endif; ?>
				</table>
				<!-- :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: -->



				<input type="hidden" id="last_index" value="<?php echo $lastIndex; ?>">
				<div style="text-align:left;margin:5px 0;clear:both;">
					<a href="javascript:void(0);" style="color:#000;text-decoration:underline;" onclick="appendRow()">Add New Main Design</a>
				</div>
			</td>
		</tr>
		<tr>
			<td height="35" width="100%" colspan="2">
				<input type="submit" value="Save" />
			</td>
		</tr>
		
</form>