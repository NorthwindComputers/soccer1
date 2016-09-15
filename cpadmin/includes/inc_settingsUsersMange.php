<?php
/**
 * Admin User Management include file
 *
 * Updated: 10 June 2016
 * By: Richard Tuttle
 */
// save user infor
if (isset($_POST["btnSave"])) {
	foreach($_POST as $key=>$value) {
		$$key = $value;
    }
		
	if ($id == '') {
		$codedPassword = password_hash($Password, PASSWORD_DEFAULT);
		$sql = "INSERT INTO users(Name, UserID, Password, Email, AccessLevel) VALUES('$Name', '$UserID', '$codedPassword', '$Email', '$AccessLevel')";
	} else {
		$sql = "UPDATE users SET Name='$Name', UserID='$UserID', Email='$Email', AccessLevel='$AccessLevel'";
		if (($nPassword != '') || ($nPassword != NULL)) {
			$cnPassword = password_hash($nPassword, PASSWORD_DEFAULT);
		    $sql .= ", Password='$cnPassword'";
		}
		$sql .= " WHERE id=$id LIMIT 1";
	}

	if (!mysql_query($sql)) {
		echo "Error Saving User: ".mysql_error();
	} else {
		echo "<script>window.location='settings.php?p=Users';</script>";
	}
}
$id = mysql_real_escape_string($_GET['id']);
if($id != '') {
	$sql_user = "SELECT * FROM users WHERE id='$id' LIMIT 1";
	$result_user = mysql_query($sql_user);
	$row_user = mysql_fetch_assoc($result_user);
	foreach($row_user as $key=>$value) {
		$$key = $value;
	}
}
?>
<script>
$(document).ready(function() {
    $('#reset').click(function() {
        $('#reset_box').show();
        return false;
    });
});
</script>
<form action="" method="post">
<table width="100%" border="0" cellpadding="5" cellspacing="0">
<tr>
    <td style="padding-bottom: 10px; font-weight: bold;">Name:<br/><input type="hidden" id="id" name="id" value="<?=$_GET['id'];?>" /><input type="text" id="Name" name="Name" value="<?=$Name;?>"/></td>
</tr>
<tr>
    <td style="padding-bottom: 10px; font-weight: bold;">userID:<br/><input type="text" id="UserID" name="UserID" value="<?=$UserID;?>"/></td>
</tr>
<?php
    if (($Password == '') || ($Password == NULL)) {
        echo '<tr>
    <td style="padding-bottom: 10px; font-weight: bold;">Password:<br/><input type="text" id="Password" name="Password" value=""/></td>
</tr>';
    } else {
?>
<tr>
    <td style="padding-bottom: 10px; font-weight: bold;">Password:<br/><input type="text" id="Password" name="Password" value="**********"/><br><small><em><a href="#" id="reset">reset password</a></em></small><div id="reset_box" style="display: none;">Enter a new password:<br><input type="text" id="nPassword" name="nPassword"></div></td>
</tr>
<?php
    }
?>
<tr>
    <td style="padding-bottom: 10px; font-weight: bold;">Email:<br/><input type="text" id="Email" name="Email" value="<?=$Email;?>"/></td>
</tr>
<tr>
    <td style="padding-bottom: 10px; font-weight: bold;">Access Level:<br/>
	<select id="AccessLevel" name="AccessLevel">
	<?php
		$sql_access = "SELECT `Name` FROM access_level";
		$result_access = mysql_query($sql_access);
		while($row_access = mysql_fetch_array($result_access)) {
			if($AccessLevel == $row_access["Name"]) {
				$selected = ' selected="selected" ';
			} else {
				$selected = '';
			}
			echo "<option value=\"$row_access[Name]\" $selected>$row_access[Name]</option>";
		}
	?>
	</select></td>
</tr>
<tr>
	<td><input type="submit" style="background: #ff7e00; color: #fff;" id="btnSave" name="btnSave" value="Save / Update" /></td>
</tr>
</table>
</form>