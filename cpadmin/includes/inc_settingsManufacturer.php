<?php
	
	require 'db.php';
	if(isset($_POST["btnAddNew"])) {
		
		foreach($_POST as $key=>$value) {
			$$key = addslashes($value);
		}		

		$sql_add  = "INSERT INTO manufacturers(Manufacturer, ContactName, PhoneNumber, Address, City, State, Zipcode, Email, Website) ";
		$sql_add .= "VALUES('$Manufacturer', '$ContactName', '$PhoneNumber', '$Address', '$City', '$State', '$Zipcode', '$Email', '$Website')";
		if(!mysql_query($sql_add)) {
			echo "Error adding Manufactuer: ".mysql_error();
		}
	}

	if(isset($_POST["btnUpdate"])) {
		
		foreach($_POST as $key=>$value) {
			$$key = addslashes($value);
		}

		$sql_update  = "UPDATE manufacturers SET Manufacturer='$Manufacturer', ContactName='$ContactName', PhoneNumber='$PhoneNumber', Address='$Address', City='$City', State='$State', Zipcode='$Zipcode', Email='$Email', Website='$Website' ";
		$sql_update .= " WHERE id=$id LIMIT 1";
		if(!mysql_query($sql_update)){
			echo "Error updating Manufacturer: ".mysql_error();
		}
	}

	
	if($_POST["type"] == "new") {
		
		?>
        		<form action="" method="post" >
			<table width="100%" border="0" cellpadding="5" cellspacing="2">
            			<tr>
                			<td>Manufacturer:</td>
                    			<td><input type="text" id="Manufacturer" name="Manufacturer" value="" /></td>
                		</tr>
            			<tr>
                			<td>Contact Name:</td>
                    			<td><input type="text" id="ContactName" name="ContactName" value="" /></td>
                		</tr>
            			<tr>
                			<td>Phone Number:</td>
                    			<td><input type="text" id="PhoneNumber" name="PhoneNumber" value="" /></td>
                		</tr>
            			<tr>
                			<td>Address:</td>
                    			<td><input type="text" id="Address" name="Address" value="" /></td>
                		</tr>
            			<tr>
                			<td>City:</td>
                    			<td><input type="text" id="City" name="City" value="" /></td>
                		</tr>
            			<tr>
                			<td>State:</td>
                    			<td><select id="State" name="State">
										<option value="">Select State...</option>
                                        <?php
											$sql_states = "SELECT * FROM states ORDER BY State";
											$result_states = mysql_query($sql_states);
											
											while($row_states = mysql_fetch_array($result_states)) {
												echo "<option value=\"$row_states[Abbreviation]\">$row_states[State]</option>";
											}
										?>
					    			</select>
								</td>
                		</tr>
            			<tr>
                			<td>Zipcode:</td>
                    			<td><input type="text" id="Zipcode" name="Zipcode" value="" /></td>
                		</tr>
            			<tr>
                			<td>Email:</td>
                    			<td><input type="text" id="Email" name="Email" value="" /></td>
                		</tr>
            			<tr>
                			<td>Website:</td>
                    			<td><input type="text" id="Website" name="Website" value="" /></td>
                		</tr>
                		<tr>
                			<td><input type="submit" class="submitform" id="btnAddNew" name="btnAddNew" value="Save"/></td>
                		</tr>
            		</table>
        		</form>
        	<?php
		
		mysql_close($conn);
		exit();
	}
	
	if($_POST["type"] == "edit") {
		$id = str_replace("e_",'',mysql_real_escape_string($_POST["id"]));
		$sql_edit = "SELECT * FROM manufacturers WHERE id=$id LIMIT 1";
		$result_edit = mysql_query($sql_edit);
		$row_edit = mysql_fetch_assoc($result_edit);

		foreach($row_edit as $key=>$value){
			$$key = stripslashes($value);
		}

		?>

			<form action="" method="post" >
			<table width="100%" border="0" cellpadding="5" cellspacing="2">
            			<tr>
                			<td>Vendor:</td>
                    			<td><input type="hidden" id="id" name="id" value="<?=$id;?>"/>
					    <input type="text" id="Manufacturer" name="Manufacturer" value="<?=$Manufacturer;?>" /></td>
                		</tr>
            			<tr>
                			<td>Contact Name:</td>
                    			<td><input type="text" id="ContactName" name="ContactName" value="<?=$ContactName;?>" /></td>
                		</tr>
            			<tr>
                			<td>Phone Number:</td>
                    			<td><input type="text" id="PhoneNumber" name="PhoneNumber" value="<?=$PhoneNumber;?>" /></td>
                		</tr>
            			<tr>
                			<td>Address:</td>
                    			<td><input type="text" id="Address" name="Address" value="<?=$Address;?>" /></td>
                		</tr>
            			<tr>
                			<td>City:</td>
                    			<td><input type="text" id="City" name="City" value="<?=$City;?>" /></td>
                		</tr>
            			<tr>
                			<td>State:</td>
                    			<td><select id="State" name="State">
									<option value="">Select State...</option>
                                        <?php
											$sql_states = "SELECT * FROM states ORDER BY State";
											$result_states = mysql_query($sql_states);
											
											while($row_states = mysql_fetch_array($result_states)) {
												if($State == $row_states["Abbreviation"]) {
													$selected = ' selected="selected"';
												} else {
													$selected = '';
												}
												echo "<option value=\"$row_states[Abbreviation]\" $selected>$row_states[State]</option>";
											}
										?>
					    			</select>	
								</td>
                		</tr>
            			<tr>
                			<td>Zipcode:</td>
                    			<td><input type="text" id="Zipcode" name="Zipcode" value="<?=$Zipcode;?>" /></td>
                		</tr>
            			<tr>
                			<td>Email:</td>
                    			<td><input type="text" id="Email" name="Email" value="<?=$Email;?>" /></td>
                		</tr>
            			<tr>
                			<td>Website:</td>
                    			<td><input type="text" id="Website" name="Website" value="<?=$Website;?>" /></td>
                		</tr>
                		<tr>
                			<td><input type="submit" class="submitform" id="btnUpdate" name="btnUpdate" value="Save"/></td>
                		</tr>
            		</table>
        		</form>

		<?php


		mysql_close($conn);
		exit();
	}


	if($_POST["type"] == "delete") {
		$id = mysql_real_escape_string($_POST["id"]);
		$sql_del = "DELETE FROM manufacturers WHERE id=$id LIMIT 1";
		if(!mysql_query($sql_del)) {
			echo "Error deleting manufactuer: ".mysql_error();
		} else {
			echo "Manufacturer Deleted";
		}

		mysql_close($conn);
		exit();
	}

