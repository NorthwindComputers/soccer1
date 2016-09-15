<?php
/***************************************
 * Certificate setup functions
 *
 * Version: 1.2.5
 * By: Richard Tuttle
 * Updated: 10 December 2014
 ***************************************/

// save function
if (isset($_POST["btnSave"])) {
	foreach($_POST as $key=>$value) {
		$$key = $value;
	}
	if ($origValue == '') { 
		$origValue = 0; 
	}
	if ($remainValue == '') { 
		$remainValue = 0; 
	}
	$temp = $codeNum + $origValue;
	$hashcode = md5($temp);
	$sql_add = "INSERT INTO certificate(certType, codeNum, origValue, used, remainValue, hash) VALUES('$certType', '$codeNum', '$origValue', '$used', '$remainValue', '$hashcode')";
	if (!mysql_query($sql_add)) {
		echo "Error adding certificate: " . mysql_error(); exit;
	}
	echo "<script>window.location='settings.php?p=Cert';</script>";
}

// update function
if (isset($_POST["btnUpdate"])) {
	foreach($_POST as $key=>$value) {
		$$key = $value;
	}
	if ($id == '') {
		$sql_update = "UPDATE certificate SET certType='$certType', codeNum='$codeNum', origValue='$origValue', used='$used', orderUsedID='$orderUsedID', remainValue='$remainValue' WHERE codeNum='$codeNum'";
	} else {
		$sql_update = "UPDATE certificate SET certType='$certType', codeNum='$codeNum', origValue='$origValue', used='$used', orderUsedID='$orderUsedID', remainValue='$remainValue' WHERE id='$id'";
	}
	// echo "SQL: " . $sql_update; exit(); // testing only
      
    if (!mysql_query($sql_update)) {
		echo "Error Updating certificate: ". mysql_error(); exit;
	}
	echo "<script>window.location='settings.php?p=Cert';</script>";
}

// new function
if ($_GET["type"] == "new") { 
?>
<script type="text/javascript">
$(document).ready(function() {
	$('#StartDate').datepicker({dateFormat: 'mm/dd/yy'});
    $('#EndDate').datepicker({dateFormat: 'mm/dd/yy'});
});
</script>
<form action="" method="post">
<strong>Certificate Type:</strong><br/><select id="certType" name="certType"><option value="gold">Gold VIP</option><option value="gift">Gift Certificate</option></select>
<table cellpadding="5" cellspacing="2">
<tr>
	<td style="width: 200px;"><strong>Coupon Code:</strong><br/><input type="text" id="Code" name="codeNum" /></td>
	<td><strong>Status:</strong><br/><select id="Status" name="used"><option value="no">Enabled</option><option value="yes">Disabled</option></select></td>
</tr>
<tr>
	<td><strong>Original Amount:</strong><br/><input type="text" id="Amount" name="origValue" /></td>
	<td><strong>Remaining Amount:</strong><br><input type="text" id="remainAmount" name="remainValue"></td>
</tr>
<tr>
	<td colspan="2"><input style="margin-right: 5px;" type="submit" id="btnSave" name="btnSave" value="Save"/><input type="button" id="btnCancel" name="btnCancel" value="Cancel" /></td>
</tr>
</table>
</form>
<script type="text/javascript">
$("#btnCancel").click(function() {
	window.location="settings.php?p=Cert";
});
</script>
<?php		
	exit();
}

