<?php
/**************************************
 * customer account info include file
 *
 * updated: 25 August 2016
 * by: Richard Tuttle
 *
 **************************************/
 
require_once '../cpadmin/includes/db.php';
session_start();

if ($_POST["type"] == "logout") {
	session_start();
	$_SESSION = array();
	if (ini_get("session.use_cookies")) {
		$params = session_get_cookie_params();
		setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
	}
	session_unset();
	session_destroy();
	// header('Location:index.php');
}

// check login
if($_POST["type"] == "chkLogin") {
	if (!empty($_REQUEST['captcha'])) {
    	if (empty($_SESSION['captcha']) || trim(strtolower($_REQUEST['captcha'])) != $_SESSION['captcha']) {
		    $err = "Invalid Code";
	    } else {
			$email = $_POST["email"];
			$pass = $_POST["pass"];
			$sql_chk = "SELECT * FROM customers WHERE EmailAddress='$email' AND Password='$pass' LIMIT 1";
			$result_chk = mysql_query($sql_chk) or die("Customer Login Error: " . mysql_error());
			$num_chk = mysql_num_rows($result_chk);
			if ($num_chk > 0) {
				$_SESSION["email"] = $email;
				echo "Loggedin";
			} else {
				echo "error";
			}
			mysql_close($conn);
		exit();
		}
	}
}

// forgot password
if ($_POST["type"] == "forgot") {
?>
    <form action="" method="post" style="margin-left: 40px;">
    <table cellpadding="5" cellspacing="1" width="400px">
    <tr>
        <td style="font-weight: bold; font-size: 12px;">Email Address:</td>
        <td><input type="text" class="address" id="email" name="email"/></td>
    </tr>
    <tr>
		<td></td>
		<td><img src="./captcha/captcha.php" id="captcha" /><br/><a href="#" onclick="document.getElementById('captcha').src='./captcha/captcha.php?'+Math.random(); document.getElementById('captcha-form').focus();" id="change-image"><small>(security code not readable? click here to change text)</small></a></td>
	</tr>
	<tr>
		<td style="font-weight: bold; font-size: 12px;">Enter security code:</td>
		<td><input type="text" name="captcha" id="captcha-form" class="address" /></td>
	</tr>
<?php if($_POST["err"] != '') { ?>
    <tr>
        <td colspan="2" class="err"><?=$_POST["err"];?></td>
    </tr>
<?php } ?>
    <tr>
        <td colspan="2"><input type="submit" class="button" style="float: right; margin-right: 20px; width: 150px;" id="btnForgot" name="btnForgot" value="Request Password" /></td>
    </tr>
    </table>
    </form>
<?php
	mysql_close($conn);
	exit();
}

// user login
if ($_POST["type"] == "login") {
	if ($_POST["CG"] != '') {
		$cg = $_POST["CG"];
		$sql_group = "SELECT id, GroupName FROM customer_group WHERE GroupCode='$cg' LIMIT 1";
		$result_group = mysql_query($sql_group) or die("Group Error: " . mysql_error());
		$row_group = mysql_fetch_assoc($result_group);
		$mess = "Welcome to the ".$row_group["GroupName"]." VIP Purchasing Group";
	}
?>
	<p class="loginText">Please sign-in (existing accounts) or register (new accounts) so we will be able to serve you better. Our online clients are able to move through the checkout process faster, view order history, and take advantage of exclusive SoccerOne promotions. If you require further assistance please contact a SoccerOne Team Member at (888) 297-6386.</p>
	<div id="acctForm">	
		<div id="loginForm"><h1 class="formHeadBkg">Existing Customers</h1>
		<form action="" method="post" id="lForm">
		<table cellpadding="3" cellspacing="1" width="100%">
<?php 
	if ($cg != '') {
?>
		<tr>
			<td class="lFormMsg" colspan="2"><?=$mess;?></td>
		</tr>
<?php
	}
	// if (isset($_POST["pid"])) {
	//	$pid = $_POST["pid"];
	//	echo '<input type="hidden" name="pid" value="' . $pid . '">';
	// }
?>
		<tr>
			<td class="lFormStyle">Email Address:</td>
			<td><input type="hidden" id="CustomerGroup" name="CustomerGroup" value="<?=$cg;?>" /><input type="text" class="address lFormEmail" id="email" name="email" /></td>
		</tr>
		<tr>
			<td class="lFormStyle">Password:</td>
			<td><input type="password" class="address lFormEmail" id="password" name="password" /></td>
		</tr>
		<tr>
			<td colspan="2"><input type="submit" class="button lFormLink" id="btnLogin" name="btnLogin" value="Login" /><a class="forgotText" href="myaccount.php?p=forgot" title="Forgot Your Password?">Forgot or Create a Password?</a></td>
		</tr>
		</table>
		</form></div>
		<div id="newForm"><h1 class="formHeadBkg">New to SoccerOne?</h1>
			<div id="testBtn"><a title="Create an Account" href="myaccount.php?p=register&CG=<?=$cg;?>">Create an Account</a> -- IT'S FREE</div>
		</div>
	</div>	
<?php
	mysql_close($conn);
	exit();
}

