<?php
	require 'db.php';

	if($_POST["type"] == "view") {
		
		?>
		
        	<table cellpadding="5" cellspacing="1" width="980px" style="margin-top: 5px;">
            	<tr>
                	<td class="headersmain" colspan="6" style="text-align: left; padding-left: 20px;">Bundle Manager
                    	<input type="button" style="float: right; border: 1px solid #bebebe; background-color: #ff6600; width: 120px; height: 25px; color: #fff;" id="btnNew" name="btnNew" value="Add New" />
                    </td>
                </tr>
                <tr>
                      <td class="headers" style="width:100px;">ID</td>
                      <td class="headers" style="width:500px; padding-left: 13px; text-align: left;">Product Name</td>
                      <td class="headers" style="width:150px;">N.M Price</td>
                      <td class="headers" style="width:100px;">Sold</td>
                      <td class="headers" style="width:110px; padding-left: 20px;">View</td>
                </tr>
		<?php
		$sql_bundles = "SELECT id, ProductDetailName, NoneMemberPrice, SKU FROM bundles ORDER BY id";
		$result_bundles = mysql_query($sql_bundles);
		
		$c = 1;
		while($row_bundles = mysql_fetch_array($result_bundles)) {
			$prodid = $row_bundles["id"];
			if($c==1) {
				$bg = "row1";
				$c++;
			} else {
				$bg = "row2";
				$c = 1;
			}
	?>
			<tr>
				  <td class="<?=$bg;?>" style="width: 100px;"><?=$row_bundles["SKU"];?></td>
				  <td class="<?=$bg;?>" style="width: 500px; text-align: left; padding-left: 13px;"><?=$row_bundles["ProductDetailName"];?></td>
				  <td class="<?=$bg;?>" style="width: 150px;">$<?=number_format($row_bundles["NoneMemberPrice"],2);?></td>
				  <td class="<?=$bg;?>" style="width: 100px;"><?php //$row_sold["TotalSold"]; ?></td>
				  <td class="<?=$bg;?>" style="width: 110px; text-align: center; padding-left: 35px;">
					<div class="delete"><a href="#" onclick="deleteprod('<?=$prodid;?>');">&nbsp;</a></div>
					<div class="copy"><a href="#" onclick="copyprod('<?=$prodid;?>');">&nbsp;</a></div>
					<div class="view"><a href="product_detail.php?id=<?=$prodid;?>">&nbsp;</a></div>
				  </td>
			</tr>
	<?php
		}
	?>
	</table>
	<script>
		$("#btnNew").click(function(){
			$("#bundles").html('<img src="images/loader.gif" />');
			$("#bundles").load("includes/inc_bundles.php", {"type":"new"});
		});

	</script>
	
	<?php 
		mysql_close($conn);
		exit(); 
	}

	
	if($_POST["type"] == "new") {
		?>
		<form action="" method="post">
		<table cellpadding="5" cellspacing="1" width="980px">
			<tr>
				<td colspan="2" class="subheader" style="font-size: 14px; width: 50%;">Browser Information</td>
				<td colspan="2" class="subheader" style="font-size: 14px; width: 50%;">Product Details</td>
			</tr>
			<tr>
				<td class="row1" style="font-weight: bold;">Browser Name:</td>
				<td class="row1"><input type="text" class="customers" style="width: 90%;" id="BrowserName" name="BrowserName" /></td>
				<td class="row1" style="font-weight: bold;">Product Detail Name:</td>
				<td class="row1"><input type="text" class="customers" style="width: 90%;" id="ProductDetailName" name="ProductDetailName" /></td>
			</tr>
			<tr>
				<td class="row2" style="font-weight: bold;">Browser Name 2:</td>
				<td class="row2"><input type="text" class="customers" style="width: 90%;" id="BrowserName2" name="BrowserName2" /></td>
				<td class="row2" style="font-weight: bold;">Bundle SKU:</td>
				<td class="row2"><input type="text" class="customers" style="width: 90%;" id="SKU" name="SKU" /></td>
			</tr>
			<tr>
				<td class="row1" style="font-weight: bold;">Browser Name 3:</td>
				<td class="row1"><input type="text" class="customers" style="width: 90%;" id="BrowserName3" name="BrowserName3" /></td>

				<td class="row1" style="font-weight: bold;">Status:</td>
				<td class="row1">
					<select id="Status" name="Status" class="customers">
						<option value="Enabled">Enabled</option>
						<option value="Disabled">Disabled</option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="row2" style="font-weight: bold;">Browser Additional Info:</td>
				<td class="row2"><input type="text" class="customers" style="width: 90%;" id="BrowserAddInfo" name="BrowserAddInfo" /></td>
				<td class="row2" style="font-weight: bold;">Customer Group Availability:</td>
				<td class="row2">
					<select id="CustomerGroupAvailability" name="CustomerGroupAvailability" class="customers">
						<?php
						
							$sql_cg = "SELECT GroupName FROM customer_group ORDER BY GroupName";
							$result_cg = mysql_query($sql_cg);

							while($row_cg = mysql_fetch_array($result_cg)) {
								echo "<option value=\"$row_cg[GroupName]\">$row_cg[GroupName]</option>";
							}
						?>
					</select>
				</td>
			</tr>
		</table>
		
		<table cellpadding="5" cellspacing="1" style="width: 980px; margin-top: 30px;">
			<tr>
				<td class="subheader" colspan="2" style="text-align: left; padding-left: 20px;">Product Descriptions</td>
			</tr>
			<tr>
				<td class="row1" style="font-weight: bold; width: 25%;">Short Description:</td>
				<td class="row1" style="width: 75%;"><textarea class="customers" style="width: 90%; height: 80px;" id="ShortDescription" name="ShortDescription"></textarea></td>
				
			</tr>
			<tr>
				<td class="row2" style="font-weight: bold; width: 25%;">Long Description:</td>
				<td class="row2" style="width: 75%;"><textarea class="customers" style="width: 90%; height: 150px;" id="LongDescription" name="LongDescription"></textarea></td>
				
			</tr>

		</table>

		<table cellpadding="5" cellspacing="1" style="width: 980px; margin-top: 30px; ">
			<tr>
				<td class="subheader" colspan="2" style="text-align: left; padding-left: 20px;">Search Engine Optimization:</td>
			</tr>
			<tr>
				<td class="row1" style="font-weight: bold; width: 25%;">Page Title:</td>
				<td class="row1" style="width: 75%;"><input type="text" class="customers" style="width: 90%;" id="MetaTitle" name="MetaTitle"></td>
				
			</tr>
			<tr>
				<td class="row2" style="font-weight: bold; width: 25%;">Meta Tags:</td>
				<td class="row2" style="width: 75%;"><input type="text" class="customers" style="width: 90%;" id="MetaTags" name="MetaTags"></td>
				
			</tr>
			<tr>
				<td class="row1" style="font-weight: bold; width: 25%;">Meta Description:</td>
				<td class="row1" style="width: 75%;"><textarea class="customers" style="width: 90%; height: 150px;" id="MetaDescription" name="MetaDescription"></textarea></td>
	
			</tr>
		</table>
        
        <table cellpadding="5" cellspacing="1" style="width: 980px; margin: 30px 0px 30px 0px;">
        	<tr>
            	<td colspan="5" class="subheader" style="text-align: left; padding-left: 20px;">Pricing:</td>
            </tr>
            <tr>
            	<td>Non Member</td>
                <td>1-49</td>
                <td>50-99</td>
                <td>100-149</td>
                <td>150-200</td>
            </tr>
            <tr>
            	<td><input type="text" class="customers" style="width: 90%; text-align: center;" id="NonMemberPrice" name="NonMemberPrice" /></td>
                <td><input type="text" class="customers" style="width: 90%; text-align: center;" id="Option1Price" name="Option1Price" /></td>
                <td><input type="text" class="customers" style="width: 90%; text-align: center;" id="Option2Price" name="Option2Price" /></td>
                <td><input type="text" class="customers" style="width: 90%; text-align: center;" id="Option3Price" name="Option3Price" /></td>
                <td><input type="text" class="customers" style="width: 90%; text-align: center;" id="Option4Price" name="Option4Price" /></td>
            </tr>
            <tr>
				<td colspan="5"><input type="submit" class="customers" style="float: right; margin-right: 10px;" id="btnSave" name="btnSave" value="Save" /> 
						<input type="button" class="customers" style="float: right; margin-right: 10px;" id="btnCancel" name="btnCancel" value="Cancel" onClick="window.location='bundles.php';" /></td>
			</tr>
        </table>
		</form>

		<?php

		mysql_close($conn);
		exit();
	}	
?>