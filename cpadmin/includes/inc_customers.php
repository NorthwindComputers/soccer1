<?php
/**
 * Customer Admin Detail include file
 *
 * Updated: 22 September 2015
 * By: Richard Tuttle
 */

require_once 'db.php';

// main customer detail listing page
if ($_POST["type"] == "list") {
	// check for customer duplicates
	$dupeSQL = "SELECT customers.id, customers.FirstName, customers.LastName, customers.EmailAddress FROM customers INNER JOIN (SELECT EmailAddress FROM customers GROUP BY EmailAddress HAVING count(EmailAddress) > 1) dupes ON customers.EmailAddress = dupes.EmailAddress ORDER BY customers.EmailAddress";
	$dupeResult = mysql_query($dupeSQL) or die("ERROR: duplicate check error - " . mysql_error());
	$dupeNum = mysql_num_rows($dupeResult);
	if ($dupeNum > 0) {
		echo "<font color='#FF0000'><strong>WARNING - DUPLICATE CUSTOMERS FOUND!</strong></font><br><ul>";
		while ($dupeRow = mysql_fetch_array($dupeResult)) {
			echo "<li><a href='customers.php?id=$dupeRow[id]'>$dupeRow[id]</a> - " . $dupeRow["FirstName"] . " " . $dupeRow["LastName"] . "</li>";
		}
		echo "</ul>";
	}
?>
	<form action="" method="post">
	<select id="totalview" name="totalview" onChange="qtyLoad(1);">
    	<option value="1000">1000 customers</option>
    	<option value="2000">2000 customers</option>
    	<option value="3000">3000 customers</option>
    	<option value="4000">4000 customers</option>
    	<option value="5000">5000 customers</option>
	</select>
	</form>
<?php
	$items = mysql_real_escape_string($_POST["totalview"]);
	$pager = mysql_real_escape_string($_POST["pager"]);
	if ($items == '') { 
		$items = 1000; 
	}
	if ($pager == '') { 
		$pager = 1; 
	}
	$offset = ($pager - 1) * $items;
	
	// pager ==========================================================
	$sql_total = "SELECT id AS totalCustomers FROM customers";
	$result_total = mysql_query($sql_total);
	$num_total = mysql_num_rows($result_total);
	$maxpage = @ceil($num_total / $items);
	if ($num_total > 0) {
		$pgr_text = '<select onchange="qtyLoad(this.value);" name="page" id="page">';
		if ($pager > 1) {
			$pgr_text .= '<option value="'.($pager - 1).'">[prev]</option>';
		}
		for ($pgnum = 1; $pgnum <= $maxpage; $pgnum++) {
			$pgr_text .= '<option value="'.$pgnum.'"';
			if ($pgnum == $pager) {
				$pgr_text .= " selected='selected'";
			}
			$pgr_text .= ">page ".$pgnum."</option>";
		}
		$pgr_text .= '</select>';
	}
	echo $pgr_text;
?>
<style type="text/css">
select.search {
	border: 1px solid #BEBEBE;
    height: 25px;
    width: 90%;
}
</style>
<table cellpadding="5" cellspacing="1" width="980px">
<tr>
	<td class="row1" style="text-align: center;" colspan="2"><table style="width:100%"><tr><td style="text-align: center;"><input class="search" type="text" id="scustomerid" name="scustomerid" value="Search Customer ID" /></td><td style="text-align: center;"><input class="search" type="text" id="sname" name="sname" value="Search By Customer Name" /></td><td style="text-align: center;">
	<select name="customer_group" id="customer_group" class="search">
	<option value="">Search by Customer Group</option>
	<?php 
		$sql="select * from customer_group order by GroupName";
		$query= mysql_query($sql); 
		while($row=mysql_fetch_array($query)) {
	?>
	<option value="<?php echo $row['GroupName'];?>"><?php echo $row['GroupName']; ?></option>
	<?php } ?>
	</select></td></tr></table></td>
	<td class="row1" style="text-align: center;"><input class="search" type="text" id="semail" name="semail" value="Search By Email" /></td>
</tr>
<tr>
	<td colspan="2"><form action="" method="post" enctype="multipart/form-data"><div><div style="float:left;"><label style="margin:0px !important; width:175px !important;">Upload customer (excell file):</label><input name="excell_file" id="excell_file" type="file" /></div><div style="float:left"><input name="upload" id="upload" type="submit" value="Upload" style="float: right; border: 1px solid #bebebe; background-color: #ff6600; width: 120px; height: 25px; color: #fff;" /></div></div></form></td>
	<td class="row1"><input type="button" style="float: right; border: 1px solid #bebebe; background-color: #ff6600; width: 120px; height: 25px; color: #fff;" id="btnSearch" name="btnSearch" value="Search" /></td>
</tr>
</table>
<script type="text/javascript">
$(function() {
	$('form').jqTransform({imgPath:'../jqtransformplugin/img/'});
});

$("#page").change(function() {
	load("includes/inc_customers.php", {
		"type":"customers",
		"offset":"<?=$offset;?>",
		"items":"1000"
	});
});

$("#scustomerid").focus(function() {
	if($(this).val() == 'Search Customer ID') {
		$(this).val('');
	}
});

$("#sname").focus(function() {
	if($(this).val() == 'Search By Customer Name') {
		$(this).val('');
	}
});

$("#semail").focus(function() {
	if($(this).val() == 'Search By Email') {
		$(this).val('');
	}
});

$("#scustomerid").focusout(function() {
	if($(this).val() == '') {
		$(this).val('Search Customer ID');
	}
});

$("#sname").focusout(function() {
	if($(this).val() == '') {
		$(this).val('Search By Customer Name');
	}
});

$("#semail").focusout(function() {
	if($(this).val() == '') {
		$(this).val('Search By Email');
	}
});

// was the Search button clicked?
$("#btnSearch").click(function() {
	$("#customerlist").html('<img src="images/loader.gif" />');
	var oid;
	var oname;
	var ogroup;
	var oemail;

	// search by customer id number - WORKS
	if($("#scustomerid").val() == 'Search Customer ID') {
		oid = '';
	} else {
		oid = $("#scustomerid").val();
	}

	// search by customer name - WORKS
	if($("#sname").val() == 'Search By Customer Name') {
		oname = '';
	} else {
		// oname = $("#sname").val();
		var lastWord = function(o) {
			var ar = o.split(" ");
			return ar.pop();
		};
		var searchWord = $("#sname").val();
		oname = lastWord(searchWord);
	}

	// search by customer group
	if($("#customer_group").val() == 'Search by Customer Group') {
		ogroup = '';
	} else {
		ogroup = $("#customer_group").val();
	}

	// search by customer email address - WORKS
	if($("#semail").val() == 'Search By Email') {
		oemail = '';
	} else {
		oemail = $("#semail").val();
	}

	$("#customerlist").load("includes/inc_customers.php", {
		"type":"customers", 
		"id":oid, 
		"name":oname, 
		"customer_group":ogroup,
		"email":oemail
	});
});

$("#customerlist").load("includes/inc_customers.php", {
	"type":"customers",
	"offset":"<?=$offset;?>",
	"items":"<?=$items;?>"
});
</script>
<form action="" method="post"><div id="customerlist"><img src="images/loader.gif" /></div></form>
<?php
	mysql_close($conn);
	exit();
} // end "list" display