// edit function
if ($_GET["type"] == "edit") {
	require "db.php";
	$cid = mysql_real_escape_string($_GET["id"]);
	$certid = mysql_real_escape_string($_GET["cid"]);
	
	// check existance of certifcate by number first
	if ($certid != '' ) {
		$ckCert = "SELECT codeNum FROM certificate WHERE codeNum='$certid' LIMIT 1";
		$ckResult = mysql_query($ckCert);
		$ckRow = mysql_fetch_assoc($ckResult);
		if (empty($ckRow)) {
			echo "<font color=red><strong>SORRY BUT THAT CERTIFICATE NUMBER DOES NOT EXIST!</strong></font>";
			exit();
		} 
	} 
	
	if ($cid) {
		$sql_c = "SELECT * FROM certificate WHERE id='$cid' LIMIT 1";
	} else {
		$sql_c = "SELECT * FROM certificate WHERE codeNum='$certid' LIMIT 1";
	}
	$result_c = mysql_query($sql_c) or die("NO SUCH CERTIFICATE NUMBER error: " . mysql_error());
	$row_c = mysql_fetch_assoc($result_c);
	foreach ($row_c as $key=>$value) {
		$$key = stripslashes($value);
	}
?>
<h2>Certificate Edit</h2>
<form action="" method="post">
<table cellpadding="5" cellspacing="2">
<tr>
	<td style="width: 200px;"><strong>Coupon Code:</strong><br/><input type="hidden" id="id" name="id" value="<?=$cid;?>" /><input type="text" id="Code" name="codeNum" value="<?=$codeNum;?>" /></td>
	<td><strong>Status:</strong><br/>
	<select id="Status" name="used">
		<option <?php if($used == "no") { echo ' Selected="Selected" '; } ?> value="no">Enabled</option>
		<option <?php if($used == "yes") { echo ' Selected="Selected" '; } ?> value="yes">Disabled</option>
	</select></td>
</tr>
<tr>
	<td><strong>Original Amount:</strong><br/>
	<?php
	if ($certType != "gold") {
		echo '<input type="text" id="Amount" name="origValue" value="';
		echo number_format($origValue, 2);
		echo '">';
	} else {
		echo "----";
	}
	?></td>
	<td><?php
	if ($certType != "gold") {
		echo '<strong>Remaining Value:</strong><br/><input type="text" name="remainValue" value="';
		if ($remainValue != NULL) { 
			echo number_format($remainValue, 2); 
		} else { 
			echo number_format($origValue, 2); 
		}
		echo '">';
	} else {
		echo "----";
	}
	?></td>
</tr>
<tr>
	<td><strong>Certificate Type:</strong><br><?php
	if ($certType == "gold") {
		echo "GoldVIP Certificate";
	} else {
		echo "Gift Certificate";
	}
	echo '<input type="hidden" name="certType" value="' . $certType . '">';
	?></td>
	<td><strong>Order(s) Used With:</strong><br><input type="text" name="orderUsedID" value="<?php echo $orderUsedID;?>"></td>
</tr>    
<tr>
	<td colspan="2"><input type="submit" style="margin-right: 5px;" id="btnUpdate" name="btnUpdate" value="Update"/><input type="button" id="btnCancel" name="btnCancel" value="Cancel" /></td>
</tr>
</table>
</form>
<script>
$("#btnCancel").click(function() {
	window.location="settings.php?p=Cert";
});
</script>
<?php
	mysql_close($conn);
	exit();		
}

// delete function
if ($_POST["type"] == "delete") {
	require "db.php";
	$sql_delete = "DELETE FROM certificate WHERE id=$_POST[id] LIMIT 1";
	if (!mysql_query($sql_delete)) {
		echo "Error removing certificate: " . mysql_error();
	} else {
		echo "Certificate Removed!";
	}
	mysql_close($conn);
	exit();
}

if ($_POST["type"] == "vipLevel") {
	require "db.php";
	$vid = mysql_real_escape_string($_POST["vid"]);
	echo '<br><strong>VIP Level:</strong><br>';
	echo '<select id="vipID" name="ApplyOption"><option value="">---</option>';
	$sql_vip = "SELECT * FROM viplevels ORDER BY level";
	$result_vip = mysql_query($sql_vip);
	if ($vid) {
		while ($row_levels = mysql_fetch_array($result_vip)) {
			echo '<option value="' . $row_levels["level"] . '"';
			if ($vid == $row_levels["level"]) {
				echo ' selected="selected"';
			}
			echo '>' . $row_levels["level"] . '</option>';
		}
	} else {
		while ($row_levels = mysql_fetch_array($result_vip)) {
			echo '<option value="' . $row_levels["level"] . '">' . $row_levels["level"] . '</option>';
		}
	}
	echo '</select><br><br>';
	exit();
}

