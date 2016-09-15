<?php

	require 'db.php';

	if($_POST["type"] == "delete") {
	
		$impoptid = mysql_real_escape_string($_POST["impoptid"]);
		$sql_del = "DELETE FROM imprint_options WHERE id=$impoptid LIMIT 1";
		mysql_query($sql_del);
		
		$sql_delimg = "DELETE FROM imprint_images WHERE OptionID=$impoptid";
		mysql_query($sql_del);
		
		echo "Imprint Option Removed";
	
		mysql_close($conn);
		exit(); 
	}
	
	if($_POST["type"] == "optImageDelete") {
		
		$impImgID = str_replace("del_", "", mysql_real_escape_string($_POST["optImgID"]));
		
		$sql_delsuboptions = "DELETE FROM imprint_images WHERE Parent=$impImgID";
		mysql_query($sql_delsuboptions);
		
		$sql_deloptions = "DELETE FROM imprint_images WHERE id=$impImgID";
		mysql_query($sql_deloptions);
		
		echo "Design Option Removed";
		
		mysql_close($conn);
		exit();
	}
	
	if($_POST["type"] == "addOption") {
		
		$parentID = mysql_real_escape_string($_POST["parentID"]);
		
		if($parentID == '' || $parentID == 0) {
			$lable = "Main";	
		} else {
			$lable = "Sub";
		}
		
		?>
        <form action="" method="post"  enctype="multipart/form-data">
        	<input type="hidden" id="parentID" name="parentID" value="<?=$parentID;?>" />
        	<div class="addcatHeader">Add <?=$lable;?> Design</div>
            <div class="addcatBody">
                <p>Image</p><!-- input type="file" class="textbox" id="impImage" name="impImage" value="" / -->
            </div>
            <div class="addcatBody">
                <p>Color (without #)</p><input type="text" class="text" id="impColor" name="impColor" value="" />
            </div>
			<div class="addcatBody">
				<p>Color Name:</p><input type="text" class="text" id="impColorName" name="impColorName" value="" />
			</div>
            <div class="addcatBody">
                <p>Type</p>
                <select id="impType" name="impType">
                    <option value="">Select Type</option>
                    <option value="Back">Back</option>
                    <option value="Front">Front</option>
                    <option value="Short">Short</option>
                    <option value="Socks">Socks</option>
                </select>
            </div>
            
            <input type="submit" class="impbutton" id="btnAddDesign" name="btnAddDesign" value="Add <?=$lable;?> Design" />
            <input type="button" class="impbutton" id="btnCancel" name="btnCancel" value="Cancel" />
        </form>
        <script>
            $('#btnCancel').click(function (e) {         
                e.preventDefault();         
                $('#mask, .window').hide();     
            }); 
        </script>
        <?php
		
		mysql_close($conn);
		exit();
	}
	
	if($_POST["type"] == "editOption") {
		
		$parentID = mysql_real_escape_string($_POST["parentID"]);
		$optImgID = mysql_real_escape_string($_POST["optImgID"]);
		
		if($parentID == '' || $parentID == 0) {
			$lable = "Main";	
		} else {
			$lable = "Sub";
		}
		
		$sql_edit = "SELECT * FROM imprint_images WHERE id=$optImgID LIMIT 1";
		$result_edit = mysql_query($sql_edit);
		$row_edit = mysql_fetch_assoc($result_edit);
		
		?>
        <form action="" method="post"  enctype="multipart/form-data">
        	<input type="hidden" id="optImgID" name="optImgID" value="<?=$optImgID;?>" />
        	<input type="hidden" id="parentID" name="parentID" value="<?=$parentID;?>" />
        	<div class="addcatHeader">Edit <?=$lable;?> Design</div>
            <div class="addcatBody">
            	<p>Current Image: </p><img src="logo/<?=$row_edit["Image"];?>" />
            </div>
            <div class="addcatBody">
                <p>Image</p><!-- input type="file" class="textbox" id="impImage" name="impImage" value="" / -->
            </div>
            <div class="addcatBody">
                <p>Color (without #)</p><input type="text" class="text" id="impColor" name="impColor" value="<?=$row_edit["Color"];?>" />
            </div>
			<div class="addcatBody">
				<p>Color Name:</p><input type="text" class="text" id="impColorName" name="impColorName" value="<?=stripslashes($row_edit["ColorName"]);?>" />
			</div>
            <div class="addcatBody">
                <p>Type</p>
                <select id="impType" name="impType">
                    <option value="">Select Type</option>
                    <option value="Back" <?=($row_edit["Type"]=="Back"?'selected="selected"':'');?> >Back</option>
                    <option value="Front" <?=($row_edit["Type"]=="Front"?'selected="selected"':'');?>>Front</option>
                    <option value="Short" <?=($row_edit["Type"]=="Short"?'selected="selected"':'');?>>Short</option>
                    <option value="Socks" <?=($row_edit["Type"]=="Socks"?'selected="selected"':'');?>>Socks</option>
                </select>
            </div>
            
            <input type="submit" class="impbutton" id="btnEditDesign" name="btnEditDesign" value="Update <?=$lable;?> Design" />
            <input type="button" class="impbutton" id="btnCancel" name="btnCancel" value="Cancel" />
        </form>
        <script>
            $('#btnCancel').click(function (e) {         
                e.preventDefault();         
                $('#mask, .window').hide();     
            }); 
        </script>
        <?php
		
		mysql_close($conn);
		exit();
	}
	
?>