// REGISTER ACCOUNT
if ($_POST["type"] == "register") {
	if ($_POST["CG"] != '') {
		$cg = $_POST["CG"];
		$sql_group = "SELECT id, GroupName FROM customer_group WHERE GroupCode='$cg' LIMIT 1";
		$result_group = mysql_query($sql_group);
		$row_group = mysql_fetch_assoc($result_group);
		$mess = "Welcome to the ".$row_group["GroupName"]." VIP Purchasing Group";
	}		
?>
		<style>
		label { float: left; }
		label.error { 
			padding: 10px 10px 10px 10px;
			margin: 10px 0;
			border: solid 1px; 
			border-color: #DF8F8F;
			background-color: #FFDCDC;
			font-size: 1em;
		}
		p { clear: both; }
		.submit { margin-left: 12em; }
		em { 
			font-weight: bold; 
			padding-right: 1em; 
			vertical-align: top; 
		}
		</style>
		<script>
	$(document).ready(function() {
		$("#register").validate({
    	rules: {
    		EmailAddress: {
    			required: true,
    			email: true
    		},
    		ReEmailAddress: {
    			equalTo: "#EmailAddress"
    		},
    		Telephone: {
    			digits: true,
    			phoneUS: true
    		},
    		Password: {
    			required: true
    			minlength: 5
    		},
    		ConfirmPassword: {
    			required: true,
    			equalTo: "#Password",
    			minlength: 5
    		},
    		BillingZip: {
    			required: true,
    			minlength: 5,
    			maxlength: 5,
    			digits: true
    		}
    	}
    	submitHandler: function(form) {
    		form.submit();
    	}
    });
	});
</script>
		<form action="" method="post" id="register" class="basic-grey">
		<h1>New Customer Registration Form
			<span>Please fill out all the fields below to get your FREE account. <small style="color:red;"><strong>*</strong> indicates a required field.</small></span>
		</h1>
		<table cellpadding="0" cellspacing="5">
		<?php if($cg != '') { ?>
		<tr>
			<th colspan="2"><font color="red"><h1><?php echo $mess; ?></h1></font><input type="hidden" id="CustomerGroup" name="CustomerGroup" value="<?=$cg;?>" /></th>
		</tr>
		<tr>
			<td style="text-align: center; font-weight: bold;" colspan="2">If you already have an account please click here to login:<br/><input type="button" class="button" value="Login" onClick="window.location='myaccount.php?p=login&CG=<?=$cg;?>'" /><br /><br /><br /><br /></td>
		</tr>
		<?php } ?>
		<tr>
			<td class="address"><font color="red">*</font><label for="FirstName">First Name</label></td>
			<td class="address"><font color="red">*</font><label for="LastName">Last Name</label></td>
		</tr>
		<tr>
			<td><input type="text" class="address" style="width: 200px;" id="FirstName" name="FirstName" required placeholder="Your First Name"></td>
			<td><input type="text" class="address" style="width: 200px;" id="LastName" name="LastName" required placeholder="Your Last Name"></td>
		</tr>
		<tr>
			<td class="address"><font color="red">*</font><label for="EmailAddress">Email Address</label></td>
			<td class="address"><font color="red">*</font><label for="ReEmailAddress">Re-Enter Email address</label></td>
		</tr>
		<tr>
			<td><input type="text" class="address" style="width: 200px;" id="EmailAddress" name="EmailAddress" minlength="5" required placeholder="Your eMail Address"></td>
			<td><input type="text" class="address" style="width: 200px;" id="ReEmailAddress" name="ReEmailAddress" minlength="5" required placeholder="Verify Your eMail Address"></td>
		</tr>
		<tr>
			<td class="address"><font color="red">*</font><label for="Telephone">Telephone</label></td>
			<td class="address">Fax</td>
		</tr>
		<tr>
			<td><input type="text" class="address" style="width: 200px;" id="Telephone" name="Telephone" required placeholder="Your Phone Number"></td>
			<td><input type="text" class="address" style="width: 200px;" id="Fax" name="Fax" placeholder="Your FAX Number"></td>
		</tr>
		<tr>
				<td class="address"><font color="red">*</font><label for="Password">Password</label></td>
				<td class="address"><font color="red">*</font><label for="ConfirmPassword">Confirm Password</label></td>
			</tr>
			<tr>
				<td><input type="password" class="address" style="width: 200px;" id="Password" name="Password" minlength="5" required placeholder="Your Password"></td>
				<td><input type="password" class="address" style="width: 200px;" id="ConfirmPassword" name="ConfirmPassword" minlength="5" required placeholder="Confirm Your Password"></td>
			</tr>
            <tr>
            	<td class="address" colspan="2"><font color="red">*</font><label for="BillingAddress">Address</label></td>
            </tr>
            <tr>
            	<td colspan="2"><input type="text" class="address" style="250px" id="BillingAddress" name="BillingAddress" required placeholder="Your Address"></td>
            </tr>
            <tr>
            	<td colspan="2">
                <table cellpadding="5" cellspacing="0">
                <tr>
                    <td class="address"><font color="red">*</font><label for="BillingCity">City</label></td>
                    <td class="address"><font color="red">*</font><label for="BillingState">State</label></td>
                    <td class="address"><font color="red">*</font><label for="BillingZip">Zipcode</label></td>
                </tr>
                <tr>
                    <td><input type="text" class="address" style="width: 200px;" id="BillingCity" name="BillingCity" required placeholder="Your City"></td>
                    <td><select class="address" id="BillingState" required name="BillingState"><option value="">Select State</option>
                    <?php
						$sql_states = "SELECT * FROM states ORDER BY State";
						$result_states = mysql_query($sql_states);
						while($row_states=mysql_fetch_array($result_states)) {
							echo "<option value=\"$row_states[Abbreviation]\" $selected>$row_states[State]</option>";
						}
					?>
                    </select></td>
                    <td><input type="text" class="address" style="width: 75px;" id="BillingZip" name="BillingZip" minlength="5" required placeholder="ZipCode"></td>
                </tr>
                </table></td>
            </tr>
            <tr>
			  <td class="address" colspan="2">Company</td>
		    </tr>
			<tr>
			  <td colspan="2"><input type="text" class="address" style="width: 200px;" id="Company" name="Company" placeholder="Your Company Name"></td>
		    </tr>
			<tr>
				<td colspan="2"><input type="submit" class="button" style="float: right; padding: 5px;" id="btnRegister" name="btnRegister" value="Register"></td>
			</tr>
		</table>
</fieldset>
</form>
<?
	mysql_close($conn);
	exit();
}

