<?php
	/**
	 * Reange (Gender) Settings include file
	 *
	 * Version: 1.2
	 * Updated: 16 Feb 2013
	 * By: Richard Tuttle
	 */
	
	require_once 'db.php';
	
	if(isset($_POST["btnAddNew"])) {
		$sql_addnew = "INSERT INTO gender(GenderName, GenderSKU) VALUES('$_POST[Gender]', '$_POST[GenderSKU]')";
		if(!mysql_query($sql_addnew)) {
			echo "Error Adding Range: ".mysql_error();
		}
	}
	
	if(isset($_POST["btnUpdate"])) {
		$id = mysql_real_escape_string($_POST["id"]);
		$sql_update = "UPDATE gender SET GenderName='$_POST[Gender]', GenderSKU='$_POST[GenderSKU]' WHERE id=$id";
		if(!mysql_query($sql_update)) {
			echo "Error updating Range: ".mysql_error();
		}
	}
	
	if($_POST["type"]=="delete") {
		$id = mysql_real_escape_string($_POST["id"]);
		$sql_del = "DELETE FROM gender WHERE id=$id LIMIT 1";
		if(!mysql_query($sql_del)) {
			echo "Error removing Range";
		} else {
			echo "Range Removed";
		}
		
		mysql_close($conn);
		exit();
	}
	
	if($_POST["type"] == "new") {
?>
        <form action="" method="post" >
			<table width="100%" border="0" cellpadding="5" cellspacing="2">
            		<tr>
                		<td>Range:</td>
                	</tr>
                    <tr>
                        <td><input type="text" class="customers" id="Gender" name="Gender" /></td>
                    </tr>
					<tr>
                    	<td>SKU:</td>
                    </tr>
                    <tr>
                    	<td><input type="text" class="customers" id="GenderSKU" name="GenderSKU" /></td>
                    </tr>
                	<tr>
                		<td><input type="submit" id="btnAddNew" name="btnAddNew" value="Save"/></td>
                	</tr>
            </table>
        </form>
<?php
mysql_close($conn);
exit();
	}
	
	if($_POST["type"] == "edit") {
		$id = str_replace("e_",'',mysql_real_escape_string($_POST["id"]));
		$sql_edit = "SELECT * FROM gender WHERE id=$id LIMIT 1";
		$result_edit = mysql_query($sql_edit);
		$row_edit = mysql_fetch_assoc($result_edit);
?>
		<form action="" method="post" >
			<table width="100%" border="0" cellpadding="5" cellspacing="2">
            		<tr>
                		<td>Range:</td>
                	</tr>
                    <tr>
                        <td><input type="hidden" id="id" name="id" value="<?=$row_edit["id"];?>" />
                            <input type="text" class="customers" id="Gender" name="Gender" value="<?=$row_edit["GenderName"];?>" /></td>
                    </tr>
                    <tr>
                    	<td>SKU:</td>
                    </tr>
                    <tr>
                    	<td><input type="text" id="GenderSKU" name="GenderSKU" value="<?=$row_edit["GenderSKU"];?>" /></td>
                    </tr>
                	<tr>
                		<td><input type="submit" id="btnUpdate" name="btnUpdate" value="Save"/></td>
                	</tr>
            	</table>
        	</form>
<?php
		mysql_close($conn);
		exit();
	}
?>
	<div id="mainbox">
        <table width="100%" border="0" cellpadding="5" cellspacing="2">
        	<tr>
            	<td colspan="3"><img src="images/plus.png" class="paddnew" style="float: right; width: 20px; cursor: pointer;" /></td>
            </tr>
            <tr>
                <td width="40%" class="headercg">Range Name</td>
                <td width="30%" class="headercg">SKU</td>
                <td width="30%" class="headercg">Options</td>
            </tr>
<?php
                $sql_gender = "SELECT * FROM gender ORDER BY GenderName";
                $result_gender = mysql_query($sql_gender);
                while($row_gender = mysql_fetch_array($result_gender)) {
?>
                        <tr>
                            <td><?=$row_gender["GenderName"];?></td>
                            <td><?=$row_gender["GenderSKU"];?></td>
                            <td style="text-align: center;">
                                <img id="e_<?=$row_gender["id"];?>" class="pedit" src="images/E.png"/>
                                <img id="<?=$row_gender["id"];?>" class="pdelete" style="cursor: pointer;" src="images/D.png"/>
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
		var del = confirm("Delete Range?");
		
		if(del) {
			$.post("includes/inc_settingsGender.php", {"type":"delete", "id":$(this).attr("id")}, 
				function(data) {
					alert(data);
					window.location.href=window.location.href;
			});
		}
	});
	
	$(".paddnew").click(function() {
		$("#mainbox").load("includes/inc_settingsGender.php", {"type":"new"});
	});
	
	$(".pedit").click(function() {
		$("#mainbox").load("includes/inc_settingsGender.php", {"type":"edit", "id":$(this).attr("id")});
	});
</script>