<?php
	
	if(isset($_POST["btnSave"])) {
		
		$id = mysql_real_escape_string($_POST["rid"]);
		$options = implode("|",mysql_real_escape_string($_POST["options"]));
		$sql = "UPDATE access_level SET AccessTo='$options' WHERE id=$id LIMIT 1";

		if(!mysql_query($sql)) {
			echo "Error saving options: ".mysql_error();
		}
	}

	if(isset($_POST["btnAddRule"])) {
		$sql_add = "INSERT INTO access_level(Name) VALUE('mysql_real_escape_string($_POST[newrule])')";
		if(!mysql_query($sql_add)) {
			echo "Error adding rule: ".mysql_error();
		}
	}

?>
<script type="text/javascript">
	$(document).ready(function(){
		$(".editRule").click(function(){
			$("#divOptions").html('<img src="images/loader.gif" />');
			$("#divOptions").load('includes/inc_settingsOptions_add.php', {"type":"rules", "rid":$(this).attr("href")});
			return false;
		});
		$("#addRule").hover(
			function(){
				$(this).attr("src", "images/plus_hover.png");
			}, function() {
				$(this).attr("src", "images/plus.png");
			});
		$("#addRule").click(function() {
			$("#newrule").val('');
			$("#divAddNew").show("slow");
		});
		$("#btnCancel").click(function() {
			$("#divAddNew").hide("hide");
		});
	});
</script>

<form action="" method="post">
<table width="100%" border="0" cellpadding="5" cellspacing="2">
      <tr>
        <td width="30%" class="headercg">Rules</td>
        <td width="70%" class="headercg">Options</td>
      </tr>
	<tr>
		<td style="vertical-align: top;">
			<ul>
			<?php
				$sql_rules = "SELECT id, Name FROM access_level ORDER BY Name";
				$result_rules = mysql_query($sql_rules);
				
				while($row_rules = mysql_fetch_array($result_rules)) {
					echo '<li><a class="editRule" href="'.$row_rules["id"].'">'.$row_rules["Name"].'</a></li>';
				}
			?>
			</ul>
            <div id="divAddNew" style="display: none; padding: 15px;">
            	<input type="text" id="newrule" name="newrule" />
                <input type="submit" class="save" id="btnAddRule" name="btnAddRule" value="Add" />
                <input type="button" class="save" style="margin-left: 5px;" id="btnCancel" name="btnCancel" value="Cancel" />
            </div>
            <div class="clear"></div>
            <img id="addRule" style="cursor: pointer; margin-top: 10px;" src="images/plus.png" />
		</td>
		<td><div id="divOptions"></div></td>
	</tr>
</table>
</form>