// Thank you page
if($_POST["type"] == "thankyou") {
	$sql_cus = "SELECT * FROM customers WHERE EmailAddress='".$_SESSION["email"]."' LIMIT 1";
	$cus_object = mysql_query($sql_cus);
	$cus_data = mysql_fetch_assoc($cus_object);
?>
        <table cellpadding="5" cellspacing="5">
        <tr>
            <td><h2>Thank you for registering</h2><p>&nbsp;</p><p>Thank you for registering with SoccerOne, your one-stop shop for all things soccer. Enjoy promotions and discounts ranging from 10% to 50% on all of your soccer needs from brand name companies, such as adidas, PUMA, and Under Armour. </p><p>&nbsp;</p><hr /><p>Please review your account by navigating the categories on your left or by clicking on “My Account” at the upper right corner. </p><p>&nbsp;</p>
			<?php if (!is_null($cus_data['CustomerGroup']) && !empty($cus_data['CustomerGroup'])): ?>
			<hr /><p>In addition, you can view and shop your affiliate program’s exclusive homepage and other unique offers and specials. Click on Group Name in the red bar under the Search Field.</p>
			<?php endif; ?>
			</td>
        </tr>
        </table>
<?php
	mysql_close($conn);
	exit();
}
	
if ($_POST["type"] == "accountinformation") {
	$sql_acc = "SELECT * FROM customers WHERE EmailAddress='$_SESSION[email]' LIMIT 1";
	$result_acc = mysql_query($sql_acc);
	$row_acc = mysql_fetch_assoc($result_acc);
	foreach ($row_acc as $key=>$value) {
		$$key = stripslashes($value);
	}	
?>
	<form action="" method="post" id="register" class="basic-grey">
	<h1>Your Account Information
		<span>Please verify that all information is still correct. <small>All fields marked with <font color="red">*</font> are required.</small></span>
	</h1>
	<table cellpadding="0" cellspacing="5">
			<tr>
				<td class="FirstName"><font color="red">*</font><label for="FirstName">First Name</label></td>
				<td class="LastName"><font color="red">*</font><label for="LastName">Last Name</label></td>
			</tr>
			<tr>
				<td><input type="text" class="address" style="width: 200px;" id="FirstName" name="FirstName" value="<?=$FirstName;?>" required></td>
				<td><input type="text" class="address" style="width: 200px;" id="LastName" name="LastName" value="<?=$LastName;?>" required></td>
			</tr>
			<tr>
				<td class="Company">Company</td>
				<td class="EmailAddress"><font color="red">*</font><label for="EmailAddress">Email Address</label></td>
			</tr>
			<tr>
				<td><input type="text" class="address" style="width: 200px;" id="Company" name="Company" value="<?=$Company;?>"/></td>
				<td><input type="text" class="address" style="width: 200px;" id="EmailAddress" name="EmailAddress" value="<?=$EmailAddress;?>" required></td>
			</tr>
			<tr>
				<td class="Telephone"><font color="red">*</font><label for="Telephone">Telephone</label></td>
				<td class="Fax">Fax</td>
			</tr>
			<tr>
				<td><input type="text" class="address" style="width: 200px;" id="Telephone" name="Telephone" value="<?=$Telephone;?>" required></td>
				<td><input type="text" class="address" style="width: 200px;" id="Fax" name="Fax" value="<?=$Fax;?>" /></td>
			</tr>
			<tr>
				<td class="Password"><font color="red">*</font><label for="Password">Password</label></td>
				<td class="ConfirmPassword"><font color="red">*</font><label for="ConfirmPassword">Confirm Password</label></td>
			</tr>
			<tr>
				<td><input type="password" class="address" style="width: 200px;" id="Password" name="Password" minlength="5" value="<?=$Password;?>" required></td>
				<td><input type="password" class="address" style="width: 200px;" id="ConfirmPassword" name="ConfirmPassword" minlength="5" value="<?=$Password;?>" required></td>
			</tr>
			<tr>
				<td colspan="2">
				<input type="submit" class="button" id="btnSaveAccountInfo" name="btnSaveAccountInfo" onClick="return chkRegister1();" value="Save / Update" />
				<input type="hidden" name="btnSaveAccountInfo" value="Save / Update" />
				
		</td>
	</tr>
	</table>
	</form>
	<script>
	$(document).ready(function() {
		$("form").validate({
    	rules: {
    		'Password': {
    			minlength: 5,
    			required: true
    		}, 
    		'ConfirmPassword': {
    			minlength: 5,
    			equalTo: "#Password",
    			required: true
    		},
    		'FirstName': {
    			required: true
    		},
    		'LastName': {
    			required: true
    		},
    		'EmailAddress': {
    			required: true,
    			email: true
    		},
    		'Telephone': {
    			required: true,
    			digits: true
    		}
    	}
    	submitHandler: function(form) {
    		form.submit();
    	}
    });
	});
	</script>
<?
	mysql_close($conn);
	exit();
}

