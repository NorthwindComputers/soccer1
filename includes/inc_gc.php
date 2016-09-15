<?php
/***********************************
 * Gift Certificate include file    
 *                                                 
 * Updated: 17 August 2016         
 * By: Richard Tuttle              
 **********************************/
	
// start customer session and connect to the database
session_start();
require_once '../cpadmin/includes/db.php';

// check balance button clicked
if ($_POST["type"] == "bal") {
	$gcNum = $_POST["num"];
	$gcCk_sql = "SELECT * FROM certificate WHERE codeNum='$gcNum' AND certType='gift' LIMIT 1";
	$gcCk_result = mysql_query($gcCk_sql);
	$gcCk_row = mysql_fetch_array($gcCk_result);
	$gcCk_numRows = mysql_num_rows($gcCk_result);
	if ($gcCk_numRows > 0) {
		if ($gcCk_row["used"] == "no") {
			$balance = $gcCk_row['remainValue'];
			echo "<script>alert('balance: $$balance');</script>";
		} else {
			echo "<script>alert('Sorry, but the entire balance has already been used.');</script>";
		}
	} else {
		echo "<script>alert('Sorry, but that Gift Certificate number is invalid!');</script>"; 
	}
}

// apply button clicked
if ($_POST["type"] == "apply") {
	$gcNum = $_POST["num"];
	$currentTotal = $_POST["gt"];
	$disc = $_POST["disc"];
	$gcDisc = 0;
	$gcFunds = 0;
	// make sure the GC only gets entered once for usage during a session
	$applyCheck = "SELECT * FROM shopping_cart WHERE ProductID='" . $gcNum . "' AND Type='GC'";
	$result_applyCheck = mysql_query($applyCheck);
	$row_applyCheck = mysql_num_rows($result_applyCheck);
	if ($row_applyCheck > 0) {
		echo "<script>alert('Sorry, but that Gift Certificate is already applied to this order!');
		$('#gcError').text('ERROR: GC already applied!').css('color', 'red');
		</script>";
	} elseif ($row_applyCheck == 0) {
		$gcCk_sql = "SELECT * FROM certificate WHERE codeNum='$gcNum' AND certType='gift' LIMIT 1";
		$gcCk_result = mysql_query($gcCk_sql);
		$gcCk_row = mysql_fetch_array($gcCk_result);
		$gcCk_numRows = mysql_num_rows($gcCk_result);
		if ($gcCk_numRows > 0) {
			if ($gcCk_row["used"] == "no") {
				$balance = $gcCk_row['remainValue'];
				
				if ($currentTotal > $balance) {
					$newGT = $currentTotal - $balance;
					$gcFunds = $balance;
				} else {
					$gcFunds = $currentTotal;
					$newGT = 0;
				}
				$newDisc = $disc + $balance;
				if ($_SESSION['gct'] > 0) {
					$_SESSION['gct'] = $_SESSION['gct'] + $gcFunds;
				} else {
					$_SESSION['gct'] = $gcFunds;
				}
				// add certificate to shopping cart with balance value
				$addCart = "INSERT INTO shopping_cart (SessionID, EmailAddress, ProductID, ProductName, Qty, VIPPrice, Price, CreatedDate, Type) VALUES ('" . session_id() . "', '$_SESSION[email]', '$gcNum', 'Gift Certificate', '1', NULL, '" . $gcFunds . "', '" . date('Y-m-d') . "', 'GC')";
				mysql_query($addCart);
				$newDisc = number_format($newDisc, 2);
				$newGT = number_format($newGT, 2);
				// $('#totaldiscountval').html('$'+'" . $newDisc . "');";
				// $('#totaldiscount').val('" . $newDisc . "');
				echo "<script>
					  $('#gcFunds').show();";
				if ($_SESSION['gct'] >= 0) {
					echo "gc = $_SESSION[gct];";
				} else {
					echo "gc = $gcFunds;";
				}
				echo "$('#gcfundsval').html('$'+gc.toFixed(2));
					  $('#gctotal').val(gc);
					  gt = $newGT; 
					  $('#grandtotal').val(gt);
					  $('#grandtotalval').html('$'+gt.toFixed(2));
					  $('#gcError').text('Gift Certificate funds applied!').css('color', 'red');
					  $('#gttr').css('background-color', '#ebebeb');
					  $('#gtspan').html('<b>Grand Total Due</b>');
				</script>";  
			} else {
				echo "<script>alert('Sorry, but the entire balance has already been used.');
				$('#gcError').text('ERROR: funds previously used!').css('color', 'red');
				</script>";
			}
		} else {
			echo "<script>alert('Sorry, but that Gift Certificate number is invalid!');
			$('#gcError').text('ERROR: invalid entry!').css('color', 'red');
			</script>"; 
		}
	}
	//unset($_SESSION['gct']);
	//return $returndata;
}
mysql_close($conn);
exit();
?>