<?php
/*********************************
 * check for duplicat email in db
 *
 * by: Richard Tuttle
 * version: 1.1
 * updated: 06 June 2013
 *********************************/
 
if ($_POST) {
	require_once 'cpadmin/includes/db.php';
	$emailRegex = "/^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*\.(com|net|org|info|us|biz|tv|name|cc|ws|bz|tc|vg|ms|gs)$/";
	$email = $_POST['email'];
	if (preg_match($emailRegex, $email)) {
		$scom = "SELECT * FROM customers WHERE EmailAddress='".$email."'";
		$result = mysql_query($scom) or die(mysql_error());
		$num_row = mysql_num_rows($result);
		if ($num_row == 0) {
		    echo 'valid'; 
		    exit;
		} else {
			echo "We're sorry, the email address you entered is already registered at soccerone.com. If you would like to update your account, please log in and click on My Account. If you need assistance, contact Customer Service at (888) 297-6386."; 
			exit;
		}
	} else {
		echo 'Please enter a valid email address'; 
		exit;
	}
}
// echo 'Please enter valid email address'; 
// exit;