<?php

	if($_POST["type"] == "productcolor") {
		$cat = $_POST["cat"];
		$num = $_POST["num"];
?>
	<div id="div<?=$cat.$num;?>">
			<table width="100%" border="0" cellpadding="5" cellspacing="1">
        		<tr>
          			<td class="header">
                    	<input type="hidden" id="<?=$cat.$num;?>_id" name="<?=$cat.$num;?>_id" value="" />
                    	<input type="text" name="<?=$cat.$num;?>" id="<?=$cat.$num;?>" /></td>
          			<td class="header">
                    	<input type="hidden" id="<?=$cat.$num;?>_img" name="<?=$cat.$num;?>_img" value="" />
                        <form id="frmImg<?=$num;?>" action="includes/imgUpload.php?t=option" method="post" enctype="multipart/form-data" target="imgUpload" style="position: relative;">
                           	<img id="img_<?=$num;?>" src="../images/blank.gif" style="width: 21px; height: 21px;" />
				    	    <input style="position: absolute; -moz-opacity:0; filter:alpha(opacity: 0); opacity: 0; width: 70px; height: 15px; float: right;" type="file" id="file<?=$num;?>" name="file" onchange="$('#<?=$cat.$num;?>_img').val($('#file<?=$num;?>').val().replace(/C:\\fakepath\\/i,'')); $('#frmImg<?=$num;?>').submit(); $('#img_<?=$num;?>').attr('src', '../images/'+$('#file<?=$num;?>').val().replace(/C:\\fakepath\\/i,''));" />
                                <img src="../images/price_browse.png"  style="float: right;"/>
                        </form></td>
          			<td class="header"><input type="text" name="<?=$cat.$num;?>_sku" id="<?=$cat.$num;?>_sku" /></td>
				<td class="remove"><input type="button" style="width: 50px;" onclick="removeopt('<?=$cat;?>', '<?=$num;?>', '');" value="remove" /></td>
        		</tr>
			</table>
		</div>
<?php
		exit();
	}
	
	if($_POST["type"] == "del") {
		require 'db.php';
		
		$sql_del = "DELETE FROM $_POST[cat] WHERE id=$_POST[rid] LIMIT 1";
		if(!mysql_query($sql_del)) {
			echo "error deleting option: ".mysql_error();
		} else {
			echo "option removed";
		}
		
		mysql_close($conn);
		exit();
	}
	
	if($_POST["type"] == "CustomerGroup") {
		require 'db.php';
		$num = $_POST["num"];
		?>
		
        <div id="div<?=$num;?>">
        	<table width="100%" border="0" cellpadding="5" cellspacing="0">
            	<tr>
        			<td width="10%" align="center" valign="middle"></td>
			        <td width="35%" align="left" valign="middle"><input style="width: 90%;" type="text" id="groupname<?=$num;?>" name="groupname<?=$num;?>" value="" /></td>
                    <td width="15%" align="center" valign="middle"><input style="width: 90%;" type="text" id="code<?=$num;?>" name="code<?=$num;?>" value="" /></td>
                    <td width="20%" align="left" valign="middle">
                      <select name="pricelevel<?=$num;?>" id="pricelevel<?=$num;?>">
                        <option value="">Select One...</option>
                        <?php
                            $sql_pricelevel = "SELECT Level FROM price_level ORDER BY Level";
                            $result_pricelevel = mysql_query($sql_pricelevel);
                            
                            while($row_pricelevel = mysql_fetch_array($result_pricelevel)) {
                                if($row_cg["PriceLevel"] == $row_pricelevel["Level"]) {
                                    $selected = ' Selected="Selected" ';
                                } else {
                                    $selected = '';
                                }
                                echo "<option value=\"$row_pricelevel[Level]\" $selected>$row_pricelevel[Level]</option>";
                            }
                        
                        ?>
                      </select>
                    </td>
                    <td width="20%" align="center" valign="middle">Category | <a href="#" onClick="removeopt('<?=$num;?>','');">Delete</a> | Inactive</td>
                    </tr>
            </table>
        </div>
   
	<?php
		mysql_close($conn);
		exit();
	}
	
