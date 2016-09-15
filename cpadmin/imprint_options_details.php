<?php
	include("includes/header.php");
	
	if($_GET["optID"] != "") {
		$optID = mysql_real_escape_string($_GET['optID']);
	} else {
		$optID = '';
	}
	
	if(isset($_POST["btnSave"])) {
		
		foreach($_POST as $key=>$value){
			$$key = addslashes($value);
		}
		
		if($optID == '') {
			$sql_optedit  = "INSERT INTO imprint_options(Name, ImprintCategory, Type, DefaultPrice, Option1, Option1Price, Option2, Option2Price, Option3, Option3Price, Option4, Option4Price) ";
			$sql_optedit .= "VALUES('$optName', '$optCategory', '$optType', '$defaultPrice', '$opt1From-$opt1To', '$opt1Price', '$opt2From-$opt2To', '$opt2Price', '$opt3From-$opt3To', '$opt3Price', '$opt4From-$opt4To', '$opt4Price')";
		} else {
			$sql_optedit  = "UPDATE imprint_options SET Name='$optName', ImprintCategory='$optCategory', Type='$optType', DefaultPrice='$defaultPrice', Option1='$opt1From-$opt1To', Option1Price='$opt1Price', ";
			$sql_optedit .= "Option2='$opt2From-$opt2To', Option2Price='$opt2Price', Option3='$opt3From-$opt3To', Option3Price='$opt3Price', Option4='$opt4From-$opt4To', Option4Price='$opt4Price' WHERE id=$optID LIMIT 1";
		}
		
		mysql_query($sql_optedit);
		header("location:imprint_options.php");
	}
	
	if(isset($_POST["btnAddDesign"])) {
		
		$parentID = mysql_real_escape_string($_POST["parentID"]);
		$color = mysql_real_escape_string($_POST["impColor"]);
		$colorName = mysql_real_escape_string($_POST["impColorName"]);
		$type = mysql_real_escape_string($_POST["impType"]);
		$optMainID = mysql_real_escape_string($_POST["optMainID"]);
		
		
		if($_FILES["impImage"]["error"] > 0) {
		} else {
			$file = microtime(True)."_".$_FILES["impImage"]["name"];
			$path = "logo/";
			move_uploaded_file($_FILES["impImage"]["tmp_name"], $path.$file);
			
			$sql_addopt  = "INSERT INTO imprint_images(OptionID, Image, Color, ColorName, Type, Parent) ";
			$sql_addopt .= "VALUES($optID, '$file', '$color', '$colorName', '$type', $parentID)";
			
			mysql_query($sql_addopt);
		}
		
	}
	
	if(isset($_POST["btnEditDesign"])) {
		
		$color = mysql_real_escape_string($_POST["impColor"]);
		$colorName = mysql_real_escape_string($_POST["impColorName"]);
		$type = mysql_real_escape_string($_POST["impType"]);
		$optImgID = mysql_real_escape_string($_POST["optImgID"]);
		$file = "";
		
		if($_FILES["impImage"]["error"] > 0) {
		} else {
			$file = microtime(True)."_".$_FILES["impImage"]["name"];
			$path = "logo/";
			move_uploaded_file($_FILES["impImage"]["tmp_name"], $path.$file);
		}
		
		if($file == "") {
			$sql_editopt = "UPDATE imprint_images SET Color='$color', ColorName='$colorName', Type='$type' WHERE id=$optImgID LIMIT 1";
		} else {
			$sql_editopt = "UPDATE imprint_images SET Color='$color', ColorName='$colorName', Type='$type', Image='$file' WHERE id=$optImgID LIMIT 1";
		}
		mysql_query($sql_editopt);
	}
	
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Imprint Options</title>
	<link rel="stylesheet" href="css/styles.css" type="text/css" />
	<link rel="stylesheet" href="jqtransformplugin/jqtransform_view.css" type="text/css"  media="all" />
	<script type="text/javascript" src="js/jquery-1.4.4.min.js"></script>
	<script language="javascript" type="text/javascript">
		$(document).ready(function(){
			$("#btnCancel").click(function(){
				window.location.href="imprint_options.php";							   
			});
			
			$('.window .close').click(function (e) {         
				e.preventDefault();         
				$('#mask, .window').hide();     
			});                
			
			$('#mask').click(function () {         
				$(this).hide();         
				$('.window').hide();     
			});
			
			$('#addMainDesign').click(function() {
				popUp("addOption", 0, 0);								   
			});
			
			$('.delopt').click(function() {
				var del = confirm("Delete Option?");
				
				if(del) {
					$.post('includes/inc_imprint_options.php', {"type":"optImageDelete", "optImgID": $(this).attr("id")}, function(data) {
						alert(data);
						location.reload();
					});
					return false;
				}
			});
			
			$('.editopt').click(function() {
				var optID = $(this).attr("name");
				popUp("editOption", 0, optID.replace("edit_",""));
			});
			
			$('.addopt').click(function() {
				var parID = $(this).attr("name");
				popUp("addOption", parID.replace("add_",""), 0);
			});
			
		});
		
		function popUp(pgType, parentID, optImgID) {
			
			var id = '#category';               
			var maskHeight = $(document).height();         
			var maskWidth = $(window).width();               
			
			$("#editcategory").html('<img src="images/loader.gif" />');
			$("#editcategory").load("includes/inc_imprint_options.php", {"type":pgType, "parentID":parentID, "optImgID":optImgID});
			
			$('#mask').css({'width':maskWidth,'height':maskHeight});                   
			$('#mask').fadeIn(200);             
			$('#mask').fadeTo("slow",0.8);                 
			
			var winH = $(window).height();         
			var winW = $(window).width();                         
			
			$(id).css('top',  winH/2-$(id).height()/2);         
			$(id).css('left', winW/2-$(id).width()/2);
			$(id).fadeIn(2000);
			
		}
		
	</script>
	</head>

	<body>

