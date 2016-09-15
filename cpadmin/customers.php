<?php
/**
 * Main Customer Information Admin Screen
 *
 * Version: 1.7.5
 * Updated: 07 October 2014
 * By: Richard Tuttle
 */

include_once("includes/header.php");

// determine which view to present
$page = 'list';
$id = '';

if($_GET["id"] != '') {
	$page = "details";
	$id = mysql_real_escape_string($_GET["id"]);
}

if(mysql_real_escape_string($_GET["id"]) == 'new') {
	$page = "new";
	$id = 0;
}

// update the customer information
if(isset($_POST["btnUpdate"])) {
	foreach($_POST as $key=>$value) {
		$$key = addslashes($value);
	}

	if($CreditLine == '') {
		$CreditLine = 0; 
	}

	if($VIPDate != '') {
		$sql_status = "Status='VIP', ";
	} else {
		$sql_status = "";
	}

	// update current customer info into database
	$sql_update  = "UPDATE customers SET FirstName='$FirstName', LastName='$LastName', Telephone='$Telephone', EmailAddress='$EmailAddress', CustomerGroup='$CustomerGroup', ";
	$sql_update .= "VIPNum='$VIPNum', VIPDate='$VIPDate', VIPLevel=$VIPLevel, BillingAddress='$BillingAddress', BillingCity='$BillingCity', BillingState='$BillingState', BillingZip='$BillingZip', ShippingFirstName='$ShippingFirstName', ShippingLastName='$ShippingLastName', ShippingAddress='$ShippingAddress', ";
	$sql_update .= $sql_status;
	$sql_update .= "ShippingCity='$ShippingCity', ShippingState='$ShippingState', ShippingZip='$ShippingZip', AccountName='$AccountName', CustomerNumber='$CustomerNumber', AccountNumber='$AccountNumber', CreditLine=$CreditLine, VIP_renewal_date='$VIPRenewDate', VIPExpDate='$ExpDate' WHERE id=$id LIMIT 1";
	// echo $sql_update;

	if(!mysql_query($sql_update)) {
		echo $sql_update."<br/><br/>";
		echo "Error updating customer: ".mysql_error();
	} 
}

// add new customer information
if (isset($_POST["btnAddNew"])) {
	foreach ($_POST as $key=>$value) {
		$$key = addslashes($value);
	}

	if ($CreditLine == '') { 
		$CreditLine = 0; 
	}
	
	if (($VIPNum != '') or ($VIPNum != NULL)) {
		$status = "VIP";
	} else {
		$status = "NonMember";
	}	
	
	// check for existing customer email address first
	$query = mysql_query("SELECT * FROM customers WHERE EmailAddress='" . $EmailAddress . "'");
	if (mysql_num_rows($query) != 0) {
		echo "<script>alert('That email address already exists in the database!');</script>";
	} else {
		// insert new customer info into database
		$sql_add = "INSERT INTO customers(FirstName, LastName, EmailAddress, Telephone, Password, BillingAddress, BillingCity, BillingState, BillingZip, ShippingAddress, ShippingCity, ShippingState, ShippingZip, Status, RegisterDate, VIPNum, VIPDate, VIPLevel, CustomerGroup, AccountName, CustomerNumber, AccountNumber, CreditLine, VIP_renewal_date, VIPExpDate) VALUES('$FirstName', '$LastName', '$EmailAddress', '$Telephone', '$password', '$BillingAddress', '$BillingCity', '$BillingState', '$BillingZip', '$ShippingAddress', '$ShippingCity', '$ShippingState', '$ShippingZip', '$status', CURDATE(), '$VIPNum', '$VIPDate', $VIPLevel, '$CustomerGroup', '$AccountName', '$CustomerNumber', '$AccountNumber', $CreditLine, '$VIPRenewDate', '$ExpDate')";
	 	if (!mysql_query($sql_add)) {
			echo "Error Adding new customer: " . mysql_error();
		} else {
			$page = 'list';
		}
	}
}

