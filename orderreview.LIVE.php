<?php
/******* USES AUTHORIZE.NET CODING *************/

/************************************
 * Order review screen before final 
 * placement of order in system     
 *                                              
 * Updated: 17 August 2016           
 * By: Richard Tuttle               
 ***********************************/

require_once 'cpadmin/includes/db.php';
session_start();
require_once 'includes/inc_calcVipprice.php';
require_once 'includes/CouponCalculation.php';

$cusTotalProduct = 0;
$submitted = 0;
$shipping = $_POST["shipping"];
$isvip = $_POST["isvip"];
$paymentMethod = $_POST['paymentmethod'];
$ot = $_POST["ordertotal"];
$td = $_POST['totaldiscount'];
$tt = $_POST['totaltax'];
$ts = $_POST['totalship'];
$ship = $_POST['shipping'];
$gt = $_POST['grandtotal'];
$notes = addslashes($_POST['orderNotes']);
$wt = $_POST['Weight'];
$submitted = $_POST['submitted'];
$gcNum = $_POST['gcNum'];
$gcTotal = $_POST['gctotal'];
$shipNotes1 = addslashes($_POST['ppmsg']);
$shipNotes2 = addslashes($_POST['dsmsg']);

if (isset($_SESSION["email"])) {
    if (isset($shipping)) {
        // choose pricing based on vip status
        if ($isvip == "yes") {
            $unitprice = "VIPPrice";
        } else {
            $unitprice = "Price";
        }

// create random GC number
function generateGCnum($length = 10) {
    $characters = "0123456789";
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}
      
if ($submitted == 1) {
    if (($gt < 0.01) || ($gt == 0.0)) {
        $date = date('m/d/Y h:i:s a');
        $mailTo = "richard@northwind.us";
        $mailSubject = "Zero Out error detected!";
        $mailHeader = 'MIME-Version: 1.0' . "\r\n";
        $mailHeader .= "Content-type: text/html; charset: utf8\r\n";
        $mailHeader .= 'X-Mailer: PHP/' . phpversion();
        $mailMsg ="A customer Zero Out error was just detected. Here are the details:<br><br><b><u>SESSION DUMP</u></b><br>";
        foreach ($_SESSION as $key=>$val) {
            $mailMsg .= $key . " " . $val . "<br>";
        }
        $mailMsg .= "<br><b><u>REQUEST DUMP</u></b><br>";
        foreach ($_REQUEST as $key=>$val) {
            $mailMsg .= $key . " " . $val . "<br>";
        }
        $mailMsg .= "<br><b><u>POST DUMP</u></b><br>";
        foreach ($_POST as $key=>$val) {
            $mailMsg .= $key . " " . $val . "<br>";
        }
        $mailMsg .= "<br><br>Date: " . $date . "<br>Order Total: $" . $ot . "<br>Tax: $" . $tt . "<br>Shipping: $" . $ts . "<br>Grand Total: $" . $gt . "<br>";
        mail($mailTo, $mailSubject, $mailMsg, $mailHeader, '-f richard@northwind.us');
    }

    if ($paymentMethod == "OpenAccount") {
        $sql_order = "INSERT INTO orders(EmailAddress, OrderDate, OrderStatus, OrderTotal, Discount, Tax, ShippingTotal, ShippingMethod, gcTotal, GrandTotal, CardType, OrderNotes, WeightTotal, referrer, shipnotes, ipAddr) VALUES('$_SESSION[email]', current_date, 'Pending', '$ot', '$td', '$tt', '$ts', '$ship', '$gcTotal', '$gt', 'OpenAccount', '$notes', '$wt', '$_SESSION[org_referrer]', '$shipNotes1 $shipNotes2', '$_SESSION[ip_address]')";
    } elseif ($paymentMethod == "gcOnly") {
        $sql_order = "INSERT INTO orders(EmailAddress, OrderDate, OrderStatus, OrderTotal, Discount, Tax, ShippingTotal, ShippingMethod, gcTotal, GrandTotal, CardType, OrderNotes, WeightTotal, referrer, shipnotes, ipAddr) VALUES('$_SESSION[email]', current_date, 'Pending', '$ot', '$td', '$tt', '$ts', '$ship', '$gcTotal', '$gt', 'Gift Certificate funds', '$notes', '$wt', '$_SESSION[org_referrer]', '$shipNotes1 $shipNotes2', '$_SESSION[ip_address]')";
    } else {
        echo "<h1>ERROR - no payment method selected! CANNOT CONTINUE!</h1>";
        exit();
    }

    if (!mysql_query($sql_order)) {
        echo "error saving order: " . mysql_error();
    } else {
        $orderid = mysql_insert_id();
        $sql_items = "SELECT * FROM shopping_cart WHERE (SessionID='".session_id()."' OR EmailAddress='$_SESSION[email]') AND (BundleID='' OR BundleID IS NULL)";
        $result_items = mysql_query($sql_items);
        while ($row_items = mysql_fetch_array($result_items)) {
            if ($row_items['Type'] == 'Coupon') {
                $row_items[$unitprice] = $td;
            }
            if ($row_items['Type'] == 'GC') {
                $row_items[$unitprice] = $row_items['Price']; // $gcTotal;
            }
            $row_items['GenderSKU'] = addslashes($row_items['GenderSKU']);
            $row_items['RootSKU'] = addslashes($row_items['RootSKU']);
            $row_items['SizeSKU'] = addslashes($row_items['SizeSKU']);
            $row_items['ColorSKU'] = addslashes($row_items['ColorSKU']);
            $row_items['ProductName'] = addslashes($row_items['ProductName']);

            // insert Ordered Items into the database
                if ($row_items["Type"] == "CouponUsed") {
                    $sql_additem = "INSERT INTO orders_items(OrderID, ProductID, ProductName, RootSKU, SizeSKU, ColorSKU, Qty, Gender, GenderSKU, Price, Type) VALUES($orderid, '$row_items[ProductID]', '$row_items[ProductName]', '$row_items[RootSKU]', '$row_items[SizeSKU]', '$row_items[ColorSKU]', '$row_items[Qty]', '".addslashes($row_items['Gender'])."', '$row_items[GenderSKU]', '$row_items[$unitprice]', 'Coupon')";
                } else {
                    $sql_additem = "INSERT INTO orders_items(OrderID, ProductID, ProductName, RootSKU, SizeSKU, ColorSKU, Qty, Gender, GenderSKU, Price, Type) VALUES($orderid, '$row_items[ProductID]', '$row_items[ProductName]', '$row_items[RootSKU]', '$row_items[SizeSKU]', '$row_items[ColorSKU]', '$row_items[Qty]', '".addslashes($row_items['Gender'])."', '$row_items[GenderSKU]', '$row_items[$unitprice]', '$row_items[Type]')";
                }
                mysql_query($sql_additem) or die("Orders Items Insertion Error: " . mysql_error());
                
                // did customer purchase a GC?
                $x = 1;
                while ($x <= $row_items['Qty']) {
                    $buyGC = $row_items["RootSKU"];
                    $value = $row_items['Price'];
                    $z = substr($buyGC, 0, 4);
                    $newGCnum = "GC-";
                    if ($z == "GIFT") {
                        $newGCnum .= generateGCnum();
                        // check for duplication GC number before insertion
                        $ckNum = mysql_query("SELECT DISTINCT codeNum FROM certificate WHERE certType='gift' AND codeNum='$newGCnum'");
                        if (mysql_num_rows($ckNum) > 0) {
                            $newGCnum = "GC-";
                            $newGCnum .= generateGCnum();
                        }
                        // add new GC to database   
                        $temp = $newGCnum + $value + generateGCnum();
                        $hashcode = md5($temp);
                        $sql_addGC = "INSERT INTO certificate(certType, codeNum, origValue, used, remainValue, hash, dateOriginated) VALUES('gift', '$newGCnum', '$value', 'no', '$value', '$hashcode', NOW())";
                        if (mysql_query($sql_addGC)) {
                            // create new GC PDF and send customer email about GC
                            $gcHeaders = "MIME-Version: 1.0\r\n";
                            $gcHeaders .= "From: SoccerOne Customer Service <customerservice@soccerone.com>\r\n"; 
                            $gcHeaders .= "Content-type: text/html; charset: utf8\r\n"; 
                            $gcHeaders .= "Reply-To: SoccerOne <customerservice@soccerone.com>\r\n";
                            $gcHeaders .= "X-Mailer: PHP/" . phpversion() . "\r\n";
                            $gcSubject = "SoccerOne Gift Certificate";
                            $gcMsg = "<h2>Gift Certificate Purchased</h2>To view and/or print your SoccerOne Gift Certificate, please click this link: <a href='https://www.soccerone.com/giftcert.php?h=$hashcode'>YOUR SOCCERONE GIFT CERTIFICATE IS READY!</a><br><br><br><i>------ Gift Certificate Details --------</i><br>Certificate Number: $newGCnum<br>Certificate Value: \$$value<br>Expiration Date: Never<br>----------------------------------------------";
                            // mail('richard@northwind.us', $gcSubject, $gcMsg, $gcHeaders, '-f customerservice@soccerone.com'); // send order email to developer for testing
                            mail($_SESSION['email'], $gcSubject, $gcMsg, $gcHeaders, '-f customerservice@soccerone.com'); // send order email to customer
                            $value = 0;
                            $newGCnum = 0;
                        } else {
                            die("New Certificate Insertion Error: " . mysql_error()); exit;
                        }
                    }
                    $x++;
                }
                
                // check for used GC and update that info in database
                if ($row_items["Type"] == "GC") {
                    // get orderUsedIDs from certificate column, if applicable
                    $ckCert = "SELECT DISTINCT * FROM certificate WHERE codeNum='$row_items[ProductID]' AND certType='gift'";
                    $resultCert = mysql_query($ckCert);
                    $resCert = mysql_fetch_assoc($resultCert);
                    if ($resCert["orderUsedID"] == '' || $resCert["orderUsedID"] == NULL) { 
                        $sql_certUpdate = "UPDATE certificate SET orderUsedID='$orderid'";
                    } else {
                        $sql_certUpdate = "UPDATE certificate SET orderUsedID=CONCAT_WS(',', orderUsedID, '$orderid')";
                    }
                        $currentTotal = $ot + $ship + $tt;
                        if ($gcTotal > $currentTotal) {
                            $remain = $gcTotal - $currentTotal;
                            $rmV = number_format($remain, 2);
                            $sql_certUpdate .= ", remainValue='$rmV'";
                        } else {
                            $sql_certUpdate .= ", remainValue='0', used='yes'";
                        }
                    $sql_certUpdate .= " WHERE codeNum='" . $row_items["ProductID"] . "'";
                    $result_certUpdate = mysql_query($sql_certUpdate);
                    if (!$result_certUpdate) { 
                        die("ERROR UPDATING CERTIFICATE FILE DATA: " . mysql_error()); 
                    }
                }
 
            $lastid = mysql_insert_id(); // get Order ID
            
            // if Bundle product insert into Order Items table
            if ($row_items["Type"] == "Bundle") {
                $sql_bitems = "SELECT * FROM shopping_cart WHERE BundleID=$row_items[id]";
                $result_bitems = mysql_query($sql_bitems);
                while ($row_bitems = mysql_fetch_array($result_bitems)) {
                    $sql_addbitem = "INSERT INTO orders_items(OrderID, ProductID, ProductName, RootSKU, SizeSKU, ColorSKU, Qty, Gender, `Type`, BundleID) VALUES($orderid, '$row_bitems[ProductID]', '$row_bitems[ProductName]', '$row_bitems[RootSKU]', '$row_bitems[SizeSKU]', '$row_bitems[ColorSKU]', '$row_bitems[Qty]', '$row_bitems[Gender]', 'Bundle', $lastid)";

                    if (!mysql_query($sql_addbitem)) {
                        echo $sql_addbitem."<br/>";
                        echo "error adding items: ".mysql_error();
                    }
                }
            }
                
            // check for single bundle items
            $sql_cksb = "SELECT * FROM shopping_cart_single WHERE singleid=$row_items[id]";
            $result_cksb = mysql_query($sql_cksb);
            while ($row_cksb = mysql_fetch_array($result_cksb)) {
                $sql_addsb = "INSERT INTO orders_items(OrderID, ProductID, ProductName, RootSKU, SizeSKU, ColorSKU, Qty, Gender, `Type`, BundleID) VALUES($orderid, '$row_cksb[ProductID]', '$row_cksb[ProductName]', '$row_cksb[RootSKU]', '$row_cksb[SizeSKU]', '$row_cksb[ColorSKU]', '$row_cksb[Qty]', '$row_cksb[Gender]', 'Single', $lastid)";
                    
                if (!mysql_query($sql_addsb)) {
                    echo "Error adding item: " . mysql_error();
                }
            }
                
            // update stock quantities
            if ($row_items["Type"] == "Product") {
                // check if stock is manageable
                $manageSQL = "SELECT ManagableStock FROM products WHERE id='$row_items[ProductID]' LIMIT 1";
                $manageResult = mysql_query($manageSQL) or die("Manage Stock Error: " . mysql_error());
                $manageChk = mysql_fetch_array($manageResult);
                if ($manageChk["ManagableStock"] == "Yes") {
                    $sql_updateqty = "UPDATE product_options SET Inventory=(Inventory-$row_items[Qty]) WHERE ProductID=$row_items[ProductID] AND ColorSKU='$row_items[ColorSKU]' AND SizeSKU='$row_items[SizeSKU]' LIMIT 1";
                    mysql_query($sql_updateqty) or die("Qty Update Error: " . mysql_error());
                    $sql_availQty = "UPDATE products SET AvailableQty = (SELECT SUM(Inventory) AS Stock FROM product_options WHERE ProductID=$row_items[ProductID]) WHERE id=$row_items[ProductID] LIMIT 1";
                    mysql_query($sql_availQty) or die("Availability Error: " . mysql_error());
                }
            }
                
            // SAVE IMPRINT TO ORDER
            $sql_imprint = "SELECT * FROM imprint_shopping_cart WHERE CartID=$row_items[id]";
            $result_imprint = mysql_query($sql_imprint);
            while ($row_imprint = mysql_fetch_array($result_imprint)) {
                $sql_addimp  = "INSERT INTO imprint_orders(EmailAddress, OrderNumber, OrderItemID, OrderDate, ProductID, ImprintPrice, Opt1Type, Opt1Image, Opt1Color, Opt1Loc, Opt1Text, Opt1Team, Opt2Type, Opt2Image, Opt2Color, Opt2Loc, Opt2Text, Opt2Team) VALUES('$_SESSION[email]', '$orderid', '$lastid', current_date, '$row_items[ProductID]', $row_imprint[ImprintPrice], '$row_imprint[Opt1Type]', '$row_imprint[Opt1Image]', '$row_imprint[Opt1Color]', '$row_imprint[Opt1Loc]', '$row_imprint[Opt1Text]', '$row_imprint[Opt1Team]', '$row_imprint[Opt2Type]', '$row_imprint[Opt2Image]', '$row_imprint[Opt2Color]', '$row_imprint[Opt2Loc]', '$row_imprint[Opt2Text]', '$row_imprint[Opt2Team]')";
                mysql_query($sql_addimp) or die("Imprint Save Error: " . mysql_error());
            }
            
            // REMOVE IMPRINT DATA FROM CART 
            $sql_remimp = "DELETE FROM imprint_shopping_cart WHERE CartID=$row_items[id]";
            mysql_query($sql_remimp) or die("Imprint Removal Error: " . mysql_error());
        } // end while
         
        // empty shopping cart after adding ordered items to the database
        $sql_removeitems = "DELETE FROM shopping_cart WHERE SessionID='".session_id()."' OR EmailAddress='$_SESSION[email]'";
        mysql_query($sql_removeitems) or die("Emptying Cart Error: " . mysql_error());

        // add ordering information to the Orders Address database
        $sql_address = "SELECT * FROM shopping_address WHERE SessionID='".session_id()."' LIMIT 1";
        $result_address = mysql_query($sql_address);
        $row_address = mysql_fetch_assoc($result_address);
        $sql_addaddress  = "INSERT INTO orders_address(OrderID, BillingFirstName, BillingLastName, BillingCompany, BillingEmailAddress, BillingAddress, BillingCity, BillingState, BillingZip, ShippingFirstName, ShippingLastName, ShippingCompany, ShippingEmailAddress, ShippingAddress, ShippingCity, ShippingState, ShippingZip) VALUES($orderid, '$row_address[BillingFirstName]', '$row_address[BillingLastName]', '$row_address[BillingCompany]', '$row_address[BillingEmailAddress]', '$row_address[BillingAddress]', '$row_address[BillingCity]', '$row_address[BillingState]', '$row_address[BillingZip]', '$row_address[ShippingFirstName]', '$row_address[ShippingLastName]', '$row_address[ShippingCompany]', '$row_address[ShippingEmailAddress]', '$row_address[ShippingAddress]', '$row_address[ShippingCity]', '$row_address[ShippingState]', '$row_address[ShippingZip]')";
        $customerName = $row_address["BillingFirstName"] . " " . $row_address["BillingLastName"];
        mysql_query($sql_addaddress);
        $sql_removeaddress = "DELETE FROM shopping_address WHERE SessionID='".session_id()."' OR EmailAddress='$_SESSION[email]'";
        mysql_query($sql_removeaddress);

        // is the customer a VIP?
        if($isvip == "yes") {
            $sql_cust = "SELECT * FROM customers WHERE EmailAddress='$_SESSION[email]' LIMIT 1";
            $result_cust = mysql_query($sql_cust);
            $row_cust = mysql_fetch_assoc($result_cust);

            // generate their VIP number
            if ($row_cust["Status"] != 'VIP') {
                $vipnumber = substr($row_cust["LastName"], 0, 4).substr($row_cust["BillingZip"], 0, 5)."-".substr($row_cust["Telephone"], -4);
                $todayDate = date("Y-m-d");
                $exDate = date("Y-m-d", strtotime('+1 year', strtotime($todayDate)));
                $sql_setvip = "UPDATE customers SET Status='VIP', VIPNum='$vipnumber', AccountNumber='$vipnumber', VIPLevel='1', VIPDate=current_date, VIPExpDate='$exDate' WHERE EmailAddress='$_SESSION[email]'";
                mysql_query($sql_setvip) or die("VIP Creation Error: " . mysql_error());
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
        }

        $_SESSION["orderid"] = $orderid;
        $sql_from = "SELECT EmailAddress FROM emails WHERE `Type`='salesorder' LIMIT 1";
        $result_from = mysql_query($sql_from);
        $row_from = mysql_fetch_assoc($result_from);
        
        // send order confirmation email
        $sql_message = "SELECT Message FROM messages WHERE `Type`='neworderconfirmation' LIMIT 1";
        $result_message = mysql_query($sql_message);
        $row_message = mysql_fetch_assoc($result_message);
        $ordermess = str_replace("{{ORDERNUMBER}}", $orderid, $row_message["Message"]);
        $orderItems = "SELECT * FROM orders_items WHERE OrderID=".$_SESSION["orderid"]." AND Type!='CouponUsed'";
        $rItems = mysql_query($orderItems);
        while ($row_items = mysql_fetch_array($rItems)) {
            $prodname1 = $row_items["ProductName"];
            $SKU1 = $row_items["RootSKU"] . "-" . $row_items["SizeSKU"] . "-" . $row_items["ColorSKU"];
            if ($row_items["GenderSKU"] != NULL) {
                $SKU1 .= "-" . $row_items["GenderSKU"];
            }
            $qty1 = $row_items["Qty"];
            if ($row_items["Type"] == "GC") {
                $gcNumber1 = $row_items["ProductID"];
            } else {
                $s1msg1 .= $qty1 . " of " . $prodname1 . " (" . $SKU1 . ")<br>";
            }
        }
        $ordermess .= "<p><b>YOU ORDERED THE FOLLOWING TODAY:</b><br>" . $s1msg1 . "</p>";
        
        /***********************************************
         * email variables for editing, as needed      *
         ***********************************************/
        $toCS = "customerservice@soccerone.com";
        $toDeveloper = "richard@northwind.us";
        $subject = "SoccerOne Order Confirmation #" . $_SESSION["orderid"];
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "From: SoccerOne Customer Service <customerservice@soccerone.com>\r\n"; 
        $headers .= "Content-type: text/html; charset: utf8\r\n"; 
        $headers .= "Reply-To: SoccerOne <customerservice@soccerone.com>\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
        /************************************************/
        
        // send VIP information email
        $sql_vipnumber = "SELECT Status, VIPNum, VIPDate, VIPLevel FROM customers WHERE EmailAddress='".$_SESSION['email']."' LIMIT 1";
        $result_vipnumber = mysql_query($sql_vipnumber);
        $row_vipnumber = @mysql_fetch_assoc($result_vipnumber);
        $cusvipnumber = isset($row_vipnumber["VIPNum"]) ? 'VIP Member: '.$row_vipnumber["VIPNum"]: '';
        $ordermess = str_replace("{{VIPNUMBER}}", strtoupper($cusvipnumber), $ordermess);
    
        $s1msg = "A new customer order has been received on the website from " . $customerName . " (" .$_SESSION['email'].").<br><br>------------------------------<br>";
        if ($notes) {
            $s1msg .= "<u>ORDER NOTES</u><br>" . $notes . "<br><br>";
        }
        $s1msg .= "<u>ORDERED ITEMS</u><br>";
        $sqlitems = "SELECT * FROM orders_items WHERE OrderID=".$_SESSION["orderid"]." AND Type!='CouponUsed'";
        $resultitems = mysql_query($sqlitems);
        $gc = "no";
        while ($rowitems = mysql_fetch_array($resultitems)) {
            $prodname = $rowitems["ProductName"];
            $SKU = $rowitems["RootSKU"] . "-" . $rowitems["SizeSKU"] . "-" . $rowitems["ColorSKU"];
            if ($rowitems["GenderSKU"] != NULL) {
                $SKU .= "-" . $rowitems["GenderSKU"];
            }
            $qty = $rowitems["Qty"];
            if ($rowitems["Type"] == "GC") {
                $gc = "yes";
                $gcNumber = $rowitems["ProductID"];
            } else {
                $s1msg .= $qty . " of " . $prodname . " (" . $SKU . ")<br>";
            }
        }
        $s1msg .= "------------------------------<br><br><u>ORDER INFORMATION</u><br />Order total: \$" . number_format($ot, 2);
        if ($td != 0)
            $s1msg .= "<br>Discount applied: \$" . number_format($td, 2);
        if ($tt >= 0.01)
            $s1msg .= "<br>Total tax: \$" . number_format($tt, 2);
        if ($ts >= 0.01)
            $s1msg .= "<br>Total shipping: \$" . number_format($ts, 2) . " via " . $ship;
        if ($gcTotal > 0)
            $s1msg .= "<br>Total Gift Certificate payment: \$" . number_format($gcTotal, 2);
        $s1msg .= "<br>Grand total charged: \$" . number_format($gt, 2);
        $s1msg .= "<br><br><u>PAYMENT INFORMATION</u>"; 
        if (($paymentMethod != "OpenAccount") && ($paymentMethod != "gcOnly")) {
            $s1msg .= "<br>" . $ct . ": " . $cardNum . "<br />CCV: " . $sc;
        } 
        if ($paymentMethod == "gcOnly") {
            $s1msg .= "<br>Paid using Gift Certificate - (" . $gcNumber . ")";
        } 
        if (($gc == "yes") && ($paymentMethod != "gcOnly")) {
            $s1msg .= "<br>Paid using Gift Certificate - (" . $gcNumber . ")";
        } 
        if ($paymentMethod == "OpenAccount") {
            $s1msg .= "<br>Paid using Open Account settings<br>";
        }
        $s1msg .= "<br><br>----------- SHIPMENT NOTES ---------------<br>" . $_POST['ppmsg'] . "<br>" . $_POST['dsmsg'];
        $s1msg .= "<br>------------------------------------------<br><br>Please logon to the Administration Portal at your earliest convenience to see new order " . $_SESSION["orderid"] . "'s details.<br><br>The user logged in from IP Address: " . $_SERVER['REMOTE_ADDR'];
        
        // send emails to appropriate people based upon status of site utlizing
        $sql_status = "SELECT id FROM status WHERE current='yes' LIMIT 1";
        $result_status = mysql_query($sql_status);
        $row_status = mysql_fetch_assoc($result_status);
        if ($row_status['id'] == "2") {
            $s1sub = "TESTING :: New Order Received - order #" . $_SESSION["orderid"];
            mail($toDeveloper, $s1sub, $s1msg, $headers); 
            mail($toDeveloper, $subject, $ordermess, $headers, '-f customerservice@soccerone.com');
        } elseif ($row_status['id'] == "1") {
            $s1sub = "New Customer Order Received - order #" . $_SESSION["orderid"];
            mail($toCS, $s1sub, $s1msg, $headers); 
            mail($_SESSION['email'], $subject, $ordermess, $headers, '-f customerservice@soccerone.com');
        } else {
            echo "SORRY BUT AN ERROR HAS OCCURED!!  No site status indicated!"; exit();
        }
        
        // go to final order screen
        header("location:orderfinal.php");
        }
      }
    } // end submit check
} else {
    // customer needs to login first
    header("location:myaccount.php");
}

$isvip = "no";
$pricename = "Price";

// begin actual page display
$pgTitle = "Order Review | Shopping Cart | SoccerOne";
include_once("includes/mainHeader.php");

/*************************************************/                             
if ($submitted != 1) {
    // instantiate the progress bar while shipment rates are garnered
    require_once("object.php");
    $po = new ProgressObj();
    $po->text = "Calculating your shipping rates....";
    
    // check if qualifies for VIP pricing
    if ($_SESSION["email"] != '') {
        $sql_status = "SELECT status FROM customers WHERE status='VIP' AND EmailAddress='$_SESSION[email]' AND VIPExpDate >= current_date()";
        $result_status = mysql_query($sql_status) or die("Customer Status Error: " . mysql_error());
        $status_row = mysql_fetch_array($result_status);
        $num_status = mysql_num_rows($result_status);
        if ($status_row["Status"] = '') {
            $status_update = "UPDATE customers SET status='NonMember' WHERE EmailAddress='$_SESSION[email]'";
            $update_result = mysql_query($status_update) or die("Status Update Error: " . mysql_error());
        }
        if ($num_status > 0) {
            $isvip = "yes";
            $pricename = "VIPPrice";
            $sql_remvip = "DELETE FROM shopping_cart WHERE ProductID='VIP' AND (EmailAddress='$_SESSION[email]' OR SessionID='" . session_id() . "')";
            mysql_query($sql_remvip);
        } 
    }

    // check for special gold certificate usage
    $couponCk = "SELECT * FROM shopping_cart WHERE Type='Cert' LIMIT 1";
    $result_couponCk = mysql_query($couponCk) or die("Gold Certificate check error: " . mysql_error());
    $num_couponCk = mysql_num_rows($result_couponCk);
    if ($num_couponCk > 0) {
        $isvip = "yes";
        $pricename = "VIPPrice";
    }
    
    // check if ordering a gift certificate and nothing else - if so give free shipping
    $gcCheck = "SELECT * FROM shopping_cart WHERE Type='Product' AND (EmailAddress='" . $_SESSION['email'] . "' OR SessionID='" . session_id() . "')";
    $result_gcCheck = mysql_query($gcCheck) or die("Gift Certificate Check error: " . mysql_error());
    $num_gcCheck = mysql_num_rows($result_gcCheck);
    $row_gcCheck = mysql_fetch_array($result_gcCheck);
    if ($num_gcCheck == 1) {
    	if (substr($row_gcCheck['RootSKU'], 0, 4) === "GIFT") {
    		$vipShip = "yes";
    	} 
    } elseif ($num_gcCheck >= 2) {
    	if (substr($row_gcCheck['RootSKU'], 0, 4) === "GIFT") {
    		$vipShip = "yes";
    	} else {
    		$vipShip = "no";
    	}
    }

    // give regular, nonVIP pricing
    if ($isvip == "no") {
        $sql_chkcart = "SELECT * FROM shopping_cart WHERE ProductID='VIP' AND (SessionID='".session_id()."' OR EmailAddress='$_SESSION[email]')";
        $result_chkcart = mysql_query($sql_chkcart);
        $num_chkcart = mysql_num_rows($result_chkcart);
        if ($num_chkcart > 0) {
            $isvip = "yes";
            $pricename = "VIPPrice";
            $ckVIPsql = "SELECT * FROM shopping_cart WHERE (SessionID='".session_id()."' OR EmailAddress='$_SESSION[email]')";
            $ckResult = mysql_query($ckVIPsql);
            $ckNum = mysql_num_rows($ckResult);
            $rowCk = mysql_fetch_array($ckResult);
            // gives free shipping when ordering VIP product only
            if ($ckNum == 1) {
            	$vipShip = "yes";
            }
            // gives free shipping when ordering VIP and a GiftCert only together
            if ($ckNum == 2) {
            	if (substr($rowCk['RootSKU'], 0, 4) === "GIFT") {
            		$vipShip = "yes";
            	}
            }
        }
    }

    if($_SESSION["email"] == '') {
        $sqlwhere = "SessionID='".session_id()."'";
    } else {
        $sqlwhere = "(EmailAddress='$_SESSION[email]' OR SessionID='".session_id()."') ";
    }

    // ** SHIPPING ** 
    // check for free shipping coupon
    $sql_scoupon = "SELECT s.id, c.ApplyOption, c.Amount, c.ShippingOption, c.Method, c.MinimumOrder FROM shopping_cart s, coupons c WHERE s.ProductID=c.Code AND s.Type='Coupon' AND c.ApplyTo='Shipping' AND s.SessionID='".session_id()."' LIMIT 1";
    $result_scoupon = mysql_query($sql_scoupon) or die("Coupon Check Error: " .mysql_error());
    $row_scoupon = mysql_fetch_assoc($result_scoupon);
    $freeShipItemList = array();

    // display the progress bar
    $po->DisplayMeter();

    /** Free Shipping Check **/ 
    if (!isset($row_scoupon["Method"]) || $row_scoupon["Method"] != 'free') {
        require_once("includes/upsRate.php"); // get UPS shipping rates
        require_once("includes/uspsRate.php"); // get USPS shipping rates
        $sql_ups = "SELECT * FROM shipping WHERE `Type`='UPS' LIMIT 1";
        $result_ups = mysql_query($sql_ups) or die("UPS Error: " . mysql_error());
        $row_ups = mysql_fetch_assoc($result_ups);
        $ups_accessnumber = $row_ups["AccessKey"];
        $ups_username = $row_ups["UserName"];
        $ups_password = $row_ups["Password"];
        $ups_shippernumber = $row_ups["AccountNumber"];
        $MAX_BOX_WEIGHT = $row_ups["boxWeight"];
        $myRate = new upsRate;
        $poRate = new uspsRate;
        $myRate->setCredentials($ups_accessnumber, $ups_username, $ups_password, $ups_shippernumber);
        $ship_ground = 0;
        $ship_1stDay = 0;
        $ship_2ndDay = 0;
        $ship_3Day = 0;
        $totalWeight = 0;
        $numShipItems = 0;

        // primary location zipcode
        $sql_shipfrom = "SELECT * FROM company LIMIT 1";
        $result_shipfrom = mysql_query($sql_shipfrom) or die("Ship From Error: " . mysql_error());
        $row_shipfrom = mysql_fetch_assoc($result_shipfrom);
        $primaryzip = $row_shipfrom["Zip"];
        $primarystate = $row_shipfrom["State"];

        // shipto zipcode
        $sql_shipto = "SELECT * FROM shopping_address WHERE SessionID='".session_id()."' LIMIT 1";
        $result_shipto = mysql_query($sql_shipto);
        $row_shipto = mysql_fetch_assoc($result_shipto);
        $shipto = $row_shipto["ShippingZip"];
        $shiptostate = $row_shipto["ShippingState"];
        $minorder = 'yes';

        if ($row_scoupon["MinimumOrder"] > 0) {
            // calculate order total for minimum order amount
            $sql_orderTotal = "SELECT SUM($pricename * Qty) AS TotalOrder FROM shopping_cart WHERE $sqlwhere AND (`Type`='Product' OR `Type`='Bundle') AND (BundleID='' OR BundleID IS NULL)";
            $result_orderTotal = mysql_query($sql_orderTotal);
            $row_orderTotal = mysql_fetch_assoc($result_orderTotal);
            if ($row_orderTotal["TotalOrder"] < $row_scoupon["MinimumOrder"]) {
                $minorder = 'no';
            }
        }

        $freeship = 'no';
        if ($row_scoupon["ApplyOption"] == "Entire Order") {
            $freeship = 'yes';
        }

        $shipmeth = '';
        if ($row_scoupon["Method"] != '') {
            $shipmeth = $row_scoupon["Method"];
        }

        $freeshipitem = '';
        if ($row_scoupon["ApplyOption"] == "Item") {
            $freeshipitem = $row_scoupon["ShippingOption"];
        }

        $arrfreeshipcats = '';
        if ($row_scoupon["ApplyOption"] == "Category") {
            $arrfreeshipcats = str_replace("|",",", $row_scoupon["ShippingOption"]);
        }

        if ($minorder == 'no') {
            $freeship = 'no';
            $shipmeth = '';
            $freeshipitem = '';
            $arrfreeshipcats = '';
        }

    // check if ordering just a giftCertificate
    $gcCk = "SELECT * FROM shopping_cart WHERE RootSKU LIKE 'GC-%' AND (SessionID='".session_id()."' OR EmailAddress='$_SESSION[email]')";
    $result_gcCk = mysql_query($gcCk);
    $num_gcCk = mysql_num_rows($result_gcCk);
    if ($num_gcCk > 0) {
        $gcCk2 = "SELECT * FROM shopping_cart WHERE Type='Product' AND (SessionID='".session_id()."' OR EmailAddress='$_SESSION[email]')";
        $result_gcCk2 = mysql_query($gcCk2);
        $num_gcCk2 = mysql_num_rows($result_gcCk2);
        if ($num_gcCk2 === 1) {
            $vipShip = "yes";
        }
    } 

        // get only products from cart
        $sql_shipitems = "SELECT ProductID, RootSKU, Qty FROM shopping_cart WHERE (SessionID='".session_id()."' OR EmailAddress='$_SESSION[email]') AND (`Type`='Product' OR `Type`='Bundle') AND (BundleID='' OR BundleID IS NULL)";
        $result_shipitems = mysql_query($sql_shipitems);
        $numShipItems = mysql_num_rows($result_shipitems);
        $i = 1;
        $dropground_ttl = 0;
        $drop1Day_ttl = 0;
        $drop2Day_ttl = 0;
        $drop3Day_ttl = 0;
        $shipground_ttl = 0;
        $ship1Day_ttl = 0;
        $ship2Day_ttl = 0;
        $ship3Day_ttl = 0;
        $shipPO_ttl = 0;
        $handlefee = 0;
        $Length = 0;
        $Width = 0;
        $Height = 0;
        $callCount = 0;
        $ship_only_ground = 0;
        $DS = 0;
        $DSI = 0;
        $count = 1;
        $dsArray = FALSE;
        $dsArrayCount = 0;

        // progress bar details
        $barCount = $numShipItems;
        $po->Calculate($barCount);

        // check to be sure max box weight is specified
        if (!is_numeric($MAX_BOX_WEIGHT) || $MAX_BOX_WEIGHT <= 0) {
            $MAX_BOX_WEIGHT = 25; // default value if not defined in database
        }
        
        // while loop to determine shipping costs
        while ($row_shipitems = mysql_fetch_array($result_shipitems)) { 
            // initialize shipping weight variables
            $shipground_wt = 0;
            $shipground_on = 0;
            $ship1Day_wt = 0;
            $ship1Day_on = 0;
            $ship2Day_wt = 0;
            $ship2Day_on = 0;
            $ship3Day_wt = 0;
            $ship3Day_on = 0;
            $dropground_pkgs = 0;
            $dropVendor = 0;

            // get product shipping information from database
            $sql_ship = "SELECT * FROM product_shipping WHERE ProductID=$row_shipitems[ProductID] LIMIT 1";
            $result_ship = mysql_query($sql_ship) or die("Shipping Error: " . mysql_error());
            $row_ship = array();
            if (@mysql_num_rows($result_ship)) {
                $row_ship = mysql_fetch_assoc($result_ship);
            }
            
            // elegible for free shipping?
            if ($row_ship && $row_ship["EligibleFreeShipping"] == 'yes') { 
                $efs = 'yes';
            } else { 
                $efs = 'no';
            }
            $freeshipcats = 'no';
            if ($row_scoupon["ApplyOption"] == "Category" && $arrfreeshipcats != '') {
                $sql_itemcategory = "SELECT CategoryID, ProductID FROM category_items WHERE ProductID=$row_shipitems[ProductID] AND CategoryID IN($arrfreeshipcats)";
                $result_itemcategory = mysql_query($sql_itemcategory);
                $num_itemcategory = @mysql_num_rows($result_itemcategory);
                if ($num_itemcategory > 0) {
                    $freeShipItemList[$row_shipitems['ProductID']] = 1;
                    $freeshipcats = 'yes';
                } else {
                    $freeshipcats = 'no';
                }
            }
            if ($freeshipitem != $row_shipitems["RootSKU"] && $row_scoupon["ApplyOption"] == "Item") {
                $freeShipItemList[$row_shipitems['ProductID']] = 1;
            }
            
            // obtain shipment poundage weight of item
            if ($row_ship["Weight"] == '') {
                $itemweight = 0;
            } else {
                $itemweight = $row_ship["Weight"];
            }
            $weight = $itemweight * $row_shipitems["Qty"];
            
            // obtain shipment ounces measurement
            if ($row_ship["Ounces"] == '') {
                $itemounces = 0;
            } else {
                $itemounces = $row_ship["Ounces"];
            }
            $ounces = $itemounces * $row_shipitems["Qty"];

            if ($row_ship["ShippingType"] == "PrimaryLocation" && $row_ship["UPS"] == "CustomerChoose") {
                if ($freeshipitem != $row_shipitems["RootSKU"] && $freeshipcats=='no' && $freeship == 'no') {
                    $ship1Day_wt += $weight;
                    $ship1Day_on += $ounces;
                } else {
                    if ($shipmeth != '1Day' && $shipmeth != 'all') {
                        $ship1Day_wt += $weight;
                        $ship1Day_on += $ounces;
                    } else {
                        if ($efs == 'no') {
                            $ship1Day_wt += $weight;
                            $ship1Day_on += $ounces;
                        }
                    }
                }

                if ($freeshipitem != $row_shipitems["RootSKU"] && $freeshipcats=='no' && $freeship == 'no') {
                    $ship2Day_wt += $weight;
                    $ship2Day_on += $ounces;
                } else {
                    if ($shipmeth != '2Day' && $shipmeth != 'all') {
                        $ship2Day_wt += $weight;
                        $ship2Day_on += $ounces;
                    } else {
                        if($efs == 'no') {
                            $ship2Day_wt += $weight;
                            $ship2Day_on += $ounces;
                        }
                    }
                }

                if ($freeshipitem != $row_shipitems["RootSKU"] && $freeshipcats=='no' && $freeship == 'no') {
                    $ship3Day_wt += $weight;
                    $ship3Day_on += $ounces;
                } else {
                    if ($shipmeth != '3Day' && $shipmeth != 'all') {
                        $ship3Day_wt += $weight;
                        $ship3Day_on += $ounces;
                    } else {
                        if($efs == 'no') {
                            $ship3Day_wt += $weight;
                            $ship3Day_on += $ounces;
                        }
                    }
                }

                if ($freeshipitem != $row_shipitems["RootSKU"] && $freeshipcats=='no' && $freeship == 'no') {
                    $shipground_wt += $weight;
                    $shipground_on += $ounces;
                } else {
                    if ($shipmeth != 'ground' && $shipmeth != 'all') {
                        $shipground_wt += $weight;
                        $shipground_on += $ounces;
                    } else {
                        if($efs == 'no') {
                            $shipground_wt += $weight;
                            $shipground_on += $ounces;
                        }
                    }
                }
                // $handlefee += $row_ship["HandlingFee"];
            } elseif ($row_ship["ShippingType"] == "PrimaryLocation" && $row_ship["UPS"] == "SpecificShipping") {
                switch($row_ship["SpecificOption"]) {
                    case "Ground":
                        if ($freeshipitem != $row_shipitems["RootSKU"] && $freeshipcats=='no' && $freeship == 'no') {
                            $shipground_wt += $weight;
                            $shipground_on += $ounces;
                            $shipground_temp = $shipground_temp + $weight;
                        } else {
                            if($shipmeth != 'ground' && $shipmeth != 'all') {
                                $shipground_wt += $weight;
                                $shipground_on += $ounces;
                                $shipground_temp = $shipground_temp + $weight;
                            } else {
                                if($efs == 'no') {
                                    $shipground_wt += $weight;
                                    $shipground_on += $ounces;
                                    $shipground_temp = $shipground_temp + $weight;                  
                                }
                            }
                        }
                        break;
                    case "1stDay":
                        if ($freeshipitem != $row_shipitems["RootSKU"] && $freeshipcats=='no' && $freeship == 'no') {
                            $ship1Day_wt += $weight;
                            $ship1Day_on += $ounces;
                            $ship1Day_temp = $ship1Day_temp + $weight;
                        } else {
                            if($shipmeth != '1Day' && $shipmeth != 'all') {
                                $ship1Day_wt += $weight;
                                $ship1Day_on += $ounces;
                                $ship1Day_temp = $ship1Day_temp + $weight;
                            } else {
                                if($efs == 'no') {
                                    $ship1Day_wt += $weight;
                                    $ship1Day_on += $ounces;
                                    $ship1Day_temp = $ship1Day_temp + $weight;                  
                                }
                            }
                        }
                        break;
                    case "2ndDay":
                        if ($freeshipitem != $row_shipitems["RootSKU"] && $freeshipcats=='no' && $freeship == 'no') {
                            $ship2Day_wt += $weight;
                            $ship2Day_on += $ounces;
                            $ship2Day_temp = $ship2Day_temp + $weight;
                        } else {
                            if($shipmeth != '2Day' && $shipmeth != 'all') {
                                $ship2Day_wt += $weight;
                                $ship2Day_on += $ounces;
                                $ship2Day_temp = $ship2Day_temp + $weight;  
                            } else {
                                if($efs == 'no') {
                                    $ship2Day_wt += $weight;
                                    $ship2Day_on += $ounces;
                                    $ship2Day_temp = $ship2Day_temp + $weight;                  
                                }
                            }
                        }
                        break;
                    case "3Day":
                        if ($freeshipitem != $row_shipitems["RootSKU"] && $freeshipcats=='no' && $freeship == 'no') {
                            $ship3Day_wt += $weight;
                            $ship3Day_on += $ounces;
                            $ship3Day_temp = $ship3Day_temp + $weight;
                        } else {
                            if ($shipmeth != '3Day' && $shipmeth != 'all') {
                                $ship3Day_wt += $weight;
                                $ship3Day_on += $ounces;
                                $ship3Day_temp = $ship3Day_temp + $weight;
                            } else {
                                if ($efs == 'no') {
                                    $ship3Day_wt += $weight;
                                    $ship3Day_on += $ounces;
                                    $ship3Day_temp = $ship3Day_temp + $weight;
                                }
                            }
                        }
                        break;
                    case "Dropship":
                        $dropship = 0;
                        break;
                }        
            // *** Drop Shipping *** //
            } elseif ($row_ship["ShippingType"] == "Dropship") {
                $DS = 1;
                $DSI = 1;
                $dropVendor = 1;
                $dropweight = $weight + ($ounces / 16);
                if ($freeshipitem != $row_shipitems["RootSKU"] && $freeshipcats=='no' && $freeship == 'no') {
                    $drop1Day_wt = $dropweight;
                } else {
                    if ($shipmeth != '1Day' && $shipmeth != 'all') {
                        $drop1Day_wt = $dropweight;
                    } else {
                        if ($efs == 'no') {
                            $drop1Day_wt = $dropweight;
                        }
                    }
                }

                if ($freeshipitem != $row_shipitems["RootSKU"] && $freeshipcats=='no' && $freeship == 'no') {
                    $drop2Day_wt = $dropweight;
                } else {
                    if ($shipmeth != '2Day' && $shipmeth != 'all') {
                        $drop2Day_wt = $dropweight;
                    } else {
                        if($efs == 'no') {
                            $drop2Day_wt = $dropweight;
                        }
                    }
                }

                if ($freeshipitem != $row_shipitems["RootSKU"] && $freeshipcats=='no' && $freeship == 'no') {
                    $drop3Day_wt = $dropweight;
                } else {
                    if ($shipmeth != '3Day' && $shipmeth != 'all') {
                        $drop3Day_wt = $dropweight;
                    } else {
                        if($efs == 'no') {
                            $drop3Day_wt = $dropweight;
                        }
                    }
                }

                if ($freeshipitem != $row_shipitems["RootSKU"] && $freeshipcats=='no' && $freeship == 'no') {
                    $dropground_wt = $dropweight;
                } else {
                    if ($shipmeth != 'ground' && $shipmeth != 'all') {
                        $dropground_wt = $dropweight;
                    } else {
                        if($efs == 'no') {
                            $dropground_wt = $dropweight;
                        }
                    }
                }

                // get item dimensions
                $DropShip_Lenght = $row_ship["Lenght"];
                $DropShip_Width = $row_ship["Width"];
                $DropShip_Height = $row_ship["Height"];
        
                // GET VENDOR ZIPCODE   
                $DropShip_vendorid = $row_ship["DropShip"];
                $sql_vendors = "SELECT State, Zipcode FROM vendors WHERE id='".$DropShip_vendorid."' LIMIT 1";
                $result_vendors = mysql_query($sql_vendors) or die("ERROR: vendors fetch");
                $row_vendors = mysql_fetch_assoc($result_vendors);
                $vendors_Zipcode = $row_vendors["Zipcode"];
                $vendors_State = $row_vendors["State"];
                
                // dropship ground rate calculations
                if ($dropVendor == 1 && $DropShip_Width == 0) {
                    if ($dropground_wt > $MAX_BOX_WEIGHT) { 
                        $dropground_pkgs = ceil($dropground_wt / $MAX_BOX_WEIGHT); 
                        $dropground_pkgw = $dropground_wt / $dropground_pkgs;
                        $dropground_rte = $myRate->getRate($vendors_Zipcode, $shipto, "03", 0, 0, 0, $dropground_pkgw, $shiptostate, $vendors_State);
                        $dropground_ttl += $dropground_rte * $dropground_pkgs;
                        $DSI = 1;
                        $totalWeight += $dropground_wt;
                    } else {
                    	if (isset($dropG)) {
                    		if (array_key_exists($vendors_Zipcode, $dropG)) {
                    			$dropG[$vendors_Zipcode]["wt"] += $dropground_wt;
                    		}
                    	} else {
                        	$dropG[$vendors_Zipcode]["wt"] = $dropground_wt; 
                        	$dropG[$vendors_Zipcode]["st"] = $vendors_State;
                      	}
                        $DSI = 1;
                        $dsArray = TRUE;
                        $dsArrayCount++;
                    } 
                } elseif ($dropVendor == 1 && $DropShip_Width > 0) {
                    $dropground_pkgs = $row_shipitems["Qty"];
                    $dropground_pkgw = $dropground_wt; 
                    $dropground_rte = $myRate->getRate($vendors_Zipcode, $shipto, "03", $DropShip_Lenght, $DropShip_Width, $DropShip_Height, $dropground_pkgw, $shiptostate, $vendors_State);
                    $dropground_ttl += $dropground_rte * $dropground_pkgs;
                    $DSI = 1;
                    $totalWeight += $dropground_wt;
                }
                if ($count >= $numShipItems || $i == $numShipItems) {  
                    if ($dsArray = TRUE) {
                        foreach ($dropG as $obj_key => $vzipVal) {
                            $dValG = 0;
                            foreach ($vzipVal as $k => $vWt) {
                                $dValG += $vWt;
                            }
                            if ($dValG != 0 && ($dValG > $MAX_BOX_WEIGHT)) {
                                $dropground_pkgs = ceil($dValG / $MAX_BOX_WEIGHT);  
                                $dropground_pkgw = $dValG / $dropground_pkgs; 
                                $dropground_rte = $myRate->getRate($obj_key, $shipto, "03", $DropShip_Lenght, $DropShip_Width, $DropShip_Height, $dropground_pkgw, $shiptostate, $dropG[$obj_key]["st"]);
                                $dsMsg .= "Dropship Packages - Weight (ea): " . $dropground_pkgs . " - " . $dropground_pkgw . "lbs<br>";
                                $dropground_ttl += $dropground_rte * $dropground_pkgs;
                            } else { 
                                $dropground_pkgw = $dValG;
                                $dropground_rte = $myRate->getRate($obj_key, $shipto, "03", $DropShip_Lenght, $DropShip_Width, $DropShip_Height, $dropground_pkgw, $shiptostate, $dropG[$obj_key]["st"]);
                                $dsMsg .= "An additional Dropshop Package from " . $obj_key . "<br>";
                                $dropground_ttl += $dropground_rte;
                            }
                        }
                    $dsArrayCount = 0;
                    }
                $totalWeight += $dValG;
                $DSI = 1;
                } // end pick-pack array
                if ($row_ship["HandlingFee"] > 0) {
                    $dropground_ttl += ($row_ship["HandlingFee"] * $row_shipitems["Qty"]);
                }

                // drop ship overnight rate calculations
                if ($dropVendor == 1 && $DropShip_Width == 0) { 
                    if ($drop1Day_wt > $MAX_BOX_WEIGHT) {
                        $drop1Day_pkgs = ceil($drop1Day_wt / $MAX_BOX_WEIGHT);
                        $drop1Day_pkgw = $drop1Day_wt / $drop1Day_pkgs;
                        $drop1Day_rte = $myRate->getRate($vendors_Zipcode, $shipto, "01", 0, 0, 0, $drop1Day_pkgw, $shiptostate, $vendors_State);
                        $drop1Day_ttl += $drop1Day_rte * $drop1Day_pkgs;
                        $DSI = 1;
                    } else {
                    	if (isset($drop1D)) {
                    		if (array_key_exists($vendors_Zipcode, $drop1D)) {
                    			$drop1D[$vendors_Zipcode]["wt"] += $drop1Day_wt;
                    		}
                    	} else {
                        	$drop1D[$vendors_Zipcode]["wt"] = $drop1Day_wt * $row_shipitems["Qty"];
                        	$drop1D[$vendors_Zipcode]["st"] = $vendors_State;
                        }
                        $DSI = 1;
                        $dsArray = TRUE;
                    }
                } elseif ($dropVendor == 1 && $DropShip_Width > 0) {
                    $drop1Day_pkgs = $row_shipitems["Qty"];
                    $drop1Day_pkgw = $drop1Day_wt;
                    $drop1Day_rte = $myRate->getRate($vendors_Zipcode, $shipto, "01", 0, 0, 0, $drop1Day_pkgw, $shiptostate, $vendors_State);
                    $drop1Day_ttl += $drop1Day_rte * $drop1Day_pkgs;
                    $DSI = 1;
                }
                if ($count >= $numShipItems || $i == $numShipItems) {
                    if ($dsArray = TRUE) {
                        foreach ($drop1D as $obj_key => $vzipVal) {
                            $dVal1D = 0;
                            foreach ($vzipVal as $k => $vWt) {
                                $dVal1D += $vWt;
                            }
                            if ($dVal1D != 0 && ($dVal1D > $MAX_BOX_WEIGHT)) {
                                $drop1Day_pkgs = ceil($dVal1D / $MAX_BOX_WEIGHT);
                                $drop1Day_pkgw = $dVal1D / $drop1Day_pkgs;
                                $drop1Day_rte = $myRate->getRate($obj_key, $shipto, "01", 0, 0, 0, $drop1Day_pkgw, $shiptostate, $drop1D[$obj_key]["st"]);
                                $drop1Day_ttl += $drop1Day_rte * $drop1Day_pkgs;
                            } else {
                                $drop1Day_pkgw = $dVal1D;
                                $drop1Day_rte = $myRate->getRate($obj_key, $shipto, "01", 0, 0, 0, $drop1Day_pkgw, $shiptostate, $drop1D[$obj_key]["st"]);
                                $drop1Day_ttl += $drop1Day_rte;
                            }
                        }
                    }
                    $DSI = 1;
                }
                if ($row_ship["HandlingFee"] > 0) {
                    $drop1Day_ttl += ($row_ship["HandlingFee"] * $row_shipitems["Qty"]);
                }
                
                // drop ship 2-day rate calaculations
                if ($dropVendor == 1 && $DropShip_Width == 0) { 
                    if ($drop2Day_wt > $MAX_BOX_WEIGHT) {
                        $drop2Day_pkgs = ceil($drop2Day_wt / $MAX_BOX_WEIGHT);
                        $drop2Day_pkgw = $drop2Day_wt / $drop2Day_pkgs;
                        $drop2Day_rte = $myRate->getRate($vendors_Zipcode, $shipto, "02", 0, 0, 0, $drop2Day_pkgw, $shiptostate, $vendors_State);
                        $drop2Day_ttl += $drop2Day_rte * $drop2Day_pkgs;
                        $DSI = 1;
                    } else {
                   		if (isset($drop2D)) {
                    		if (array_key_exists($vendors_Zipcode, $drop2D)) {
                    			$drop2D[$vendors_Zipcode]["wt"] += $drop2Day_wt;
                    		}
                    	} else {
                        	$drop2D[$vendors_Zipcode]["wt"] = $drop2Day_wt * $row_shipitems["Qty"];
                        	$drop2D[$vendors_Zipcode]["st"] = $vendors_State;
                        }
                        $DSI = 1;
                        $dsArray = TRUE;
                    }
                } elseif ($dropVendor == 1 && $DropShip_Width > 0) {
                    $drop2Day_pkgs = $row_shipitems["Qty"];
                    $drop2Day_pkgw = $drop2Day_wt;
                    $drop2Day_rte = $myRate->getRate($vendors_Zipcode, $shipto, "02", 0, 0, 0, $drop2Day_pkgw, $shiptostate, $vendors_State);
                    $drop2Day_ttl += $drop2Day_rte * $drop2Day_pkgs;
                    $DSI = 1;
                }
                if ($count >= $numShipItems || $i == $numShipItems) {
                    if ($dsArray = TRUE) {
                        foreach ($drop2D as $obj_key => $vzipVal) {
                            $dVal2D = 0;
                            foreach ($vzipVal as $k => $vWt) {
                                $dVal2D += $vWt;
                            }
                            if ($dVal2D != 0 && ($dVal2D > $MAX_BOX_WEIGHT)) {
                                $drop2Day_pkgs = ceil($dVal2D / $MAX_BOX_WEIGHT);
                                $drop2Day_pkgw = $dVal2D / $drop2Day_pkgs;
                                $drop2Day_rte = $myRate->getRate($obj_key, $shipto, "02", 0, 0, 0, $drop2Day_pkgw, $shiptostate, $drop2D[$obj_key]["st"]);
                                $drop2Day_ttl += $drop2Day_rte * $drop2Day_pkgs;
                            } else {
                                $drop2Day_pkgw = $dVal2D;
                                $drop2Day_rte = $myRate->getRate($obj_key, $shipto, "02", 0, 0, 0, $drop2Day_pkgw, $shiptostate, $drop2D[$obj_key]["st"]);
                                $drop2Day_ttl += $drop2Day_rte;
                            }
                        }
                    }
                    $DSI = 1;
                }
                if ($row_ship["HandlingFee"] > 0) {
                    $drop2Day_ttl += ($row_ship["HandlingFee"] * $row_shipitems["Qty"]);
                }

                // drop ship 3-day rate calulations
                if ($dropVendor == 1 && $DropShip_Width == 0) { 
                    if ($drop3Day_wt > $MAX_BOX_WEIGHT) {
                        $drop3Day_pkgs = ceil($drop3Day_wt / $MAX_BOX_WEIGHT);
                        $drop3Day_pkgw = $drop3Day_wt / $drop3Day_pkgs;
                        $drop3Day_rte = $myRate->getRate($vendors_Zipcode, $shipto, "12", 0, 0, 0, $drop3Day_pkgw, $shiptostate, $vendors_State);
                        $drop3Day_ttl += $drop3Day_rte * $drop3Day_pkgs;
                        $DSI = 1;
                    } else {
                    	if (isset($drop3D)) {
                    		if (array_key_exists($vendors_Zipcode, $drop3D)) {
                    			$drop3D[$vendors_Zipcode]["wt"] += $drop3Day_wt;
                    		}
                    	} else {
                       		$drop3D[$vendors_Zipcode]["wt"] = $drop3Day_wt * $row_shipitems["Qty"];
                        	$drop3D[$vendors_Zipcode]["st"] = $vendors_State;
                        }
                        $DSI = 1;
                        $dsArray = TRUE;
                    }
                } elseif ($dropVendor == 1 && $DropShip_Width > 0) {
                    $drop3Day_pkgs = $row_shipitems["Qty"];
                    $drop3Day_pkgw = $drop3Day_wt;
                    $drop3Day_rte = $myRate->getRate($vendors_Zipcode, $shipto, "12", 0, 0, 0, $drop3Day_pkgw, $shiptostate, $vendors_State);
                    $drop3Day_ttl += $drop3Day_rte * $drop3Day_pkgs;
                    $DSI = 1;
                }
                if ($count >= $numShipItems || $i == $numShipItems) {
                    if ($dsArray = TRUE) {
                        foreach ($drop3D as $obj_key => $vzipVal) {
                            $dVal3D = 0;
                            foreach ($vzipVal as $k => $vWt) {
                                $dVal3D += $vWt;
                            }
                            if ($dVal3D != 0 && ($dVal3D > $MAX_BOX_WEIGHT)) {
                                $drop3Day_pkgs = ceil($dVal3D / $MAX_BOX_WEIGHT);
                                $drop3Day_pkgw = $dVal3D / $drop3Day_pkgs;
                                $drop3Day_rte = $myRate->getRate($obj_key, $shipto, "12", 0, 0, 0, $drop3Day_pkgw, $shiptostate, $drop3D[$obj_key]["st"]);
                                $drop3Day_ttl += $drop3Day_rte * $drop3Day_pkgs;
                            } else {
                                $drop3Day_pkgw = $dVal3D;
                                $drop3Day_rte = $myRate->getRate($obj_key, $shipto, "12", 0, 0, 0, $drop3Day_pkgw, $shiptostate, $drop3D[$obj_key]["st"]);
                                $drop3Day_ttl += $drop3Day_rte;
                            }
                        }
                    }
                    $DSI = 1;
                }
                if ($row_ship["HandlingFee"] > 0) {
                    $drop3Day_ttl += ($row_ship["HandlingFee"] * $row_shipitems["Qty"]);
                }
            } // *** end of Drop Shipping *** //
     
        // get item dimensions
        $Length = $row_ship["Lenght"];
        $Width = $row_ship["Width"];
        $Height = $row_ship["Height"];    

        /***** CALCULATE SHIPPING ******/
        // change ounces to pounds and add to complete weight variable
        $shipground_wt += ($shipground_on / 16); 
        $ship1Day_wt += ($ship1Day_on / 16);
        $ship2Day_wt += ($ship2Day_on / 16);
        $ship3Day_wt += ($ship3Day_on / 16);

        // UPS Ground Shipping Rate calculations
        if ($Width == 0 && $dropVendor != 1) { // Pick-Pack indicator   
            if ($shipground_wt >= $MAX_BOX_WEIGHT) { 
                $shipground_pkgs = ceil($shipground_wt / $MAX_BOX_WEIGHT); 
                $shipground_pkgw = $shipground_wt / $shipground_pkgs;
                $shipground_rte = $myRate->getRate($primaryzip, $shipto, "03", 0, 0, 0, $shipground_pkgw, $shiptostate, $primarystate);
                $shipground_ttl += $shipground_rte * $shipground_pkgs;
                $totalWeight += $shipground_wt;
                $ppMsg .= "Pick/Pack Packages: " . $shipground_pkgs . "<br/>";
            } else { 
                $ppArrayG[] = $shipground_wt;
            }
        } elseif ($Width > 0 && $dropVendor != 1) {
            $shipground_pkgs = $row_shipitems["Qty"];
            $shipground_pkgw = $row_ship["Weight"]; 
            $shipground_rte = $myRate->getRate($primaryzip, $shipto, "03", $Length, $Width, $Height, $shipground_pkgw, $shiptostate, $primarystate);
            $shipground_ttl += $shipground_rte * $shipground_pkgs;
            $totalWeight += $shipground_wt;
            $ppMsg .= "Pick/Pack DIM package: " . $Length . " x " . $Width . " x " . $Height . "<br>";
        } 
        // if (!empty($ppArrayG)) { // get Pick-Pack weights for amalgamation rate
        if ($i == $numShipItems) { // but only after all p-p items have been accounted for in order
            if (!empty($ppArrayG)) {
                $aValG = array_sum($ppArrayG);
                $totalWeight += $aValG;
                if ($aValG > $MAX_BOX_WEIGHT) {
                    $shipground_pkgs = ceil($aValG / $MAX_BOX_WEIGHT); 
                    $shipground_pkgw = $aValG / $shipground_pkgs; 
                    $shipground_rte = $myRate->getRate($primaryzip, $shipto, "03", $Length, $Width, $Height, $shipground_pkgw, $shiptostate, $primarystate);
                    $shipground_ttl += $shipground_rte * $shipground_pkgs;
                    $ppMsg .= $shipground_pkgs . " Pick/Pack " . $shipground_pkgw . "lb packages<br>";
                } else {
                    $shipground_pkgs = 1;
                    $shipground_pkgw = $aValG;
                    $shipground_rte = $myRate->getRate($primaryzip, $shipto, "03", $Length, $Width, $Height, $shipground_pkgw, $shiptostate, $primarystate);
                    $shipground_ttl += $shipground_rte;
                    $ppMsg .= "1 Pick/Pack package at " . $shipground_pkgw . "lbs<br>";
                } 
            }
        } // end Pick-n-Pack
        if ($row_ship["HandlingFee"] > 0 && $dropVendor != 1) {
            $shipground_ttl += ($row_ship["HandlingFee"] * $row_shipitems["Qty"]);
            $ppMsg .= $row_shipitems["Qty"] . " FRS pick/pack package(s)";
        }
     
        // UPS overnight rate calculations
        if ($Width == 0 && $dropVendor != 1) {
            if ($ship1Day_wt >= $MAX_BOX_WEIGHT) {
                $ship1Day_pkgs = ceil($ship1Day_wt / $MAX_BOX_WEIGHT);
                $ship1Day_pkgw = $ship1Day_wt / $ship1Day_pkgs;
                $ship1Day_rte = $myRate->getRate($primaryzip, $shipto, "01", 0, 0, 0, $ship1Day_pkgw, $shiptostate, $primarystate);
                $ship1Day_ttl += $ship1Day_rte * $ship1Day_pkgs;
            } else {
                $ppArray1d[] = $ship1Day_wt;
            }   
        } elseif ($Width > 0 && $dropVendor != 1) {
            $ship1Day_pkgs = $row_shipitems["Qty"];
            $ship1Day_pkgw = $row_ship["Weight"];
            $ship1Day_rte = $myRate->getRate($primaryzip, $shipto, "01", $Length, $Width, $Height, $ship1Day_pkgw, $shiptostate, $primarystate);
            $ship1Day_ttl += $ship1Day_rte * $ship1Day_pkgs;
        }
        if ($i == $numShipItems) { 
            if (!empty($ppArray1d)) {
                $aVal1d = array_sum($ppArray1d);
                if ($aVal1d > $MAX_BOX_WEIGHT) {
                    $ship1Day_pkgs = ceil($aVal1d / $MAX_BOX_WEIGHT);
                    $ship1Day_pkgw = $aVal1d / $ship1Day_pkgs;
                    $ship1Day_rte = $myRate->getRate($primaryzip, $shipto, "01", $Length, $Width, $Height, $ship1Day_pkgw, $shiptostate, $primarystate);
                    $ship1Day_ttl += $ship1Day_rte * $ship1Day_pkgs;
                } else {
                    $ship1Day_pkgs = 1;
                    $ship1Day_pkgw = $aVal1d;
                    $ship1Day_rte = $myRate->getRate($primaryzip, $shipto, "01", $Length, $Width, $Height, $ship1Day_pkgw, $shiptostate, $primarystate);
                    $ship1Day_ttl += $ship1Day_rte;
                }
            }
        }
        if ($row_ship["HandlingFee"] > 0 && $dropVendor != 1) {
            $shipg1Day_ttl += ($row_ship["HandlingFee"] * $row_shipitems["Qty"]);
        }

        // UPS 2-day rate calculations
        if ($Width == 0 && $dropVendor != 1) {
            if ($ship2Day_wt >= $MAX_BOX_WEIGHT) {
                $ship2Day_pkgs = ceil($ship2Day_wt / $MAX_BOX_WEIGHT);
                $ship2Day_pkgw = $ship2Day_wt / $ship2Day_pkgs;
                $ship2Day_rte = $myRate->getRate($primaryzip, $shipto, "02", 0, 0, 0, $ship2Day_pkgw, $shiptostate, $primarystate);
                $ship2Day_ttl += $ship2Day_rte * $ship2Day_pkgs;
            } else {
                $ppArray2d[] = $ship2Day_wt;
            }
        } elseif ($Width > 0 && $dropVendor != 1) {
            $ship2Day_pkgs = $row_shipitems["Qty"];
            $ship2Day_pkgw = $row_ship["Weight"];
            $ship2Day_rte = $myRate->getRate($primaryzip, $shipto, "02", $Length, $Width, $Height, $ship2Day_pkgw, $shiptostate, $primarystate);
            $ship2Day_ttl += $ship2Day_rte * $ship2Day_pkgs;
        }
        if ($i == $numShipItems) {
            if (!empty($ppArray2d)) {
                $aVal2d = array_sum($ppArray2d);
                if ($aVal2d > $MAX_BOX_WEIGHT) {
                    $ship2Day_pkgs = ceil($aVal2d / $MAX_BOX_WEIGHT);
                    $ship2Day_pkgw = $aVal2d / $ship2Day_pkgs;
                    $ship2Day_rte = $myRate->getRate($primaryzip, $shipto, "02", $Length, $Width, $Height, $ship2Day_pkgw, $shiptostate, $primarystate);
                    $ship2Day_ttl += $ship2Day_rte * $ship2Day_pkgs;
                } else {
                    $ship2Day_pkgs = 1;
                    $ship2Day_pkgw = $aVal2d;
                    $ship2Day_rte = $myRate->getRate($primaryzip, $shipto, "02", $Length, $Width, $Height, $ship2Day_pkgw, $shiptostate, $primarystate);
                    $ship2Day_ttl += $ship2Day_rte;
                }
            }
        }
        if ($row_ship["HandlingFee"] > 0 && $dropVendor != 1) {
            $ship2Day_ttl += ($row_ship["HandlingFee"] * $row_shipitems["Qty"]);
        }

        // UPS 3-day rate calculations
        if ($Width == 0 && $dropVendor != 1) {
            if($ship3Day_wt >= $MAX_BOX_WEIGHT) {
                $ship3Day_pkgs = ceil($ship3Day_wt / $MAX_BOX_WEIGHT);
                $ship3Day_pkgw = $ship3Day_wt / $ship3Day_pkgs;
                $ship3Day_rte = $myRate->getRate($primaryzip, $shipto, "12", 0, 0, 0, $ship3Day_pkgw, $shiptostate, $primarystate);
                $ship3Day_ttl += $ship3Day_rte * $ship3Day_pkgs;
            } else {
                $ppArray3d[] = $ship3Day_wt;
            }
        } elseif ($Width > 0 && $dropVendor != 1) {
            $ship3Day_pkgs = $row_shipitems["Qty"];
            $ship3Day_pkgw = $row_ship["Weight"];
            $ship3Day_rte = $myRate->getRate($primaryzip, $shipto, "12", $Length, $Width, $Height, $ship3Day_pkgw, $shiptostate, $primarystate);
            $ship3Day_ttl += $ship3Day_rte * $ship3Day_pkgs;
        }
        if ($i == $numShipItems) {
            if (!empty($ppArray3d)) {
                $aVal3d = array_sum($ppArray3d);
                if ($aVal3d > $MAX_BOX_WEIGHT) {
                    $ship3Day_pkgs = ceil($aVal3d / $MAX_BOX_WEIGHT);
                    $ship3Day_pkgw = $aVal3d / $ship3Day_pkgs;
                    $ship3Day_rte = $myRate->getRate($primaryzip, $shipto, "12", $Length, $Width, $Height, $ship3Day_pkgw, $shiptostate, $primarystate);
                    $ship3Day_ttl += $ship3Day_rte * $ship3Day_pkgs;
                } else {
                    $ship3Day_pkgs = 1;
                    $ship3Day_pkgw = $aVal3d;
                    $ship3Day_rte = $myRate->getRate($primaryzip, $shipto, "12", $Length, $Width, $Height, $ship3Day_pkgw, $shiptostate, $primarystate);
                    $ship3Day_ttl += $ship3Day_rte;
                }
            }
        }
        if ($row_ship["HandlingFee"] > 0 && $dropVendor != 1) {
            $ship3Day_ttl += ($row_ship["HandlingFee"] * $row_shipitems["Qty"]);
        }

        // USPS Priority Mail rate calculations
        if ($Width == 0 && $dropVendor != 1) {
            if ($shipground_wt >= $MAX_BOX_WEIGHT) {
                $shipPO_pkgs = ceil($shipground_wt / $MAX_BOX_WEIGHT);
                $shipPO_pkgw = $shipground_wt / $shipPO_pkgs;
                $shipPO_rte = $poRate->uspsRate($shipPO_pkgw, $shipto, $primaryzip);
                $shipPO_ttl += ($shipPO_rte + 3) * $shipPO_pkgs;
            } else {
                $ppArrayPO[] = $shipground_wt;
            }
        } elseif ($Width > 0 && $dropVendor != 1) {
            $shipPO_pkgs = $row_shipitems["Qty"];
            $shipPO_pkgw = $row_ship["Weight"];
            $shipPO_rte = $poRate->uspsRate($shipPO_pkgw, $shipto, $primaryzip);
            $shipPO_ttl += ($shipPO_rte + 3) * $shipPO_pkgs;
        }
        if ($i == $numShipItems) {
            if (!empty($ppArrayPO)) {
                $aValPO = array_sum($ppArrayPO);
                if ($aValPO > $MAX_BOX_WEIGHT) {
                    $shipPO_pkgs = ceil($aValPO / $MAX_BOX_WEIGHT);
                    $shipPO_pkgw = $aValPO / $shipPO_pkgs;
                    $shipPO_rte = $poRate->uspsRate($shipPO_pkgw, $shipto, $primaryzip);
                    $shipPO_ttl += ($shipPO_rte + 3) * $shipPO_pkgs;
                } else {
                    $shipPO_pkgs = 1;
                    $shipPO_pkgw = $aValPO;
                    $shipPO_rte = $poRate->uspsRate($shipPO_pkgw, $shipto, $primaryzip);
                    $shipPO_ttl += $shipPO_rte + 3;
                }
            }
        }

        if ($row_ship["UPS"] == "SpecificShipping" && $row_ship["SpecificOption"] == "Ground") {
            $ship_only_ground = 1;
        }

        $i++;
        $DS = 0;
        $count++;

        // progress end of loop
        $po->Animate();
        ob_flush();
        flush();  
    } // NEW end while

    if ($dsArrayCount != 0) {
        if ($dsArray = TRUE) {
            foreach ($dropG as $obj_key => $vzipVal) {
                $dValG = 0;
                foreach ($vzipVal as $k => $vWt) {
                    $dValG += $vWt;
                }
                if ($dValG != 0 && ($dValG > $MAX_BOX_WEIGHT)) {
                    $dropground_pkgs = ceil($dValG / $MAX_BOX_WEIGHT); 
                    $dropground_pkgw = $dValG / $dropground_pkgs;
                    $dropground_rte = $myRate->getRate($obj_key, $shipto, "03", $DropShip_Lenght, $DropShip_Width, $DropShip_Height, $dropground_pkgw, $shiptostate, $dropG[$obj_key]["st"]);
                    $dsMsg .= "Dropship Packages - Weight (ea): " . $dropground_pkgs . " - " . $dropground_pkgw . "lbs<br>";
                    $dropground_ttl += $dropground_rte * $dropground_pkgs;
                } else {
                    $dropground_pkgw = $dValG;
                    $dropground_rte = $myRate->getRate($obj_key, $shipto, "03", $DropShip_Lenght, $DropShip_Width, $DropShip_Height, $dropground_pkgw, $shiptostate, $dropG[$obj_key]["st"]);
                    $dropground_ttl += $dropground_rte;
                }
            }
            foreach ($drop1D as $obj_key => $vzipVal) {
                $dVal1D = 0;
                foreach ($vzipVal as $k => $vWt) {
                    $dVal1D += $vWt;
                }
                if ($dVal1D != 0 && ($dVal1D > $MAX_BOX_WEIGHT)) {
                    $drop1Day_pkgs = ceil($dVal1D / $MAX_BOX_WEIGHT);
                    $drop1Day_pkgw = $dVal1D / $drop1Day_pkgs;
                    $drop1Day_rte = $myRate->getRate($obj_key, $shipto, "01", 0, 0, 0, $drop1Day_pkgw, $shiptostate, $drop1D[$obj_key]["st"]);
                    $drop1Day_ttl += $drop1Day_rte * $drop1Day_pkgs;
                } else {
                    $drop1Day_pkgw = $dVal1D;
                    $drop1Day_rte = $myRate->getRate($obj_key, $shipto, "01", 0, 0, 0, $drop1Day_pkgw, $shiptostate, $drop1D[$obj_key]["st"]);
                    $drop1Day_ttl += $drop1Day_rte;
                }
            }
            foreach ($drop2D as $obj_key => $vzipVal) {
                $dVal2D = 0;
                foreach ($vzipVal as $k => $vWt) {
                    $dVal2D += $vWt;
                }
                if ($dVal2D != 0 && ($dVal2D > $MAX_BOX_WEIGHT)) {
                    $drop2Day_pkgs = ceil($dVal2D / $MAX_BOX_WEIGHT);
                    $drop2Day_pkgw = $dVal2D / $drop2Day_pkgs;
                    $drop2Day_rte = $myRate->getRate($obj_key, $shipto, "02", 0, 0, 0, $drop2Day_pkgw, $shiptostate, $drop2D[$obj_key]["st"]);
                    $drop2Day_ttl += $drop2Day_rte * $drop2Day_pkgs;
                } else {
                    $drop2Day_pkgw = $dVal2D;
                    $drop2Day_rte = $myRate->getRate($obj_key, $shipto, "02", 0, 0, 0, $drop2Day_pkgw, $shiptostate, $drop2D[$obj_key]["st"]);
                    $drop2Day_ttl += $drop2Day_rte;
                }
            }
            foreach ($drop3D as $obj_key => $vzipVal) {
                $dVal3D = 0;
                foreach ($vzipVal as $k => $vWt) {
                    $dVal3D += $vWt;
                }
                if ($dVal3D != 0 && ($dVal3D > $MAX_BOX_WEIGHT)) {
                    $drop3Day_pkgs = ceil($dVal3D / $MAX_BOX_WEIGHT);
                    $drop3Day_pkgw = $dVal3D / $drop3Day_pkgs;
                    $drop3Day_rte = $myRate->getRate($obj_key, $shipto, "12", 0, 0, 0, $drop3Day_pkgw, $shiptostate, $drop3D[$obj_key]["st"]);
                    $drop3Day_ttl += $drop3Day_rte * $drop3Day_pkgs;
                } else {
                    $drop3Day_pkgw = $dVal3D;
                    $drop3Day_rte = $myRate->getRate($obj_key, $shipto, "12", 0, 0, 0, $drop3Day_pkgw, $shiptostate, $drop3D[$obj_key]["st"]);
                    $drop3Day_ttl += $drop3Day_rte;
                }
            }
        $totalWeight += $dValG;
        }
    }

    // hide the progress bar now
    $po->Hide();
    $shipAmt = 0.00;
    if ($shipground_ttl > 0) {
        $ship_ground += $shipground_ttl;
    } 
    if ($dropground_ttl > 0) {
        $ship_ground += $dropground_ttl;
    } 
    if ($ship1Day_ttl > 0) {
        $ship_1stDay += $ship1Day_ttl;
    }
    if ($drop1Day_ttl > 0) {
        $ship_1stDay += $drop1Day_ttl;
    }
    if ($ship2Day_ttl > 0) {
        $ship_2ndDay += $ship2Day_ttl;
    }
    if ($drop2Day_ttl > 0) {
        $ship_2ndDay += $drop2Day_ttl;
    }
    if ($ship3Day_ttl > 0) {
        $ship_3Day += $ship3Day_ttl;
    }
    if ($drop3Day_ttl > 0) {
        $ship_3Day += $drop3Day_ttl;
    }
    if ($shipPO_ttl > 0 ) {
        $ship_PO = $shipPO_ttl;
    }
    /**** END CALCULATE SHIPPING ****/
    
    $shippingopt = '<table width="100%" border="0" cellpadding="0" cellspacing="5">';
    if ($ship_only_ground == 0) {
        if ($ship_1stDay != '' || $ship_1stDay != 0) {
            $shippingopt .= "<tr><td width=\"50%\" height=\"35\" align=\"left\" valign=\"middle\" bgcolor=\"#EBEBEB\">&nbsp;<input type=\"radio\" style=\"padding: 0px 10px 0px 10px;\" class=\"optshipping required\" id=\"shipping\" name=\"shipping\" value=\"Next Day Air\" ".($shipmeth=='1Day'?'checked="true"':'')." /><span style=\"margin-top:-1px;position:absolute\">&nbsp;Next Day Air ($".number_format($ship_1stDay,2).") &nbsp;<img style=\"margin-top:-1px;position:absolute\" src=\"ups.png\" height=\"20\"/></span></td></tr>";
            $java .= 'case "Next Day Air": shipping='.$ship_1stDay.'; break;';
            $shipAmt = $ship_1stDay;
        }
        if ($ship_2ndDay != '' || $ship_2ndDay != 0) {
            $shippingopt .= "<tr><td width=\"50%\" height=\"35\" align=\"left\" valign=\"middle\" bgcolor=\"#FFFFFF\">&nbsp;<input type=\"radio\" style=\"padding: 0px 10px 0px 10px;\" class=\"optshipping required\" id=\"shipping\" name=\"shipping\" value=\"2nd Day Air\" ".($shipmeth=='2Day'?'checked="true"':'')." /><span style=\"margin-top:-1px;position:absolute\">&nbsp;2nd Day Air ($".number_format($ship_2ndDay,2).") &nbsp;<img style=\"margin-top:-1px;position:absolute\" src=\"ups.png\" height=\"20\"/></span></td></tr>";
            $java .= 'case "2nd Day Air": shipping='.$ship_2ndDay.'; break;';
            $shipAmt = $ship_2ndDay;
        }
        if ($ship_3Day != '' || $ship_3Day != 0) {
            $shippingopt .= "<tr><td width=\"50%\" height=\"35\" align=\"left\" valign=\"middle\" bgcolor=\"#EBEBEB\">&nbsp;<input type=\"radio\" style=\"padding: 0px 10px 0px 10px;\" class=\"optshipping required\" id=\"shipping\" name=\"shipping\" value=\"3 Day Select\" ".($shipmeth=='3Day'?'checked="true"':'')." /><span style=\"margin-top:-1px;position:absolute\">&nbsp;3 Day Select ($".number_format($ship_3Day,2).") &nbsp;<img style=\"margin-top:-1px;position:absolute\" src=\"ups.png\" height=\"20\"/></span></td></tr>";
            $java .= 'case "3 Day Select": shipping='.$ship_3Day.'; break;';
            $shipAmt = $ship_3Day;
        }
        if ($ship_ground != '' || $ship_ground != 0) {
            $shippingopt .= "<tr><td width=\"50%\" height=\"35\" align=\"left\" valign=\"middle\" bgcolor=\"#FFFFFF\">&nbsp;<input type=\"radio\" style=\"padding: 0px 10px 0px 10px;\" class=\"optshipping required\" id=\"shipping\" name=\"shipping\" value=\"Ground\" ".($shipmeth=='ground'?'checked="true"':'')." /><span style=\"margin-top:-1px;position:absolute\">&nbsp;Ground ($".number_format($ship_ground,2).") &nbsp;<img style=\"margin-top:-1px;position:absolute\" src=\"ups.png\" height=\"20\"/></span></td></tr>";
            $java .= 'case "Ground": shipping='.$ship_ground.'; break;';
            $shipAmt = $ship_ground;
        } 
        if ($DSI == 0) {
            if ($ship_PO != '' || $ship_PO >= 3) {
                $shippingopt .= "<tr><td width=\"50%\" height=\"35\" align=\"left\" valign=\"middle\" bgcolor=\"#EBEBEB\">&nbsp;<input type=\"radio\" style=\"padding: 0px 10px 0px 10px;\" class=\"optshipping required\" id=\"shipping\" name=\"shipping\" value=\"USPS Priority\" ".($shipmeth=='USPS'?'checked="true"':'')." /><span style=\"margin-top:-1px;position:absolute\">&nbsp;Priority Mail ($".number_format($ship_PO,2).") &nbsp;<img style=\"margin-top:-1px;position:absolute\" src=\"usps.png\" height=\"20\"/></span></td></tr>";
                $java .= 'case "USPS Priority": shipping='.$ship_PO.'; break;';
                $shipAmt = $ship_PO;
            } 
        }
        if ($DSI != 0) {
            $shippingopt .= "<tr><td width=\"50%\" height=\"35\" align=\"left\" valign=\"middle\" bgcolor=\"#FFFFFF\">&nbsp;<input type=\"checkbox\" style=\"padding: 0px 10px 0px 10px;\" class=\"required\" id=\"dshipping\" name=\"dshipping\" value=\"ack_note\" /><span style=\"margin-top:-1px;position:absolute\">&nbsp;I have read all of the shipping messages above</span></td></tr>";
        }
        if (intval($row_scoupon["MinimumOrder"]) > intval($_SESSION["orderTotal"])) {
            $shippingopt .= "<tr><td width=\"50%\" height=\"35\" align=\"left\" valign=\"middle\" bgcolor=\"#FFFFFF\">&nbsp;<input type=\"radio\" style=\"padding: 0px 10px 0px 10px;\" class=\"optshipping required\" id=\"shipping\" name=\"shipping\" value=\"Free\" /><span style=\"margin-top:-1px;position:absolute\">&nbsp;Free Shipping ($".number_format(0 ,2).")</span></td></tr>";
            $java .= 'case "Free Shipping": shipping=0; break;';
        }
    } else {
        $shippingopt .= "<tr><td width=\"50%\" height=\"35\" align=\"left\" valign=\"middle\" bgcolor=\"#FFFFFF\">&nbsp;<input type=\"radio\" style=\"padding: 0px 10px 0px 10px;\" class=\"optshipping required\" id=\"shipping\" name=\"shipping\" value=\"Ground\" ".($shipmeth=='ground'?'checked="true"':'')." /><span style=\"margin-top:-1px;position:absolute\">&nbsp;Ground ($".number_format($ship_ground,2).") &nbsp;<img style=\"margin-top:-1px;position:absolute\" src=\"ups.png\" height=\"20\"/></span></td></tr>";
        $java .= 'case "Ground": shipping='.$ship_ground.'; break;';
        $shipAmt = $ship_ground;
    }
    if ($vipShip == "yes") {
        $shippingopt .= "<tr><td width=\"50%\" height=\"35\" align=\"left\" valign=\"middle\" bgcolor=\"#FFFFFF\">&nbsp;<input type=\"radio\" style=\"padding: 0px 10px 0px 10px;\" class=\"optshipping required\" id=\"shipping\" name=\"shipping\" value=\"Free\" /><span style=\"margin-top:-1px;position:absolute\">&nbsp;Free Shipping ($".number_format(0 ,2).")</span></td></tr>";
        $java .= 'case "Free Shipping": shipping=0; break;';
    }
}
$shippingopt .= "</table>";
// ** END SHIPPING **

// begin actual page display
?>
<script type="text/javascript">
$(document).ready(function() {
    $("#gcFunds").hide();
    $(".optshipping").click(function() {
        var shipping = 0.00;
        var grandtotal = 0.00;
        var discount = $("#totaldiscount").val();
        var tax = $("#totaltax").val();
        var ordertotal = $("#ordertotal").val();
        var gctotal = $("#gctotal").val();
        switch($(this).val()) {
            <?=$java;?>
        }

        shipping = parseFloat(shipping);
        tax = parseFloat(tax);
        ordertotal = parseFloat(ordertotal);
        discount = parseFloat(discount);
        gctotal = parseFloat(gctotal);

        if (isNaN(discount)) {
            discount = 0.00;
        }
        if (isNaN(shipping)) {
        }
        if (isNaN(tax)) {
            tax = 0.00;
            $("#tax").val(tax.toFixed(2));
            $("#totaltax").val(tax.toFixed(2));
        }
        if (isNaN(ordertotal)) {
        }
        if (isNaN(gctotal)) {
        	gctotal = 0.00;
        }

        grandtotal = shipping + tax + ordertotal + discount - gctotal;

        if (isNaN(grandtotal) || grandtotal <= 0) {
            alert("Something went wrong- please contact SoccerOne Customer Service! ERROR CODE: 00gt01");
            return false;
        }
        
        $("#totaldiscount").val(discount);
        $("#totaldiscountval").html('$'+discount.toFixed(2));
        $("#totalshippingval").html('$'+shipping.toFixed(2));
        $("#totalshipping").val(shipping.toFixed(2));
        $("#totalshipping2").val(shipping.toFixed(2));
        $("#grandtotalval").html('$'+grandtotal.toFixed(2));
        $("#grandtotal").val(grandtotal.toFixed(2));
        $(".shipvia").html("This item ships: " + $(this).val());
        
        if (gctotal > 0) {
        	$("#gcFunds").show();
        	$("#gcfundsval").html('$'+gctotal.toFixed(2));
        	$('#gttr').css('background-color', '#ebebeb');
			$('#gtspan').html('<b>Grand Total Due</b>');
        }
    });

    function calCulateDefaultShipping() {
        var shipping = 0.00;
        var grandtotal = 0.00;
        var discount = $("#totaldiscount").val();
        var tax = $("#totaltax").val();
        var ordertotal = $("#ordertotal").val();
        switch($('input[name=shipping]:checked').val()) {
            <?=$java;?>
        }

        shipping = parseFloat(shipping);
        tax = parseFloat(tax);
        ordertotal = parseFloat(ordertotal);
        discount = parseFloat(discount);
        grandtotal = shipping + tax + ordertotal + discount;

        if (isNaN(grandtotal) || grandtotal <= 0) {
            alert("Something went wrong- please contact SoccerOne Customer Service! ERROR CODE: 00gt02");
        }
        
        $("#totalshippingval").html('$'+shipping.toFixed(2));
        $("#totalshipping2").val(shipping.toFixed(2));
        $("#grandtotalval").html('$'+grandtotal.toFixed(2));
        $("#totalshipping").val(shipping.toFixed(2));
        $("#grandtotal").val(grandtotal.toFixed(2));
        $(".shipvia").html("This item ships: " + $('input[name=shipping]:checked').val());
    };
    
    <?php if ($shipmeth != ''): ?>
        calCulateDefaultShipping();
    <?php endif; ?>

    // has the Submit Order button been clicked?
    $("#SubmitOrder").click(function() {
        var gct = $("#gctotal").val();
        if ($('input[name=paymentmethod]').is(":checked")) {
        } else {
        	alert("Please choose a payment method!");
        	return false;
        }

        if (($('input[name=shipping]').is(":checked")) || ($('input[name=dshipping]').is(":checked"))) {
        } else {
            alert("Please choose a shipping method!");
            return false;
        }
                
        // check for agreement to Terms
        if ($("#agree").is(":checked")) {
        } else {
            alert('Please agree with the terms and conditions before completing the order!');
            return false;
        }

        // check for dropshipment acknowledgement
        if ($("#dshipping").length) {
            if ($("#dshipping").is(":checked")) {
            } else {
                alert('Please acknowledge that you have read the shipping notification for the product(s) listed above!');
                return false;
            }
        }
            
        $("form").submit();
    }); // end submit function
    
    // if selected, show card entry fields   
    $("#pmCreditCard").click(function() {
        $("#SubmitOrder").val("Continue to Payment Method").css({'width':'300px','background-color':'red'});
        $("#orderForm").attr("action", "process.php");
        $("#paymenttype").load("includes/inc_checkout.php", {
            "type":"card",
            "amount":$("#grandtotal").val(),
            "lid":"<?=$loginID;?>",
            "seq":"<?=$sequence;?>",
            "times":"<?=$timestamp;?>",
            "tk":"<?=$transactionKey;?>"
        });
    });

    $("#pmOpenAccount").click(function() {
        $("#SubmitOrder").val("Submit Order").css({'width':'150px'});
        $("#orderForm").attr("action", "");
    })

    $("#shipping").click(function() {
        var shipMethod = $('input[name=shipping]:checked').val();
        $("#ship").val(shipMethod);
    });

    
    // was the GC apply link clicked?
    $("#pmGiftCert").click(function() {
        var ts = $("#totalshipping2").val();
        if (ts == "" || ts == "0") {
            alert("Please choose a shipping option!");
        } else {
            $("#gcError").load("includes/inc_gc.php", {
                "type":"apply",
                "num":$("#gcNum").val(),
                "gt":$("#grandtotal").val(),                                                              
                "disc":$("#totaldiscount").val()
            }, function(data) {
            	if ($("#grandtotal").val() == 0) {
            		$('#paymentDIV').html('<input type="radio" name="paymentmethod" value="gcOnly" checked="checked"> Gift Certificate funds only');
        		}
            });
        } 
       // if ($("#grandtotal").val() == 0) {
       //      $('#paymentDIV').html('<input type="radio" name="paymentmethod" value="gcOnly" checked="checked"> Gift Certificate funds only');
       // }
    });
    
    // was the GC check balance clicked?
    $("#gcBal").click(function() {
        $("#gcError").load("includes/inc_gc.php", {
            "type":"bal",
            "num":$("#gcNum").val()
        });
    });
});
</script> 
<style type="text/css">
.style1 { color: #FFFFFF; }
.style2 { font-size: 0.875em; }
.style3 { font-size: 0.875em; }
.style4 { font-size: 0.875em; color: #FFFFFF; }
</style>
</head>
<body>
<div class="Master_div"><?php include_once('includes/header.php'); ?>
  <div class="container container1">
    <div class="navigation">
      <div class="navi_L"></div>
      <div class="navi_C"><?php include_once('includes/topnav.php'); ?>
        <div class="clear"></div>
      </div>
      <div class="navi_R"></div>
      <div class="clear"></div>
    </div>
    <div class="clear"></div>
    <div class="main">
<?php
    // get all products from Shopping Cart
    $sql_cart = "SELECT * FROM shopping_cart WHERE $sqlwhere AND (`Type`='Product' OR `Type`='Bundle' OR `Type`='VIP') AND (BundleID='' OR BundleID IS NULL)";
    $ordertotal = 0;
    $availablediscount = 0;
    $taxableamount = 0;
    $customVipPrice = 0;
    $orderTotalWithoutSpePrice = 0;
    $couponCalc = new CouponCalculation(0, 0, $isvip);
    $result_cart = mysql_query($sql_cart) or die("Shopping Cart Error: " . mysql_error());
    
    // process the Shopping Cart products
    if (@mysql_num_rows($result_cart)) {
?>
    <script type="text/javascript" src="/js/jquery.validate.min.js"></script>
    <style type="text/css">
        label { 
            width: 10em; 
            float: left; 
        }
        label.error { 
            padding: 10px 10px 10px 10px;
            margin: 10px 0;
            border: solid 1px; 
            border-color: #DF8F8F;
            background-color: #FFDCDC;
            font-size: 1em;
        }
        p { 
            clear: both; 
        }
        .submit { 
            margin-left: 12em; 
        }
        em { 
            font-weight: bold; 
            padding-right: 1em; 
            vertical-align: top; 
        }
    </style>
    <form method="post" id="orderForm" name="orderForm" action="">
    <table border="0" align="center" cellpadding="3" cellspacing="0" class="ordering">
    <tr>
        <td style="background-color:#FF0000;color:#fff;border:none;height:35px;width:100px">&nbsp;</td>
        <td style="width: 200px;background-color:#FF0000;color:#fff;border:none;height:35px;font-size:0.875em">Product Name</td>
        <td style="width: 150px;background-color:#FF0000;color:#fff;border:none;height:35px;font-size:0.875em">SKU</td>
        <td style="background-color:#FF0000;color:#fff;border:none;height:35px;font-size:0.875em">Non Member Price</td>
    <?php 
        if ($isvip == "yes") { 
            echo '<td style="background-color:#FF0000;color:#fff;border:none;height:35px;font-size:0.875em">VIP Price</td>';
        } 
    ?>
        <td style="background-color:#FF0000;color:#fff;border:none;height:35px;font-size:0.875em">QTY</td>
        <td style="background-color:#FF0000;color:#fff;border:none;height:35px;font-size:0.875em">Subtotal</td>
    </tr>
<?php
    while ($row_cart = mysql_fetch_array($result_cart)) {
        $cusTotalProduct++;
            if ($row_cart["Type"] != "VIP") {
                $sql_shiptype = "SELECT * FROM product_shipping WHERE ProductID=$row_cart[ProductID] LIMIT 1";
                $result_shiptype = mysql_query($sql_shiptype) or die("ShipType Error: " . mysql_error());
                $row_shiptype = array();
                if (@mysql_num_rows($result_shiptype)) {
                    $row_shiptype = mysql_fetch_assoc($result_shiptype);
                }
                if ($row_shiptype && $row_shiptype["ShippingType"] == "PrimaryLocation" && $row_shiptype["UPS"] == "SpecificShipping") {
                    $shipmess = "This item ships: ".$row_shiptype["SpecificOption"];
                } elseif($row_shiptype["ShippingType"] == 'Dropship') {
                    $shipmess = "This item will ship from another location";
                } else {
                    $shipmess = '<span class="shipvia"></span>';
                }

                if ($row_cart["ColorSKU"] == '') {
                    $imgColorSKU = "IS NULL ";
                } else {
                    $imgColorSKU = "='$row_cart[ColorSKU]' ";
                }

                if ($row_cart["SizeSKU"] == '') {
                    $imgSizeSKU = "IS NULL ";
                } else {
                    $imgSizeSKU = "='$row_cart[SizeSKU]' ";
                }
            
                // obtain main product image
                if ($imgColorSKU != "IS NULL ") {
                    $sql_image = "SELECT ColorImage FROM product_options WHERE ProductID=$row_cart[ProductID] AND ColorSKU $imgColorSKU AND SizeSKU $imgSizeSKU LIMIT 1";
                } else {
                    $sql_image = "SELECT ColorImage FROM product_options WHERE ProductID=$row_cart[ProductID] LIMIT 1";
                }
                $result_image = mysql_query($sql_image) or die("Image Error: " . mysql_error());
                $row_image = array();
                if (@mysql_num_rows($result_image)) {
                    $row_image = mysql_fetch_assoc($result_image);
                }
                    
                // create final product order complete SKU
                $sql_skuorder = "SELECT SKUOrder FROM products WHERE id=$row_cart[ProductID] LIMIT 1";
                $result_skuorder = mysql_query($sql_skuorder);
                    
                // get rootsku
                if (@mysql_num_rows($result_skuorder)) {
                    $row_skuorder = mysql_fetch_assoc($result_skuorder);
                    $skuorder = explode("|", $row_skuorder["SKUOrder"]);
                    $prodsku = $row_cart[$skuorder[0]."SKU"];

                    if ($row_cart[$skuorder[1]."SKU"] != '') {
                        $prodsku .= "-".$row_cart[$skuorder[1]."SKU"];
                    }

                    if ($row_cart[$skuorder[2]."SKU"] != '') {
                        $prodsku .= "-".$row_cart[$skuorder[2]."SKU"];
                    }
                }

                if ($row_cart["GenderSKU"] != '') {
                    $prodsku .= "-".$row_cart["GenderSKU"];
                }

            $total = $row_cart["Qty"] * $row_cart[$pricename];
            $ordertotal = $ordertotal + $total;

            /** Tax Calculation **/
                $sql_prodtax = "SELECT Taxable FROM products WHERE id=$row_cart[ProductID] LIMIT 1";
                $result_prodtax = mysql_query($sql_prodtax) or die("Tax Error: " . mysql_error());
                if (@mysql_num_rows($result_prodtax)) {
                    $row_prodtax = mysql_fetch_assoc($result_prodtax);
                    if ($row_prodtax["Taxable"] == 'Yes') {
                        $taxableamount = $taxableamount + ($row_cart[$pricename] * $row_cart["Qty"]);
                    }
                }
    ?>
            <tr>
    <?php 
            $isConfirmProduct = false;
            if ($row_image) { 
                $isConfirmProduct = false;
                // show VIP membership image if that is the product
                if ($row_cart["Type"] == "VIP") {
                    echo '<td class="cartitem"><img class="cartthumb" src="images/productImages/' . $row_vipd["Image"] . '" /></td>';
                // otherwise show the main image for the product
                } else {
                    echo '<td class="cartitem"><img class="cartthumb" src="images/productImages/' . $row_image["ColorImage"] . '" /></td>';
                } 
            }
            // no image to show? make blank table cell
            if ($row_image == '' or $row_image == NULL) { 
                echo '<td class="cartitem"></td>';
            } 
    ?>
    <?php 
            if (!$isConfirmProduct) { 
                echo '<td class="cartitem">' . $row_cart["ProductName"];
                if (array_key_exists($row_cart['ProductID'], $freeShipItemList)) {
                    echo '<br/><span style="font-size: 11px;">This item ships: Free shipping</span>';
                } else {
                    if ($shipmess != '') {
                        echo '<br/><span style="font-size: 11px;">' . $shipmess . '</span>';
                    }
                }
                echo '</td>';
                echo '<td class="cartitem">'. $prodsku .'</td>';
            }
    ?>
                <td class="cartitem">
    <?php 
        if ($isvip == "yes") {
            echo '<span style="color: #909090;text-decoration:line-through;">$' . number_format($row_cart["Price"], 2) . '</span>';
        } else {
            echo "$" . number_format($row_cart[$pricename], 2);
        }
    ?>
                </td>
    <?php 
        if ($isvip == "yes") { 
            echo '<td class="cartitem">$'. number_format($row_cart[$pricename], 2) . '</td>';
        } 
    ?>
                <td class="cartitem"><?=$row_cart["Qty"];?></td>
                <td class="cartitem">$<?=number_format($total, 2);?></td>
            </tr>
    <?php
                $sql_ship1 = "SELECT * FROM product_shipping WHERE ProductID=$row_cart[ProductID] LIMIT 1";
                $result_ship1 = mysql_query($sql_ship1) or die("Shipping error: " . mysql_error());
                $mysql_fetch1 = mysql_fetch_array($result_ship1);
                if (isset($mysql_fetch1['Description'])) {
                    echo '<tr><td bgcolor="#EBEBEB" height="35" colspan="7"><div style="width:100%; text-align:left; float:left;">' . $mysql_fetch1['Description'] . '</div></td></tr>';
                }
            
            if ($row_cart["Type"] == "Bundle") {
                $sql_bitems = "SELECT * FROM shopping_cart WHERE $sqlwhere AND BundleID=$row_cart[id] ORDER BY ProductName";
                $result_bitems = mysql_query($sql_bitems) or die("Bundle Error: " . mysql_error());
                while ($row_bitems = mysql_fetch_array($result_bitems)) {
                    $sql_bimage = "SELECT ColorImage FROM product_options WHERE ProductID=$row_bitems[ProductID] AND ColorSKU='$row_bitems[ColorSKU]' AND SizeSKU='$row_bitems[SizeSKU]' LIMIT 1";
                    $result_bimage = mysql_query($sql_bimage) or die("Bundle Image Error: " . mysql_error());
                    $row_bimage = mysql_fetch_assoc($result_bimage);
    ?>
            <tr>
                <td class="cartitem"></td>
                <td class="cartitem"><img class="cartthumb" style="width: 27px; float: left;" src="images/productImages/<?=$row_bimage["ColorImage"];?>" /><?=$row_bitems["ProductName"];?></td>
                <td class="cartitem"><?=$row_bitems["RootSKU"]."-".$row_bitems["ColorSKU"]."-".$row_bitems["SizeSKU"]." x ".$row_bitems["Qty"];?></td>
                <td class="cartitem"></td>
    <?php 
        if ($isvip == "yes") { 
            echo '<td class="cartitem"></td>';
        }
    ?>
                <td class="cartitem"></td>
                <td class="cartitem"></td>
            </tr>    
    <?php
            $sql_ship2 = "SELECT * FROM product_shipping WHERE ProductID=$row_bitems[ProductID] LIMIT 1";
            $result_ship2 = mysql_query($sql_ship2) or die("Bundle Shipping Error: " . mysql_error());
            $mysql_fetch2 = mysql_fetch_array($result_ship2);
    ?>       
            <tr>
                <td bgcolor="#EBEBEB" height="35" colspan="7"><div style="width:100%; text-align:left; float:left;"><?php echo $mysql_fetch1['Description'];?></div></div></td>
            </tr>
    <?php
                } // end while bundle
            } else {
                $sql_bitems = "SELECT * FROM shopping_cart_single WHERE ".$sqlwhere." AND singleid=".$row_cart['id']." ORDER BY ProductName";
                $result_bitems = mysql_query($sql_bitems) or die("Single Item Error: " . mysql_error());
                while ($row_bitems = mysql_fetch_array($result_bitems)) {
                    $sql_bimage = "SELECT ColorImage FROM product_options WHERE ProductID=".$row_bitems['ProductID']." AND ColorSKU='".$row_bitems['ColorSKU']."' AND SizeSKU='".$row_bitems['SizeSKU']."' LIMIT 1";
                    $result_bimage = mysql_query($sql_bimage) or die("Image Error: " . mysql_error());
                    $row_bimage = mysql_fetch_assoc($result_bimage);
    ?>
            <tr>
                <td class="cartitem"></td>
                <td class="cartitem"><img class="cartthumb" style="width: 27px; float: left;" src="images/productImages/<?=$row_bimage["ColorImage"];?>" /><?=$row_bitems["ProductName"];?></td>
                <td class="cartitem"><?=$row_bitems["RootSKU"]."-".$row_bitems["ColorSKU"]."-".$row_bitems["SizeSKU"]." x ".$row_bitems["Qty"];?></td>
                <td class="cartitem"></td>
    <?php 
        if ($isvip == "yes") {
            echo '<td class="cartitem"></td>';
        }
    ?>
                <td class="cartitem"></td>
                <td class="cartitem"></td>
            </tr>
    <?php
                $sql_ship2 = "SELECT * FROM product_shipping WHERE ProductID=$row_bitems[ProductID] LIMIT 1";
                $result_ship2 = mysql_query($sql_ship2);
                $mysql_fetch2 = mysql_fetch_array($result_ship2);
            } // end while
        } // end bundle if
            
            // IMPRINT INFORMATION 
            $sql_imp = "SELECT * FROM imprint_shopping_cart WHERE $sqlwhere AND ProductID='$row_cart[ProductID]'";
            $result_imp = mysql_query($sql_imp);
            $num_imp = mysql_num_rows($result_imp);
            $impPrice = 0;
            if ($num_imp > 0) {
                $imprint_data = '<table class="imprintOptions" cellpadding="3" cellspacing="0"><tr><td class="impheader" colspan="3">Imprint Options</td><td class="impheader"></td></tr>';
                while ($row_imp = mysql_fetch_array($result_imp)) {
                    $impPrice += floatval($row_imp["ImprintPrice"]);
                    $optName = ucfirst($row_imp["Opt1Type"]);
                    if ($row_imp["Opt2Type"] != "") {
                        $optName .= " & ".ucfirst($row_imp["Opt2Type"]);
                    }
                    $optTeam = '';
                    switch ($row_imp["Opt1Type"]) {
                        case "chestlogo":
                            $optType1 = "Chest Logo";
                            $optTeam = stripslashes($row_imp["Opt1Team"]);
                            break;
                        case "pocketlogo":
                            $optType1 = "Pocket Logo";
                            $optTeam = stripslashes($row_imp["Opt1Team"]);
                            break;
                        default:
                            $optType1 = ucfirst($row_imp["Opt1Type"]);
                    }   
                    $imprint_data .= '<tr><td class="impLocation">'.$row_imp["Opt1Loc"].'</td><td class="impType">'.$optType1;
                                
                    if ($optTeam != '') {
                        $imprint_data .= " (Team: ".$optTeam.")";
                    }
                                
                    if ($row_imp["Opt1Color"] != '') {
                        $imprint_data .= " (Color: ".$row_imp["Opt1Color"].")";
                    }
                                
                    if ($row_imp["Opt1Text"] != '') {
                        $imprint_data .= ':<br/>'.str_replace("|","<br/>",$row_imp["Opt1Text"]).' ';
                    }
                    
                    if ($row_imp["Opt2Type"] != '') {
                        $optTeam = '';
                        switch ($row_imp["Opt2Type"]) {
                            case "chestlogo":
                                $optType2 = "Chest Logo";
                                $optTeam = stripslashes($row_imp["Opt2Team"]);
                                break;
                            case "pocketlogo":
                                $optType2 = "Pocket Logo";
                                $optTeam = stripslashes($row_imp["Opt2Team"]);
                                break;
                            default:
                                $optType2 = ucfirst($row_imp["Opt2Type"]);
                        }
                        $imprint_data .= '<br/>'.$optType2;     
                        if ($optTeam != '') {
                            $imprint_data .= " (Team: ".$optTeam.")";
                        }
                                    
                        if ($row_imp["Opt2Color"] != '') {
                            $imprint_data .= " (Color: ".$row_imp["Opt2Color"].") ";
                        }                                   
                    }
                                
                    if ($row_imp["Opt2Text"] != '') {
                        $imprint_data .= ':<br/>'.str_replace("|","<br/>",$row_imp["Opt2Text"]).' ';
                    }
                                
                    $imprint_data .= '</td><td class="impPrice">$'.number_format($row_imp["ImprintPrice"],2).'</td><td class="impImage">';
                    if ($row_imp["Opt1Image"] != '') {
                        $imprint_data .= '<img src="'.$row_imp["Opt1Image"].'" alt="'.$row_imp["Opt1Type"].'" title="'.$row_imp["Opt1Loc"]." - ".$row_imp["Opt1Type"].'" />';
                    }
                                
                    if ($row_imp["Opt2Image"] != '') {
                        $imprint_data .= '&nbsp; <img src="'.$row_imp["Opt2Image"].'" alt="'.$row_imp["Opt2Type"].'" title="'.$row_imp["Opt2Loc"]." - ".$row_imp["Opt2Type"].'" />';
                    }
                                
                    $imprint_data .= '</td></tr>';
                } // end while
                $imprint_data .= '<tr><td class="noBtmBdr"></td><td class="noBtmBdr right">Imprint Total:</td><td class="noBtmBdr">$'.number_format($impPrice, 2).'</td><td class="noBtmBdr"></td></tr></table>';
        ?>
            <tr>
                <td colspan="7"><?=$imprint_data;?></td>
            </tr>
        <?php
                $ordertotal = $ordertotal + $impPrice;
            } else {
                $imprint_data = "";
            }
            // END IMPRINT INFORMATION

            $freeitemHtml = $couponCalc->getSkuFreeItem(false, $shipmess, $row_cart['RootSKU'], 0, $row_cart["Qty"]);
            foreach ($freeitemHtml as $html) { 
                echo $html[0];
            }
        } // end row_image if loop
    } // end main while loop
    
    // get coupon/discount information
    if (isset($_SESSION['discount'])) {
         $discount = $_SESSION['discount'];
    } else {
        $discount = 0.00;
    }
    if (is_nan($discount)) {
        $discount = 0.00;
    }
    
    // add coupon info to the cart listing  
    $sql_cp1 = "SELECT * FROM shopping_cart WHERE $sqlwhere AND `Type`='Coupon'";
    $result_cp1 = mysql_query($sql_cp1);
    while($row_cp1 = mysql_fetch_array($result_cp1)) {
    ?>
    <tr>
        <td class="cartitem">&nbsp;</td>
        <td class="cartitem"><?php echo $row_cp1["ProductName"];?></td>
        <td class="cartitem"><?php echo $row_cp1["ProductID"];?></td>
        <td class="cartitem">-</td>
    <?php if ($isvip == "yes") : ?>
        <td class="cartitem">-</td>
    <?php endif; ?>
        <td class="cartitem">1</td>
        <td class="cartitem">
    <?php 
        $sql_cp = "SELECT * FROM coupons WHERE Code='" . $row_cp1["ProductID"] . "'";
        $resultcp = mysql_query($sql_cp) or die("Coupon Retrieval Error: " . mysql_error());
        $row_cp = mysql_fetch_array($resultcp);
        if ($row_cp["Type"] == "dollar") {
            echo "$" . number_format($row_cp1["Price"], 2);
            echo "</td>";
        } else {
            echo $row_cp1["Price"] . "%";
            echo "</td>";
        }
    ?>
    </tr>
    <?php
    }
    
    // add Certificate info to the Cart
        $sql_cp = "SELECT * FROM shopping_cart WHERE $sqlwhere AND `Type`='Cert'";
        $result_cp = mysql_query($sql_cp);
        while($row_cp = mysql_fetch_array($result_cp)) {
        ?>
        <tr>
            <td class="cartitem">&nbsp;</td>
            <td class="cartitem"><?php echo $row_cp["ProductName"];?></td>
            <td class="cartitem"><?php echo $row_cp["ProductID"];?></td>
            <td class="cartitem">-</td>
        <?php if ($isvip == "yes") : ?>
            <td class="cartitem">-</td>
        <?php endif; ?>
            <td class="cartitem">1</td>
            <td class="cartitem">$<?php echo number_format($row_cp["Price"], 2); ?></td>
        </tr>
        <?php
        }
        
        // add GC info to the Cart
        $sql_cp2 = "SELECT * FROM shopping_cart WHERE $sqlwhere AND `Type`='GC'";
        $result_cp2 = mysql_query($sql_cp2);
        while ($row_cp2 = mysql_fetch_array($result_cp2)) {
            $gcfundstotal += $row_cp2["Price"];
        }
        
        // add VIP memberhsip to cart 
        $sql_vip = "SELECT * FROM shopping_cart WHERE $sqlwhere AND `Type`='VIP'";
        $result_vip = mysql_query($sql_vip) or die("VIP Cart Error: " . mysql_error());
        while ($row_vip = mysql_fetch_array($result_vip)) {
            $sql_vipd = "SELECT Image FROM vip LIMIT 1";
            $result_vipd = mysql_query($sql_vipd);
            $row_vipd = mysql_fetch_assoc($result_vipd);
    ?>
        <tr>
            <td class="cartitem"><img class="cartthumb" src="images/productImages/<?=$row_vipd["Image"];?>" /></td>
            <td class="cartitem"><?=$row_vip["ProductName"];?></td>
            <td class="cartitem"><?=$row_vip["ProductID"];?></td>
            <td class="cartitem">-</td>
    <?php 
        if ($isvip=="yes") { ?>
            <td class="cartitem"></td>
    <?php 
        } 
    ?>
            <td class="cartitem">1</td>
            <td class="cartitem">$<?=number_format($row_vip["Price"],2);?></td>
        </tr>
    <?php
            $ordertotal = $ordertotal + $row_vip["Price"];
        }
        
        $sql_shipst = "SELECT ShippingState FROM shopping_address WHERE SessionID='".session_id()."' LIMIT 1";
        $result_shipst = mysql_query($sql_shipst);
        $row_shipst = mysql_fetch_assoc($result_shipst);
        $sql_tax = "SELECT Tax FROM taxes WHERE State='$row_shipst[ShippingState]' LIMIT 1";
        $result_tax = mysql_query($sql_tax);
        $row_tax = mysql_fetch_assoc($result_tax);
        $totaltax = $taxableamount * ($row_tax["Tax"] / 100);
        $grandtotal = $totaltax + ($ordertotal + $discount);
        if ($grandtotal < 0) {
            $grandtotal = 0;
        }
    ?>
        </table>
        <input type="hidden" id="grandtotal" name="grandtotal" value="">
        <input type="hidden" id="totaltax" name="totaltax" value="<?=$totaltax;?>">
        <input type="hidden" id="totalshipping2" name="totalship" value="">
        <input type="hidden" id="notes" name="notes" value="">
        <input type="hidden" name="referrer" value="<?=$_SESSION['org_referrer'];?>">
        <input type="hidden" id="ordertotal" name="ordertotal" value="<?=$ordertotal;?>">
        <input type="hidden" id="totaldiscount" name="totaldiscount" value="<?=$discount;?>">
        <input type="hidden" id="isvip" name="isvip" value="<?=$isvip;?>">
        <input type="hidden" id="gctotal" name="gctotal" value="<?=$gcfundstotal;?>">
        <input type="hidden" id="weight" name="Weight" value="<?=$totalWeight;?>">
        <input type="hidden" id="ship" name="shipping" value="">
        <textarea id="csmsg" style="display:none;" name="ppmsg"><?=$ppMsg;?></textarea>
        <textarea id="csmsg" style="display:none;" name="dsmsg"><?=$dsMsg;?></textarea>
        <table class="ordering" border="0" align="center" cellpadding="3" cellspacing="0">
        <tr>
            <td width="100%" height="35" bgcolor="#FF0000" colspan="2"><span class="style1"> &nbsp;&nbsp;&nbsp;Shipping Options</span></td>
        </tr>
        <tr>
            <td width="37%" height="35" align="left" valign="top" bgcolor="#FFFFFF"><?=$shippingopt;?></td>
            <td width="63%" height="35" align="right" valign="top" bgcolor="#FFFFFF">
            <table id="ttlOrderBx" border="0" cellpadding="0" cellspacing="5">
            <tr>
                <td width="50%" height="35" align="left" valign="middle">&nbsp;<span class="style3">Order Total</span></td>
                <td width="50%" align="left" valign="middle">&nbsp;<span id="ordertotalval">$<?=number_format($ordertotal, 2);?></span></td>
            </tr>
            <tr>
                <td width="50%" height="35" align="left" valign="middle" bgcolor="#EBEBEB">&nbsp;<span class="style3">Tax<input type="hidden" id="tax" name="tax" value="<?=$row_tax["Tax"];?>" /></span></td>
                <td width="50%" align="left" valign="middle" bgcolor="#EBEBEB">&nbsp;<span id="totaltaxval">$<?=number_format($totaltax, 2);?></span></td>
            </tr>
            <tr>
                <td width="50%" height="35" align="left" valign="middle" bgcolor="#FFFFFF">&nbsp;<span class="style3">Discount</span></td>
                <td width="50%" align="left" valign="middle" bgcolor="#FFFFFF">&nbsp;<span id="totaldiscountval">$<?=number_format($discount, 2);?></span></td>
            </tr>
            <tr>
                <td width="50%" height="35" align="left" valign="middle" bgcolor="#EBEBEB">&nbsp;<span class="style3">Shipping</span></td>
                <td width="50%" align="left" valign="middle" bgcolor="#EBEBEB">&nbsp;<span id="totalshippingval">$0.00</span></td>
            </tr>
            	<tr id="gcFunds">
                <td width="50%" height="35" align="left" valign="middle" bgcolor="#FFFFFF">&nbsp;<span class="style3"><small>GIFT CERTIFICATE</small><br>Payment Applied</span></td>
                <td width="50%" align="left" valign="middle" bgcolor="#FFFFFF">&nbsp;<span id="gcfundsval">$0.00</span></td>
            </tr>
            <?php if ($gcfundstotal > 0) { $grandtotal = $grandtotal - $gcfundstotal; } ?>
            <tr id="gttr">
                <td height="35" align="left" valign="middle">&nbsp;<span class="style3" id="gtspan">Grand Total</span></td>
                <td align="left" valign="middle">&nbsp;<span id="grandtotalval">$<?=number_format($grandtotal, 2);?></span></td>
            </tr>
            </table></td>
        </tr>
        </table>
        <table class="ordering" border="0" align="center" cellpadding="5" cellspacing="5">
        <tr>
            <td width="50%" height="35" align="left" bgcolor="#FF0000"><span class="style1"> &nbsp;&nbsp;&nbsp;Payment Information:</span></td>
            <td width="50%" height="35" align="left" bgcolor="#FF0000"><span class="style1"> &nbsp;&nbsp;&nbsp;Gift Certificate:</span></td>
        </tr>
        <tr>
            <td bgcolor="#FFFFFF"><div id="paymentDIV"><?php
                $sql_chkcredit = "SELECT CreditLine, AccountNumber FROM customers WHERE EmailAddress='$_SESSION[email]' LIMIT 1";
                $result_chkcredit = mysql_query($sql_chkcredit);
                $row_chkcredit = mysql_fetch_assoc($result_chkcredit);
                if ($row_chkcredit["CreditLine"] == '1') {
                    echo '<input type="radio" id="pmOpenAccount" name="paymentmethod" style="font-size:0.875em;" class="required" value="OpenAccount" />&nbsp;Pay with Open Account <small>#' . $row_chkcredit['AccountNumber'] . '</small><br>'; 
                }
            ?>
            <input type="radio" id="pmCreditCard" name="paymentmethod" class="required" style="padding: 5px 0px;font-size:0.875em;" value="CreditCard" />&nbsp;Pay with Credit / Debit card</div></td>
            <td valign="top" width="30%"><table><tr><td><img src="images/gift_cert.jpg" width="75"></td><td><div id="gcError"></div>Enter the Gift Certificate number<br><input type="text" placeholder="enter GC number" id="gcNum" name="gcNum" size="20"><br><br><a class="GCbutton" id="pmGiftCert">APPLY</a> <a class="GCbutton" id="gcBal">Check Balance</a></td></tr></table></td>
        </tr>
        <tr>
            <td><div id="paymenttype" style="width: 100%;"></div></td>
        </tr>
        </table>
        <table class="ordering" border="0" align="center" cellpadding="3" cellspacing="0">
        <tr>
            <td width="63%" height="35" align="left" bgcolor="#FF0000"><span class="style1"> &nbsp;&nbsp;&nbsp;Special Instructions</span></td>
        </tr>
        <tr>
            <td width="63%" height="35" align="left" valign="middle" bgcolor="#FFFFFF"><small><em>If you have any special shipping, delivery or other instructions for SoccerOne Customer Service please enter them here:</em></small><textarea name="orderNotes" id="orderNotes" style="width:99%;height:100px;"></textarea></td>
        </tr>
        </table>
        <table class="ordering" border="0" align="center" cellpadding="3" cellspacing="0">
        <tr>
            <td width="63%" height="35" align="left" bgcolor="#FF0000"><span class="style1"> &nbsp;&nbsp;&nbsp;Terms and Conditions</span></td>
        </tr>
        <tr>
            <td width="60%" height="35" align="left" valign="middle" bgcolor="#FFFFFF"><?php
            $sql_message = "SELECT * FROM messages WHERE Type = 'termsconditions' LIMIT 1";
			$result_message = mysql_query($sql_message);
			$row_msg = mysql_fetch_array($result_message);
			echo $row_msg['Message'];
            ?></td>
        </tr>
        </table>
        <table class="ordering" border="0" align="center" cellpadding="3" cellspacing="0">
        <tr>
            <td width="3%" height="35" align="left" valign="middle" bgcolor="#FFFFFF"><p><input type="checkbox" id="agree" name="agree" /></p></td>
            <td width="97%" align="left" valign="middle" bgcolor="#FFFFFF">I have read, understand and agree with all terms and conditions.</td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: right;"><input type="hidden" name="submitted" value="1" /><input class="shoppingcart" type="button" id="SubmitOrder" name="SubmitOrder" value="Submit Order" /></td>
        </tr>
        </table>
    </form>
<?php   
        // } // end of main while loop
    } else {
?>
    <br /><br />
    <h1><span style="color: rgb(255, 0, 0);">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Sorry, but your shopping cart is empty!</span></h1>
    <br /><br /><br /><br /><br /><br />
<?php 
    }
} 
?>
    </div>    
    <div class="clear"></div>
</div>
    <div class="footer">
        <div class="foot_box"><?php include_once("includes/footer.php"); ?></div>
    </div>
</div>
</body>
<?php mysql_close($conn); ?>