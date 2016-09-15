<?php
/********************************
 *
 * Main login admin file
 *
 * By: Richard Tuttle
 * Last udpated: 10 June 2016
 *
 *******************************/
 
if (isset($_GET['email'])) { 
	$filtered_var = htmlspecialchars($_GET['email'], ENT_QUOTES); 
	$_GET['email'] = $filtered_var;
}
if (isset($_POST['email'])) { 
	$filtered_var = htmlspecialchars($_POST['email'], ENT_QUOTES);
	$_POST['email'] = $filtered_var;
}
if (isset($_REQUEST['email'])) { 
	$filtered_var = htmlspecialchars($_REQUEST['email'], ENT_QUOTES); 
	$_REQUEST['email'] = $filtered_var;
}
session_start();

// login function
if (isset($_POST["btnLogin"])) {
		require 'includes/db.php';
		$user = $_POST['user'];
		$pswd = $_POST['pass'];
		$sql_login = "SELECT userid, password FROM users WHERE userid='$user' LIMIT 1";
		$result_login = mysql_query($sql_login);
		$num_login = mysql_num_rows($result_login);
		$row_login = mysql_fetch_assoc($result_login);
		
	if ($num_login > 0) {
		if (password_verify($pswd, $row_login["password"])) {
			$_SESSION["userid"] = $row_login["userid"];
			header("location:index.php");
		} else {
			$err = "Invalid Password!";
		}
	} else {
		$err = "Invalid UserID!";
	}
	mysql_close($conn);
}
	
// forgot function
if (isset($_POST["btnForgot"])) {
	require 'includes/db.php';
	function newPass() {
		$alpha = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$pass = array();
		$alphalength = strlen($alpha) - 1;
		for ($i = 0; $i < 10; $i++) {
			$n = rand(0, $alphalength);
			$pass[] = $alpha[$n];
		}
		return implode($pass);
	}
	$newPass = newPass();
	$sql_setpass = "UPDATE users SET Password='" . password_hash($newPass, PASSWORD_DEFAULT) . "' WHERE Email='" . $_POST[email] . "' LIMIT 1";
	// echo "SQL: " . $sql_setpass; exit();
	$result_email = mysql_query($sql_setpass);
	$to = $_POST["email"];
	$subject = "SoccerOne Login Information";
	$mess  = "Here is your new password for login to SoccerOne\n\r";
	$mess .= "New Password: $newPass\n\r\n\r\n\r";
	mail($to, $subject, $mess);
	$err = "your login information has been sent.";
	mysql_close($conn);
}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
	<meta charset="utf-8">
	<title>SoccerOne Administration Login</title>
	<link rel="stylesheet" href="css/styles.css" type="text/css" />
	<script src="../js/jquery.min.js"></script>
	<script>
	$(document).ready(function() {
		$("a").click(function() {
			$("#login").slideUp('slow', function() {
				$("#forgot").css("display", "block").slideDown('slow');
			});
		});
	});
	</script>
</head>
<body>
<div class="Master_div"> 
	<div align="center"><img src="../images/browser_logo.png"><h2>Administrative Portal</h2></div>
  <div class="login">
    <div class="login_T"></div>
    <form action="" method="post">
    <div id="login">
    	<div class="login_C">
            <label>User Name</label>
            <label>Password</label>
            <input type="text" id="user" name="user" value="" />
            <input type="password" id="pass" name="pass" value="" />
            <input type="submit" id="btnLogin" name="btnLogin" value="" class="L_submit">
            <h6><span><?=$err;?></span></h6>
            <h6><a href="#">Forget your password?</a></h6>
            <div class="clear"></div>
      	</div>
    </div>
    <div id="forgot" style="display: none;">
    	<div class="login_C" style="text-align: center">
            <label>Email Address</label><br/><br/>
            <input type="text" id="email" name="email" />
            <input type="submit" id="btnForgot" name="btnForgot" value="" class="L_submitF">
        </div>
        <div class="clear"></div>
    </div>
  </form>
  </div>
</div>
</body>
</html>