if($_POST["type"] == "customers") {
	$id = mysql_real_escape_string($_POST["id"]);
	$name = mysql_real_escape_string($_POST["name"]);
	$email = mysql_real_escape_string($_POST["email"]);
	$customer_group = mysql_real_escape_string($_POST["customer_group"]);
?>
	<table cellpadding="5" cellspacing="1" width="980px" style="margin-top: 20px;">
	<tr>
		<td colspan="8" class="headersmain">Customer Manager
		<input type="button" style="float: right; border: 1px solid #bebebe; background-color: #ff6600; width: 120px; height: 25px; color: #fff; margin-left: 10px;" id="btnExport" name="btnExport" value="Export to Excel" />
		<input type="button" style="float: right; border: 1px solid #bebebe; background-color: #ff6600; width: 120px; height: 25px; color: #fff;" onClick="window.location='customers.php?id=new'" id="btnNew" name="btnNew" value="Add New" /></td>
	</tr>
	<tr>
		<td class="headers" style="width: 100px;">Customer ID</td>
		<td class="headers" style="width: 300px;">Name</td>
		<td class="headers" style="width: 200px;">Email Address</td>
		<td class="headers" style="width: 150px;">Phone</td>
		<td class="headers" style="width: 130px;">Status</td>
		<td class="headers" style="width: 100px;">Total Purchase</td>
		<td class="headers" style="width: 40px;"></td>
	</tr>
<?php
	if (isset($_POST['offset'])) {
		$offset = mysql_real_escape_string($_POST['offset']);
	} else {
		$offset = 0;
	}
	
	if (isset($_POST['items'])) {
		$items = mysql_real_escape_string($_POST['items']);
	} else {
		$items = 1000;
	}
		
	$sql_customers  = "SELECT id, FirstName, LastName, EmailAddress, Telephone, Status FROM customers WHERE ";
	if($id != '') {
		$sql_customers .= " id=$id AND ";
	}
	if($name != '') {
		$sql_customers .= " (FirstName LIKE '%$name%' OR LastName LIKE '%$name%') AND ";
	}
	if($email != '') {
		$sql_customers .= " EmailAddress LIKE '%$email%' AND ";
	}
	if($customer_group != '') {
		$sql_customers .= " CustomerGroup = '$customer_group' AND ";
	}
	if(substr($sql_customers, -6) == "WHERE ") {
		$sql_customers = substr($sql_customers, 0, -6);
	} else {
		$sql_customers = substr($sql_customers, 0, -4);
	}
	$sql_customers .= " ORDER BY id DESC LIMIT " .$offset.", ".$items;
	
	// echo $sql_customers; exit; // testing use only
	
	$result_customers = mysql_query($sql_customers) or die("Customer List error: " . mysql_error());
?>
<?php
	$r_num = 1;
	while ($row_customers = mysql_fetch_array($result_customers)) {
		if ($r_num == 1) {
			$color = "row1";
			$r_num++;
		} else {
			$color = "row2";
			$r_num = 1;
		}
?>
	<tr>
		<td class="<?=$color;?>" style="text-align: center; font-weight: bold;"><a href="customers.php?id=<?=$row_customers["id"];?>"><?=$row_customers["id"];?></a></td>  
		<td class="<?=$color;?>"><?=$row_customers["FirstName"]." ".$row_customers["LastName"];?></td>
		<td class="<?=$color;?>"><?=$row_customers["EmailAddress"];?></td>
		<td class="<?=$color;?>"><?=$row_customers["Telephone"];?></td>
		<td class="<?=$color;?>"><?=$row_customers["Status"];?></td>
		<td class="<?=$color;?>">
		<?php
		$sql_ototal = "SELECT SUM(GrandTotal) AS Total FROM orders WHERE EmailAddress='$row_customers[EmailAddress]'";
		$result_ototal = mysql_query($sql_ototal);
		$row_ototal = mysql_fetch_assoc($result_ototal);
		echo "$".number_format($row_ototal["Total"], 2);
		?>
		</td>
		<td class="<?=$color;?>" style="text-align: center;"><img class="delorder" style="cursor: pointer; width: 17px;" src="images/delete.png" id="<?=$row_customers["id"];?>" /></td>
	</tr>
<?php
	} // end while
?>
	</table>
	<script type="text/javascript">
	$(".delorder").click(function() {
		var del = confirm('Delete Customer?');
		if(del) {
			$.post('includes/inc_customers.php', {
				"type":"delete", 
				"id":$(this).attr("id")
			}, function(data) {
				alert(data);
				$("#customerlist").html('<img src="images/loader.gif" />');
				var oid;
				var oname;
				var odate;
				
				if($("#scustomerid").val() == 'Search Customer ID') {
					oid = '';
				} else {
					oid = $("#scustomerid").val();
				}
		
				if($("#sname").val() == 'Search By Customer Name') {
					oname = '';
				} else {
					oname = $("#sname").val();
				}

				if($("#semail").val() == 'Search By Email') {
					oemail = '';
				} else {
					oemail = $("#semail").val();
				}

				$("#customerlist").load("includes/inc_customers.php", {
					"type":"customers", 
					"id":oid, 
					"name":oname, 
					"email":oemail
				});
			});
		}
	});

	// was the Export button clicked?
	$("#btnExport").click(function() {
		var oid;
		var oname;
		var odate;

		if($("#scustomerid").val() == 'Search Customer ID') {
			oid = '';
		} else {
			oid = $("#scustomerid").val();
		}

		if($("#sname").val() == 'Search By Customer Name') {
			oname = '';
		} else {
			oname = $("#sname").val();
		}

		if($("#semail").val() == 'Search By Email') {
			oemail = '';
		} else {
			oemail = $("#semail").val();
		}

		window.location='includes/export.php';
	});
	</script>
	<?php
	mysql_close($conn);
	exit();
} // end "customers" display

