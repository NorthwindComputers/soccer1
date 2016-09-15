<?php
	
	require 'db.php';
		
	if($_POST["type"] == "setcolor") {
		
		$id = mysql_real_escape_string($_POST["id"]);
		$sql_categories = "SELECT DISTINCT Category FROM colors ORDER BY Category";
		$result_categories = mysql_query($sql_categories);
		
		$sql_optcat = "SELECT ColorCategory FROM product_options WHERE BundleID=$id LIMIT 1";
		$result_optcat = mysql_query($sql_optcat);
		$row_optcat = mysql_fetch_assoc($result_optcat);
		
		?>
        
		<strong>Select Color Category: </strong><br/>
		<select id="colorCat" name="colorCat" onchange="changeColor();" style="width: 200px;">
        	<option value="">Select Category</option>
			<?php
            while($row_categories = mysql_fetch_array($result_categories)) {
				if($row_optcat["ColorCategory"] == $row_categories["Category"]) {
					$selected = ' selected="selected" ';
				} else {
					$selected = '';
				}
                echo '<option value="'.$row_categories["Category"].'" '.$selected.'>'.$row_categories["Category"].'</option>';
            }
            ?>
		</select>
		<br/><br/>
        <div id="viewcolors"></div>
        <script>
			$("#viewcolors").load('includes/inc_multimedia.php', {"type":"colors", "id":<?=$id;?>, "cat":""});
			function changeColor() {
				var cat = $("#colorCat").val();
				$("#viewcolors").html('<img src="images/loader.gif" />');
				$("#viewcolors").load('includes/inc_multimedia.php', {"type":"colors", "id":<?=$id;?>, "cat":cat});
			}
		</script>
		<?php
		mysql_close($conn);
		exit();
	}
	
	if($_POST["type"] == "colors") {
		
		?>
		
        <strong>Select Color:</strong><br/>
        <select id="color" name="color" style="width: 200px;">
        	<option value="">Select Color</option>
            <?php
				$id = mysql_real_escape_string($_POST['id']);
				$sql_optcolor = "SELECT Color FROM product_options WHERE BundleID=$id LIMIT 1";
				$result_optcolor = mysql_query($sql_optcolor);
				$row_optcolor = mysql_fetch_assoc($result_optcolor);
			
				$sql_colors = "SELECT Color FROM colors ";
				if($_POST["cat"] != '') { 
					$sql_colors .= "WHERE Category='$_POST[cat]'";
				}
				$result_colors = mysql_query($sql_colors);
				
				while($row_colors = mysql_fetch_array($result_colors)) {
					if($row_colors["Color"] == $row_optcolor["Color"]) {
						$selected = ' selected="selected" ';
					} else {
						$selected = '';
					}
					echo '<option value="'.$row_colors["Color"].'" '.$selected.'>'.$row_colors["Color"].'</option>';
				}
			?>
        </select>
        <br/><br/>
        <input type="button" id="ColorSave" name="ColorSave" value="Save" style="width: 100px;" />
        <script>
			$("#ColorSave").click(function() {
				$.post('includes/inc_multimedia.php', {"type":"savecolor", "id":"<?=mysql_real_escape_string($_POST["id"]);?>", "category":"<?=mysql_real_escape_string($_POST["cat"]);?>", "color":$("#color").val()}, function(data){
					alert(data);
				});
			});
		</script>
        
        <?php
		mysql_close($conn);
		exit();
	}
	
	if($_POST["type"] == "savecolor") {
		
		$id = mysql_real_escape_string($_POST["id"]);
		
		$sql_img = "SELECT ProductID, Image FROM product_images WHERE id=$id LIMIT 1";
		$result_img = mysql_query($sql_img);
		$row_img = mysql_fetch_assoc($result_img);
		$image = $row_img["Image"];
		$prodid = $row_img["ProductID"];
		
		$sql_chk = "SELECT id FROM product_options WHERE BundleID=$id LIMIT 1";
		$result_chk = mysql_query($sql_chk);
		$num_chk = mysql_num_rows($result_chk);
		
		$sql = "SET ProductID=$prodid, Color='$_POST[color]', ColorImage='$image', ColorCategory='$_POST[category]', BundleID=$id";
		
		if($num_chk>0) {
			$sql_update = "UPDATE product_options $sql WHERE BundleID=$id LIMIT 1";
			if(!mysql_query($sql_update)) {
				echo mysql_error();
			}
		} else {
			$sql_add = "INSERT INTO product_options $sql ";
			if(!mysql_query($sql_add)) {
				echo mysql_error();
			}
		}
		echo "Color Saved!";
		
		mysql_close($conn);
		exit();
	}
?>