<!-- Add/Edit Categories -->
<div id="edit">
   <div id="category" class="window" style="text-align: center;">
   		<div id="editcategory"></div>
   </div>
   <div id="mask"></div> 
</div>     
<!-- End Add/Edit Categories -->

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
      <!-- Header Div ends here --> 
      <!-- Products view Div starts from here -->
      	<div class="product_view">
			<div class="PV_center">
          		
                <?php
					$sql_opt = "SELECT * FROM imprint_options WHERE id=$optID LIMIT 1";
					$result_opt = @mysql_query($sql_opt);
					$row_opt = @mysql_fetch_assoc($result_opt);
				?>
                
                <div id="Options" class="impOptions">
                <form action="" method="post">
                	<input type="hidden" id="optID" name="optID" value="<?=$optID;?>" />
                	<h1>Imprint Option - <?=stripslashes($row_opt["Name"]);?></h1>
                    <p class="priceHeader">Option Pricing</p>
                    <div class="pricing default">Default Price</div>
                    
                    <?php
						for($i=1;$i<5;$i++) { 
							$opts = explode("-",$row_opt["Option".$i]);
							?>
                            <div class="pricing opt<?=$i;?>">
                                <input type="text" class="pricebox" id="opt<?=$i;?>From" name="opt<?=$i;?>From" value="<?=$opts[0];?>" /> TO
                                <input type="text" class="pricebox" id="opt<?=$i;?>To" name="opt<?=$i;?>To" value="<?=$opts[1];?>" />
                            </div>				
                            
					<?php } ?>
                    <div class="clear"></div>
                    <div class="pricing amounts first"><input type="text" class="pricebox larger" id="defaultPrice" name="defaultPrice" value="<?=number_format($row_opt["DefaultPrice"],2);?>" /></div>
                    
                    <?php
						for($i=1;$i<5;$i++) { ?>
							<div class="pricing amounts <?=($i==4?'last':'');?>"><input type="text" class="pricebox larger" id="opt<?=$i;?>Price" name="opt<?=$i;?>Price" value="<?=number_format($row_opt["Option".$i."Price"],2);?>" /></div>
					<?php } ?>
                    
                    <div class="clear"></div>
                    <p class="priceHeader tpmargin">Option Information</p>
                    <div class="optname">Name</div><div class="optname optvalue"><input type="text" class="textbox" id="optName" name="optName" value="<?=stripslashes($row_opt["Name"]);?>" /></div>
                    <div class="clear"></div>
                    <div class="optname">Category</div><div class="optname optvalue">
                        <select class="selectbox" id="optCategory" name="optCategory">
                        	<option value="">Select Category</option>
                            <?php
								$sql_optcats = "SELECT id, Name FROM imprint_categories ORDER BY Name";
								$result_optcats = mysql_query($sql_optcats);
								
								while($row_optcats = mysql_fetch_array($result_optcats)) {
									if($row_opt["ImprintCategory"] == $row_optcats["id"]) {
										$sel = ' selected="selected" ';	
									} else {
										$sel = '';
									}
									echo '<option value="'.$row_optcats["id"].'" '.$sel.'>'.stripslashes($row_optcats["Name"]).'</option>';
								}
							?>
                        </select>
                    </div>
                    <div class="clear"></div>
                    <div class="optname">Type</div><div class="optname optvalue">
                        <select class="selectbox" id="optType" name="optType">
                        	<option value="">Select Type</option>
                            <option value="logo" <?=($row_opt["Type"]=='logo'?'selected="selected"':'');?>>Logo</option>
							<option value="pocket" <?=($row_opt["Type"]=='pocket'?'selected="selected"':'');?>>Pocket</option>
                            <option value="number" <?=($row_opt["Type"]=='number'?'selected="selected"':'');?>>Number</option>
                            <option value="name" <?=($row_opt["Type"]=='name'?'selected="selected"':'');?>>Name</option>
                        </select>
                    </div>
                    
                    
                    <div class="clear"></div>
                    <p class="priceHeader tpmargin">Option Images : (Best Dimension: [For Logo(63x63), For Name(100x50), For Number(63x63)]) </p>
                    
                    <!-- OPTION Images =========================================================== -->
                    	
                    	<table cellpadding="5" cellspacing="3">
                        	<?php
								$sql_main = "SELECT * FROM imprint_images WHERE Parent=0 AND OptionID = $optID";
								$result_main = @mysql_query($sql_main);
								
								while($row_main = @mysql_fetch_array($result_main)) {
									?>
                                    
                                    <tr>
                                    	<td class="small">Main Design</td>
                                        <td class="med"><img src="logo/<?=stripslashes($row_main["Image"]);?>" /></td>
                                        <td class="med"><p class="color">Color: (<?=$row_main["Color"];?>)</p><div class="optColor" style="background-color: #<?=$row_main["Color"];?>;"></div></td>
                                        <td class="med">Type: <?=$row_main["Type"];?></td>
                                        <td class="lrg">
                                        	<input type="button" class="optbutton editopt noMrg" id="edit_<?=$row_main["id"];?>" name="edit_<?=$row_main["id"];?>" value="edit" />
                                            <input type="button" class="optbutton delopt noMrg" id="del_<?=$row_main["id"];?>" name="del_<?=$row_main["id"];?>" value="delete" />
                                            <input type="button" class="optbutton addopt noMrg" id="add_<?=$row_main["id"];?>" name="add_<?=$row_main["id"];?>" value="Add SupOption" />
                                        </td>                                    
                                    </tr>
                                        
                                    <?php
									
									$sql_sub = "SELECT * FROM imprint_images WHERE Parent=$row_main[id]";
									$result_sub = mysql_query($sql_sub);
									
									while($row_sub = mysql_fetch_array($result_sub)) {
										?>
											<tr>
												<td class="sub"></td>
												<td class="sub"><img src="logo/<?=stripslashes($row_sub["Image"]);?>" /></td>
												<td class="sub"><p class="color">Color: (<?=$row_sub["Color"];?>;<?=stripslashes($row_sub["ColorName"]);?>) </p><div class="optColor" alt="<?=$row_sub["Color"];?>" title="<?=$row_sub["Color"];?>" style="background-color: #<?=$row_sub["Color"];?>;"></div></td>
												<td class="sub">Type: <?=$row_sub["Type"];?></td>
												<td class="sub">
													<input type="button" class="optbutton editopt noMrg" id="edit_<?=$row_sub["id"];?>" name="edit_<?=$row_sub["id"];?>" value="edit" />
													<input type="button" class="optbutton delopt noMrg" id="del_<?=$row_sub["id"];?>" name="del_<?=$row_sub["id"];?>" value="delete" />
												</td>                                    
											</tr>
										<?php
									}
								}
							
							?>
                        
                        </table>
                    
                    <!-- =========================================================== -->
                    <div class="clear"></div>
                    <p class="priceHeader bottom"> <input type="button" class="optbutton noMrg" id="addMainDesign" name="addMainDesign" value="Add Main Design" /></p>
                    
                    <input type="submit" class="optbutton" id="btnSave" name="btnSave" value="Save Option" />
                    <input type="button" class="optbutton" id="btnCancel" name="btnCancel" value="Cancel" />
				</form>
                </div>
        	</div>
  		</div>
      	<!-- Products view Div ends here --> 
    </div>
</body>
</html>