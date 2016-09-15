<?php
/*******************************
 * Sales Message admin entry
 *
 * By: Richard Tuttle
 * Last update: 03 December 2015
 ********************************/
 
if (isset($_POST["btnSubmit"])) {
	$messages = array("newcustomerwelcome", "neworderconfirmation", "ordercancel", "ordershippment", "termsconditions", "forgotpassword");
	foreach($messages as $message) {
		$sql_chk = "SELECT Message FROM messages WHERE `Type`='$message'";
		$result_check = mysql_query($sql_chk);
		$num_check = mysql_num_rows($result_check);
		if ($num_check > 0) {
			$sql = "UPDATE messages SET Message='".addslashes($_POST[$message])."' WHERE `Type`='$message' LIMIT 1";
		} else {
			$sql = "INSERT INTO messages(Message, `Type`) VALUES('".addslashes(mysql_real_escape_string($_POST[$message]))."', '$message')";
		}
		if (!mysql_query($sql)) {
			echo "Error saving message: ".mysql_error();
		}
	}
}
	
$sql_message = "SELECT Message, Type FROM messages WHERE `Type` IN ('newcustomerwelcome', 'neworderconfirmation', 'ordercancel', 'ordershippment', 'termsconditions', 'forgotpassword')";
$result_message = mysql_query($sql_message);
while($row_message = mysql_fetch_array($result_message)) {
	$$row_message["Type"] = stripslashes($row_message["Message"]);
}
?>
<form action="" method="post" >
	<table width="100%" cellpadding="5" cellspacing="1">
	<tr>
		<td class="headerst">New Customer Welcome Message:<br/><textarea id="newcustomerwelcome" name="newcustomerwelcome"><?=$newcustomerwelcome;?></textarea></td>
	</tr>
	<tr>
		<td class="headerst">New Order Confirmation:<br/><span style="font-weight: normal;">please use {{ORDERNUMBER}} to include order number</span><textarea id="neworderconfirmation" name="neworderconfirmation"><?=$neworderconfirmation;?></textarea></td>
	</tr>
	<tr>
		<td class="headerst">Order Cancellation Message:<br/><textarea id="ordercancel" name="ordercancel"><?=$ordercancel;?></textarea></td>
	</tr>
	<tr>
		<td class="headerst">Order Shippment Message:<br/><textarea id="ordershippment" name="ordershippment"><?=$ordershippment;?></textarea></td>
	</tr>
    <tr>
		<td class="headerst">Terms & Conditions(Order Page):<br/><textarea id="termsconditions" name="termsconditions"><?=$termsconditions;?></textarea></td>
	</tr>
    <tr>
		<td class="headerst">Forgot Email Message:<br/><textarea id="forgotpassword" name="forgotpassword"><?=$forgotpassword;?></textarea></td>
	</tr>
	<tr>
		<td><input type="submit" style="background: #ef9800;" id="btnSubmit" name="btnSubmit" value="Save / Update" /></td>
	</tr>
</table>
</form>
<script>
CKEDITOR.config.width = 600;
CKEDITOR.replace('newcustomerwelcome', {
	uiColor: '#9AB8F3'
});        
CKEDITOR.replace('neworderconfirmation', {
	uiColor: '#9AB8F3'
});        
CKEDITOR.replace('ordercancel', {
	uiColor: '#9AB8F3'
}); 
CKEDITOR.replace('ordershippment', {
	uiColor: '#9AB8F3'
}); 
CKEDITOR.replace('termsconditions', {
	uiColor: '#9AB8F3'
}); 
CKEDITOR.replace('forgotpassword', {
	uiColor: '#9AB8F3'
});
</script>