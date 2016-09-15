<?php
/************************************
 * Customer Account Info Screen     
 *                                                 
 * Updated: 25 February 2016        
 * By: Richard Tuttle               
 ***********************************/
 
require_once 'cpadmin/includes/db.php';
session_start();

if(mysql_real_escape_string($_GET["p"]) == 'register') {
	$page = 'register';
} elseif(mysql_real_escape_string($_GET["p"]) == 'forgot') {
	$page = 'forgot';
} elseif($_SESSION["email"] == '') {
	$page = 'login';
} else {
	if(mysql_real_escape_string($_GET["p"]) == '') {
		$page = 'accountinformation';
	} else {
		$page = mysql_real_escape_string($_GET["p"]);
	}
}

// Customer LOGIN
if (isset($_POST["btnLogin"])) {
	$email = mysql_real_escape_string(strtolower($_POST['email']));
	$pswd = mysql_real_escape_string($_POST['password']);
    $sql_login = "SELECT id, FirstName, LastName, LOWER(EmailAddress), Password, CustomerGroup, BillingZip, Telephone, VIPExpDate, VIPNum, VIPLevel FROM customers WHERE EmailAddress='$email' AND Password='$pswd' LIMIT 1";
	$result_login = mysql_query($sql_login) or die("Login Error: " . mysql_error());
	$num_login = mysql_num_rows($result_login);
	$row_login = mysql_fetch_assoc($result_login);

	if($num_login > 0) {
		$_SESSION["email"] = $email;
		$_SESSION["name"] = $row_login["FirstName"];
		// set default myAccount page upon successful login
		if ($row_login["VIPExpDate"] != NULL) {
			$tempDate = date('Y-m-d');
			$today = strtotime($tempDate);
			$exp = strtotime($row_login["VIPExpDate"]);
			if ($exp <= $today) {
				$page = 'vip';
				$expired = "yes";
			// } elseif (isset($_POST["pid"])) {
			//	$pid = $_POST["pid"];
			//	header("Location: details.php?id=$pid");
			} else {
				// $page = 'accountinformation';
				header("Location: index.php");
				$expired = "no";
			}
		}
		if ($_POST["CustomerGroup"] != '') {
			$customerGroup = mysql_real_escape_string($_POST['CustomerGroup']);
			$vipnum = $_POST["CustomerGroup"]."-".substr($row_login["LastName"],0,4).substr($row_login["BillingZip"],0,5)."-".substr($row_login["Telephone"],-4);
			$sql_group = "SELECT GroupName FROM customer_group WHERE GroupCode='$customerGroup' LIMIT 1";
			$result_group = mysql_query($sql_group);
			$row_group = mysql_fetch_assoc($result_group);
			$sql_update = "UPDATE customers SET CustomerGroup='$row_group[GroupName]', Status='VIP', VIPNum='$vipnum', VIPDate=current_date WHERE EmailAddress='$_SESSION[email]'";
			mysql_query($sql_update);
			$sql_vipemail = "SELECT EmailAddress FROM emails WHERE `Type`='customerservice' LIMIT 1";
			$result_vipemail = mysql_query($sql_vipemail);
			$row_vipemail = mysql_fetch_assoc($result_vipemail);
			$vipheaders  = "From: $row_vipemail[EmailAddress]\r\n"; 
			$vipheaders .= "Content-type: text/html\r\n"; 
			$vipsubject = "SoccerOne VIP Confirmation";
			$sql_vipmess = "SELECT Message FROM messages WHERE `Type`='newvipwelcome' LIMIT 1";
			$result_vipmess = mysql_query($sql_vipmess);
			$row_vipmess = mysql_fetch_assoc($result_vipmess);
			mail($_SESSION["email"], $vipsubject, $row_vipmess["Message"], $vipheaders);	
		}
		$sql_updatecart = "UPDATE shopping_cart SET SessionID='".session_id()."' WHERE EmailAddress='$email'";
		mysql_query($sql_updatecart);
	} else {
		$page = 'login';
		echo "<script>alert('Whoops! The email address and/or password are incorrect. Please try again or contact us at 800-297-6386');</script>";
	}
}

