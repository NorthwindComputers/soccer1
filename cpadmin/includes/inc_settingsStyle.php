<?php
/***********************************
 * Style Settings include file    
 *                                 
 * Version: 1.1                   
 * Updated: 09 September 2014          
 * By: Richard Tuttle              
 **********************************/
require 'db.php';
	
if(isset($_POST["btnAddNew"])) {
	$sql_addnew = "INSERT INTO styles(Style) VALUES('$_POST[Style]')";
	if(!mysql_query($sql_addnew)) {
		echo "Error Adding Style: ".mysql_error();
	}
}
	
if(isset($_POST["btnUpdate"])) {
	$id = mysql_real_escape_string($_POST["id"]);
	$sql_update = "UPDATE styles SET Style='$_POST[Style]' WHERE id=$id";
	if(!mysql_query($sql_update)) {
		echo "Error updating Style: ".mysql_error();
	}
}
	
if($_POST["type"]=="delete") {
	$id = mysql_real_escape_string($_POST["id"]);
	$sql_del = "DELETE FROM styles WHERE id=$id LIMIT 1";
	if(!mysql_query($sql_del)) {
		echo "Error removing Style";
	} else {
		echo "Style Removed";
	}
		
	mysql_close($conn);
	exit();
}
	
if($_POST["type"] == "new") {	
?>
	<form action="" method="post">
	<table width="100%" border="0" cellpadding="5" cellspacing="2">
	<tr>
    	<td>Style:</td>
	</tr>
	<tr>
		<td><input type="text" class="customers" id="Style" name="Style" /></td>
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
	$sql_edit = "SELECT * FROM styles WHERE id=$id LIMIT 1";
	$result_edit = mysql_query($sql_edit);
	$row_edit = mysql_fetch_assoc($result_edit);
?>
	<form action="" method="post" >
	<table width="100%" border="0" cellpadding="5" cellspacing="2">
    <tr>
        <td>Style:</td>
    </tr>
	<tr>
		<td><input type="hidden" id="id" name="id" value="<?=$row_edit["id"];?>" />
		<input type="text" class="customers" id="Style" name="Style" value="<?=$row_edit["Style"];?>" /></td>
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
    <td width="60%" class="headercg">Style</td>
    <td width="40%" class="headercg">Options</td>
</tr>
<?php
$sql_styles = "SELECT * FROM styles ORDER BY Style";
$result_styles = mysql_query($sql_styles);
while($row_styles = mysql_fetch_array($result_styles)) {
?>
    <tr>
        <td><?=$row_styles["Style"];?></td>
        <td style="text-align: center;"><img id="e_<?=$row_styles["id"];?>" class="pedit" src="images/E.png"/><img id="<?=$row_styles["id"];?>" class="pdelete" style="cursor: pointer;" src="images/D.png"/></td>
    </tr>
<?php
}
?>
</table>
</div>
<script>
$(".paddnew").hover(function() {
	$(this).attr("src", "images/plus_hover.png");
}, function() {
	$(this).attr("src", "images/plus.png");
});

$(".pedit").hover(function() {
	$(this).attr("src", "images/E_hover.png");
}, function() {
	$(this).attr("src", "images/E.png");
});
		
$(".pdelete").hover(function() {
	$(this).attr("src", "images/D_hover.png");
}, function() {
	$(this).attr("src", "images/D.png");
});
	
$(".pdelete").click(function() {
	var del = confirm("Delete Style?");
	if(del) {
		$.post("includes/inc_settingsStyle.php", {"type":"delete", "id":$(this).attr("id")}, 
			function(data) {
				alert(data);
				location.reload();
		});
	}
});
	
$(".paddnew").click(function() {
	$("#mainbox").load("includes/inc_settingsStyle.php", {"type":"new"});
});
	
$(".pedit").click(function() {
	$("#mainbox").load("includes/inc_settingsStyle.php", {"type":"edit", "id":$(this).attr("id")});
});	
</script>