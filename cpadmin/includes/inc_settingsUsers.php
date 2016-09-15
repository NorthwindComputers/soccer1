<script type="text/javascript">
function delUser(id) {
	var del = confirm("Delete User?");
	if(del) {
		$.post("includes/inc_settingsOptions_add.php", {
		    "type":"deluser",
		    "rid":id
		}, function(data) {
			alert(data);
			location.reload();
		});
	}
	return false;
}
</script>
<table width="100%" border="0" cellpadding="5" cellspacing="2">
<tr>
    <td width="11%" class="headercg">Name</td>
    <td width="39%" class="headercg">Access Level</td>
    <td width="25%" class="headercg">Option</td>
</tr>
<?php
    $sql_users = "SELECT id, `Name`, AccessLevel FROM users";
	$result_users = mysql_query($sql_users);
	while($row_users = mysql_fetch_array($result_users)) {
?>			
<tr>
	<td><?=$row_users["Name"];?></td>
	<td><?=$row_users["AccessLevel"];?></td>
	<td style="text-align: center;"> <a href="#" onClick="delUser('<?=$row_users["id"];?>');">Delete</a> | <a href="settings.php?p=UsersMange&id=<?=$row_users["id"];?>">Edit</a></td>
</tr>
<?php
	}
?>
</table>
<table width="100%" border="0" cellpadding="5" cellspacing="0">
<tr>
	<td><input type="button" onClick="window.location='settings.php?p=UsersMange';" style="background: #ff7e00; color: #fff; float: right;" id="btnAdd" name="btnAdd" value="Add User" /></td>
</tr>
</table>