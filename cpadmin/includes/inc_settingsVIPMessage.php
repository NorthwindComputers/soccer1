<?php

	if(isset($_POST["btnSubmit"])) {
		$messages = array("newvipwelcome","vipexpiration");

		foreach($messages as $message) {
			
			$sql_chk = "SELECT Message FROM messages WHERE `Type`='$message'";
			$result_check = mysql_query($sql_chk);
			$num_check = mysql_num_rows($result_check);

			if($num_check>0) {
				$sql = "UPDATE messages SET Message='".addslashes($_POST[$message])."' WHERE `Type`='$message' LIMIT 1";
			} else {
				$sql = "INSERT INTO messages(Message, `Type`) VALUES('".addslashes($_POST[$message])."', '$message')";
			}

			if(!mysql_query($sql)) {
				echo "Error saving message: ".mysql_error();
			}
		}
	}
	

	$sql_message = "SELECT Message, Type FROM messages WHERE `Type` IN ('newvipwelcome','vipexpiration')";
	$result_message = mysql_query($sql_message);

	while($row_message = mysql_fetch_array($result_message)) {
		$$row_message["Type"] = stripslashes($row_message["Message"]);
	}
	
?>

<form action="" method="post" >

	<table width="100%" cellpadding="5" cellspacing="1">
		<tr>
			<td class="headerst">New VIP Member Welcome:<br/>
				<textarea id="newvipwelcome" name="newvipwelcome"><?=$newvipwelcome;?></textarea></td>
		</tr>
		<tr>
			<td class="headerst">VIP Expiration Message:<br/>
				<textarea id="vipexpiration" name="vipexpiration"><?=$vipexpiration;?></textarea></td>
		</tr>
		<tr>
			<td><input type="submit" style="background: #ef9800;" id="btnSubmit" name="btnSubmit" value="Save / Update" /></td>
		</tr>
	</table>

</form>
<script>
	$('#newvipwelcome').wysiwyg();
	$('#vipexpiration').wysiwyg();
</script>