// FORGOT PASSWORD
$forgotButton = $_POST["btnForgot"];
$email = $_POST['email'];
if(isset($forgotButton)) {
	if (!empty($_REQUEST['captcha'])) {
    	if (empty($_SESSION['captcha']) || trim(strtolower($_REQUEST['captcha'])) != $_SESSION['captcha']) {
		    $err = "Sorry ... that is an invalid security code!";
	    } else {
    	    $sql_chkemail = "SELECT EmailAddress, Password FROM customers WHERE EmailAddress='$email' LIMIT 1";
			$result_chkemail = mysql_query($sql_chkemail) or die("Email Check error: " . mysql_error());
			$num_chkemail = mysql_num_rows($result_chkemail);
			if ($num_chkemail > 0) {
				function newPass() {
					$alpha = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
					$pass = array();
					$alphalength = strlen($alpha) - 1;
					for ($i = 0; $i < 6; $i++) {
						$n = rand(0, $alphalength);
						$pass[] = $alpha[$n];
					}
					return implode($pass);
				}
				$row_chkemail = mysql_fetch_assoc($result_chkemail);
				$sql_from = "SELECT EmailAddress FROM emails WHERE Type='customerservice' LIMIT 1";
				$result_from = mysql_query($sql_from);
				$row_from = mysql_fetch_assoc($result_from);
				$sql_mess = "SELECT Message FROM messages WHERE Type='forgotpassword' LIMIT 1";
				$result_mess = mysql_query($sql_mess);
				$row_mess = mysql_fetch_assoc($result_mess);
				$headers = "MIME-Version: 1.0\r\n";
				$headers .= "From: SoccerOne Customer Service <customerservice@soccerone.com>\r\n"; 
				$headers .= "Content-type: text/html; charset: utf8\r\n"; 
				$headers .= "Reply-To: SoccerOne <customerservice@soccerone.com>\r\n";
				$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
				$subject  = "SoccerOne Account Information";
				$message  = stripslashes($row_mess["Message"]);
				$message .= "<p><b>Password:</b> <em>";
				$newPass = newPass();
				$sql_setpass = "UPDATE customers SET Password='".$newPass."' WHERE EmailAddress='$email' LIMIT 1";
				mysql_query($sql_setpass) or die("Password Generation Error: " . mysql_error());
				$message .= $newPass;
				$message .= "</em></p>";
				mail($_POST["email"], $subject, $message, $headers);
				$err = "A temporary password has been sent to your email address. You may now <a href='myaccount.php'>log into soccerone.com</a> using this password, which may be changed by you at a later time.";
			} else {
				$err = "Email Address was not found";
			}	
    	}
	}	
}

