<?php
	/*************************************
	 * Options and Pricing include file
	 *
	 * Updated: 03 August 2016
	 * By: Richard Tuttle
	 ************************************/
	
	require 'db.php';
	
	/* if (isset($_POST["btnUpload"])) {
		if($_FILES["file"]["name"] != '') {
			if($_FILES["file"]["error"] > 0) {
				echo "Error: " . $_FILES["file"]["error"];
			} else {
				$fileName = $_FILES["file"]["name"];
				$folderLoc = "/home/socnet/public_html/images/productImages/";
				move_uploaded_file($_FILES["file"]["tmp_name"], $folderLoc.$fileName);
			}
		}
	}
	*/
	$num = mysql_real_escape_string($_POST["num"]);
	$prodid = mysql_real_escape_string($_POST["id"]);
	$typeChoice = mysql_real_escape_string($_POST["type"]);

	if ($typeChoice == "color") {
		$colorCat = mysql_real_escape_string($_POST["colorCat"]);
		
		if($colorCat != '' && $colorCat != 'All') {
			$where = " WHERE Category='$colorCat' ORDER BY Color";
		} else {
			$where = " ORDER BY Color";
		}
		
?>
		<div id="divColor<?=$num;?>">
			<table border="0" cellpadding="0" cellspacing="0" style="margin: 0 0 0 2px;">
				<tr>
				  <td style="padding: 6px 0 6px 0; height: 40px; width: 130px;">
					<select id="color<?=$num;?>" name="color<?=$num;?>" onChange="setSKU('colorsku<?=$num;?>', this.value, 'colors')">
					<option value="">select color..</option>
						<?php
						$sql_color = "SELECT * FROM colors".$where;
						$result_color = mysql_query($sql_color);
						while ($row_color = mysql_fetch_array($result_color)) {
							echo "<option value=\"$row_color[id]\">$row_color[Color]</option>";
						}
						?>
					</select>
				  </td>
				  <td style="height: 36px; width: 60px;"><p id="colorsku<?=$num;?>">&nbsp;</p></td>
				  <td class="img_change" style="text-align: center; height: 42px; width: 100px;"><img id="coloricon<?=$num;?>" name="coloricon<?=$num;?>" src="../images/blank.gif" alt="" style="float: none;"></td>
				  <td class="img_change" style="height: 42px; width: 130px;">
						<form id="frmImg<?=$num;?>" action="includes/imgUpload.php" target="imgUpload" method="post" enctype="multipart/form-data" style="position: relative;"> 
						<input style="position: absolute; -moz-opacity:0; filter:alpha(opacity: 0); opacity: 0; width: 100px; height: 15px;" type="file" id="file<?=$num;?>" name="file" onchange="$('#frmImg<?=$num;?>').submit(); $('#colorimg<?=$num;?>').val($('#file<?=$num;?>').val().replace(/C:\\fakepath\\/i,'')); $('#img<?=$num;?>').attr('src', '../images/productImages/'+$('#file<?=$num;?>').val().replace(/C:\\fakepath\\/i,''));">
						<img id="img<?=$num;?>" src="" style="width: 40px; height: 40px; float: left">
						<input type="submit" id="btnUpload" name="btnUpload" value="Upload">
						<!-- img src="../images/price_browse.png" -->
						</form>
				<input type="hidden" id="colorimg<?=$num;?>" name="colorimg<?=$num;?>" value="">
			  </td>
              	  <td style="padding: 4px 0; height: 44px; width: 180px;">
                  	<select id="trim<?=$num;?>" name="trim<?=$num;?>">
                    	<option value="">Select Trim</option>
                        <?php
							$sql_trim = "SELECT * FROM colors";
							$result_trim = mysql_query($sql_trim);
							
							while($row_trim = mysql_fetch_array($result_trim)) {
								echo "<option value=\"$row_trim[Color]\">$row_trim[Color]</option>";
							}
						?>
                    </select>
                  </td>
				  <td style="padding:4px 0; height: 44px; width: 140px;"><input type="text" style="height: 22px; border: 1px solid #a1a1a1; background-color: #bfbfbf; text-align: center;" id="coloraddprice<?=$num;?>" name="coloraddprice<?=$num;?>" value="0.00" /></td>
				  <td style="border-right:1px solid #a1a1a1; padding:4px 0; height: 44px; width: 80px;"><input type="button" style="background-color: #f0f0f0; border: 1px solid #c9c9c9; width: 70px;" id="c_remove<?=$num;?>" name="c_remove<?=$num;?>" onclick="removeItem('Color', '<?=$num;?>');" value="remove" /></td>
				</tr>
			</table>
		</div>
		<?php 
		mysql_close($conn);
		exit(); 
	} 

	if($typeChoice == "size") {
		$sizeCat = mysql_real_escape_string($_POST["sizeCat"]);
		
		if($sizeCat != '' && $sizeCat != 'All') {
			$where = " WHERE Category='$sizeCat' ORDER BY Size";
		} else {
			$where = " ORDER BY Size";
		}
		?>
		<div id="divSize<?=$num;?>">
			<table border="0" cellspacing="0" cellpadding="0" style="margin: 0 0 0 2px;">
				<tr>
				  <td style="padding: 6px 0 6px 0;">
					<select id="size<?=$num;?>" name="size<?=$num;?>" onChange="setSKU('sizesku<?=$num;?>', this.value, 'sizes')">
					<option value="">select size...</option>
						<?php
							$sql_size = "SELECT * FROM sizes".$where;
							$result_size = mysql_query($sql_size);
							
							while($row_size = mysql_fetch_array($result_size)) {
								echo "<option value=\"$row_size[id]\">$row_size[Size]</option>";
							}
						?>
					</select>
				  </td>
				  <td style="padding:4px 0;">
				  		<input type="text" style="height: 22px; border: 1px solid #a1a1a1; background-color: #bfbfbf; text-align: center;" id="position<?=$num;?>" name="position<?=$num;?>" value="0" />
				 </td>
				  <td style="width: 120px;"><p id="sizesku<?=$num;?>">&nbsp;</p></td>
				  <td class="img_change" style="text-align: center;"><img id="sizeicon<?=$num;?>" name="sizeicon<?=$num;?>" src="../images/blank.gif" alt="" style="float: none;" /></td>
				  <td style="padding:4px 0;"><input type="text" style="height: 22px; border: 1px solid #a1a1a1; background-color: #bfbfbf; text-align: center;" id="sizeaddprice<?=$num;?>" name="sizeaddprice<?=$num;?>" value="0.00" /></td>
				  <td style="width: 140px;">
					<select id="sizegender<?=$num;?>" name="sizegender<?=$num;?>">
						<option value="">select range...</option>
						<?php
							$sql_gender = "SELECT GenderName FROM gender ORDER BY GenderName";
							$result_gender = mysql_query($sql_gender);
							
							while($row_gender = mysql_fetch_array($result_gender)) {
								echo "<option value=\"$row_gender[GenderName]\">$row_gender[GenderName]</option>";
							}
						?>
					</select>
				  </td>
				  <td style="border-right:1px solid #a1a1a1; padding:4px 0; height: 24px; width: 80px;"><input type="button" style="background-color: #f0f0f0; border: 1px solid #c9c9c9; width: 70px;" id="s_remove<?=$num;?>" name="s_remove<?=$num;?>" onclick="removeItem('Size', '<?=$num;?>');" value="remove" /></td>
				</tr>
			</table>
		</div> 
		<?php
		mysql_close($conn);
		exit();
	}

	if($typeChoice == "sku") {
		$num = mysql_real_escape_string($_POST["num"]);
		$tbl = mysql_real_escape_string($_POST["name"]);
		$sql_sku = "SELECT SKU FROM $tbl WHERE id='$num' LIMIT 1";
		$result_sku = mysql_query($sql_sku);
		$row_sku = mysql_fetch_assoc($result_sku);
		echo $row_sku["SKU"];
		mysql_close($conn);
		exit();
	}

	if($typeChoice == "icon") {
		$num = mysql_real_escape_string($_POST["num"]);
		$tbl = mysql_real_escape_string($_POST["name"]);
		$sql_icon = "SELECT Icon FROM $tbl WHERE id='$num' LIMIT 1";
		$result_icon = mysql_query($sql_icon);
		$row_icon = mysql_fetch_assoc($result_icon);
		echo "../images/".$row_icon["Icon"];
		mysql_close($conn);
		exit();
	}


	if($typeChoice == "inventoryColorSize") {
		if($_POST["size"] == '') {
			echo "<script>alert('Please add sizes before generating inventory');</script>";
			mysql_close($conn);
			exit();
		}
		
		if($_POST["color"] == '') {
			echo "<script>alert('Please add colors before generating inventory');</script>";
			mysql_close($conn);
			exit();
		}
		
		foreach($_POST["size"] as $value_s) {
			$sizevals = explode("_",$value_s);
			if($sizevals[0]=='') {
				echo "<script>alert('Please make a selection for all sizes before generating inventory');</script>";
				mysql_close($conn);
				exit();
			}
			
			$sql_size = "SELECT Size FROM sizes WHERE id=$sizevals[0] LIMIT 1";
			$result_size = mysql_query($sql_size);
			$row_size = mysql_fetch_assoc($result_size);
			$header_s .="<th>$row_size[Size]</th>";
		}

		foreach($_POST["color"] as $value_c) {
			$colorvals = explode("_", $value_c);

			if($colorvals[0]=='') {
				echo "<script>alert('Please make a selection for all colors before generating inventory');</script>";
				mysql_close($conn);

				exit();
			}

			$sql_color = "SELECT Color FROM colors WHERE id=$colorvals[0] LIMIT 1";
			$result_color = mysql_query($sql_color);			
			$row_color = mysql_fetch_assoc($result_color);
			$rows .= '<tr style="width: 100%;">';
			$rows .= '<td style="border-top:1px solid #a1a1a1; border-right:0px; padding:8px 0 7px 0;">'.$row_color["Color"].'</td>';

			foreach($_POST["size"] as $value_s) {
				$sizevals = explode("_",$value_s);
				$rows .= '<td style="padding:4px 0;"><input class="inv" type="text" id="color'.$colorvals[1].'_size'.$sizevals[1].'" name="color'.$colorvals[1].'_size'.$sizevals[1].'" value="0"/></td>';
			}

			$rows .= "</tr>";
		}
	?>
		<!-- table style="position: relative; width: 400px; float: right; margin-bottom: 40px;" -->
		<table>
			<tr>
				<td style="height: 30px; border-top: 1px solid #a1a1a1; vertical-align: text-bottom;">Default Inventory</td>
				<td style="height: 30px; border-top: 1px solid #a1a1a1;"><input type="text" id="defaultInv" name="defaultInv" style="height: 22px; border: 1px solid #a1a1a1; background-color: #bfbfbf; text-align: center;" value="0" /></td>
				<td style="height: 30px; border-top: 1px solid #a1a1a1; border-right: 1px solid #a1a1a1;"><input type="button" style="background-color: #ffb400; border: 1px solid #a1a1a1;" id="updateInv" name="updateInv" value="Update Inventory" onClick="setInv();" /></td>
			</tr>
		</table>
	<table style="width: 100%; float: none; margin-top: 80px;">
		<tr style="width: 100%">
			<th style="border:none; border-right:1px solid #a1a1a1; background:none; padding-left:1px;">&nbsp;</th>
        		<?=$header_s;?>
      		</tr>
		<?=$rows;?>
        </table>	
	<?php	
		mysql_close($conn);
		exit();
	}
	
	if($typeChoice == "inventorySize") {
		if($_POST["size"] == '') {
			echo "<script>alert('Please add sizes before generating inventory');</script>";
			mysql_close($conn);
			exit();
		}
		
		foreach($_POST["size"] as $value_s) {
			$sizevals = explode("_", $value_s);

			if($sizevals[0]=='') {
				echo "<script>alert('Please make a selection for all sizes before generating inventory');</script>";
				mysql_close($conn);
				exit();
			}

			$sql_size = "SELECT Size FROM sizes WHERE id=$sizevals[0] LIMIT 1";
			$result_size = mysql_query($sql_size);			
			$row_size = mysql_fetch_assoc($result_size);
			$rows .= '<tr style="width: 100%;">';
			$rows .= '<td style="border-top:1px solid #a1a1a1; border-right:0px; padding:8px 0 7px 0;">'.$row_size["Size"].'</td>';
			$rows .= '<td style="padding:4px 0;"><input class="inv" type="text" id="invsize'.$sizevals[1].'" name="invsize'.$sizevals[1].'" value="0" /></td>';
			$rows .= '</tr>';
		}
		?>
        <!-- table style="position: relative; width: 400px; float: right; margin-bottom: 40px;" -->
        <table>
			<tr>
				<td style="height: 30px; border-top: 1px solid #a1a1a1; vertical-align: text-bottom;">Default Inventory</td>
				<td style="height: 30px; border-top: 1px solid #a1a1a1;"><input type="text" id="defaultInv" name="defaultInv" style="height: 22px; border: 1px solid #a1a1a1; background-color: #bfbfbf; text-align: center;" value="0" /></td>
				<td style="height: 30px; border-top: 1px solid #a1a1a1; border-right: 1px solid #a1a1a1;"><input type="button" style="background-color: #ffb400; border: 1px solid #a1a1a1;" id="updateInv" name="updateInv" value="Update Inventory" onClick="setInv();" /></td>
			</tr>
		</table>
        <table style="width: 100%; float: none; margin-top: 80px;">
            <tr style="width: 100%">
                <th style="border:none; border-right:1px solid #a1a1a1; background:none; padding-left:1px;">&nbsp;</th>
                <th>Inventory</th>
            </tr>
            <?=$rows;?>
        </table>	
        <?php
		mysql_close($conn);
		exit();
	}
	
	if($typeChoice == "inventoryColor") {
		if($_POST["color"] == '') {
			echo "<script>alert('Please add Colors before generating inventory');</script>";
			mysql_close($conn);
			exit();
		}
		
		foreach($_POST["color"] as $value_c) {
			$colorvals = explode("_", $value_c);

			if($colorvals[0]=='') {
				echo "<script>alert('Please make a selection for all colors before generating inventory');</script>";
				mysql_close($conn);
				exit();
			}

			$sql_color = "SELECT Color FROM colors WHERE id=$colorvals[0] LIMIT 1";
			$result_color = mysql_query($sql_color);			
			$row_color = mysql_fetch_assoc($result_color);
			$rows .= '<tr style="width: 100%;">';
			$rows .= '<td style="border-top:1px solid #a1a1a1; border-right:0px; padding:8px 0 7px 0;">'.$row_color["Color"].'</td>';
			$rows .= '<td style="padding:4px 0;"><input class="inv" type="text" id="invcolor'.$colorvals[1].'" name="invcolor'.$colorvals[1].'" value="0" /></td>';
			$rows .= '</tr>';
		}
		?>
		<!-- table style="position: relative; width: 400px; float: right; margin-bottom: 40px;" -->
        <table>
			<tr>
				<td style="height: 30px; border-top: 1px solid #a1a1a1; vertical-align: text-bottom;">Default Inventory</td>
				<td style="height: 30px; border-top: 1px solid #a1a1a1;"><input type="text" id="defaultInv" name="defaultInv" style="height: 22px; border: 1px solid #a1a1a1; background-color: #bfbfbf; text-align: center;" value="0" /></td>
				<td style="height: 30px; border-top: 1px solid #a1a1a1; border-right: 1px solid #a1a1a1;"><input type="button" style="background-color: #ffb400; border: 1px solid #a1a1a1;" id="updateInv" name="updateInv" value="Update Inventory" onClick="setInv();" /></td>
			</tr>
		</table>
        <table style="width: 100%; float: none; margin-top: 80px;">
            <tr style="width: 100%">
                <th style="border:none; border-right:1px solid #a1a1a1; background:none; padding-left:1px;">&nbsp;</th>
                <th>Inventory</th>
            </tr>
            <?=$rows;?>
        </table>
        <?php
		mysql_close($conn);
	exit();
	}

	if($typeChoice == "pricing") {
	?>
    <div id="price_<?=$num;?>">
	<table class="P_right_table" style="width: 100%;" cellpadding="0" cellspacing="1">
       <tr>
          <td style="width: 154px; background-color: #1f89b5;">
          	<select id="GenderSKU_<?=$num;?>" name="GenderSKU_<?=$num;?>" style="height: 22px; width: 90%;" onchange="$('#gender_<?=$num;?>').val($('#GenderSKU_<?=$num;?> :selected').text());">
            	<option value="">Select option</option>
			<?php
				$sql_gender = "SELECT GenderName, GenderSKU FROM gender";
				$result_gender = mysql_query($sql_gender);
				
				while($row_gender = mysql_fetch_array($result_gender)) {
					echo "<option value=\"$row_gender[GenderSKU]\">$row_gender[GenderName]</option>";
				}
			?>
            </select>
            <input type="hidden" id="gender_<?=$num;?>" name="gender_<?=$num;?>" value="" />
          </td>
          <td><input type="text" id="MSRP_<?=$num;?>" name="MSRP_<?=$num;?>" value="0.00"/></td>
          <td><input type="text" id="NonMember_<?=$num;?>" name="NonMember_<?=$num;?>" value="0.00"/></td>
          <td class="PRT_color1"><input type="text" id="Option1_<?=$num;?>" name="Option1_<?=$num;?>" value="0.00"/></td>
          <td class="PRT_color2"><input type="text" id="Option2_<?=$num;?>" name="Option2_<?=$num;?>" value="0.00"/></td>
          <td class="PRT_color3"><input type="text" id="Option3_<?=$num;?>" name="Option3_<?=$num;?>" value="0.00"/></td>
          <td class="PRT_color4"><input type="text" id="Option4_<?=$num;?>" name="Option4_<?=$num;?>" value="0.00"/></td>
          <td class="edit_pen" style="width:31px;"><img src="../images/del_img.png" alt="Remove pricing option" style="cursor: pointer;" onClick="remPrice('<?=$num;?>');" /></td>
        </tr>
	</table>
    </div>
	<?php
		mysql_close($conn);
		exit();
	}

	if($typeChoice == "viewcolor") {
		$sql_coloropt = "SELECT DISTINCT Color, ColorSKU, ColorIcon, ColorAddPrice, ColorImage, ColorCategory, TrimColor FROM product_options WHERE ProductID=$prodid ORDER BY id";
		$result_coloropt = mysql_query($sql_coloropt);
		$c_num = 2;
		while($row_coloropt = mysql_fetch_array($result_coloropt)) {
			if($row_coloropt["ColorCategory"] == 'All') {
				$where = " ORDER BY Color";
			} else {
				$where = " WHERE Category='$row_coloropt[ColorCategory]' ORDER BY Color";
			}
?>  
<div id="divColor<?=$c_num;?>">
	<table border="0" cellpadding="0" cellspacing="0" style="margin: 0 0 0 2px;">
 		<tr>
          <td style="padding: 6px 0 6px 0; height: 40px; width: 130px;">
          	<select id="color<?=$c_num;?>" name="color<?=$c_num;?>" onChange="setSKU('colorsku<?=$c_num;?>', this.value, 'colors')">
			<option value="">select color ...</option>
            	<?php
					$sql_color = "SELECT * FROM colors".$where;
					$result_color = mysql_query($sql_color);
					$found = 'no';
					while($row_color = mysql_fetch_array($result_color)) {
						if($row_coloropt["Color"] == $row_color["Color"]) {
							$selected = ' selected="selected"';
							$found = 'yes';
						} else {
							$selected = '';
						}
						echo "<option value=\"$row_color[id]\" $selected>$row_color[Color]</option>";
					}

					if($found == 'no') {
						$sql_getcolor = "SELECT * FROM colors WHERE SKU='$row_coloropt[ColorSKU]' AND Color='$row_coloropt[Color]' LIMIT 1";
						$result_getcolor = mysql_query($sql_getcolor);
						$row_getcolor = mysql_fetch_assoc($result_getcolor);
						echo "<option value=\"$row_getcolor[id]\" selected='selected'>$row_getcolor[Color]</option>";
					}
				?>
            </select>
          </td>
          <td style="height: 36px; width: 60px;"><p id="colorsku<?=$c_num;?>"><?=$row_coloropt["ColorSKU"];?></p></td>
          <td class="img_change" style="text-align: center; height: 42px; width: 100px;"><img id="coloricon<?=$c_num;?>" name="coloricon<?=$c_num;?>" src="../images/<?=$row_coloropt["ColorIcon"];?>" alt="" style="float: none;"></td>
          <td class="img_change" style="height: 42px; width: 150px;">
                <form id="frmImg<?=$c_num;?>" action="includes/imgUpload.php" method="post" enctype="multipart/form-data" target="imgUpload" style="position: relative;">
          		<input style="position: absolute; -moz-opacity:0; filter:alpha(opacity: 0); opacity: 0; width: 100px;" type="file" id="file<?=$c_num;?>" name="file" onchange="$('#colorimg<?=$c_num;?>').val($('#file<?=$c_num;?>').val().replace(/C:\\fakepath\\/i,'')); $('#frmImg<?=$c_num;?>').submit(); $('#img<?=$c_num;?>').attr('src', '../images/productImages/'+$('#file<?=$c_num;?>').val().replace(/C:\\fakepath\\/i,''));">
                	<img id="img<?=$c_num;?>" src="../images/productImages/<?=$row_coloropt["ColorImage"];?>" style="width: 40px; height: 40px; float: left">
                	<input type="submit" id="btnUpload" name="btnUpload" value="Upload">
                	<!-- img src="../images/price_browse.png" -->
                </form>
            <input type="hidden" id="colorimg<?=$c_num;?>" name="colorimg<?=$c_num;?>" value="<?=$row_coloropt["ColorImage"];?>">
          </td>
      	  <td style="padding: 4px 2px; height: 44px; width: 180px;">
          		<select id="trim<?=$c_num;?>" name="trim<?=$c_num;?>">
                	<option value="">Select Trim ...</option>
                    <?php
						$sql_trim = "SELECT * FROM colors";
						$result_trim = mysql_query($sql_trim);
						while($row_trim = mysql_fetch_array($result_trim)) {
							if($row_coloropt["TrimColor"] == $row_trim["Color"]) {
								$selected = ' selected="selected"';
							} else {
								$selected = '';
							}
							echo "<option value=\"$row_trim[Color]\" $selected>$row_trim[Color]</option>";
						}
					?>
                </select>
          </td>
          <td style="padding:4px 0; height: 44px; width: 100px;"><input type="text" style="height: 22px; border: 1px solid #a1a1a1; background-color: #bfbfbf; text-align: center;" id="coloraddprice<?=$c_num;?>" name="coloraddprice<?=$c_num;?>" value="<?=$row_coloropt["ColorAddPrice"];?>" /></td>
          <td style="border-right:1px solid #a1a1a1; padding:4px 0; height: 44px; width: 79px;"><input type="button" style="background-color: #f0f0f0; border: 1px solid #c9c9c9; width: 70px;" id="c_remove<?=$num;?>" name="c_remove<?=$num;?>" onclick="removeItem('Color', '<?=$c_num;?>');" value="remove" /></td>
		</tr>
	</table>
</div>
<script type="text/javascript">
	arrcolors.push(<?=$c_num;?>);
	$("#colornum").val(<?=$c_num;?>);
</script>
<?php
	$c_num++;
}
		mysql_close($conn);
		exit();
	}

	if($typeChoice == "viewsize") {
		$sql_sizeopt = "SELECT DISTINCT Size, Position, SizeSKU, SizeIcon, SizeAddPrice, Gender, SizeCategory FROM product_options WHERE ProductID=$prodid ORDER BY id";
		$result_sizeopt = mysql_query($sql_sizeopt);
		$s_num = 2;
		while($row_sizeopt = mysql_fetch_array($result_sizeopt)) {
			if($row_sizeopt["SizeCategory"] == 'All') {
				$where = " ORDER BY Size";
			} else {
				$where = " WHERE Category='$row_sizeopt[SizeCategory]' ORDER BY Size";
			}
	?>
<div id="divSize<?=$s_num;?>">
	<table border="0" cellspacing="0" cellpadding="0" style="margin: 0 0 0 2px;">
        <tr>
          <td style="padding: 6px 0 6px 0;">
          	<select id="size<?=$s_num;?>" name="size<?=$s_num;?>" onChange="setSKU('sizesku<?=$s_num;?>', this.value, 'sizes')">
			<option value="">select size ...</option>
            	<?php
					$sql_size = "SELECT * FROM sizes ".$where;
					$result_size = mysql_query($sql_size);
					$found = 'no';
					while($row_size = mysql_fetch_array($result_size)) {
						if(stripslashes($row_sizeopt["Size"]) == $row_size["Size"]) {
							$selected = ' selected="selected"';
							$found='yes';
						} else {
							$selected='';
						}
						echo "<option value=\"$row_size[id]\" $selected>$row_size[Size]</option>";
					}

					if($found == 'no') {
						$sql_getsize = "SELECT * FROM sizes WHERE SKU='$row_sizeopt[SizeSKU]' AND Size='".stripslashes($row_sizeopt[Size])."' LIMIT 1";
						$result_getsize = mysql_query($sql_getsize);
						$row_getsize = mysql_fetch_assoc($result_getsize);
						echo "<option value=\"$row_getsize[id]\" selected='selected'>$row_getsize[Size]</option>";
					}
				?>
            </select>
          </td>
          <td style="padding:4px 0;width:100px;">
          	<input type="text" style="width:80px;height: 22px; border: 1px solid #a1a1a1; background-color: #bfbfbf; text-align: center;" id="position<?=$s_num;?>" name="position<?=$s_num;?>" value="<?=$row_sizeopt["Position"];?>" />
          </td>
          <td style="width: 120px;"><p id="sizesku<?=$s_num;?>"><?=$row_sizeopt["SizeSKU"];?></p></td>
          <td class="img_change" style="text-align: center;width: 120px;"><img id="sizeicon<?=$s_num;?>" name="sizeicon<?=$s_num;?>" src="../images/<?=$row_sizeopt["SizeIcon"];?>" alt="" style="float: none;" /></td>
          <td style="padding:4px 0;width: 100px;"><input type="text" style="width: 80px;height: 22px; border: 1px solid #a1a1a1; background-color: #bfbfbf; text-align: center;" id="sizeaddprice<?=$s_num;?>" name="sizeaddprice<?=$s_num;?>" value="<?=$row_sizeopt["SizeAddPrice"];?>" /></td>
	  <td style="width: 140px;">
		<select id="sizegender<?=$s_num;?>" name="sizegender<?=$s_num;?>">
			<option value="">select range ...</option>
			<?php
				$sql_gender = "SELECT GenderName FROM gender ORDER BY GenderName";
				$result_gender = mysql_query($sql_gender);
				while($row_gender = mysql_fetch_array($result_gender)) {
					$selected = ($row_gender["GenderName"] == $row_sizeopt["Gender"]?' Selected="Selected" ':'');
					echo "<option value=\"$row_gender[GenderName]\" $selected>$row_gender[GenderName]</option>";
				}
			?>
		</select>
	  </td>
          <td style="border-right:1px solid #a1a1a1; padding:4px 0; height: 24px; width: 80px;"><input type="button" style="background-color: #f0f0f0; border: 1px solid #c9c9c9; width: 70px;" id="s_remove<?=$s_num;?>" name="s_remove<?=$s_num;?>" onclick="removeItem('Size', '<?=$s_num;?>');" value="remove" /></td>
        </tr>
	</table>
</div> 
<script type="text/javascript">
	arrsizes.push(<?=$s_num;?>);
	$("#sizenum").val(<?=$s_num;?>);
</script>
<?php
			$s_num++;
		}
		mysql_close($conn);
		exit();
	}

	if($typeChoice == "viewprice") {
		$sql_priceopt = "SELECT * FROM product_pricing WHERE ProductID=$prodid ORDER BY id";
		$result_priceopt = mysql_query($sql_priceopt);

		$p_num = 2;
		while($row_priceopt = mysql_fetch_array($result_priceopt)) {
?>
<div id="price_<?=$p_num;?>">
<table class="P_right_table" style="width: 100%;" cellpadding="0" cellspacing="1">
        <tr>
          <td style="width: 154px; background-color: #1f89b5;">
          	<select id="GenderSKU_<?=$p_num;?>" name="GenderSKU_<?=$p_num;?>" style="height: 22px; width: 90%;" onchange="$('#gender_<?=$p_num;?>').val($('#GenderSKU_<?=$p_num;?> :selected').text());">
            	<option value="">Select Option</option>
			<?php
				$sql_gender = "SELECT GenderName, GenderSKU FROM gender";
				$result_gender = mysql_query($sql_gender);
				while($row_gender = mysql_fetch_array($result_gender)) {
					$selected = ($row_priceopt["Gender"] == $row_gender["GenderName"]?' Selected="Selected" ':'');
					echo "<option value=\"$row_gender[GenderSKU]\" $selected>$row_gender[GenderName]</option>";
				}
			?>
            </select>
            <input type="hidden" id="gender_<?=$p_num;?>" name="gender_<?=$p_num;?>" value="<?=$row_priceopt["Gender"];?>" />
          </td>
          <td><input type="text" id="MSRP_<?=$p_num;?>" name="MSRP_<?=$p_num;?>" value="<?=$row_priceopt["MSRP"];?>"/></td>
          <td><input type="text" id="NonMember_<?=$p_num;?>" name="NonMember_<?=$p_num;?>" value="<?=$row_priceopt["NonMember"];?>"/></td>
          <td><input type="text" id="Option1_<?=$p_num;?>" name="Option1_<?=$p_num;?>" value="<?=$row_priceopt["Option1Price"];?>"/></td>
          <td class="PRT_color2"><input type="text" id="Option2_<?=$p_num;?>" name="Option2_<?=$p_num;?>" value="<?=$row_priceopt["Option2Price"];?>"/></td>
          <td class="PRT_color3"><input type="text" id="Option3_<?=$p_num;?>" name="Option3_<?=$p_num;?>" value="<?=$row_priceopt["Option3Price"];?>"/></td>
          <td class="PRT_color4"><input type="text" id="Option4_<?=$p_num;?>" name="Option4_<?=$p_num;?>" value="<?=$row_priceopt["Option4Price"];?>"/></td>
          <td class="edit_pen" style="width:31px;"><a href="#"><img src="../images/del_img.png" alt="" onClick="remPrice('<?=$p_num;?>');" /></a></td>
        </tr>
</table>
</div>
<script type="text/javascript">
	arrprices.push(<?=$p_num;?>);
	$("#pricenum").val(<?=$p_num;?>);
</script>
<?php
			$p_num++;
		}
		mysql_close($conn);
		exit();
	}

	if($typeChoice == "viewinventoryColorSize") {
		$sql_invsize = "SELECT DISTINCT Size, SizeSKU FROM product_options WHERE ProductID=$prodid ORDER BY id";
		$result_invsize = mysql_query($sql_invsize);
		$num_rows = mysql_num_rows($result_invsize);
		if($num_rows < 1) {
			mysql_close($conn);
			exit();
		}

        $s_num = 2;
		while($row_invsize = mysql_fetch_array($result_invsize)) {
			$header_s .= "<th style='width: 65px; height: auto;'>$row_invsize[Size]</th>";
			$sql_invcolor = "SELECT DISTINCT Color FROM product_options WHERE ProductID=$prodid AND SizeSKU='$row_invsize[SizeSKU]' ORDER BY id";
			$result_invcolor = mysql_query($sql_invcolor);
			$c_num = 2;
			while($row_invcolor = mysql_fetch_array($result_invcolor)) {
             	 $sql_invsize_color = "SELECT Size, Inventory FROM product_options WHERE ProductID=$prodid AND SizeSKU='$row_invsize[SizeSKU]' AND Color='$row_invcolor[Color]' ORDER BY id";
		    	 $result_invsize_color = mysql_query($sql_invsize_color);
				 while($row_invsize_color = mysql_fetch_array($result_invsize_color)) {
			      		$rows[$row_invcolor["Color"]][] = '<td style="padding:4px 0;width:65px;"><input class="inv" type="text" id="color'.$c_num.'_size'.$s_num.'" name="color'.$c_num.'_size'.$s_num.'" value="'.$row_invsize_color['Inventory'].'" style="width:60px"/></td>';
					$c_num++;
				}
			}
        	$s_num++;
		}	
	?>
		<!-- table style="position: relative; width: 400px; float: right; margin-bottom: 20px;clear:both;display:block;" -->
		<table>
			<tr>
				<td style="height: 30px; border-top: 1px solid #a1a1a1; vertical-align: text-bottom;">Default Inventory</td>
				<td style="height: 30px; border-top: 1px solid #a1a1a1;"><input type="text" id="defaultInv" name="defaultInv" style="height: 22px; border: 1px solid #a1a1a1; background-color: #bfbfbf; text-align: center;" value="0" /></td>
				<td style="height: 30px; border-top: 1px solid #a1a1a1; border-right: 1px solid #a1a1a1;"><input type="button" style="background-color: #ffb400; border: 1px solid #a1a1a1;" id="updateInv" name="updateInv" value="Update Inventory" onClick="setInv();" /></td>
			</tr>
		</table>
	<div style="clear:both;height:1px">&nbsp;</div>
	<table style="width: 100%; float: none; margin-top: 80px;">
		<tr style="width: 100%">
			<th style="border:none; border-right:1px solid #a1a1a1; background:none; padding-left:1px;">&nbsp;</th>
        		<?=$header_s;?>
      		</tr>
		<?php foreach ($rows as $key => $color): ?>
		     <tr style="width: 100%;">
                        <td style="border-top:1px solid #a1a1a1; border-right:0px; padding:8px 0 7px 0;"><?php echo $key; ?></td>
		  <?php foreach ($color as $item): ?>
		      <?php echo $item; ?>
		  <?php endforeach ;?>
		  </tr>
		<?php endforeach; ?>
        </table>
<?php
		mysql_close($conn);
		exit();
	}

	if($typeChoice == "viewinventorySize") {
		$sql_invsize = "SELECT DISTINCT Size, Inventory FROM product_options WHERE ProductID=$prodid ORDER BY id";
		$result_invsize = mysql_query($sql_invsize);
		$s_num = 2;
		while($row_invsize = mysql_fetch_array($result_invsize)) {
			$rows .= '<tr style="width: 100%;">';
			$rows .= '<td style="border-top:1px solid #a1a1a1; border-right:0px; padding:8px 0 7px 0;">'.$row_invsize["Size"].'</td>';
			$rows .= '<td style="padding:4px 0;"><input class="inv" type="text" id="invsize'.$s_num.'" name="invsize'.$s_num.'" value="'.$row_invsize[Inventory].'"/></td>';
			$rows .= "</tr>";
			$s_num++;
		}
		?>
        <!-- table style="position: relative; width: 400px; float: right; margin-bottom: 40px;" -->
        <table>
			<tr>
				<td style="height: 30px; border-top: 1px solid #a1a1a1; vertical-align: text-bottom;">Default Inventory</td>
				<td style="height: 30px; border-top: 1px solid #a1a1a1;"><input type="text" id="defaultInv" name="defaultInv" style="height: 22px; border: 1px solid #a1a1a1; background-color: #bfbfbf; text-align: center;" value="0" /></td>
				<td style="height: 30px; border-top: 1px solid #a1a1a1; border-right: 1px solid #a1a1a1;"><input type="button" style="background-color: #ffb400; border: 1px solid #a1a1a1;" id="updateInv" name="updateInv" value="Update Inventory" onClick="setInv();" /></td>
			</tr>
		</table>
        <table style="width: 100%; float: none; margin-top: 80px;">
            <tr style="width: 100%">
                <th style="border:none; border-right:1px solid #a1a1a1; background:none; padding-left:1px;">&nbsp;</th>
                <th>Inventory</th>
            </tr>
            <?=$rows;?>
        </table>	
        <?php
		mysql_close($conn);
		exit();
	}

	if($typeChoice == "viewinventoryColor") {
		$sql_invcolor = "SELECT DISTINCT Color, Inventory FROM product_options WHERE ProductID=$prodid ORDER BY id";
		$result_invcolor = mysql_query($sql_invcolor);
		$c_num = 2;
		while($row_invcolor = mysql_fetch_array($result_invcolor)) {
			$rows .= '<tr style="width: 100%;">';
			$rows .= '<td style="border-top:1px solid #a1a1a1; border-right:0px; padding:8px 0 7px 0;">'.$row_invcolor["Color"].'</td>';
			$rows .= '<td style="padding:4px 0;"><input class="inv" type="text" id="invcolor'.$c_num.'" name="invcolor'.$c_num.'" value="'.$row_invcolor[Inventory].'"/></td>';
			$rows .= "</tr>";
			$c_num++;
		}
		?>
        <!-- table style="position: relative; width: 400px; float: right; margin-bottom: 40px;" -->
        <table>>
			<tr>
				<td style="height: 30px; border-top: 1px solid #a1a1a1; vertical-align: text-bottom;">Default Inventory</td>
				<td style="height: 30px; border-top: 1px solid #a1a1a1;"><input type="text" id="defaultInv" name="defaultInv" style="height: 22px; border: 1px solid #a1a1a1; background-color: #bfbfbf; text-align: center;" value="0" /></td>
				<td style="height: 30px; border-top: 1px solid #a1a1a1; border-right: 1px solid #a1a1a1;"><input type="button" style="background-color: #ffb400; border: 1px solid #a1a1a1;" id="updateInv" name="updateInv" value="Update Inventory" onClick="setInv();" /></td>
			</tr>
		</table>
        <table style="width: 100%; float: none; margin-top: 80px;">
            <tr style="width: 100%">
                <th style="border:none; border-right:1px solid #a1a1a1; background:none; padding-left:1px;">&nbsp;</th>
                <th>Inventory</th>
            </tr>
            <?=$rows;?>
        </table>	
        <?php
		mysql_close($conn);
		exit();
	}

	if($typeChoice == "hidegender") {	
		?>
        <table class="P_right_table" style="width: 100%;" cellpadding="0" cellspacing="1">
            <tr>
              <td style="width: 154px; background-color: #FFFFFF;"></td>
			  <td><input type="text" id="MSRP_<?=$num;?>" name="MSRP_<?=$num;?>" value="0.00"/></td>
              <td><input type="text" id="NonMember_<?=$num;?>" name="NonMember_<?=$num;?>" value="0.00"/></td>
              <td class="PRT_color1"><input type="text" id="Option1_<?=$num;?>" name="Option1_<?=$num;?>" value="0.00"/></td>
              <td class="PRT_color2"><input type="text" id="Option2_<?=$num;?>" name="Option2_<?=$num;?>" value="0.00"/></td>
              <td class="PRT_color3"><input type="text" id="Option3_<?=$num;?>" name="Option3_<?=$num;?>" value="0.00"/></td>
              <td class="PRT_color4"><input type="text" id="Option4_<?=$num;?>" name="Option4_<?=$num;?>" value="0.00"/></td>
              <td class="edit_pen" style="width:31px;"></td>
            </tr>
        </table>
        <script type="text/javascript">
			arrprices.push(<?=$num;?>);
			$("#pricenum").val(<?=$num;?>);
		</script>
        <?php
		mysql_close($conn);
		exit();
	}

	if($typeChoice == "genderprice") {	
		$sql_price = "SELECT * FROM product_pricing WHERE ProductID=$prodid";
		$result_price = mysql_query($sql_price);
		$row_price = mysql_fetch_assoc($result_price);
		?>
        <table class="P_right_table" style="width: 100%;" cellpadding="0" cellspacing="1">
            <tr>
              <td style="width: 154px; background-color: #FFFFFF;"></td>
			  <td><input type="text" id="MSRP_<?=$num;?>" name="MSRP_<?=$num;?>" value="<?=$row_price["MSRP"];?>"/></td>
              <td><input type="text" id="NonMember_<?=$num;?>" name="NonMember_<?=$num;?>" value="<?=$row_price["NonMember"];?>"/></td>
              <td class="PRT_color1"><input type="text" id="Option1_<?=$num;?>" name="Option1_<?=$num;?>" value="<?=$row_price["Option1Price"];?>"/></td>
              <td class="PRT_color2"><input type="text" id="Option2_<?=$num;?>" name="Option2_<?=$num;?>" value="<?=$row_price["Option2Price"];?>"/></td>
              <td class="PRT_color3"><input type="text" id="Option3_<?=$num;?>" name="Option3_<?=$num;?>" value="<?=$row_price["Option3Price"];?>"/></td>
              <td class="PRT_color4"><input type="text" id="Option4_<?=$num;?>" name="Option4_<?=$num;?>" value="<?=$row_price["Option4Price"];?>"/></td>
              <td class="edit_pen" style="width:31px;"></td>
            </tr>
        </table>
        <script type="text/javascript">
			arrprices.push(<?=$num;?>);
			$("#pricenum").val(<?=$num;?>);
		</script>
        <?php
		mysql_close($conn);
		exit();
	}

	if($typeChoice == "bundleitems") {
		$id = mysql_real_escape_string($_POST["id"]);
		$sql_inv = "SELECT Inventory FROM product_options WHERE ProductID='$id'";
		$result_inv = mysql_query($sql_inv);
		$row_inv = mysql_fetch_assoc($result_inv);
	?>
		<table cellpadding="5" cellspacing="1" style="width: 940px;">
        	<tr style="float: none;">
            	<td style="text-align: left; float: none; padding-left: 15px; border: 0px; font-weight: bold; font-size: 14px; width: 140px;">Inventory:</td>
            	<td style="text-align: left; float: none; padding-left: 15px; border: 0px; font-weight: bold; font-size: 14px; width: 800px;"><input type="text" style="width: 200px;" id="inventory" name="inventory" value="<?=$row_inv["Inventory"];?>" /></td>
            </tr>
        </table>
		<table cellpadding="5" cellspacing="1" style="width: 940px;">
				<tr style="float: none;">
					<td colspan="3" style="text-align: left; float: none; padding-left: 15px; border: 0px; font-weight: bold; font-size: 14px;">Current Items in Bundle</td>
				</tr>
				<?php
					$sql_bitems = "SELECT b.id, b.SortOrder, p.RootSKU, p.BrowserName FROM products p, product_bundles b WHERE b.Items=p.id AND b.ProductID=$id ORDER BY b.SortOrder";
					$result_bitems = mysql_query($sql_bitems);
					$num_bitems = mysql_num_rows($result_bitems);

					if($num_bitems>0) {
						$bnum = 1;
						while($row_bitems=mysql_fetch_array($result_bitems)) {
							echo '<tr style="float: none;"><td style="width: 70%; float: none; border: 0px; text-align: left; padding-left: 20px;">';
							echo $row_bitems["RootSKU"]." -- ".$row_bitems["BrowserName"];
							echo '</td><td style="float: none; border: 0px; width: 15;"><input type="hidden" id="bundleitem_'.$bnum.'" name="bundleitem_'.$bnum.'" value="'.$row_bitems["id"].'" />';
							echo '<input type="text" id="bundleitemsort_'.$bnum.'" name="bundleitemsort_'.$bnum.'" value="'.$row_bitems["SortOrder"].'" /></td>';
							echo '<td style="float: none; border: 0px; width: 15%;"><input type="button" onclick="remBundleItem(\''.$row_bitems["id"].'\')" value="remove" /></td></tr>';
							$bnum++;
						}
					} else {
						echo '<tr style="float: none;"><td colspan="2" style="float: none; border: 0px; text-align: center;"> No Items in bundle</td></tr>';				
					}
				?>			
			</table>
			<input type="hidden" id="totalbundlenum" name="totalbundlenum" value="<?=$bnum;?>" />
	<?php
		mysql_close($conn);
		exit();
	}

	if($typeChoice == "bundlesearch") {
		$sku = mysql_real_escape_string($_POST["sku"]);
		$name = mysql_real_escape_string($_POST["name"]);

		if($sku != '') {
			$where = " WHERE RootSKU LIKE '%$sku%' ";
		}

		if($sku != '' && $name != '') {
			$where .= " AND BrowserName LIKE '%$name%' ";
		}

		if($sku == '' && $name != '') {
			$where .= " WHERE BrowserName LIKE '%$name%' ";
		}
		?>
			<table cellpadding="5" cellspacing="1" style="width: 900px;">
				<?php
					$sql_bsearch = "SELECT id, RootSKU, BrowserName FROM products ".$where." ORDER BY BrowserName";
					$result_bsearch = mysql_query($sql_bsearch);
					$num_bsearch = mysql_num_rows($result_bsearch);

					if($num_bsearch>0) {
						while($row_bsearch = mysql_fetch_array($result_bsearch)) {
							echo '<tr style="float: none;"><td style="padding-left: 20px; width: 80%; float: none; border: 0px; text-align: left;">';
							echo $row_bsearch["RootSKU"]." -- ".$row_bsearch["BrowserName"];
							echo '</td><td style="width: 20%; float: none; border: 0px;"><input type="button" id="AddItem" onClick="AddBundleItems(\''.$row_bsearch["id"].'\')" value="Add to Bundle" /></td></tr>';						
						}
					} else {
						echo '<tr style="float: none;"><td style="float: none; border: 0px;">No items found. please search again.</td></tr>';
					}
				?>
			</table>
		<?php		
		mysql_close($conn);
		exit();
	}

	if($typeChoice == "rembundleitem") {
		$id = mysql_real_escape_string($_POST['id']);
		$sql_rem = "DELETE FROM product_bundles WHERE id='$id' LIMIT 1";
		if(!mysql_query($sql_rem)) {
			echo "Error removing item: ".mysql_error();
		} else {
			echo "Item Removed!";
		}
		mysql_close($conn);
		exit();
	}

	if($typeChoice == "addbundleitem") {
		$id = mysql_real_escape_string($_POST['id']);
		$bid = mysql_real_escape_string($_POST['bid']);
		$sql_chk = "SELECT ProductID, Items FROM product_bundles WHERE ProductID='$id' AND Items='$bid'";
		$result_chk = mysql_query($sql_chk);
		$num_chk = mysql_num_rows($result_chk);

		if($num_chk>0) {
		} else {
			$sql_badd = "INSERT INTO product_bundles(ProductID, Items) VALUES ('$id', '$bid')";
			mysql_query($sql_badd);
		}
		mysql_close($conn);
		exit();
	}
?>