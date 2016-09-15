<?php

	if($_POST["type"] == "list") {
		require 'db.php';
		
		$id = mysql_real_escape_string($_POST["id"]);
		$name = mysql_real_escape_string($_POST["name"]);
		$account = mysql_real_escape_string($_POST["account"]);
		
?>
    	<form action="" method="post" >
		<table cellpadding="5" cellspacing="1" width="800px" style="margin-top: 20px;">
			<tr>
				<td colspan="4" class="headersmain">
					Payment Manager
				</td>
			</tr>
			<tr>
				<td class="headers" style="width: 100px;">Customer ID</td>
				<td class="headers" style="width: 360px;">Customer Name</td>
				<td class="headers" style="width: 170px;">Account Code</td>
				<td class="headers" style="width: 170px;">Credit Line</td>
			</tr>
			
			<?php
				
				$sql_credit = "SELECT DISTINCT * FROM customers WHERE CreditLine>0 ";
				if($id != '') {
					$sql_credit .= " AND id LIKE '%$id%' ";
				}
				if($name != '') {
					$sql_credit .= " AND (FirstName LIKE '%$name%' OR LastName LIKE '%$name%') ";
				}
				if($account != '') {
					$sql_credit .= " AND AccountNumber LIKE '%$account%' ";
				}
				
				$result_credit = mysql_query($sql_credit);
			
				$i_num = 1;
				while($row_credit = mysql_fetch_array($result_credit)) {
					if($i_num == 1) {
						$color = "row1";
						$i_num++;
					} else {
						$color = "row2";
						$i_num = 1;
					}
					?>
						<tr>
							<td class="<?=$color;?>" style="text-align: center;"><a href="customers.php?id=<?=$row_credit["id"];?>"><?=$row_credit["id"];?></a></td>
							<td class="<?=$color;?>" style="text-align: center;"><?=$row_credit["FirstName"]." ".$row_credit["LastName"];?></td>
							<td class="<?=$color;?>" style="text-align: center;"><?=$row_credit["AccountNumber"];?></td>
							<td class="<?=$color;?>" style="text-align: center;">$<?=number_format($row_credit["CreditLine"],2);?></td>
						</tr>
					<?php
				}

			?>
		</table>
    	</form>
    
<?php
		mysql_close($conn);
		exit();
	}

?>

	<div class="orders">
		<table cellpadding="5" cellspacing="1" width="800px">
			<tr>
				<td class="row1" style="text-align: center;"><input class="search" type="text" id="screditnum" name="screditnum" value="Search Customer ID" /></td>
				<td class="row1" style="text-align: center;"><input class="search" type="text" id="screditname" name="screditname" value="Search Customer Name" /></td>
				<td class="row1" style="text-align: center;"><input class="search" type="text" id="screditaccount" name="screditaccount" value="Search Account Number" /></td>
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td class="row1">
					<input type="button" style="float: right; border: 1px solid #fff; background-color: #ff6600; width: 120px; height: 25px; color: #fff;" id="btnSearch" name="btnSearch" value="Search" />
				</td>

			</tr>
		</table>
	</div>
    
    <div class="orders" id="creditlist"></div>
    
    
    <script>
		$("#screditnum").focus(function() {
			if($(this).val() == 'Search Customer ID') {
				$(this).val('');
			}
		});
		$("#screditname").focus(function() {
			if($(this).val() == 'Search Customer Name') {
				$(this).val('');
			}
		});
		$("#screditaccount").focus(function() {
			if($(this).val() == 'Search Account Number') {
				$(this).val('');
			}
		});

		$("#screditnum").focusout(function() {
			if($(this).val() == '') {
				$(this).val('Search Customer ID');
			}
		});
		$("#screditname").focusout(function() {
			if($(this).val() == '') {
				$(this).val('Search Customer Name');
			}
		});
		$("#screditaccount").focusout(function() {
			if($(this).val() == '') {
				$(this).val('Search Account Number');
			}
		});

		$("#btnSearch").click(function() {
			$("#creditlist").html('<img src="images/loader.gif" />');
			var oid;
			var oname;
			var oaccount;

			if($("#screditnum").val() == 'Search Customer ID') {
				oid = '';
			} else {
				oid = $("#screditnum").val();
			}

			if($("#screditname").val() == 'Search Customer Name') {
				oname = '';
			} else {
				oname = $("#screditname").val();
			}

			if($("#screditaccount").val() == 'Search Account Number') {
				oaccount = '';
			} else {
				oaccount = $("#screditaccount").val();
			}

			$("#creditlist").load("includes/inc_settingsPayment.php", {"type":"list", 
									 "id":oid, 
									 "name":oname, 
									 "account":oaccount}
			);

		});

		$("#creditlist").load("includes/inc_settingsPayment.php", {"type":"list"});
    </script>