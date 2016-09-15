<?php

	if($_POST["type"] == "list") {
		require 'db.php';
		
		$id = mysql_real_escape_string($_POST["id"]);
		$name = mysql_real_escape_string($_POST["name"]);
		$date = mysql_real_escape_string($_POST["date"]);
		
?>
    	<form action="" method="post" >
		<table cellpadding="5" cellspacing="1" width="800px" style="margin-top: 20px;">
			<tr>
				<td colspan="6" class="headersmain">
					VIP Manager
				</td>
			</tr>
			<tr>
				<td class="headers" style="width: 100px;">Customer ID</td>
				<td class="headers" style="width: 100px;">Purchased On</td>
				<td class="headers" style="width: 270px;">Customer</td>
				<td class="headers" style="width: 130px;">VIP Number</td>
				<td class="headers" style="width: 100px;">Exp Date</td>
				<td class="headers" style="width: 100px;"><input style="width: 15px; border: 0px;" type="checkbox" id="chkAll" name="chkAll" /> Check All</td>
			</tr>
			
			<?php
				
				$sql_vip = "SELECT DISTINCT * FROM customers WHERE Status='VIP' ";
				if($id != '') {
					$sql_vip .= " AND VIPNum LIKE '%$id%' ";
				}
				if($name != '') {
					$sql_vip .= " AND (FirstName LIKE '%$name%' OR LastName LIKE '%$name%') ";
				}
				if($date != '') {
					$sql_vip .= " AND date_add(VIPDate, INTERVAL 1 YEAR) = str_to_date('$date', '%c/%d/%Y') ";
				}
				
				$result_vip = mysql_query($sql_vip);
			
				$i_num = 1;
				while($row_vip = mysql_fetch_array($result_vip)) {
					if($i_num == 1) {
						$color = "row1";
						$i_num++;
					} else {
						$color = "row2";
						$i_num = 1;
					}
					?>
						<tr>
							<td class="<?=$color;?>" style="text-align: center;"><?=$row_vip["id"];?></td>
							<td class="<?=$color;?>" style="text-align: center;">
								<?php 
									$vipdate = strtotime($row_vip["VIPDate"]);
									echo date('m/d/Y', $vipdate);
								?>
							</td>
							<td class="<?=$color;?>"><?=$row_vip["FirstName"]." ".$row_vip["LastName"];?></td>
							<td class="<?=$color;?>" style="text-align: center;"><?=$row_vip["VIPNum"];?></td>
							<td class="<?=$color;?>" style="text-align: center;">
								<?php 
									$date = strtotime($row_vip["VIPDate"]);
									$date = mktime(0, 0, 0, date("m", $date), date("d", $date), date("Y", $date)+1);
									echo date('m/d/Y', $date);
								?>
							</td>
							<td class="<?=$color;?>" style="text-align: center;">
								<input type="checkbox" style="width: 15px; border: 0px;" class="chkvips" id="vipcustomer" name="vipcustomer[]" value="<?=$row_vip["id"];?>" />
							</td>
						</tr>
					<?php
				}

			?>
		</table>
    	</form>

	<script>
		$("#chkAll").click(function() {
			if($(this).attr('checked') == true) {
				$(".chkvips").attr('checked', true);
			} else {
				$(".chkvips").attr('checked', false);
			}
		});

	</script>
    
<?php
		mysql_close($conn);
		exit();
	}

?>

	<div class="orders">
		<table cellpadding="5" cellspacing="1" width="800px">
			<tr>
				<td class="row1" style="text-align: center;"><input class="search" type="text" id="svipnum" name="svipnum" value="Search VIP Number" /></td>
				<td class="row1" style="text-align: center;"><input class="search" type="text" id="sname" name="sname" value="Search By Customer Name" /></td>
				<td class="row1" style="text-align: center;"><input class="search" type="text" id="sdate" name="sdate" value="Search By Exp Date" /></td>
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
    
    <div class="orders" id="customerlist"></div>
    
    
    <script>
		$("#sdate").datepicker();
		$("#svipnum").focus(function() {
			if($(this).val() == 'Search VIP Number') {
				$(this).val('');
			}
		});
		$("#sname").focus(function() {
			if($(this).val() == 'Search By Customer Name') {
				$(this).val('');
			}
		});
		$("#sdate").focus(function() {
			if($(this).val() == 'Search By Exp Date') {
				$(this).val('');
			}
		});

		$("#svipnum").focusout(function() {
			if($(this).val() == '') {
				$(this).val('Search VIP Number');
			}
		});
		$("#sname").focusout(function() {
			if($(this).val() == '') {
				$(this).val('Search By Customer Name');
			}
		});
		$("#sdate").focusout(function() {
			if($(this).val() == '') {
				$(this).val('Search By Exp Date');
			}
		});

		$("#btnSearch").click(function() {
			$("#customerlist").html('<img src="images/loader.gif" />');
			var oid;
			var oname;
			var odate;

			if($("#svipnum").val() == 'Search VIP Number') {
				oid = '';
			} else {
				oid = $("#svipnum").val();
			}

			if($("#sname").val() == 'Search By Customer Name') {
				oname = '';
			} else {
				oname = $("#sname").val();
			}

			if($("#sdate").val() == 'Search By Exp Date') {
				odate = '';
			} else {
				odate = $("#sdate").val();
			}

			$("#customerlist").load("includes/inc_settingsVIPManage.php", {"type":"list", 
									 "id":oid, 
									 "name":oname, 
									 "date":odate}
			);

		});

		$("#customerlist").load("includes/inc_settingsVIPManage.php", {"type":"list"});
    </script>