if($_POST["type"] == "delete") {
	$customer_email="";
	$query_c=mysql_query("select * from customers where id=$id");
	if($query_c) {
		$row=mysql_fetch_array($query_c);
		$customer_email=$row['EmailAddress'];
	}

	$query_0=mysql_query("select * from orders where EmailAddress='".$customer_email."'");
	if($query_0) {
		$row2=mysql_fetch_array($query_0);
		$order_id=$row2['id'];
	}

	$query_ss=mysql_query("select * from shopping_cart where EmailAddress='".$customer_email."'");
	while($row3=mysql_fetch_array($query_ss)) {
		$session_ids=$row3['SessionID'];
		$sqlsss="DELETE FROM  shopping_address where SessionID='".$session_ids."'";
		mysql_query($sqlsss);
	}

	$id = $_POST["id"];
	$sql_del = "DELETE FROM customers WHERE id=$id LIMIT 1";
	if(!mysql_query($sql_del)) {
		echo "error deleting customer: ".mysql_error();
	} else {
		$sql2="DELETE FROM orders where EmailAddress='".$customer_email."'";
		mysql_query($sql2);
		if($order_id>0) {
			$sql3="DELETE FROM  orders_address where OrderID=".$order_id;
			mysql_query($sql3);
		}
		
		if($order_id>0) {
			$sql4="DELETE FROM orders_items where OrderID=".$order_id;
			mysql_query($sql4);
		}

		$sql5="DELETE FROM shopping_cart where EmailAddress='".$customer_email."'";
		mysql_query($sql5);
		echo "Customer Deleted!";
	}
	
	mysql_close($conn);
	exit();
} // end "delete" view