// customer address book	
if ($_POST["type"] == 'addressbook') {
		$sql_add = "SELECT * FROM customers WHERE EmailAddress='$_SESSION[email]' LIMIT 1";
		$result_add = mysql_query($sql_add);
		$row_add = mysql_fetch_assoc($result_add);
		foreach($row_add as $key=>$value) {
			$$key = stripslashes($value);
		}
		?>
        <form action="" id="register" method="post" style="margin-left: 30px;" class="basic-grey">
        <h1>Customer Address Book
        <span>Please verify that all information is still correct.</span></h1>
        <table cellpadding="5" cellspacing="0">
        <tr>
            <td colspan="3"><u><h2>Billing Address</h2></u></td>
        </tr>
        <tr>
            <td colspan="3" class="address">Address:<br/><input type="text" class="address" id="BillingAddress" name="BillingAddress" value="<?=$BillingAddress;?>"></td>
        </tr>
        <tr>
            <td class="address">City:<br/><input type="text" class="address" id="BillingCity" name="BillingCity" value="<?=$BillingCity;?>"></td>
            <td class="address">State:<br/>
                <select class="address" id="BillingState" name="BillingState">
                    <option value="">select state</option>
                    <?php
					$sql_states = "SELECT * FROM states ORDER BY State";
					$result_states = mysql_query($sql_states);
					while ($row_states=mysql_fetch_array($result_states)) {
						if($BillingState == $row_states["Abbreviation"]) {
							$selected = ' Selected="Selected"';
						} else {
							$selected = '';
						}
						echo "<option value=\"$row_states[Abbreviation]\" $selected>$row_states[State]</option>";
					}
					?>
                </select></td>
            <td class="address">Zip:<br/><input type="text" class="address" id="BillingZip" name="BillingZip" value="<?=$BillingZip;?>"></td>
        </tr>
        </table>
        <table cellpadding="5" cellspacing="0" style="margin-top: 30px;">
        <tr>
            <td colspan="3"><u><h2>Shipping Address</h2></u></td>
        </tr>
		<tr>
			<td class="address" colspan="3">First Name:<br/><input type="text" class="address" id="ShippingFirstName" name="ShippingFirstName" value="<?=$ShippingFirstName;?>"></td>
		</tr>
		<tr>
			<td class="address" colspan="3">Last Name:<br/><input type="text" class="address" id="ShippingLastName" name="ShippingLastName" value="<?=$ShippingLastName;?>"></td>
		</tr>
        <tr>
            <td colspan="3" class="address">Address:<br/><input type="text" class="address" id="ShippingAddress" name="ShippingAddress" value="<?=$ShippingAddress;?>"></td>
        </tr>
        <tr>
            <td class="address">City:<br/><input type="text" class="address" id="ShippingCity" name="ShippingCity" value="<?=$ShippingCity;?>"></td>
            <td class="address">State:<br/>
            	<select class="address" id="ShippingState" name="ShippingState">
                    <option value="">select state</option>
					<?php
						$sql_states = "SELECT * FROM states ORDER BY State";
						$result_states = mysql_query($sql_states);
						
						while($row_states=mysql_fetch_array($result_states)) {
							if($ShippingState == $row_states["Abbreviation"]) {
								$selected = ' Selected="Selected"';
							} else {
								$selected = '';
							}
							echo "<option value=\"$row_states[Abbreviation]\" $selected>$row_states[State]</option>";
						}
					?>
                </select></td>
            <td class="address">Zip:<br/><input type="text" class="address" id="ShippingZip" name="ShippingZip" value="<?=$ShippingZip;?>"></td>
        </tr>
        </table>
        <table cellpadding="5" cellspacing="0">
        <tr>
            <td><input type="submit" class="button" id="btnSaveAddress" name="btnSaveAddress" value="Save / Update"></td>
    	</tr>
    	</table>
    </form>
<script>
$(document).ready(function() {
	$("form").validate({
	rules: { 
		'BillingAddress': {
			required: true
		},
		'BillingCity': {
			required: true
		},
		'BillingState': {
			required: true
		},
		'BillingZip': {
			minlength: 5,
			required: true
		},
		'ShippingZip': {
			minlength: 5,
			required: true
		}
	} // end rules
	submitHandler: function(form) {
		form.submit();
	}
	}); // end validate
});
</script>
<?php
	mysql_close($conn);
	exit();
}