// REGISTER
if(isset($_POST["btnRegister"])) {
	foreach($_POST as $key=>$value) {
		$$key = addslashes($value);
	}
	
	// check for email address already in database
	if ($EmailAddress != '') {
		$scom = "SELECT * FROM customers WHERE EmailAddress='".$EmailAddress."'";
		$EmailAddress = ltrim(strtolower($EmailAddress)); // trim leading whitespace and make all lowercase
		$result = mysql_query($scom) or die(mysql_error());
		$num_row = mysql_num_rows($result);
		if ($num_row == 0) {
			if ($CustomerGroup != '') {
				$vipnum = $CustomerGroup . "-" . substr($LastName,0,4) . substr($BillingZip,0,5) . "-" . substr($Telephone,-4); // set VIP number
				$date = new DateTime(date('Y-m-d')); // get today's date
				$date->modify('+1 year'); // set new date to one year from now
				$expDate = $date->format('Y-m-d'); // make new date the VIP expirate date
				$sql_group = "SELECT GroupName FROM customer_group WHERE GroupCode='$CustomerGroup' LIMIT 1";
				$result_group = mysql_query($sql_group);
				$row_group = mysql_fetch_assoc($result_group);
				$sql_register = "INSERT INTO customers(FirstName, LastName, Company, EmailAddress, Telephone, Fax, Password, CustomerGroup, Status, VIPNum, VIPDate, VIPLevel, BillingAddress, BillingCity, BillingState, BillingZip, AccountName, VIPExpDate) ";
				$sql_register .= "VALUES('$FirstName', '$LastName', '$Company', '$EmailAddress', '$Telephone', '$Fax', '$Password', '$row_group[GroupName]', 'VIP', '$vipnum', current_date, '1', '$BillingAddress', '$BillingCity', '$BillingState', '$BillingZip', '$FirstName $LastName', '$expDate')";
				
				// create and send VIP email
				$sql_vipemail = "SELECT EmailAddress FROM emails WHERE `Type`='customerservice' LIMIT 1";
				$result_vipemail = mysql_query($sql_vipemail);
				$row_vipemail = mysql_fetch_assoc($result_vipemail);
				$vipheaders = "MIME-Version: 1.0\r\n";
				$vipheaders .= "From: SoccerOne Customer Service <customerservice@soccerone.com>\r\n"; 
				$vipheaders .= "Content-type: text/html; charset: utf8\r\n"; 
				$vipheaders .= "Reply-To: SoccerOne <customerservice@soccerone.com>\r\n";
				$vipheaders .= "X-Mailer: PHP/" . phpversion() . "\r\n";
				$vipsubject = "SoccerOne VIP Confirmation";
				$sql_vipmess = "SELECT Message FROM messages WHERE `Type`='newvipwelcome' LIMIT 1";
				$result_vipmess = mysql_query($sql_vipmess);
				$row_vipmess = mysql_fetch_assoc($result_vipmess);
				$row_vipmess["Message"] = str_replace("{{VIPNUMBER}}",strtoupper($vipnum), $row_vipmess["Message"]);
				mail($EmailAddress, $vipsubject, $row_vipmess["Message"], $vipheaders);
			} else {
				$sql_register = "INSERT INTO customers(FirstName, LastName, Company, EmailAddress, Telephone, Fax, Password, BillingAddress, BillingCity, BillingState, BillingZip, Status, AccountName) ";
				$sql_register .= "VALUES('$FirstName', '$LastName', '$Company', '$EmailAddress', '$Telephone', '$Fax', '$Password', '$BillingAddress', '$BillingCity', '$BillingState', '$BillingZip', 'NonMember', '$FirstName $LastName')";
			}
			if (!mysql_query($sql_register)) {
				echo "Error in registration: "  . mysql_error();
			} else {
				// send out Thank You letter
				$page = "thankyou";
				$_SESSION["email"] = $EmailAddress;
				$sql_email = "SELECT EmailAddress FROM emails WHERE `Type`='salesorder' LIMIT 1";
				$result_email = mysql_query($sql_email);
				$row_email = mysql_fetch_assoc($result_email);		
				$sql_message = "SELECT Message FROM messages WHERE `Type`='newcustomerwelcome' LIMIT 1";
				$result_message = mysql_query($sql_message);
				$row_message = mysql_fetch_assoc($result_message);
				$headers = "MIME-Version: 1.0\r\n";
				$headers .= "From: SoccerOne Customer Service <customerservice@soccerone.com>\r\n"; 
				$headers .= "Content-type: text/html; charset: utf8\r\n"; 
				$headers .= "Reply-To: SoccerOne <customerservice@soccerone.com>\r\n";
				$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
				$subject = "Welcome to Soccer One";
				mail($EmailAddress, $subject, $row_message["Message"], $headers);
				$sql_cart = "UPDATE shopping_cart SET EmailAddress='$EmailAddress' WHERE SessionID='".session_id()."'";
				mysql_query($sql_cart) or die(mysql_error());
			}	
		} else {
			echo "<p>We're sorry, the email address you entered is already registered at soccerone.com. If you would like to update your account, please log in and click on My Account. If you need assistance, contact Customer Service at (888) 297-6386.</p><p><h2><a href='index.php'>Go back to our Homepage</a></h2></p>"; 
			exit;
		}
	}
}

// SAVE ACCOUNT INFO
if (isset($_POST["btnSaveAccountInfo"])) {
	foreach($_POST as $key=>$value) {
		$$key = addslashes($value);
	}
	$EmailAddress = ltrim(strtolower($EmailAddress));	
	$sql_update = "UPDATE customers SET FirstName='$FirstName', LastName='$LastName', Company='$Company', EmailAddress='$EmailAddress', Telephone='$Telephone', Fax='$Fax', Password='$Password' WHERE EmailAddress='$_SESSION[email]' LIMIT 1";
	if(!mysql_query($sql_update)) {
		echo "Error updating account information: ".mysql_error();
	} else {
		$_SESSION["email"] = $EmailAddress;
		echo '<script>alert("Updates Saved!");</script>';
	}
}
	
