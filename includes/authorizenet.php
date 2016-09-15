<?php
/************************************
 * Authorize.net info file     
 *                                              
 * Updated: 14 January 2016            
 * By: Richard Tuttle               
 ***********************************/

// initalize variables needed for Authorize.net
$relayURL = "https://www.soccerone.com/anprocess.php"; // page to send customer to after CC info to Authoize.net
$url = "https://secure2.authorize.net/gateway/transact.dll";
$loginID = '2mWdR449Q'; 
$transactionKey = '759zzH8t2NHgMj4V';
$description = "Website Order";
$sequence = rand(1, 1000);
$timestamp = time();
$xtype = "AUTH_ONLY";
?>