// Order History display
if ($_POST["type"]=="orderhistory") {
?>
	<table cellpadding="5" cellspacing="1" width="700px">
	<tr>
		<td style="font-weight: bold; font-size: 14px;">Order History</td>
	</tr>
	<tr>
		<td>
		<table cellpadding="5" cellspacing="0" width="100%">
		<tr>
			<td class="orderheader">Order#</td>
			<td class="orderheader">Order Date</td>
			<td class="orderheader">Product Name</td>
			<td class="orderheader">Unit Price</td>
			<td class="orderheader">Order Total</td>
		</tr>
<?php
		$sql_orders = "SELECT id, OrderDate, GrandTotal FROM orders WHERE EmailAddress='$_SESSION[email]' ORDER BY OrderDate DESC";
		$result_orders = mysql_query($sql_orders);
		$c_num = 0;
		while($row_orders = mysql_fetch_array($result_orders)) {
			if($c_num == 0) {
				$color = "#f2f2f2";
				$c_num++;
			} else {
				$color = "#e7e7e7";
				$c_num = 0;
			}
			$sql_items = "SELECT ProductName, Price FROM orders_items WHERE OrderID=$row_orders[id] AND Type!='C_Product'";
			$result_items = mysql_query($sql_items);
			$prodname = '';
			$unitprice = '';
			while($row_items = mysql_fetch_array($result_items)) {
				$prodname .= $row_items["ProductName"]."<br/>";
				$unitprice .= "$".number_format($row_items["Price"], 2)."<br/>";
			}
			$sql_imp = "SELECT SUM(ImprintPrice) AS TotalImp FROM imprint_orders WHERE OrderNumber=$row_orders[id]";
			$result_imp = mysql_query($sql_imp);
			$row_imp = mysql_fetch_assoc($result_imp);
			if($row_imp["TotalImp"] > 0) {
				$prodname .= "Imprint<br/>";
				$unitprice .= "$".number_format($row_imp["TotalImp"], 2)."<br/>";
			}
?>
		<tr>
			<td class="orderitems" style="background-color: <?=$color;?>;"><?php echo '<a href="myaccount.php?p=orderdetail&id=' . $row_orders["id"] . '">' . $row_orders["id"] . '</a>';?></td>
			<td class="orderitems" style="background-color: <?=$color;?>;"><?=$row_orders["OrderDate"];?></td>
			<td class="orderitems" style="background-color: <?=$color;?>;"><?=$prodname;?></td>
			<td class="orderitems" style="background-color: <?=$color;?>;"><?=$unitprice;?></td>
			<td class="orderitems" style="background-color: <?=$color;?>;"><?="$".number_format($row_orders["GrandTotal"], 2);?></td>
		</tr>
<?php
		}
?>
		</table></td>
	</tr>
	</table>
<?php 
	mysql_close($conn);
	exit();
}

