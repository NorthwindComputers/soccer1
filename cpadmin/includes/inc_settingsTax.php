<?php
	
	if(isset($_POST["btnSave"])) {
		$sql_update = "UPDATE taxes SET Tax=$_POST[tax] WHERE State='mysql_real_escape_string($_POST[State])' LIMIT 1";
		if(!mysql_query($sql_update)) {
			echo "Error Updating Tax";
		}
	}
	
	$sql_tax = "SELECT Tax FROM taxes WHERE State='CA' LIMIT 1";
	$result_tax = mysql_query($sql_tax);
	$row_tax = mysql_fetch_assoc($result_tax);
	
	?>
		<form action="" method="post" >
			<table width="100%" border="0" cellpadding="5" cellspacing="2">
            	<tr>
                	<td><strong>California State Tax:</strong><br/>
                    	<input type="hidden" id="State" name="State" value="CA" />
                    	<input type="text" id="tax" name="tax" value="<?=$row_tax["Tax"];?>" />
                    </td>
                </tr>
                <tr>
                	<td><input type="submit" id="btnSave" name="btnSave" value="Save" /></td>
                </tr>
            </table>
        </form>