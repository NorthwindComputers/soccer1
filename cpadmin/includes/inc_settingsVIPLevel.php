<?php
	
	require 'db.php';
	
	if(isset($_POST["btnUpdate"])) {
		
		foreach ($_POST as $key=>$value) {
			$$key = addslashes($value);
		}
		
		$sql_update = "UPDATE viplevels SET level='$level' WHERE id=$id LIMIT 1";
		if(!mysql_query($sql_update)) {
			echo "Error updating VIP Level: ".mysql_error();
		}
		
	}

	if($_POST["type"] == "edit") {
		$eid = str_replace("e_",'',mysql_real_escape_string($_POST["id"]));
		$sql_edit = "SELECT * FROM viplevels WHERE id=$eid LIMIT 1";
		$result_edit = mysql_query($sql_edit);
		$row_edit = mysql_fetch_assoc($result_edit);
		
		foreach($row_edit as $key=>$value) {
			$$key = stripslashes($value);
		}
		
		?>
		<form action="" method="post" >
			<table width="100%" border="0" cellpadding="5" cellspacing="2">
            		<tr>
                		<td style="width: 20%;"><strong>Level Name:</strong></td>
						<td><input type="hidden" id="id" name="id" value="<?=$id;?>" />
                        	<input type="text" id="level" name="level" value="<?=$level;?>" /></td>
                	</tr>
                	<tr>
                		<td colspan="2"><input type="submit" id="btnUpdate" name="btnUpdate" value="Update"/> <input type="button" style="margin-left: 20px;" id="btnCancel" name="btnCancel" onClick="location.reload()" value="Cancel" /></td>
                	</tr>
            </table>
        </form>
        <?php
		
		mysql_close($conn);
		exit();
	}

?>

	<div id="mainbox">
        <table width="100%" border="0" cellpadding="5" cellspacing="2">

            <tr>
                <td width="80%" class="headercg">Level</td>
                <td width="20%" class="headercg" style="text-align: center;">Options</td>
            </tr>
            
            <?php
            
                $sql_levels = "SELECT * FROM viplevels ORDER BY levelnum";
                $result_levels = mysql_query($sql_levels);
                
                while($row_levels = mysql_fetch_array($result_levels)) {
                    ?>
                        <tr>
                            <td><?=$row_levels["level"];?></td>
                            <td style="text-align: center;">
                                <img id="e_<?=$row_levels["id"];?>" class="pedit" src="images/E.png"/>
                            </td>
                        </tr>
                    <?php
                }
            ?>
        </table>
    </div>
<script>
	$(".pedit").hover(
		function() {
			$(this).attr("src", "images/E_hover.png");
		},
		function() {
			$(this).attr("src", "images/E.png");
	});
		
	$(".pedit").click(function() {
		$("#mainbox").load("includes/inc_settingsVIPLevel.php", {"type":"edit", "id":$(this).attr("id")});
	});
	
</script>