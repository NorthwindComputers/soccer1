<?php
/**********************************
 * Credit card encryptor          *
 * Version: 1.1                   *
 * Author: Richard Tuttle         *
 * Updated: 07 Feb 2013           *
 *********************************/

// connect to the database
require("includes/db.php");

// get customer CC num via the Order ID
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$sql = "SELECT * FROM orders WHERE id='" . (int)$id . "' LIMIT 1";
$result = mysql_query($sql);
if (!$result) {
	echo mysql_error();
}
$row = mysql_fetch_assoc($result);
	
	// for testing only
	/* 
	echo "<br /><b>Order ID: " . $id . "</b><br />";
	echo "CCN: " . $row['CCNum'];
	echo "/ SC: " . $row['SecurityCode'];
	*/
	
// scrub the data 
// $newCCNum = ereg_replace("[0-9]", "x", $row['CCNum']);
$newCCNum = substr_replace($row['CCNum'], str_repeat('*', strlen($row['CCNum']) - 4), 0, -4);
$newSC = ereg_replace("[0-9]", "x", $row["SecurityCode"]);

	// testing only display
	/*
	echo "<br /><br />";
	echo "CCN: " . $newCCNum;
	echo "/ SC: " . $newSC;
	*/

// input scrubbed info into the database
$updateSQL = "UPDATE orders SET CCNum='$newCCNum', SecurityCode='$newSC' WHERE id=$id";

	// testing only
	// echo "<p>" . $updateSQL . "</p>";

$retval = mysql_query($updateSQL);
if (!$retval) {
	die('Could not update that data: ' . mysql_error());
} else {
	echo "The credit card data has been successfully secured!";
}
?>