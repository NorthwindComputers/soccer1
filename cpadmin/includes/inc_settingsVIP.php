<?php
/******************************
 *  VIP information settings
 *
 * updated: 22 June 2016
 * by: Richard Tuttle
 ******************************/
if (isset($_POST["btnUpdate"])) {
	if ($_FILES["Image"]["name"] != '') {
		if ($_FILES["Image"]["error"]>0) {
			echo "Error: ".$_FILES["Image"]["error"];
		} else {
			$fileName = $_FILES["Image"]["name"];
			$folderLoc = "/home/socnet/public_html/images/productImages/";
		}
		move_uploaded_file($_FILES["Image"]["tmp_name"], $folderLoc.$fileName);
	} else {
		$fileName = mysql_real_escape_string($_POST["curImage"]);
	}
		
	foreach ($_POST as $key=>$value) {
		$$key = addslashes($value);
	}
		
	if ($Price == '') {
		$Price = 0;
	}
		
		$sql_chk = "SELECT * FROM vip";
		$result_chk = mysql_query($sql_chk);
		$num_chk = mysql_num_rows($result_chk);
		
		if($num_chk>0) {
			$sql_update  = "UPDATE vip SET `Name`='$Name', Price=$Price, MetaTitle='$MetaTitle', Description='$Description', Image='$fileName', MetaTag='$MetaTag', MetaDescription='$MetaDescription' ";
		} else {
			$sql_update = "INSERT INTO vip(`Name`, Price, MetaTitle, Description, Image, MetaTag, MetaDescription) VALUES('$Name', $Price, '$MetaTitle', '$Description', '$fileName', '$MetaTag', '$MetaDescription')";
		}
		if(!mysql_query($sql_update)) {
			echo "error saving VIP: ".mysql_error();
		}
	}
	
	$sql_vip = "SELECT * FROM vip LIMIT 1";
	$result_vip = mysql_query($sql_vip);
	$row_vip = mysql_fetch_assoc($result_vip);
	$num_vip = mysql_num_rows($result_vip);
	
	if($num_vip>0) {
		foreach($row_vip as $key=>$value) {
			$$key = stripslashes($value);
		}
	}	
?>
			<form action="" method="post" enctype="multipart/form-data" >
				<table width="100%" border="0" cellpadding="5" cellspacing="2">
            			<tr>
                			<td style="width: 120px;"><strong>VIP Name:</strong></td>
                    			<td><input type="text" style="width: 300px;" id="Name" name="Name" value="<?=$Name;?>" /></td>
                		</tr>
                        <tr>
                			<td style="width: 120px;"><strong>Price:</strong></td>
                    			<td><input type="text" style="width: 300px;" id="Price" name="Price" value="<?=number_format($Price,2);?>" /></td>
                		</tr>
                        <tr>
                			<td><strong>Page Title:</strong></td>
                    			<td><input type="text" style="width: 300px;" id="MetaTitle" name="MetaTitle" value="<?=$MetaTitle;?>" /></td>
                		</tr>
            			<tr>
                			<td><strong>Description:</strong></td>
                    			<td><textarea id="Description" name="Description" style="width: 500px;"><?=$Description;?></textarea></td>
                		</tr>
            			<tr>
                			<td><strong>Meta Tags:</strong></td>
                    			<td><textarea id="MetaTag" name="MetaTag" style="width: 500px;"><?=$MetaTag;?></textarea></td>
                		</tr>
            			<tr>
                			<td><strong>Meta Description:</strong></td>
                    			<td><textarea id="MetaDescription" name="MetaDescription" style="width: 500px;"><?=$MetaDescription;?></textarea></td>
                		</tr>
            			<tr>
                			<td><strong>Image:</strong></td>
                    			<td>
                                	<input type="hidden" id="curImage" name="curImage" value="<?=$Image;?>" />
                                	<input type="file" id="Image" name="Image" /><br/>
                                    <?php
									if ($Image != '') {
										echo '<img src="../images/productImages/'.$Image.'">';
									}
									?>
                                </td>
                		</tr>
            			<tr>
                			<td><input type="submit" class="submitform" id="btnUpdate" name="btnUpdate" value="Save"/></td>
                		</tr>
            		</table>
        		</form>
		<script>
		CKEDITOR.replace( 'Description', {
			fullPage : true
		});
		</script>