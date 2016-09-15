<?php
	require 'db.php';
	if(isset($_POST["btnUpdate"])) {
		
		foreach($_POST as $key=>$value) {
			$$key = addslashes($value);
		}

		$sql_update  = "UPDATE shipping SET AccountNumber='$AccountNumber', UserName='$UserName', Password='$Password', AccessKey='$AccessKey', boxWeight='$boxWeight' WHERE `Type`='UPS' LIMIT 1";
		if(!mysql_query($sql_update)){
			echo "Error updating Shipping Settings: ".mysql_error();
		}
	}
	
	$sql_ship = "SELECT * FROM shipping WHERE `Type`='UPS'";
	$result_ship = mysql_query($sql_ship);
	$row_ship = mysql_fetch_assoc($result_ship);
?>
  	<form action="" method="post" >
		<table width="100%" border="0" cellpadding="5" cellspacing="2">
        <tr>
            <td colspan="2"><strong>Various Shipping Settings</strong></td>
        </tr>
        <tr>
            <td style="width: 150px;">UPS User Name:</td>
            <td><input type="text" id="UserName" name="UserName" value="<?=$row_ship["UserName"];?>" /></td>
        </tr>
        <tr>
            <td>UPS Password:</td>
            <td><input type="password" id="Password" name="Password" value="<?=$row_ship["Password"];?>" /></td>
        </tr>
        <tr>
            <td>UPS Account Number:</td>
            <td><input type="text" id="AccountNumber" name="AccountNumber" value="<?=$row_ship["AccountNumber"];?>" /></td>
        </tr>
        <tr>
            <td>UPS Access Key:</td>
            <td><input type="text" id="AccessKey" name="AccessKey" value="<?=$row_ship["AccessKey"];?>" /></td>
        </tr>
        <tr>
            <td>Max Box Weight <small>(in lbs)</small>:</td>
            <td><input type="text" id="boxWeight" name="boxWeight" value="<?=$row_ship["boxWeight"];?>" /></td>
        </tr>
        <tr>
            <td><input type="submit" id="btnUpdate" name="btnUpdate" value="Save"/></td>
        </tr>
        </table>
    </form>
<?php
	mysql_close($conn);
	exit();
?>