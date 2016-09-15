<?php
/*****************************************
 * Order reciept screen after processing
 * from Authorize.net occurs    
 *                                              
 * Updated: 19 August 2015            
 * By: Richard Tuttle               
 *****************************************/

require_once 'cpadmin/includes/db.php';
session_start();

$shipping = $_POST["shipping"];
$isvip = $_POST["isvip"];
$paymentMethod = $_POST['paymentmethod'];
$cardNum = $_POST["CardNumber"];
$ot = $_POST["ordertotal"];
$td = $_POST['totaldiscount'];
$tt = $_POST['totaltax'];
$ts = $_POST['totalshipping'];
$ship = $_POST['shipping'];
$gt = $_POST['grandtotal'];
$ct = $_POST['CardType'];
$noc = $_POST['NameOnCard'];
$ed = $_POST['ExpDate'];
$sc = $_POST['SecurityCode'];
$notes = addslashes($_POST['orderNotes']);
$wt = $_POST['Weight'];
$submitted = $_POST['submitted'];
$gcNum = $_POST['gcNum'];
$gcTotal = $_POST['gctotal'];

// create random GC number
function generateGCnum($length = 10) {
    $characters = "0123456789";
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}
      
    
$sql_order = "INSERT INTO orders(EmailAddress, OrderDate, OrderStatus, OrderTotal, Discount, Tax, ShippingTotal, ShippingMethod, gcTotal, GrandTotal, CardType, NameOnCard, CCNum, ExpDate, SecurityCode, OrderNotes, WeightTotal, referrer) VALUES('$_SESSION[email]', current_date, 'Pending', '$ot', '$td', '$tt', '$ts', '$ship', '$gcTotal', '$gt', '$ct', '$noc', '$cardNumSafe', '$ed', '$sc', '$notes', '$wt', '$_SESSION[org_referrer]')"; 

// echo "SQL: " . $sql_order; exit; // testing use only
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
                $row_items[$unitprice] = $gcTotal;
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
            // echo "SQL: " . $sql_additem . "<br />"; exit;
            mysql_query($sql_additem) or die("Orders Items Insertion Error: " . mysql_error());
                
            // did customer purchase a GC?
            $x = 1;
            while ($x <= $row_items['Qty']) {
                $buyGC = $row_items["RootSKU"];
                $value = $row_items['Price'];
                $z = substr($buyGC, 0, 3);
                $newGCnum = "GC-";
                if ($z == "GC-") {
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
                    $sql_addGC = "INSERT INTO certificate(certType, codeNum, origValue, used, remainValue, hash) VALUES('gift', '$newGCnum', '$value', 'no', '$value', '$hashcode')";
                    // echo "--== DEBUG ==--<br>temp = " . $temp . "<br>hashcode = " . $hashcode . "<br>SQL: " . $sql_addGC; exit(); // testing only
                    if (mysql_query($sql_addGC)) {
                        // create new GC PDF and send customer email about GC
                        $gcHeaders = "MIME-Version: 1.0\r\n";
                        $gcHeaders .= "From: SoccerOne Customer Service <customerservice@soccerone.com>\r\n"; 
                        $gcHeaders .= "Content-type: text/html; charset: utf8\r\n"; 
                        $gcHeaders .= "Reply-To: SoccerOne <customerservice@soccerone.com>\r\n";
                        $gcHeaders .= "X-Mailer: PHP/" . phpversion() . "\r\n";
                        $gcSubject = "SoccerOne Gift Certificate";
                        $gcMsg = "<h2>Gift Certificate Purchased</h2>To view and/or print your SoccerOne Gift Certificate, please click this link: <a href='https://soccerone.com/giftcert.php?h=$hashcode'>YOUR SOCCERONE GIFT CERTIFICATE IS READY!</a><br><br><br><i>------ Gift Certificate Details --------</i><br>Certificate Number: $newGCnum<br>Certificate Value: \$$value<br>Expiration Date: Never<br>----------------------------------------------";
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
                if ($td > $currentTotal) {
                    $remain = $td - $currentTotal;
                    $rmV = number_format($remain, 2);
                    $sql_certUpdate .= ", remainValue='$rmV'";
                } else {
                    $sql_certUpdate .= ", remainValue='0', used='yes'";
                }
                $sql_certUpdate .= " WHERE codeNum='" . $row_items["ProductID"] . "'";
                // echo "SQL: " . $sql_certUpdate; exit(); // testing only
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
                    // echo "SQL: " . $sql_updateqty; exit; // testing use only
                    mysql_query($sql_updateqty) or die("Qty Update Error: " . mysql_error());
                    $sql_availQty = "UPDATE products SET AvailableQty = (SELECT SUM(Inventory) AS Stock FROM product_options WHERE ProductID=$row_items[ProductID]) WHERE id=$row_items[ProductID] LIMIT 1";
                    // echo "SQL: " . $sql_availQty; exit; // testing use only
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
                // echo "SQL: " . $sql_setvip . "<br />"; exit; // testing only
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
        $s1msg .= "------------------------------<br><br><u>ORDER INFORMATION</u><br />Order total: \$" . $ot;
        if ($td != 0)
            $s1msg .= "<br>Discount applied: \$" . $td;
        if ($tt >= 0.01)
            $s1msg .= "<br>Total tax: \$" . $tt;
        if ($ts >= 0.01)
            $s1msg .= "<br>Total shipping: \$" . $ts . " via " . $ship;
        if ($gcTotal > 0)
            $s1msg .= "<br>Total Gift Certificate payment: \$" . $gcTotal;
        $s1msg .= "<br>Grand total charged: \$" . $gt;
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
        $s1msg .= "<br><br>Please logon to the Administration Portal at your earliest convenience to see new order " . $_SESSION["orderid"] . "'s details.<br><br>The user logged in from IP Address: " . $_SERVER['REMOTE_ADDR'];
        
        // send emails to appropriate people based upon status of site utlizing
        $sql_status = "SELECT id FROM status WHERE current='yes' LIMIT 1";
        $result_status = mysql_query($sql_status);
        $row_status = mysql_fetch_assoc($result_status);
        // echo "DEBUG: current = " . $row_status['id']; exit; // testing use only
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
        // echo "--==DEBUG==--<br>Order Total: $" . $ot . "<br>Grand Total: $" . $gt; // TESTING ONLY
        }
    }
// } // end submit check