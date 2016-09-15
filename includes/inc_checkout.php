<?php
/***********************************
 * Order checkout include file     
 *                                                   
 * Updated: 05 April 2016          
 * By: Richard Tuttle              
 **********************************/
	
// start customer session and connect to the database
session_start();
require_once '../cpadmin/includes/db.php';

// need to login?
if($_POST["type"] == 'login') {
?>
	<form action="" method="post" id="loginForm">
	<table cellpadding="5" cellspacing="0">
	<tr>
		<td class="cartheader" style="text-align: left; border-left: 2px solid #bebebe; border-right: 2px solid #bebebe;" colspan="3"> &nbsp;Login</td>
	</tr>
	<tr>
		<td class="checkouttext" style="width: 50px; text-align: right;">Email Address:</td>
		<td class="checkouttext"><input type="text" class="address" style="width: 200px;" id="lEmailAddress" name="lEmailAddress" /></td>
		<td rowspan="3" class="checkouttext" style="padding-left: 10px;">Forgot your password? <a href="myaccount.php?p=forgot">click here!</a></td>
	</tr>
	<tr>
		<td class="checkouttext" style="width: 50px; text-align: right;">Password:</td>
		<td class="checkouttext"><input type="password" class="address" style="width: 200px;" id="lpassword" name="lpassword" /></td>
	</tr>
<?php 
	if($_POST["err"] != '') { 
?>
	<tr>
		<td colspan="2" class="err"><span style="margin-left: 222px;"><?=$_POST["err"];?></span></td>
	</tr>
<?php 
	} 
?>
	<tr>
		<td colspan="2"><input type="submit" style="margin-left: 180px;" class="button" id="login" name="login" value="Login" /></td>
	</tr>
	</table>
	</form>
	<form action="" method="post" id="registerForm">
	<table cellpadding="5" cellspacing="0">
	<tr>
		<td class="cartheader" style="text-align: left; border-left: 2px solid #bebebe; border-right: 2px solid #bebebe;" colspan="2"> &nbsp;Register</td>
	</tr>
	<tr rowspan="6">
		<td><font color="red">*</font> <em>denotes a required field</em></td>
	</tr>
	<tr>
		<td class="checkouttext"><font color="red">*</font>First Name:<br/><input type="text" class="address required" style="width: 200px;" id="FirstName" name="FirstName" /></td>
		<td class="checkouttext"><font color="red">*</font>Last Name:<br/><input type="text" class="address required" style="width: 200px;" id="LastName" name="LastName" /></td>
	</tr>
	<tr>
		<td class="checkouttext">Company:<br/><input type="text" class="address" style="width: 200px;" id="Company" name="Company" /></td>
		<td class="checkouttext"><font color="red">*</font>Email Address:<br/><input type="text" class="address required" style="width: 200px;" id="EmailAddress" name="EmailAddress" /></td>
	</tr>
	<tr>
		<td class="checkouttext"><font color="red">*</font>Telephone:<br/><input type="text" class="address required" style="width: 200px;" id="Telephone" name="Telephone" /></td>
		<td class="checkouttext">Fax:<br/><input type="text" class="address" style="width: 200px;" id="Fax" name="Fax" /></td>
	</tr>
	<tr>
		<td class="checkouttext"><font color="red">*</font>Password:<br/><input type="password" class="address required" style="width: 200px;" id="Password" name="Password" /></td>
		<td class="checkouttext"><font color="red">*</font>Confirm Password:<br/><input type="password" class="address required" style="width: 200px;" id="ConfirmPassword" name="ConfirmPassword" /></td>
	</tr>
	<tr>
		<td colspan="2"><input type="submit" class="button" id="register" name="register" value="Register" onClick="return chkRegister();" /></td>
	</tr>
	</table>
	</form>
	<script type="text/javascript">
  	$(document).ready(function() {
    	$("#registerForm").validate({
    		rules: {
    			EmailAddress: {
    				required: true,
    				email: true
    			},
    			Password: {
    				required: true
    			},
    			ConfirmPassword: {
    				required: true,
    				equalTo: "#Password"
    		}
    	});
  	});
  	</script>
<?php
	mysql_close($conn);
	exit();
}

if ($_POST["type"] == 'address') {
	$sql_address = "SELECT * FROM customers WHERE EmailAddress='$_SESSION[email]' LIMIT 1";
	$result_address = mysql_query($sql_address) or die("Address Info Error: " . mysql_error());
	$row_address = mysql_fetch_assoc($result_address);
	foreach ($row_address as $key=>$value) {
		$$key = stripslashes($value);
	}
?>
	<style>
		label { width: 10em; float: left; }
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
		em { font-weight: bold; padding-right: 1em; vertical-align: top; }
	</style>
  	<div align="center">
  		<h2>Customer Shipping &amp; Billing Information</h2>
  		<em><font color="red">*</font> indicates a required field</em><br>
		<form name="ship" action="" method="post" id="shipForm">
    		<!-- Billing Information::::::::::::::::::::::::::::::::::::::::::::: -->
    		<table cellpadding="5" cellspacing="0" id="addrForm" class="basic-grey">
    		<tr>
        		<td class="cartheader"><u><h3>Billing Information</h3></u></td>
    		</tr>
    		<tr>
        		<td><table cellpadding="5" cellspacing="2" style="margin-left: 20px;">
            		<tr>
                		<td class="checkouttext"><font color="red">*</font> First Name<br/><input type="text" class="address required" required id="BillingFirstName" name="BillingFirstName" value="<?=$FirstName;?>"></td>
                		<td class="checkouttext"><font color="red">*</font> Last Name<br/><input type="text" class="address required" required id="BillingLastName" name="BillingLastName" value="<?=$LastName;?>"></td>
                		<td class="checkouttext"></td>
            		</tr>
            		<tr>
                		<td class="checkouttext">Company<br/><input type="text" class="address" id="BillingCompany" name="BillingCompany" value="<?=$Company;?>"></td>
                		<td class="checkouttext"><font color="red">*</font> Email Address<br/> <input type="text" class="address required" id="BillingEmailAddress" name="BillingEmailAddress" value="<?=$EmailAddress;?>" required></td>
                		<td class="checkouttext"></td>
            		</tr>
            		<tr>
                		<td colspan="3" class="checkouttext"><font color="red">*</font> Address<br/><input type="text" class="address required" style="width: 620px;" id="BillingAddress" name="BillingAddress" value="<?=$BillingAddress;?>" required></td>
            		</tr>
            		<tr>
                		<td class="checkouttext"><font color="red">*</font> City<br/><input type="text" class="address required" id="BillingCity" name="BillingCity" value="<?=$BillingCity;?>" required></td>
                		<td class="checkouttext"><font color="red">*</font> State<br/>
                		<select class="address required" required id="BillingState" name="BillingState">
                			<option value="">select state</option>
						<?php
							$sql_states = "SELECT * FROM states ORDER BY State";
							$result_states = mysql_query($sql_states);
							while ($row_states=mysql_fetch_array($result_states)) {
								if ($BillingState == $row_states["Abbreviation"]) {
									$selected = ' Selected="Selected"';
								} else {
									$selected = '';
								}
								echo "<option value=\"$row_states[Abbreviation]\" $selected>$row_states[State]</option>";
							}
						?></select></td>
                		<td class="checkouttext"><font color="red">*</font> Zip<br/><input type="text" class="address required" id="BillingZip" name="BillingZip" value="<?=$BillingZip;?>" required></td>
            		</tr>
            		<tr>
        				<td class="checkouttext">Telephone<br/><input type="text" class="address" id="BillingTelephone" name="BillingTelephone" value="<?=$Telephone;?>" required></td>
                		<td class="checkouttext">Fax<br/><input type="text" class="address" id="BillingFax" name="BillingFax" value="<?=$Fax;?>" /></td>
                		<td class="checkouttext"></td>
            		</tr>
            		</table></td>
        		</tr>
        		</table>
        		<table cellpadding="5" cellspacing="0" id="addrForm" class="basic-grey">
        		<tr>
            		<td class="cartheader"><u><h3>Shipping Address</h3></u> <span class="checkoutheadersmall">(same as billing <input type="checkbox" class="samebilling" id="samebilling" name="samebilling" value="sameasbilling" />)</span></td>
        		</tr>
        		<tr>
        			<td><table cellpadding="5" cellspacing="2" style="margin-left: 20px;">
                		<tr>
                    		<td class="checkouttext"><font color="red">*</font> First Name<br/><input type="text" class="address required" id="ShippingFirstName" name="ShippingFirstName" value="<?=$ShippingFirstName;?>" /></td>
                    		<td class="checkouttext"><font color="red">*</font> Last Name<br/><input type="text" class="address required" id="ShippingLastName" name="ShippingLastName" value="<?=$ShippingLastName;?>" /></td>
                    		<td class="checkouttext"></td>
                		</tr>
                		<tr>
							<td class="checkouttext">Company<br/><input type="text" class="address" id="ShippingCompany" name="ShippingCompany" value="" /></td>
							<td class="checkouttext">Email Address<br/><input type="text" class="address" id="ShippingEmailAddress" name="ShippingEmailAddress" value="<?=$EmailAddress;?>" /></td>
							<td class="checkouttext"></td>
						</tr>
                		<tr>
                    		<td colspan="3" class="checkouttext"><font color="red">*</font> Address<br/><input type="text" class="address required" style="width: 620px;" id="ShippingAddress" name="ShippingAddress" value="<?=$ShippingAddress;?>" /></td>
                		</tr>
                		<tr>
                    		<td class="checkouttext"><font color="red">*</font> City<br/><input type="text" class="address required" id="ShippingCity" name="ShippingCity" value="<?=$ShippingCity;?>" /></td>
                    		<td class="checkouttext"><font color="red">*</font> State<br/><select class="address required" id="ShippingState" name="ShippingState">
                    			<option value="">select state</option>
								<?php
									$sql_states = "SELECT * FROM states ORDER BY State";
									$result_states = mysql_query($sql_states);
									while ($row_states=mysql_fetch_array($result_states)) {
										if ($ShippingState == $row_states["Abbreviation"]) {
											$selected = ' Selected="Selected"';
										} else {
											$selected = '';
										}
										echo "<option value=\"$row_states[Abbreviation]\" $selected>$row_states[State]</option>";
									}
								?></select></td>
								<td class="checkouttext"><font color="red">*</font> Zip<br/><input type="text" class="address required" id="ShippingZip" name="ShippingZip" value="<?=$ShippingZip;?>" /></td>
							</tr>
							</table></td>
        				</tr>
        				</table>
        				<table cellpadding="5" cellspacing="0" id="addrForm">
        				<tr>
            				<td><input type="submit" class="proceed" id="continue" name="continue" value="" /></td>
            			</tr>
        				</table>
       <?php
        if (isset($_POST['discount'])) {
			$discount = $_POST['discount'];
			echo '<input type="hidden" name="discount" value="$discount" />';
	}
	?>
	</form>
	<script type="text/javascript">
  		$(document).ready(function() {
    		$("#shipForm").validate({
    			rules: {
    				BillingEmailAddress: {
    					required: true,
    					email: true
    				},
    				BillingZip: {
    					required: true,
    					minlength: 5,
    					maxlength: 5
    				},
    				ShippingZip: {
    					required: true,
    					minlength: 5,
    					maxlength: 5
    				}
    			}
    		});
  		});
		$("#samebilling").click(function() {
			if($(this).is(':checked')) {			
				$("#ShippingFirstName").val($("#BillingFirstName").val()).attr("readonly", true);
				$("#ShippingLastName").val($("#BillingLastName").val()).attr("readonly", true);
				$("#ShippingCompany").val($("#BillingCompany").val()).attr("readonly", true);
				$("#ShippingEmailAddress").val($("#BillingEmailAddress").val()).attr("readonly", true);
				$("#ShippingAddress").val($("#BillingAddress").val()).attr("readonly", true);
				$("#ShippingCity").val($("#BillingCity").val()).attr("readonly", true);
				$("#ShippingState").val($("#BillingState").val()).attr("readonly", true);
				$("#ShippingCity").val($("#BillingCity").val()).attr("readonly", true);
				$("#ShippingZip").val($("#BillingZip").val()).attr("readonly", true);
			} else {
				$("#ShippingFirstName").attr("readonly", false);
				$("#ShippingLastName").attr("readonly", false);
				$("#ShippingCompany").attr("readonly", false);
				$("#ShippingEmailAddress").attr("readonly", false);
				$("#ShippingAddress").attr("readonly", false);
				$("#ShippingCity").attr("readonly", false);
				$("#ShippingState").attr("readonly", false);
				$("#ShippingCity").attr("readonly", false);
				$("#ShippingZip").attr("readonly", false);
			}
		});
	</script>
<?php
	mysql_close($conn);
	exit();
}
	
// paying on Open Account?
if ($_POST["type"] == "CheckCredit") {
	$code = $_POST["code"];
	$sql_chk = "SELECT id FROM customers WHERE EmailAddress='$_SESSION[email]' AND AccountNumber='$code' LIMIT 1";
	$result_chk = mysql_query($sql_chk);
	$num_chk = mysql_num_rows($result_chk);
		
	// check account number for validity
	if ($num_chk > 0) {
		echo 'OK';
	} else {
		echo 'Invalid';
	}
		
	mysql_close($conn);
	exit();
}

// paying by credit card?  finish details for Authorize.Net
/****************  AUTHORIZE NET USE ONLY *******************/
if ($_POST["type"] == "card") {
	$amount = $_POST["amount"];
	$loginID = $_POST["lid"];
	$sequence = $_POST["seq"];
	$timestamp = $_POST["times"];
	$transactionKey = $_POST["tk"];
    $fingerprint = hash_hmac("md5", $loginID . "^" . $sequence . "^" . $timestamp . "^" . $amount . "^", $transactionKey);
?>
	<script>
	$("#paymenttype").html("<input type='hidden' name='x_fp_hash' value='<?=$fingerprint;?>'>");
	</script>
<?php
	exit();
}

/************** manual inpurt card use only 
if ($_POST["type"] == "card") {
?>
	<script>
	$('.active').card({
		container: '.card-wrapper',
		numberInput: 'input#CardNumber', 
		expiryInput: 'input#ExpDate',
		cvcInput: 'input#SecurityCode',
		nameInput: 'input#NameOnCard',
	});
	</script>
	<div class="card-wrapper"></div>
	<div class="active">
	<table cellpadding="5" cellspacing="1" width="100%">
	<tr>
		<td colspan="4">Credit Card Type: <select class="address" style="width: 150px;padding:0px;" id="CardType" name="CardType"><option value="Visa">Visa</option><option value="MasterCard">Master Card</option><option value="DiscoverCard">Discover Card</option><option value="American Express">American Express</option></select></td>
	</tr>
	<tr>
		<td class="checkouttext">Name on Card:<br/><input type="text" class="address" id="NameOnCard" name="NameOnCard" /></td>
        <td class="checkouttext">Card Number:<br/><input type="text" class="address" id="CardNumber" onkeypress="return isNumberKey(event);" name="CardNumber" /><br /><span id="cardmsg"></span></td>
        <td class="checkouttext">Exp Date&nbsp;(mo/yr):<br/><input type="text" class="address" id="ExpDate" name="ExpDate" /></td>
        <td class="checkouttext">Security Code:<br/><input type="text" class="address" id="SecurityCode" name="SecurityCode" /></td>
	</tr>
	</table>
	</div>
<?php
	mysql_close($conn);
	exit();
}
******************************/
?>