$rid = $_POST['rid'];

	if($_POST["type"] == "delCustomerGroup") {
		require 'db.php';
		$sql_del = "DELETE FROM customer_group WHERE id=$rid LIMIT 1";
		if(!mysql_query($sql_del)) {
			echo "error deleting customer group : ".mysql_error();
		} else {
			echo "customer group removed";
		}
		
		mysql_close($conn);
		exit();
	}

	if($_POST["type"] == "deluser") {
		require 'db.php';
		$sql_del = "DELETE FROM users WHERE id=rid LIMIT 1";
		if(!mysql_query($sql_del)) {
			echo "Error removing user: ".mysql_error();
		} else {
			echo "User removed";
		}

		mysql_close($conn);
		exit();
	}

	if($_POST["type"] == "rules") {
		require 'db.php';
		$id = $_POST["rid"];
		$sql_rules = "SELECT id, `Name`, AccessTo FROM access_level WHERE id=$id LIMIT 1";
		$result_rules = mysql_query($sql_rules);
		$row_rules = mysql_fetch_assoc($result_rules);
		
		?>
        	<table border="0" cellpadding="5" cellspacing="1">
            	<tr>
                	<td><strong>Rule:</strong> <?=$row_rules["Name"];?>
                    	<input type="hidden" id="rid" name="rid" value="<?=$row_rules["id"];?>" />
                    </td>
                </tr>
                <tr>
                	<td>
                	<ul>
                    	<?php
							$sql_options = "SELECT `Option` FROM access_options ORDER BY `Option`";
							$result_options = mysql_query($sql_options);
							
							while($row_options = mysql_fetch_array($result_options)) {
								$pos = strpos($row_rules["AccessTo"], $row_options["Option"]);
								if($pos === false) {
									$checked = '';
								} else {
									$checked = ' checked ';
								}
								echo '<li><input type="checkbox" style="width: 20px;" '.$checked.' id="options[]" name="options[]" value="'.$row_options["Option"].'" />'.$row_options["Option"].'</li>';
							}
						?>
                    </ul>
                    </td>
                </tr>
            </table>
        	<input type="submit" style="background: #ff7e00; float: left; color: #fff;" id="btnSave" name="btnSave" value="Save / Update" />
            
        <?php
		mysql_close($conn);
		exit ();
	}


	if($_POST["type"] == "optionlist") {
		require 'db.php';
		$cat = $_POST["cat"];
		$optcat = $_POST["optcat"];

		if($cat == "sizes") {
			$field = "Size";
		} else {
			$field = "Color";
		}
		

		$sql_option = "SELECT * FROM $cat WHERE Category='$optcat'";
		$result_option = mysql_query($sql_option);
		
		$c_opt = 1;	
		while($row_option = mysql_fetch_array($result_option)) {
		?>
			<div id="div<?=$cat.$c_opt;?>">
			<table width="100%" border="0" cellpadding="5" cellspacing="1">
        		<tr>
          			<td class="header"><input type="hidden" name="<?=$cat.$c_opt;?>_id" id="<?=$cat.$c_opt;?>_id" value="<?=$row_option["id"];?>" />
			    	    <input type="text" name="<?=$cat.$c_opt;?>" id="<?=$cat.$c_opt;?>" value="<?=$row_option[$field];?>" /></td>
          			<td class="header">
                    	<input type="hidden" id="<?=$cat.$c_opt;?>_img" name="<?=$cat.$c_opt;?>_img" value="<?=$row_option["Icon"];?>" />
                        <form id="frmImg<?=$c_opt;?>" action="includes/imgUpload.php?t=option" method="post" enctype="multipart/form-data" target="imgUpload" style="position: relative;">
                        	<img id="img_<?=$c_opt;?>" src="../images/<?=$row_option["Icon"];?>" style="width: 21px; height: 21px; float: left;" />
				    	    <input style="position: absolute; -moz-opacity:0; filter:alpha(opacity: 0); opacity: 0; width: 70px; height: 15px; float: right;" type="file" id="file<?=$c_opt;?>" name="file" onchange="$('#<?=$cat.$c_opt;?>_img').val($('#file<?=$c_opt;?>').val().replace(/C:\\fakepath\\/i,'')); $('#frmImg<?=$c_opt;?>').submit(); $('#img_<?=$c_opt;?>').attr('src', '../images/'+$('#file<?=$c_opt;?>').val().replace(/C:\\fakepath\\/i,''));" />
                                <img src="../images/price_browse.png" style="float: right;" />
                        </form></td>
          			<td class="header"><input type="text" name="<?=$cat.$c_opt;?>_sku" id="<?=$cat.$c_opt;?>_sku" value="<?=$row_option["SKU"];?>" /></td>
				<td class="remove"><input type="button" style="width: 50px;" onclick="removeopt('<?=$cat;?>', '<?=$c_opt;?>', '<?=$row_option["id"];?>');" value="remove" /></td>
        		</tr>
			</table>
			</div>
			<script> arrItems.push(<?=$c_opt;?>); </script>

		<?php
			$c_opt++;
		}
		?>
		<div id="divOptions"></div>
		<script>$("#num").val(<?=$c_opt;?>);</script>
		<table width="100%" border="0" cellpadding="5" cellspacing="1">
			<tr>
				<td><input type="submit" style="background: #ef9800;" id="btnSubmit2" name="btnSubmit2" value="Save Options" onClick="setIDs();" /><input type="button" style="float: right; margin-left: 20px; background: #ef9800;" id="addnew" name="addnew" onClick="addopt();" value="Add Option" /></td>
			</tr>
		</table>

		<?php
		mysql_close($conn);
		exit();
	}
?>