// upload and process excel customer list
if(isset($_POST['upload'])) {
	if (($_FILES["excell_file"]["type"] == "application/vnd.ms-excel")) {
		if ($_FILES["excell_file"]["error"] > 0) {

		} else {
			$file_name = "customers-" . rand(10,100) . ".xls";
			if (file_exists("customer_excell/" .  $file_name)) {

			} else {
				$file_name = "customers-" . rand(10,100) . ".xls";
				move_uploaded_file($_FILES["excell_file"]["tmp_name"],"customer_excell/" . $file_name);
			}
		}
		
		require_once 'Excel/reader.php';
		$data = new Spreadsheet_Excel_Reader();
		$data->setOutputEncoding('CP1251');
		$data->read("customer_excell/".$file_name);
		// mysql_query("truncate customers");

		for($x = 2; $x <= count($data->sheets[0]["cells"]); $x++) {
			$FirstName 			= addslashes($data->sheets[0]["cells"][$x][1]);
			$LastName 			= addslashes($data->sheets[0]["cells"][$x][2]);
			$Company 			= addslashes($data->sheets[0]["cells"][$x][3]);
			$EmailAddress 		= addslashes($data->sheets[0]["cells"][$x][4]);
			$Telephone 			= addslashes($data->sheets[0]["cells"][$x][5]);
			$Fax 				= addslashes($data->sheets[0]["cells"][$x][6]);
			$Password 			= addslashes($data->sheets[0]["cells"][$x][7]);
			$BillingAddress 	= addslashes($data->sheets[0]["cells"][$x][8]);
			$BillingCity   		= addslashes($data->sheets[0]["cells"][$x][9]);
			$BillingState 		= addslashes($data->sheets[0]["cells"][$x][10]);
			$BillingZip 		= addslashes($data->sheets[0]["cells"][$x][11]);
			$ShippingFirstName 	= addslashes($data->sheets[0]["cells"][$x][12]);
			$ShippingLastName 	= addslashes($data->sheets[0]["cells"][$x][13]);
			$ShippingAddress 	= addslashes($data->sheets[0]["cells"][$x][14]);
			$shippingCity 		= addslashes($data->sheets[0]["cells"][$x][15]);
			$ShippingState 		= addslashes($data->sheets[0]["cells"][$x][16]);
			$ShippingZip 		= addslashes($data->sheets[0]["cells"][$x][17]);
			$Status 			= addslashes($data->sheets[0]["cells"][$x][18]);
			$register_Date		= $data->sheets[0]["cells"][$x][19];
			$VIPNum 			= $data->sheets[0]["cells"][$x][20];
			$VIPDate 			= $data->sheets[0]["cells"][$x][21];
			$VIPLevel 			= addslashes($data->sheets[0]["cells"][$x][22]);
			$CustomerGroup 		= addslashes($data->sheets[0]["cells"][$x][23]);
			$AccountNumber 		= addslashes($data->sheets[0]["cells"][$x][24]);
			$CreditLine 		= addslashes($data->sheets[0]["cells"][$x][25]);
			$AccountName 		= addslashes($data->sheets[0]["cells"][$x][26]);
			$CustomerNumber 	= addslashes($data->sheets[0]["cells"][$x][27]);
			$VIP_renewal_date 	= $data->sheets[0]["cells"][$x][28];
			$VIPExpDate			= $data->sheets[0]["cells"][$x][29];
			
			if ($register_date != "") {
				list($d, $m, $y) = explode('/', $register_date);
				$mk = mktime(0, 0, 0, $m, $d, $y);
				$newregister_date = strftime('%Y-%m-%d', $mk);
			} else {
				$newregister_date = "1999-01-01";
			}
			
			if ($VIPDate != "") {
				list($d, $m, $y) = explode('/', $VIPDate);
				$mk = mktime(0, 0, 0, $m, $d, $y);
				$newVIPDate = strftime('%Y-%m-%d', $mk);
			} else {
				$newVIPDate = "1999-01-01";
			}
			
			if ($VIP_renewal_date != "") {
				list($d, $m, $y) = explode('/', $VIP_renewal_date);
				$mk = mktime(0, 0, 0, $m, $d, $y);
				$newVIP_renewal_date = strftime('%Y-%m-%d', $mk);
			} else {
				$newVIP_renewal_date = "1999-01-01";
			}
			
			if ($VIPExpDate != "") {
				list($d, $m, $y) = explode('/', $VIPExpDate);
				$mk = mktime(0, 0, 0, $m, $d, $y);
				$newVIPExpDate = strftime('%Y-%m-%d', $mk);
			} else {
				$newVIPExpDate = "1999-01-01";
			}
			
			if (($Status = "") || ($Status == NULL)) {
				$Status = "NonMember";
			}

			// check for duplicate email entry first
			if (isset($EmailAddress)) {
				$emailSQL = "SELECT EmailAddress FROM customers WHERE EmailAddress='$EmailAddress' LIMIT 1";
				$emailResult = mysql_query($emailSQL);
				if (mysql_num_rows($emailResult) > 0) {
					echo "<li> There is already a customer entry for " . $EmailAddress . " in the database.<br />";
				} elseif (($FirstName != '') || ($LastName != '') || ($Company != '')) {
					$sql = "INSERT INTO customers (FirstName,LastName,Company,EmailAddress,Telephone,Fax,Password,BillingAddress,BillingCity,BillingState,BillingZip,ShippingFirstName,ShippingLastName,ShippingAddress,ShippingCity,ShippingState,ShippingZip,Status,RegisterDate,VIPNum,VIPDate,VIPLevel,CustomerGroup,AccountNumber,CreditLine,AccountName,CustomerNumber,VIP_renewal_date, VIPExpDate) VALUES ('$FirstName','$LastName','$Company','$EmailAddress','$Telephone','$Fax','$Password','$BillingAddress','$BillingCity','$BillingState','$BillingZip','$ShippingFirstName','$ShippingLastName','$ShippingAddress','$shippingCity','$ShippingState','$ShippingZip','$Status','$newregister_Date','$VIPNum','$newVIPDate','$VIPLevel','$CustomerGroup','$AccountNumber','$CreditLine','$AccountName','$CustomerNumber','$newVIP_renewal_date', '$newVIPExpDate')";
					$query_r = mysql_query($sql) or die("Cusomter Upload Error: " .mysql_error());
				}
			} // end check
		} // end for loop
 	} else {
		echo "Invalid file";
	}
}

include_once("includes/mainHeader.php");
?>
<script language="javascript" type="text/javascript">
$(function() {
	$('form').jqTransform({imgPath:'jqtransformplugin/img/'});
});

$(document).ready(function() {
	$("#customers").load("includes/inc_customers.php", {
		"type":"<?=$page;?>", 
		"id":"<?=$id;?>"
	});
});

function qtyLoad(pager) {
	var totalnum = $("#totalview").val();
	$("#customers").load('<img src="images/loader.gif" />');
	$("#customers").load("includes/inc_customers.php", {
		"type":"list", 
		"totalview":totalnum, 
		"pager":pager
	});
}
</script>
</head>
<body>
<!-- Master Div starts from here -->
<div class="Master_div"> 
	<div class="PD_header">
	<div class="upper_head"></div>
	<div class="navi"><?php include_once('includes/menu_main.php'); ?>
		<div class="clear"></div>
	</div>
</div>
<!-- Product Detail Div starts from here -->
<div class="PD_main_form">

<!-- /div>
<div class="PD_main_form" -->
	<div class="orders" id="customers"></div>
	<div class="clear"></div>
</div>
<!-- Product Detail Div ends here --> 
</div>
</body>
</html>
<?php mysql_close($conn); ?>