if (isset($_POST["btnSaveAddress"])) {
	foreach($_POST as $key=>$value) {
		$$key = addslashes($value);
	}
		
	$sql_update = "UPDATE customers SET BillingAddress='$BillingAddress', BillingCity='$BillingCity', BillingState='$BillingState', BillingZip='$BillingZip', ShippingFirstName='$ShippingFirstName', ShippingLastName='$ShippingLastName', ShippingAddress='$ShippingAddress', ShippingCity='$ShippingCity', ShippingState='$ShippingState', ShippingZip='$ShippingZip' WHERE EmailAddress='$_SESSION[email]' LIMIT 1";
	if(!mysql_query($sql_update)) {
		echo "Error updating address information: ".mysql_error();
	}
}

$pageTitle = 'Login or Create an Account';
$p = mysql_real_escape_string($_GET["p"]);
$cg = mysql_real_escape_string($_GET["CG"]);

if (isset($_SESSION["email"])) {
	$p = isset($p) ? $p : '';
	$pageTitle = 'Account Information';
	if($p == 'orderhistory') {
		$pageTitle = 'My Orders';
	} elseif($p == 'vip') {
		$pageTitle = 'VIP Information';
	} elseif($p == 'addressbook') {
		$pageTitle = 'My Address';
	} elseif($p == 'newsletter') {
		$pageTitle = 'Newsletter Subscription';
	} else {
		$pageTitle = 'Account Information';
	}
} else {
	$p = isset($p) ? $p : '';
	if($p == 'forgot') {
		$pageTitle = 'Forgot Password';
	} else if ($p == 'register') {
		$pageTitle = 'Create an Account';
	}
}
include_once("includes/mainHeader.php");
?>
<script language="javascript" type="text/javascript">
	$(function() {
		<?php if (stristr($_SERVER['HTTP_USER_AGENT'], "Mobile")) { } else { ?> 
			$('form').jqTransform({imgPath:'jqtransformplugin/img/'});
		<?php } ?>
		$('#divPage').load('includes/inc_account.php', {
			"type":"<?=$page;?>", 
			"err":"<?=$err;?>", 
			"CG":"<?=$cg;?>",
			"expired":"<?=$expired;?>",
			"id":"<?=$_GET['id'];?>"
			// "pid":"<?=$_GET['prodid'];?>"
		});
	});

	function chkRegister() {
		var emailcheck = true;
		var message = '';
		$.post("duplicate_email_check.php",{ 
			// email:$('#EmailAddress').val()
			'email' : emailVal
		}, function(data) {
			if(data != 'valid') {
				alert(data);
		        emailcheck = false;
		        return false;     
		    } else {
				 $('#register').submit();
			}
		});
		return true;
	}
	
	/*
	function chkRegister1() {
		var arr = new Array("FirstName", "LastName", "EmailAddress", "Password", "ConfirmPassword", "Telephone", "BillingZip");
		for(var i=0; i<arr.length; i++) {
			if($("#"+arr[i]).val() == '') {
				alert("Please fill in all required fields!");
				$("#"+arr[i]).focus();
				return false;
			}
		}
		
		if($("#Password").val() != $("#ConfirmPassword").val()) {
			alert("Passwords are not the same.  Please re-enter the password");
			return false;
		}
	}
	*/
</script>
</head>
<body>
<div class="Master_div"> <?php include_once('includes/header.php'); ?>
  <div class="container container1">
    <div class="navigation">
      <div class="navi_L"></div>
      <div class="navi_C"><?php include_once('includes/topnav.php'); ?>
        <div class="clear"></div>
      </div>
      <div class="navi_R"></div>
      <div class="clear"></div>
    </div>
    <div class="browser_color">
      <div class="clear"></div>
      <h1>My Account</h1>
      <div class="apparel_ul">
        <ul>
        	<li <?php if($page=='accountinformation') { echo 'class="app_sel"';}?>><a href="myaccount.php?p=accountinformation">Account Information</a></li>
          	<li <?php if($page=='vip') { echo 'class="app_sel"';}?>><a href="myaccount.php?p=vip">VIP</a></li>
          	<li <?php if($page=='orderhistory') { echo 'class="app_sel"';} ?>><a href="myaccount.php?p=orderhistory">Order History</a></li>
          	<li <?php if($page=='addressbook') { echo 'class="app_sel"';}?>><a href="myaccount.php?p=addressbook">Address Book</a></li>
        </ul>
      </div>
    </div>
    	<div id="divPage" class="browser_product"><img src="images/loader.gif" /></div>
    <div class="clear"></div>
  </div>
  <div class="footer">
	<div class="foot_box"><?php include_once("includes/footer.php"); ?></div>
  </div>
</div>
</body>
</html>
<?php mysql_close($conn); ?>