<?php

	if(isset($_POST["btnSubmit"])) {
		$emails = array("salesorder", "salescomment", "customerservice");

		foreach($emails as $email) {
			
			$sql_chk = "SELECT EmailAddress FROM emails WHERE `Type`='$email'";
			$result_check = mysql_query($sql_chk);
			$num_check = mysql_num_rows($result_check);

			if($num_check>0) {
				$sql = "UPDATE emails SET EmailAddress='$_POST[$email]' WHERE `Type`='$email' LIMIT 1";
			} else {
				$sql = "INSERT INTO emails(EmailAddress, `Type`) VALUES('mysql_real_escape_string($_POST[$email])', '$email')";
			}

			if(!mysql_query($sql)) {
				echo "Error saving email address: ".mysql_error();
			}
		}
	}
	

	$sql_email = "SELECT EmailAddress, Type FROM emails WHERE `Type` IN ('salesorder', 'salescomment', 'customerservice')";
	$result_email = mysql_query($sql_email);

	while($row_email = mysql_fetch_array($result_email)) {
		$$row_email["Type"] = $row_email["EmailAddress"];
	}
	
?>

<form action="" method="post" >

	<table width="100%" cellpadding="5" cellspacing="1">
		<tr>
			<td style="width: 200px; padding-bottom: 10px; font-weight: bold;">Sales order email address:<br/>
            <input style="width: 250px;" type="text" name="salesorder" id="salesorder" value="<?=$salesorder;?>" /></td>
		</tr>
		<tr>
			<td style="width: 200px; padding-bottom: 10px; font-weight: bold;">Sales comment email address:<br/>
            <input style="width: 250px;" type="text" name="salescomment" id="salescomment" value="<?=$salescomment;?>" /></td>
		</tr>
		<tr>
			<td style="width: 200px; padding-bottom: 10px; font-weight: bold;">Customer Service Email Address:<br/>
            <input style="width: 250px;" type="text" name="customerservice" id="customerservice" value="<?=$customerservice;?>" /></td>
		</tr>
		<tr>
			<td><input type="submit" style="background: #ef9800;" id="btnSubmit" name="btnSubmit" value="Save / Update" /></td>
		</tr>
	</table>

</form>