?>

	<div id="mainbox">
        <table width="100%" border="0" cellpadding="5" cellspacing="2">
        	<tr>
            	<td colspan="3"><img src="images/plus.png" class="maddnew" style="float: right; width: 20px; cursor: pointer;" /></td>
            </tr>
            <tr>
                <td width="50%" class="headercg">Manufacturer</td>
                <td width="35%" class="headercg">Phone</td>
                <td width="15%" class="headercg">Option</td>
            </tr>
            
            <?php
            
                $sql_manu = "SELECT id, Manufacturer, PhoneNumber FROM manufacturers";
                $result_manu = mysql_query($sql_manu);
                
                while($row_manu = mysql_fetch_array($result_manu)) {
                    ?>
                        <tr>
                            <td><?php echo stripslashes($row_manu["Manufacturer"]); ?></td>
                            <td><?php echo stripslashes($row_manu["PhoneNumber"]); ?></td>
                            <td style="text-align: center;">
                                <img id="e_<?=$row_manu["id"];?>" class="medit" style="cursor: pointer;" src="images/E.png"/>
                                <img id="<?=$row_manu["id"];?>" class="mdelete" style="cursor: pointer;" src="images/D.png"/>
                            </td>
                        </tr>
                    <?php
                }
            ?>
        </table>
    </div>
<script>
	$(".maddnew").hover(
		function() {
			$(this).attr("src", "images/plus_hover.png");
		}, function() {
			$(this).attr("src", "images/plus.png");
	});
	$(".medit").hover(
		function() {
			$(this).attr("src", "images/E_hover.png");
		},
		function() {
			$(this).attr("src", "images/E.png");
	});
		
	$(".mdelete").hover(
		function() {
			$(this).attr("src", "images/D_hover.png");
		},
		function() {
			$(this).attr("src", "images/D.png");
	});
	
	$(".mdelete").click(function() {
		var del = confirm("Delete Manufacturer?");
		
		if(del) {
			$.post("includes/inc_settingsManufacturer.php", {"type":"delete", "id":$(this).attr("id")}, 
				function(data) {
					alert(data);
					location.reload();
			});
		}
	});
	
	$(".maddnew").click(function() {
		$("#mainbox").load("includes/inc_settingsManufacturer.php", {"type":"new"});
	});
	
	$(".medit").click(function() {
		$("#mainbox").load("includes/inc_settingsManufacturer.php", {"type":"edit", "id":$(this).attr("id")});
	});
	
	$(".submitform").click(function() {
		var fields = new Array("Vendor", "ContactName", "PhoneNumber", "Email");

		for(var i=0; i<fields.length; i++) {
			if($("#"+fields[i]).val() == '') {
				alert("Please enter a "+fields[i]);
				$("#"+fields[i]).focus();
				return false;
			}
		}
	});
	
</script>