// Order history detail display
if ($_POST["type"]=="orderdetail") {
    $_POST["id"];
    $sql_order = "SELECT * FROM orders WHERE EmailAddress='".$_SESSION['email']."' AND id='".$_POST['id']."'";
    // echo "SQL: " . $sql_order; exit(); // testing use only
	$result_order = mysql_query($sql_order);
	while($row_order = mysql_fetch_array($result_order)) {
	    $orderDate = date_create($row_order["OrderDate"]);
	    echo "<h2>Details of Order Number " . $_POST['id'] . "</h2>";
	    echo "<div class='detailsInfoBox'><b>Order Date:</b> " . date_format($orderDate, "F j, Y") . "<br><b>Shipment Method Chosen:</b> " . $row_order["ShippingMethod"] . "<br><b>Order Status:</b> " . $row_order["OrderStatus"] . "<br><br><p><u>Items Ordered:</u>";
	    echo "<table class='orderItems'><tr><th style='width:60%;'>Product Name</th><th>Quantity</th><th>Price</th></tr>";
	    $sql_items = "SELECT ProductName, Price, Qty FROM orders_items WHERE OrderID='".$_POST['id']."' AND Type!='C_Product'";
		$result_items = mysql_query($sql_items);
		while($row_items = mysql_fetch_array($result_items)) {
			echo "<tr>";
			echo "<td>" . $row_items["ProductName"] . "</td>";
			echo "<td>" . $row_items["Qty"] . "</td>";
			echo "<td>$" . number_format($row_items["Price"], 2) . "</td>";
			echo "</tr>";
		}
	echo "</table></p>";
	echo "<div align='right'><b>Order Total:</b> $" . $row_order["OrderTotal"] . "<br>";
	if ($row_order["Discount"] != NULL) {
	    echo "<b>Discount:</b> $" . number_format($row_order["Discount"], 2) . "<br>";
	} else {
	    echo "<b>Discount:</b> $0<br>";
	}
	if ($row_order["ShippingTotal"] != NULL) {
	    echo "<b>Shipping:</b> $" . number_format($row_order["ShippingTotal"], 2) . "<br>";
	} else {
	    echo "<b>Shipping:</b> $0<br>";
	}
	if ($row_order["Tax"] != NULL) {
	    echo "<b>Tax:</b> $" . number_format($row_order["Tax"], 2) . "<br>";
	} else {
	    echo "<b>Tax:</b> $0<br>";
	}
	echo "<b>Grand Total Paid:</b> $" . number_format($row_order["GrandTotal"], 2) . "<br>";
	echo "Paid via: " . $row_order["CardType"];
	echo "</div>";
	echo "</div>";
	}
}

	
if($_POST["type"] == "vip") {
	$sql_vip = "SELECT Status, VIPNum, VIPDate, VIPLevel, VIPExpDate FROM customers WHERE EmailAddress='$_SESSION[email]' LIMIT 1";
	$result_vip = mysql_query($sql_vip);
	$row_vip = mysql_fetch_assoc($result_vip);

	if ($row_vip["Status"] == "VIP") {
		if ($row_vip["VIPLevel"] == '' || $row_vip["VIPLevel"] == 0) {
			$level = 1;				
		} else {
			$level = $row_vip["VIPLevel"];
		}
			
		$sql_level = "SELECT level FROM viplevels WHERE id=$level LIMIT 1";
		$result_level = mysql_query($sql_level);
		$row_level = mysql_fetch_assoc($result_level);
	?>
        <table cellpadding="5" cellspacing="0" style="margin-left: 20px;">
        <tr>
            <td style="border-right: 5px solid #bebebe; font-weight: bold; width: 200px; background-color: #f2f2f2;">Your VIP Number</td>
            <td style="font-weight: bold; padding-left: 10px; background-color: #f2f2f2; width: 200px;">Expiration Date</td>
            <td style="font-weight: bold; padding-left: 10px; background-color: #f2f2f2; width: 200px;">VIP Level</td>
        </tr>
        <tr>
            <td style="border-right: 5px solid #bebebe; font-size: 16px; height: 70px; font-weight: bold; vertical-align: top; color: #ff3300;"><?=strtoupper($row_vip["VIPNum"]);?></td>
            <td style="padding-left: 10px; font-size: 16px; height: 70px; font-weight: bold; vertical-align: top; color: #FF3300">
    <?php 
    		$today = date('Y-m-d');
    		if ($row_vip["VIPExpDate"] < $today) {
    			$expired = "yes";
    		}
			$date = strtotime($row_vip["VIPExpDate"]);
			echo date('m/d/Y', $date);
	?></td>
            <td style="padding-left: 10px; font-size: 16px; height: 70px; font-weight: bold; vertical-align: top; color: #FF3300"><span style="border-left: 5px solid #bebebe; font-size: 16px; height: 70px; font-weight: bold; vertical-align: top; color: #ff3300;"> &nbsp;&nbsp;<?=strtoupper($row_level["level"]);?></span></td>
        </tr>
        </table>
	<?php
		} else {
	?>
        <table cellpadding="5" cellspacing="0" style="margin-left: 20px;">
        <tr>
            <td style="font-weight: bold;">You are currently not a VIP Member.<br /><a href="details.php?id=VIP&exp=no">Click here to become a VIP</a></td>
        </tr>
        </table>
	<?php
		}
		
		if (($_POST["expired"] == "yes") || ($expired == "yes")) {
			echo '<table cellpadding="5" cellspacing="0" style="margin-left: 20px;"><tr><td style="font-weight: bold;"><h3>We are sorry but your VIP Member account has expired!</h3><br />Please consider renewing today to continue to get the great discounts.<br /><br /><p><b><a href="details.php?id=VIP&exp=yes">RENEW TODAY</a></b></p></td></tr></table>';
		}
		
		mysql_close($conn);
		exit();
	}
?>