// upload and process excel spreadsheet
if (isset($_POST['upload'])) {
	if ($_FILES["excel_file"]["type"] == "application/vnd.ms-excel") {
		if ($_FILES["excel_file"]["error"] > 0) {
		} else {
			$file_name = "certs-" . rand(10,100) . ".xls";
			if (file_exists("certs_excel/" . $file_name)) {
			} else {
				$file_name = "certs-" . rand(10,100) . ".xls";
				move_uploaded_file($_FILES["excel_file"]["tmp_name"],"certs_excel/" . $file_name);
			}
		}
		function genRanNum($length = 10) {
			$characters = "0123456789";
			$randomString = '';
			for ($i = 0; $i < $length; $i++) {
				$randomString .= $characters[rand(0, strlen($characters) - 1)];
			}
			return $randomString;
		}
		require_once 'Excel/reader.php';
		$data = new Spreadsheet_Excel_Reader();
		$data->setOutputEncoding('CP1251');
		$data->read("certs_excel/".$file_name);
		for ($x = 2; $x <= count($data->sheets[0]["cells"]); $x++) {
			$certType = addslashes($data->sheets[0]["cells"][$x][1]);
			$codeNum = addslashes($data->sheets[0]["cells"][$x][2]);
			$origValue = addslashes($data->sheets[0]["cells"][$x][3]);
			$used = addslashes($data->sheets[0]["cells"][$x][4]);
			// $orderUsedID = addslashes($data->sheets[0]["cells"][$x][5]);
			// $remainValue = addslashes($data->sheets[0]["cells"][$x][6]);
			$temp = $codeNum + $origValue + genRanNum();
			$hashcode = md5($temp);
			$sql = "INSERT INTO certificate (certType, codeNum, origValue, used, orderUsedID, remainValue, hash) VALUES ('$certType', '$codeNum', '$origValue', '$used', NULL, '$origValue', '$hashcode')";
			$query_r = mysql_query($sql) or die("Certificate upload error: " . mysql_error());
		}
	} else {
		echo "Invalid file!";
	}
}

?>
<?php if ($_GET['type'] == 'list' || !isset($_GET['type']) || $_GET['type'] == '') : ?>
<div id="main"><h2>Certificates Administration</h1>
<table cellpadding="5" cellspacing="2">
<tr>
    <td colspan="2"><form action="" method="post" enctype="multipart/form-data">
	Upload file:<br>
	<input name="excel_file" type="file"><br>
	<input name="upload" type="submit" value="Upload">
</form></td>
    <td>&nbsp;</td>
    <td colspan="2"><form action="" method="get">
    <input class="search" type="text" id="certid" name="certid" placeholder="number" size="10"> <input class="search" type="button" style="border:1px solid #bebebe; background-color:#ff6600; width:50px; height:25px; color:#fff;" id="btnSearch" name="btnSearch" value="Search">
    </form></td>
    <td><img src="images/plus.png" class="caddnew" style="float: right; width: 20px; cursor: pointer;" /></td>
</tr>
<tr>
	<td class="headercg" style="width: 20%;">Type</td>
	<td class="headercg" style="width: 25%;">Cert Num</td>
	<td class="headercg" style="width: 15%;">Original Amount</td>
	<td class="headercg" style="width: 15%;">Remaining Value</td>
	<td class="headercg" style="width: 10%";>Status</td>
	<td class="headercg" style="width: 20%;">Action</td>