// customer detailed view
if ($_POST["type"] == "details") {
	$id = mysql_real_escape_string($_POST["id"]);
	$sql_cust = "SELECT * FROM customers WHERE id=$id LIMIT 1";
	$result_cust = mysql_query($sql_cust);
	$row_cust = mysql_fetch_assoc($result_cust);
?>
	<form action="" method="post">
	<table cellpadding="5" cellspacing="1" width="980px">
	<tr>
		<td colspan="2" class="subheader" style="font-size: 14px;">Customer Info</td>
	</tr>
	<tr>
		<td class="row1" style="width: 40%; font-weight: bold;">First Name</td>
		<td class="row1" style="width: 60%;"><input type="hidden" id="id" name="id" value="<?=$row_cust["id"];?>" />
		<input type="text" class="customers" id="FirstName" name="FirstName" value="<?=$row_cust["FirstName"];?>" /></td>
	</tr>
	<tr>
		<td class="row2" style="width: 40%; font-weight: bold;">Last Name</td>
		<td class="row2" style="width: 60%;"><input type="text" class="customers" id="LastName" name="LastName" value="<?=$row_cust["LastName"];?>" /></td>
	</tr>
	<tr>
		<td class="row1" style="width: 40%; font-weight: bold;">Phone Number</td>
		<td class="row1" style="width: 60%;"><input type="text" class="customers" id="Telephone" name="Telephone" value="<?=$row_cust["Telephone"];?>" /></td>
	</tr>
	<tr>
		<td class="row2" style="width: 40%; font-weight: bold;">Email Address</td>
		<td class="row2" style="width: 60%;"><input type="text" class="customers" id="EmailAddress" name="EmailAddress" value="<?=$row_cust["EmailAddress"];?>" /></td>
	</tr>
	<tr>
		<td class="row1" style="width: 40%; font-weight: bold;">Customer Group</td>
		<td class="row1" style="width: 60%;"><select id="CustomerGroup" name="CustomerGroup">
		<option value="">Select Customer Group...</option>
		<?php
		$sql_cg = "SELECT GroupName FROM customer_group ORDER BY GroupName";
		$result_cg = mysql_query($sql_cg);
		while($row_cg = mysql_fetch_array($result_cg)) {
			if($row_cust["CustomerGroup"] == $row_cg["GroupName"]) {
				$selected = ' selected="selected" ';
			} else {
				$selected = '';
			}
		echo "<option value=\"$row_cg[GroupName]\" $selected>$row_cg[GroupName]</option>";
		}	
		?>
		</select></td>
	</tr>
	<tr>
		<td class="row2" style="width: 40%; font-weight: bold;">VIP</td>
	 	<td class="row2" style="width: 60%;">Number - <input type="text" class="customers" style="margin-bottom: 5px;" id="VIPNum" name="VIPNum" value="<?=$row_cust["VIPNum"];?>" /> 
		<script type="text/javascript">
		$(function() {
			$("#ExpDate").datepicker({dateFormat: "yy-mm-dd"});
			$("#VIPRenewDate").datepicker({dateFormat: "yy-mm-dd"});
		});
		</script>
		<br/>VIP Date - <input type="text" class="customers" style="margin-bottom: 5px;" id="VIPDate" name="VIPDate" value="<?=$row_cust['VIPDate'];?>" /><br/>Exp Date - <input type="text" class="customers" style="margin-bottom: 5px;" id="ExpDate" name="ExpDate" value="<?=$row_cust['VIPExpDate'];?>" /><br/>Ren.Date - <input type="text" class="customers" style="margin-bottom: 5px;" id="VIPRenewDate" name="VIPRenewDate" value="<?=$row_cust['VIP_renewal_date'];?>" /></td>
	</tr>
	<tr>
		<td class="row1" style="width: 40%; font-weight: bold;">VIP Level</td>
		<td class="row1" style="width: 60%;"><select id="VIPLevel" name="VIPLevel">
		<option value="0">Select VIP Level...</option>
		<?php
		$sql_level = "SELECT * FROM viplevels ORDER BY levelnum";
		$result_level = mysql_query($sql_level);
		while($row_level = mysql_fetch_array($result_level)) {
			if($row_level["levelnum"] == $row_cust["VIPLevel"]) {
				$selected = ' selected="selected" ';
			} else {
				$selected = '';
			}
			echo "<option value=\"$row_level[id]\" $selected>$row_level[level]</option>";
		}
		?>
		</select></td>
	</tr>
	<tr>
		<td class="row2" style="width: 40%; font-weight: bold;">Password</td>
		<td class="row2" style="width: 60%;"><input type="button" id="resetpassword" name="resetpassword" value="Reset Password" /></td>
	</tr>
	<tr>
		<td class="row1" style="width: 40%; font-weight: bold;">Account Name</td>
		<td class="row1" style="width: 60%;"><input type="text" class="customers" id="AccountName" name="AccountName" value="<?=$row_cust["AccountName"];?>" /></td>
	</tr>
	<tr>
		<td class="row2" style="width: 40%; font-weight: bold;">Customer Number</td>
		<td class="row2" style="width: 60%;"><input type="text" class="customers" id="CustomerNumber" name="CustomerNumber" value="<?=$row_cust["CustomerNumber"];?>" /></td>
	</tr>
	<tr>
		<td class="row1" style="width: 40%; font-weight: bold;">Account Number</td>
		<td class="row1" style="width: 60%;"><input type="text" class="customers" id="AccountNumber" name="AccountNumber" value="<?=$row_cust["AccountNumber"];?>" /></td>
	</tr>
	<tr>
		<td class="row2" style="width: 40%; font-weight: bold;">Credit Line</td>
		<td class="row2" style="width: 60%; text-align: left;"><input type="checkbox" class="customers" id="CreditLine" name="CreditLine" value="true" <?php if($row_cust["CreditLine"]=='1') { echo ' checked="checked" '; } ?> /></td>
	</tr>
	</table>
	<script type="text/javascript">
	$("#resetpassword").click(function() {
		var resetpass = confirm("Reset Password?");
		if (resetpass) {
			$.post('includes/inc_customers.php', {
				'type':'reset',
				'cid':'<?=$row_cust["id"];?>'
			}, function(data) { 
				alert(data); 
			});
		}
	});
	</script>
	<table cellpadding="5" cellspacing="1" width="980px" style="margin-top: 20px;">
	<tr>
		<td class="subheader">Billing Address</td>
	</tr>
	<tr>
		<td class="row1"><input type="text" class="customers" style="margin: 5px; width: 300px;" id="BillingAddress" name="BillingAddress" value="<?=$row_cust["BillingAddress"];?>" /><br/><input type="text" class="customers" style="margin: 5px;" id="BillingCity" name="BillingCity" value="<?=$row_cust["BillingCity"];?>" /><select class="customers" style="margin: 5px; height: 25px;" id="BillingState" name="BillingState" ><option value="">Select State...</option>
		<?php
		$sql_states = "SELECT State, Abbreviation FROM states ORDER BY State";
		$result_states = mysql_query($sql_states);
		while($row_states = mysql_fetch_array($result_states)) {
			if($row_cust["BillingState"] == $row_states["Abbreviation"]) {
				$selected = ' selected="selected"';
			} else {
				$selected = '';
			}
			echo "<option value=\"$row_states[Abbreviation]\" $selected>$row_states[State]</option>";
		}
		?>
		</select>
		<input type="text" class="customers" style="margin: 5px;" id="BillingZip" name="BillingZip" value="<?=$row_cust["BillingZip"];?>" /></td>
	</tr>
	</table>
	<table cellpadding="5" cellspacing="1" width="980px">
	<tr>
		<td class="subheader">Shipping Address</td>
	</tr>
	<tr>
		<td class="row1"><span style="margin-left: 5px;">First Name: </span><input type="text" class="customers" style="margin: 5px;" id="ShippingFirstName" name="ShippingFirstName" value="<?=$row_cust["ShippingFirstName"];?>" /><span style="margin-left: 5px;">Last Name: </span><input type="text" class="customers" style="margin: 5px;" id="ShippingLastName" name="ShippingLastName" value="<?=$row_cust["ShippingLastName"];?>" /><br/><input type="text" class="customers" style="margin: 5px; width: 300px;" id="ShippingAddress" name="ShippingAddress" value="<?=$row_cust["ShippingAddress"];?>" /><br/><input type="text" class="customers" style="margin: 5px;" id="ShippingCity" name="ShippingCity" value="<?=$row_cust["ShippingCity"];?>" /><select class="customers" style="margin: 5px; height: 25px;" id="ShippingState" name="ShippingState" ><option value="">Select State...</option>
		<?php
		$sql_states = "SELECT State, Abbreviation FROM states ORDER BY State";
		$result_states = mysql_query($sql_states);
		while($row_states = mysql_fetch_array($result_states)) {
			if($row_cust["ShippingState"] == $row_states["Abbreviation"]) {
				$selected = ' selected="selected"';
			} else {
				$selected = '';
			}
			echo "<option value=\"$row_states[Abbreviation]\" $selected>$row_states[State]</option>";
		}
		?>
		</select>
		<input type="text" class="customers" style="margin: 5px;" id="ShippingZip" name="ShippingZip" value="<?=$row_cust["ShippingZip"];?>" /></td>
	</tr>
	</table>
	<table cellpadding="5" cellspacing="1" width="980px">
	<tr>
		<td><input type="submit" class="customers" style="float: right;" id="btnUpdate" name="btnUpdate" value="Save Changes" /></td>
	</tr>
	</table>
	</form>
    <table cellpadding="5" cellspacing="1" width="980px" style="margin-top: 20px;">
	<tr>
		<td colspan="7" class="headersmain">Order Info</td>
	</tr>
	<tr>
		<td class="headers" style="width: 100px;">Order ID</td>
		<td class="headers" style="width: 150px;">Date</td>
		<td class="headers" style="width: 150px;">Status</td>
		<td class="headers" style="width: 150px;">Order Total</td>
		<td class="headers" style="width: 100px;">Tax</td>
		<td class="headers" style="width: 100px;">Shipping</td>
		<td class="headers" style="width: 100px;">Grand Total</td>
	</tr>
	<?php
		$sql_order = "SELECT * FROM orders WHERE EmailAddress='$row_cust[EmailAddress]' ORDER BY id";
		$result_order = mysql_query($sql_order);
		$row_n = 1;
		while($row_order = mysql_fetch_array($result_order)) {
			if($row_n == 1) {
				$class = "row1";
				$row_n++;
			} else {
				$class = "row2";
				$row_n = 1;
			}
		?>
		<tr>
			<td class="<?=$class;?>" style="text-align: center;"><a href="orders.php?id=<?=$row_order["id"];?>"><?=$row_order["id"];?></a></td>
			<td class="<?=$class;?>" style="text-align: center;"><?=$row_order["OrderDate"];?></td>
			<td class="<?=$class;?>" style="text-align: center;"><?=$row_order["OrderStatus"];?></td>
			<td class="<?=$class;?>" style="text-align: center;">$<?=number_format($row_order["OrderTotal"], 2);?></td>
			<td class="<?=$class;?>" style="text-align: center;">$<?=number_format($row_order["Tax"],2);?></td>
			<td class="<?=$class;?>" style="text-align: center;">$<?=number_format($row_order["ShippingTotal"],2);?></td>
			<td class="<?=$class;?>" style="text-align: center;">$<?=number_format($row_order["GrandTotal"],2);?></td>
		</tr>
		<?php
		}
	?>
	</table>
	<?php
	mysql_close($conn);
	exit();
} // end "details" view

