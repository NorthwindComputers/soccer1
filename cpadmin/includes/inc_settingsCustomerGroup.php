<?php
/**
 * Customer Group Admin Setting spage
 *
 * Version: 1.2
 * Updated: 13 Apr 2013
 * By: Richard Tuttle
 */	
	require_once 'db.php';
	
	if(isset($_POST["btnAddNew"])) {
		foreach($_POST as $key=>$value) {
			$$key = addslashes($value);
		}
		
		$sql_add  = "INSERT INTO customer_group(GroupName, GroupCode, PriceLevel, Status, PageTitle, Description, MetaKeywords, MetaDescription) ";
		$sql_add .= " VALUES('$GroupName', '$GroupCode', '$PriceLevel', '$Status', '$PageTitle', '$Description', '$MetaKeywords', '$MetaDescription')";
		if(!mysql_query($sql_add)) {
			echo "Error adding Customer Group: ".mysql_error();
		}
	}
	
	if(isset($_POST["btnUpdate"])) {
		foreach ($_POST as $key=>$value) {
			$$key = addslashes($value);
		}
		
		$sql_update = "UPDATE customer_group SET GroupName='$GroupName', GroupCode='$GroupCode', PriceLevel='$PriceLevel', Status='$Status', PageTitle='$PageTitle', Description='$Description', MetaKeywords='$MetaKeywords', MetaDescription='$MetaDescription' WHERE id=$id LIMIT 1";
		if(!mysql_query($sql_update)) {
			echo "Error updating customer group: ".mysql_error();
		}
	}
	
	if($_POST["type"]=="delete") {
		$id = mysql_real_escape_string($_POST["id"]);
		$sql_del = "DELETE FROM customer_group WHERE id=$id LIMIT 1";
		if(!mysql_query($sql_del)) {
			echo "Error deleting customer group".mysql_error();
		} else {
			echo "Customer Group Removed";
		}
		
		mysql_close($conn);
		exit();
	}
	
    $isSecure = (!empty($_SERVER['HTTPS'])) && ($_SERVER['HTTPS'] != 'off');
    $url = ($isSecure ? 'https://' : 'http://') . $host;
    $basePath = str_replace('includes', '', dirname($_SERVER['SCRIPT_NAME']));
    $url  .= $_SERVER['SERVER_NAME'].('/' == $basePath ? '' : $basePath);
      
	if($_POST["type"] == "new") {
		?>
        <form action="" method="post" >
			<table width="100%" border="0" cellpadding="5" cellspacing="2">
            		<tr>
                		<td style="width: 100px;"><strong>Group Name:</strong></td>
						<td><input type="text" style="width: 200px;" id="GroupName" name="GroupName" /></td>
                	</tr>
                    <tr>
                        <td><strong>Group Code: </strong></td>
                        <td><input type="text" style="width: 200px;" id="GroupCode" name="GroupCode" /></td>
                    </tr>
                    <tr>
                    	<td><strong>Price Level:</strong></td>
                        <td>
                        	<select style="width: 200px;" id="PriceLevel" name="PriceLevel">
                            	<option value="">Select...</option>
                                <?php
									$sql_pricelevel = "SELECT Level FROM price_level ORDER BY Level";
									$result_pricelevel = mysql_query($sql_pricelevel);
									while($row_pricelevel = mysql_fetch_array($result_pricelevel)) {
										echo "<option value=\"$row_pricelevel[Level]\">$row_pricelevel[Level]</option>";
									}
								?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                    	<td><strong>Status:</strong></td>
                        <td>
                        	<select style="width: 200px;" id="Status" name="Status">
                            	<option value="Enabled">Enabled</option>
                                <option value="Disabled">Disabled</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                    	<td><strong>Page Title:</strong></td>
                        <td><input type="text" style="width: 200px;" id="PageTitle" name="PageTitle" /></td>
                    </tr>
                    <tr>
                    	<td><strong>Meta Keywords:</strong></td>
                        <td><input type="text" style="width: 200px;" id="MetaKeywords" name="MetaKeywords" /></td>
                    </tr>
                    <tr>
                    	<td><strong>Meta Description:</strong></td>
                        <td><input type="text" style="width: 200px;" id="MetaDescription" name="MetaDescription" /></td>
                    </tr>
                    <tr>
                    	<td colspan="2"><strong>Content:</strong></td>
                    </tr>
                    <tr>
                    	<td colspan="2"><textarea id="Description" name="Description"></textarea></td>
                    </tr>
                	<tr>
                		<td colspan="2"><input type="submit" id="btnAddNew" name="btnAddNew" value="Save"/><input type="button" style="margin-left: 20px;" id="btnCancel" value="Cancel" onClick="location.reload()" /></td>
                	</tr>
            </table>
        </form>
        
        <script type="text/javascript">
CKEDITOR.replace('Description');
			</script>
        <?php
		mysql_close($conn);
		exit();
	}
	
	if($_POST["type"] == "edit") {
		$eid = str_replace("e_",'',mysql_real_escape_string($_POST["id"]));
		$sql_edit = "SELECT * FROM customer_group WHERE id=$eid LIMIT 1";
		$result_edit = mysql_query($sql_edit);
		$row_edit = mysql_fetch_assoc($result_edit);
		
		foreach($row_edit as $key=>$value) {
			$$key = stripslashes($value);
		}
		?>
		<form action="" method="post" >
			<table width="100%" border="0" cellpadding="5" cellspacing="2">
            		<tr>
                		<td style="width: 20%;"><strong>Group Name:</strong></td>
						<td><input type="hidden" id="id" name="id" value="<?=$id;?>" />
                        	<input type="text" id="GroupName" name="GroupName" value="<?=$GroupName;?>" /></td>
                	</tr>
                    <tr>
                        <td><strong>Group Code: </strong></td>
                        <td><input type="text" id="GroupCode" name="GroupCode" value="<?=$GroupCode;?>" /></td>
                    </tr>
                    <tr>
                    	<td><strong>Price Level:</strong></td>
                        <td>
                        <select id="PriceLevel" name="PriceLevel">
                            	<option value="">Select...</option>
                                <?php
									$sql_pricelevel = "SELECT Level FROM price_level ORDER BY Level";
									$result_pricelevel = mysql_query($sql_pricelevel);
									while($row_pricelevel = mysql_fetch_array($result_pricelevel)) {
										if($PriceLevel == $row_pricelevel["Level"]) {
											$selected = ' selected="selected"';
										} else {
											$selected = '';
										}
										echo "<option value=\"$row_pricelevel[Level]\" $selected>$row_pricelevel[Level]</option>";
									}
								?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                    	<td><strong>Status:</strong></td>
                        <td>
                        	<select id="Status" name="Status">
                            	<option <?php if($Status == 'Enabled') { echo 'selected="selected" '; }?> value="Enabled">Enabled</option>
                                <option <?php if($Status == 'Disabled') { echo 'selected="selected" '; }?> value="Disabled">Disabled</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                    	<td><strong>Page Title:</strong></td>
                        <td><input type="text" id="PageTitle" name="PageTitle" value="<?=$PageTitle;?>" /></td>
                    </tr>
                    <tr>
                    	<td><strong>Meta Keywords:</strong></td>
                        <td><input type="text" id="MetaKeywords" name="MetaKeywords" value="<?=$MetaKeywords;?>" /></td>
                    </tr>
                    <tr>
                    	<td><strong>Meta Description:</strong></td>
                        <td><input type="text" id="MetaDescription" name="MetaDescription" value="<?=$MetaDescription;?>" /></td>
                    </tr>
                    <tr>
                    	<td colspan="2"><strong>Content:</strong></td>
                    </tr>
                    <tr>
                    	<td colspan="2"><textarea id="Description" name="Description"><?=$Description;?></textarea></td>
                    </tr>
                	<tr>
                		<td colspan="2"><input type="submit" id="btnUpdate" name="btnUpdate" value="Update"/> <input type="button" style="margin-left: 20px;" id="btnCancel" name="btnCancel" onClick="location.reload()" value="Cancel" /></td>
                	</tr>
            </table>
        </form>
        <script type="text/javascript">
CKEDITOR.replace('Description');
			</script>
        <?php
		mysql_close($conn);
		exit();
	}
