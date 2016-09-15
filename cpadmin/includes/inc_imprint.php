<?php

	require 'db.php';

	if($_POST["type"] == "delete") {
	
		$impid = mysql_real_escape_string($_POST["impid"]);
		$sql_del = "DELETE FROM imprint_categories WHERE id=$impid LIMIT 1";
		mysql_query($sql_del);
		echo "Imprint Category Removed";
	
		mysql_close($conn);
		exit(); 
	}
	
	if($_POST["type"] == "add") {
		
		?>
        
        <form action="" method="post" enctype="multipart/form-data">
        
        	<div class="addcatHeader">Add Imprint Category</div>
            <div class="addcatBody">
            	<p>Name</p><input type="text" class="textbox" id="impName" name="impName" value="" />
            </div>
            <div class="addcatBody">
                <p>Description</p><input type="text" class="text" id="impDesc" name="impDesc" value="" />
            </div>
			<div class="addcatBody">
				<p>Category Banner</p><!-- input type="file" class="text" id="impBanner" name="impBanner" / -->
			</div>
            
            <input type="submit" class="impbutton" id="btnImpAdd" name="btnImpAdd" value="Add New Category" />
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
	
	if($_POST["type"] == "edit") {
		
		$impCatID = mysql_real_escape_string($_POST["catID"]);
		
		$sql_impcat = "SELECT * FROM imprint_categories WHERE id=$impCatID LIMIT 1";
		$result_impcat = mysql_query($sql_impcat);
		$row_impcat = mysql_fetch_assoc($result_impcat);
		
		?>
        
        <form action="" method="post" enctype="multipart/form-data">
        	<input type="hidden" id="impCatID" name="impCatID" value="<?=$impCatID;?>" />
        	<div class="addcatHeader">Edit Imprint Category</div>
            <div class="addcatBody">
            	<p>Name</p><input type="text" class="textbox" id="impName" name="impName" value="<?=stripslashes($row_impcat["Name"]);?>" />
            </div>
            <div class="addcatBody">
                <p>Description</p><input type="text" class="text" id="impDesc" name="impDesc" value="<?=stripslashes($row_impcat["Description"]);?>" />
            </div>
			<div class="addcatBody">
				<p>Category Banner</p><!-- input type="file" class="text" id="impBanner" name="impBanner" / -->
			</div>
			<?php if($row_impcat["BannerImage"] != '') { ?>
				<div class="addcatBody">
					<img src="images/ImprintCategory/<?=stripslashes($row_impcat["BannerImage"]);?>" />
				</div>
            <?php } ?>
			
            <input type="submit" class="impbutton" id="btnImpEdit" name="btnImpEdit" value="Update Category" />
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