// enter a new customer's information
if($_POST["type"] == "new") {
	?>
	<form action="" method="post">
	<table cellpadding="5" cellspacing="1" width="980px">
	<tr>
		<td colspan="2" class="subheader" style="font-size: 14px;">Customer Info</td>
	</tr>
	<tr>
		<td class="row1" style="width: 40%; font-weight: bold;">First Name</td>
		<td class="row1" style="width: 60%;"><input type="text" class="customers" id="FirstName" name="FirstName" value="" /></td>
	</tr>
	<tr>
		<td class="row2" style="width: 40%; font-weight: bold;">Last Name</td>
		<td class="row2" style="width: 60%;"><input type="text" class="customers" id="LastName" name="LastName" value="" /></td>
	</tr>
	<tr>
		<td class="row1" style="width: 40%; font-weight: bold;">Phone Number</td>
		<td class="row1" style="width: 60%;"><input type="text" class="customers" id="Telephone" name="Telephone" value="" /></td>
	</tr>
	<tr>
		<td class="row2" style="width: 40%; font-weight: bold;">Email Address</td>
		<td class="row2" style="width: 60%;"><input type="text" class="customers" id="EmailAddress" name="EmailAddress" value="" /></td>
	</tr>
	<tr>
		<td class="row1" style="width: 40%; font-weight: bold;">Customer Group</td>
		<td class="row1" style="width: 60%;"><select class="customers" id="CustomerGroup" name="CustomerGroup">
		<option value="">Select Customer Group...</option>
		<?php
		$sql_cg = "SELECT GroupName FROM customer_group ORDER BY GroupName";
		$result_cg = mysql_query($sql_cg);
		while($row_cg = mysql_fetch_array($result_cg)) {
			echo "<option value=\"$row_cg[GroupName]\">$row_cg[GroupName]</option>";
		}
		?>
		</select></td>
	</tr>
	<script>
		$(function() {
			$("#VIPDate").datepicker({
				dateFormat: "yy-mm-dd"
			});
			$("#ExpDate").datepicker({
				dateFormat: "yy-mm-dd"
			});
			$("#VIPRenewDate").datepicker({
				dateFormat: "yy-mm-dd"
			});
		});
		</script>
	<tr>
		<td class="row2" style="width: 40%; font-weight: bold;">VIP</td>
		<td class="row2" style="width: 60%;">Number - <input type="text" style="margin-bottom: 5px;" class="customers" id="VIPNum" name="VIPNum" value="" /><br />
		VIP Date - <input type="text" class="customers" style="margin-bottom: 5px;" id="VIPDate" name="VIPDate" value="" /><br/>
		Exp Date - <input type="text" class="customers" style="margin-bottom: 5px;" id="ExpDate" name="ExpDate" value="" /><br/ >
		Ren.Date - <input type="text" class="customers" style="margin-bottom: 5px;" id="VIPRenewDate" name="VIPRenewDate" value="" /><br />
		VIP Level - <select id="VIPLevel" name="VIPLevel">
		<option value="0">Select VIP Level...</option>
		<?php
		$sql_level = "SELECT * FROM viplevels ORDER BY levelnum";
		$result_level = mysql_query($sql_level);
		while($row_level = mysql_fetch_array($result_level)) {
			echo "<option value=\"$row_level[id]\">$row_level[level]</option>";
		}
		?>
		</select></td>
	</tr>
	<tr>
		<td class="row1" style="width: 40%; font-weight: bold;">Password</td>
		<td class="row1" style="width: 60%;"><input type="text" class="customers" id="password" name="password" value="" /></td>
	</tr>
	<tr>
		<td class="row2" style="width: 40%; font-weight: bold;">Account Name</td>
		<td class="row2" style="width: 60%;"><input type="text" class="customers" id="AccountName" name="AccountName" value="" /></td>
	</tr>
	<tr>
		<td class="row1" style="width: 40%; font-weight: bold;">Customer Number</td>
		<td class="row1" style="width: 60%;"><input type="text" class="customers" id="CustomerNumber" name="CustomerNumber" value="" /></td>
	</tr>
	<tr>
		<td class="row2" style="width: 40%; font-weight: bold;">Account Number</td>
		<td class="row2" style="width: 60%;"><input type="text" class="customers" id="AccountNumber" name="AccountNumber" value="" /></td>
	</tr>
	<tr>
		<td class="row1" style="width: 40%; font-weight: bold;">Credit Line</td>
		<td class="row1" style="width: 60%; text-align: left;"><input type="checkbox" class="customers" id="CreditLine" name="CreditLine" value="true" /></td>
	</tr>
	</table>
	<table cellpadding="5" cellspacing="1" width="980px" style="margin-top: 20px;">
	<tr>
		<td class="subheader">Billing Address</td>
	</tr>
	<tr>
		<td class="row1"><input type="text" class="customers" style="margin: 5px; width: 300px;" id="BillingAddress" name="BillingAddress" value="" /><br/><input type="text" class="customers" style="margin: 5px;" id="BillingCity" name="BillingCity" value="" /><select class="customers" style="margin: 5px; height: 25px;" id="BillingState" name="BillingState" >
		<option value="">Select State...</option>
		<?php
		$sql_states = "SELECT State, Abbreviation FROM states ORDER BY State";
		$result_states = mysql_query($sql_states);
		while($row_states = mysql_fetch_array($result_states)) {
			echo "<option value=\"$row_states[Abbreviation]\">$row_states[State]</option>";
		}
		?>
		</select>
		<input type="text" class="customers" style="margin: 5px;" id="BillingZip" name="BillingZip" value="<?=$row_cust["BillingZip"];?>" /></td>
	</tr>
	</table>
	<table cellpadding="5" cellspacing="1" width="980px">
	<tr>
		<td class="subheader">Shipping Address</td>
	</tr>
	<tr>
		<td class="row1"><input type="text" class="customers" style="margin: 5px; width: 300px;" id="ShippingAddress" name="ShippingAddress" value="" /><br/><input type="text" class="customers" style="margin: 5px;" id="ShippingCity" name="ShippingCity" value="" /><select class="customers" style="margin: 5px; height: 25px;" id="ShippingState" name="ShippingState" >
		<option value="">Select State...</option>
		<?php
		$sql_states = "SELECT State, Abbreviation FROM states ORDER BY State";
		$result_states = mysql_query($sql_states);
		while($row_states = mysql_fetch_array($result_states)) {
			echo "<option value=\"$row_states[Abbreviation]\" $selected>$row_states[State]</option>";
		}
		?>
		</select>
		<input type="text" class="customers" style="margin: 5px;" id="ShippingZip" name="ShippingZip" value="" /></td>
	</tr>
	</table>
	<table cellpadding="5" cellspacing="1" width="980px" style="margin-bottom: 20px;">
	<tr>
		<td><input type="submit" class="customers" style="float: right;" id="btnAddNew" name="btnAddNew" value="Add Customer" /></td>
	</tr>
	</table>
	</form>
	<?php
	mysql_close($conn);
	exit();
} // end "new" view

if($_POST["type"] == 'reset') {
	function newPass() {
		$alpha = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$pass = array();
		$alphalength = strlen($alpha) - 1;
		for ($i = 0; $i < 6; $i++) {
			$n = rand(0, $alphalength);
			$pass[] = $alpha[$n];
		}
		return implode($pass);
	}
	$cid = mysql_real_escape_string($_POST["cid"]);
	$newPass = newPass();
	$sql_setpass = "UPDATE customers SET Password='".$newPass."' WHERE id=$cid LIMIT 1";
	mysql_query($sql_setpass) or die("Password Generation Error: " . mysql_error());
	echo 'Password reset to '.$newPass;
	mysql_close($conn);
	exit();
} // end "reset" view
?>