?>
	<div id="mainbox">
        <table width="100%" border="0" cellpadding="5" cellspacing="2">
        	<tr>
            	<td colspan="4"><img src="images/plus.png" class="paddnew" style="float: right; width: 20px; cursor: pointer;" /></td>
            </tr>
            <tr>
                <td width="10%" class="headercg" style="text-align: center;">Group ID</td>
                <td width="50%" class="headercg">Name</td>
                <td width="20%" class="headercg" style="text-align: center;">Code</td>
                <td width="20%" class="headercg" style="text-align: center;">Options</td>
            </tr>
            <?php
                $sql_groups = "SELECT * FROM customer_group ORDER BY GroupName";
                $result_groups = mysql_query($sql_groups);
                
                while($row_groups = mysql_fetch_array($result_groups)) {
                    ?>
                        <tr>
                            <td style="text-align: center;"><?=$row_groups["id"];?></td>
                            <td><?=$row_groups["GroupName"];?></td>
                            <td style="text-align: center;"><?=$row_groups["GroupCode"];?></td>
                            <td style="text-align: center;">
                                <img id="e_<?=$row_groups["id"];?>" class="pedit" src="images/E.png"/>
                                <img id="<?=$row_groups["id"];?>" class="pdelete" style="cursor: pointer;" src="images/D.png"/>
                            </td>
                        </tr>
                    <?php
                }
            ?>
        </table>
    </div>
<script>
	$(".paddnew").hover(
		function() {
			$(this).attr("src", "images/plus_hover.png");
		}, function() {
			$(this).attr("src", "images/plus.png");
	});
	$(".pedit").hover(
		function() {
			$(this).attr("src", "images/E_hover.png");
		},
		function() {
			$(this).attr("src", "images/E.png");
	});
		
	$(".pdelete").hover(
		function() {
			$(this).attr("src", "images/D_hover.png");
		},
		function() {
			$(this).attr("src", "images/D.png");
	});
	
	$(".pdelete").click(function() {
		var del = confirm("Delete Customer Group?");
		
		if(del) {
			$.post("includes/inc_settingsCustomerGroup.php", {
				"type":"delete", 
				"id":$(this).attr("id")
				}, function(data) {
					alert(data);
					location.reload();
			});
		}
	});
	
	$(".paddnew").click(function() {
		$("#mainbox").load("includes/inc_settingsCustomerGroup.php", {"type":"new"});
	});
	
	$(".pedit").click(function() {
		$("#mainbox").load("includes/inc_settingsCustomerGroup.php", {
			"type":"edit", 
			"id":$(this).attr("id")
		});
	});
</script>