</tr>
<?php
$sql_coupons = "SELECT * FROM certificate ORDER BY codeNum ASC";
$result_coupons = mysql_query($sql_coupons);
while ($row_coupons = mysql_fetch_array($result_coupons)) {
	$ccode = stripslashes($row_coupons["codeNum"]);
?>
	<tr>
		<td><?php
		// list type
		if ($row_coupons["certType"] == "gift") {
			echo "Gift Certificate";
		} else {
			echo "Gold VIP Certificate";
		}
		?></td>
		<td><?=$ccode;?></td>
		<td><?php
		// list original amount value
		if ($row_coupons["certType"] == "gold") {
			echo "----";
		} else {
			echo "$" . number_format($row_coupons["origValue"], 2);
		}
		?></td>
		<td><?php
		// list remaining value amount
		if (($row_coupons["remainValue"] == NULL) && ($row_coupons["certType"] != "gold")) {
			echo "$" . number_format($row_coupons["origValue"], 2);
		} elseif (($row_coupons["orderUsedID"] != NULL) && ($row_coupons["certType"] == "gift")) {
			// get previous used order totals to determine remaining value
			$total = 0;
			$shipping = 0;
			$array = explode(', ', $row_coupons["orderUsedID"]);
			foreach ($array as $value) {
				$sql = "SELECT * FROM orders WHERE id='" . $value . "'";
				$querySql = mysql_query($sql) or die("Order Info Retrieval Error: " . mysql_error());
				$orderTotal = mysql_fetch_array($querySql);
				$total += $orderTotal["OrderTotal"];
				$shipping += $orderTotal["ShippingTotal"];
			}
			$remaining = $row_coupons["origValue"] - ($total + $shipping);
			
			// update Remaining Value, if necessary
			if ($row_coupons["remainValue"] != $remaining) {
				if ($remaining <= 0) {
					$remaining = 0;
				}
				$sql = "UPDATE certificate SET remainValue='$remaining'";
				if ($remaining == 0) {
					$sql .= ", used='yes'";
				}
				$sql .= " WHERE id='$row_coupons[id]'";
				$result = mysql_query($sql) or die("Remaining Value Update Error: " . mysql_error());
			}
			
			echo "$" .  number_format($remaining, 2);
		} elseif (($row_coupons["remainValue"] != NULL) && ($row_coupons["certType"] == "gift")) {
			echo "$" . number_format($row_coupons["remainValue"],2);
		} else {
			echo "-----";
		}
		?></td>
		<td><?php
			if ($row_coupons["used"] == "yes") {
				echo "<font color='red'>used</font>";
			} else {
				echo "active";
			}
			?></td>
		<td style="text-align: center;"><img class="cedit" id="<?=$row_coupons["id"];?>" style="cursor: pointer; margin-right: 5px;" src="images/E.png"/><img class="cdelete" style="cursor: pointer;" id="<?=$row_coupons["id"];?>" src="images/D.png" /></td>
	</tr>
	<?php 
	} 
	?>
</table>
</div>
<script>
	$("#certid").focus(function() {
		if($(this).val() == 'Search Certificate Num') {
			$(this).val('');
		}
	});
	
	$("#certid").focusout(function() {
		if($(this).val() == '') {
			$(this).val('Search Certificate Num');
		}
	});
	
	$("#btnSearch").click(function() {
		var cid;
		if ($("#certid").val() == 'Search Certificate Num') {
			cid = '';
		} else {
			cid = $("#certid").val();
			window.location = "settings.php?p=Cert&type=edit&cid="+cid;
		}
	});
	
	$(".caddnew").hover(function() {
		$(this).attr("src", "images/plus_hover.png");		

	}, function() {
		$(this).attr("src", "images/plus.png");
	});

	$(".cedit").hover(function() {
		$(this).attr("src", "images/E_hover.png");
	}, function() {
		$(this).attr("src", "images/E.png");
	});

	$(".cdelete").hover(function() {
		$(this).attr("src", "images/D_hover.png");
	}, function() {
		$(this).attr("src", "images/D.png");
	});

	$(".caddnew").click(function() {
		window.location = "settings.php?p=Cert&type=new";
	});

	$(".cedit").click(function() {
		window.location = "settings.php?p=Cert&type=edit&id="+$(this).attr("id");
	});

	$(".cdelete").click(function() {
		var del = confirm("Delete Certificate?");
		if (del) {
			$.post("includes/inc_settingsCert.php", {
				"type":"delete", 
				"id":$(this).attr("id")
		}, function(data){
			alert(data);
			location.reload();
		});
	}
});
</script>
<?php endif; ?>