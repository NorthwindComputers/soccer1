<?php
/********************************
 * Category settings admin
 *
 * programmed by: Richard Tuttle
 * last update: 10 June 2014
 ********************************/	
require 'db.php';
if (isset($_POST["btnAddNew"])) {
	$sql_addnew = "INSERT INTO attribute_category(Name, Type) VALUES('$_POST[Name]', '$_POST[Category]')";
	if (!mysql_query($sql_addnew)) {
		echo "Error Adding Category: ".mysql_error();
	}
}
	
if (isset($_POST["btnUpdate"])) {
	$id = $_POST["id"];
	$sql_update = "UPDATE attribute_category SET Name='$_POST[Name]', Type='$_POST[Category]' WHERE id=$id";
	if (!mysql_query($sql_update)) {
		echo "Error updating category: ".mysql_error();
	}
}
	
if ($_POST["type"]=="delete") {
	$id = $_POST["id"];
	$sql_del = "DELETE FROM attribute_category WHERE id=$id LIMIT 1";
	if (!mysql_query($sql_del)) {
		echo "Error removing Category";
	} else {
		echo "Category Removed";
	}
	mysql_close($conn);
	exit();
}
	
if ($_POST["type"] == "new") {
?>
    <form action="" method="post" >
	<table width="100%" border="0" cellpadding="5" cellspacing="2">
    <tr>
        <td>Name:</td>
		<td>Category:</td>
    </tr>
	<tr>
		<td><input type="text" class="customers" id="Name" name="Name" /></td>
		<td>
		<select id="Category" name="Category">
			<option value="colors">Color</option>
			<option value="sizes">Sizes</option>
		</select></td>
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
	
if ($_POST["type"] == "edit") {
	$id = str_replace("e_",'',$_POST["id"]);
	$sql_edit = "SELECT * FROM attribute_category WHERE id='$id' LIMIT 1";
	$result_edit = mysql_query($sql_edit);
	$row_edit = mysql_fetch_assoc($result_edit);
?>
<form action="" method="post" >
<table width="100%" border="0" cellpadding="5" cellspacing="2">
<tr>
    <td>Name:</td>
	<td>Category:</td>
</tr>
<tr>
	<td><input type="hidden" id="id" name="id" value="<?=$row_edit["id"];?>" /><input type="text" class="customers" id="Name" name="Name" value="<?=$row_edit["Name"];?>" /></td>
	<td>
	<select id="Category" name="Category">
		<option <?php if($row_edit["Type"] == "colors") { echo ' selected="selected"'; } ?> value="colors">Color</option>
		<option <?php if($row_edit["Type"] == "sizes") { echo ' selected="selected"'; } ?> value="sizes">Sizes</option>
	</select></td>
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
    <td width="60%" class="headercg">Name</td>
    <td width="20%" class="headercg">Category</td>
    <td width="20%" class="headercg">Options</td>
</tr>  
<?php
$sql_cats = "SELECT * FROM attribute_category ORDER BY Type";
$result_cats = mysql_query($sql_cats);
while ($row_cats = mysql_fetch_array($result_cats)) {
?>
<tr>
    <td><?=$row_cats["Name"];?></td>
    <td><?=$row_cats["Type"];?></td>
    <td style="text-align: center;"><img id="e_<?=$row_cats["id"];?>" class="pedit" src="images/E.png"/><img id="<?=$row_cats["id"];?>" class="pdelete" style="cursor: pointer;" src="images/D.png"/></td>
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
		var del = confirm("Delete Category?");
		
		if(del) {
			$.post("includes/inc_settingsCategory.php", {"type":"delete", "id":$(this).attr("id")}, 
				function(data) {
					alert(data);
					location.reload();
			});
		}
	});
	
	$(".paddnew").click(function() {
		$("#mainbox").load("includes/inc_settingsCategory.php", {"type":"new"});
	});
	
	$(".pedit").click(function() {
		$("#mainbox").load("includes/inc_settingsCategory.php", {"type":"edit", "id":$(this).attr("id")});
	});
</script>