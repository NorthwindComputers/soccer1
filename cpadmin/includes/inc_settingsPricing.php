<?php
	
	require 'db.php';
	
	if(isset($_POST["btnAddNew"])) {
		$specialname = addslashes(mysql_real_escape_string($_POST["SpecialName"]));
		$sql_add = "INSERT INTO pricing_special(SpecialName) Values('$specialname')";
		if(!mysql_query($sql_add)) {
			echo "Error adding Special";
		}
	}
	
	if(isset($_POST["btnUpdate"])) {
		$id = mysql_real_escape_string($_POST["id"]);
		$specialname = addslashes(mysql_real_escape_string($_POST["SpecialName"]));
		
		$sql_add = "UPDATE pricing_special SET SpecialName='$specialname' WHERE id=$id LIMIT 1";
		if(!mysql_query($sql_add)) {
			echo "Error Updating Special";
		}
	}
	
	if($_POST["type"]=="delete") {
		$id = mysql_real_escape_string($_POST["id"]);
		$sql_del = "DELETE FROM pricing_special WHERE id=$id LIMIT 1";
		if(!mysql_query($sql_del)) {
			echo "Error removing Special";
		} else {
			echo "Special Removed";
		}
		
		mysql_close($conn);
		exit();
	}
	
	
	if($_POST["type"] == "new") {
		?>
        <form action="" method="post" >
			<table width="100%" border="0" cellpadding="5" cellspacing="2">
            	<tr>
                	<td>Price Name:<br/>
                    	<input type="text" id="SpecialName" name="SpecialName" value="" />
                    </td>
                </tr>
                <tr>
                	<td><input type="submit" id="btnAddNew" name="btnAddNew" value="Save"/></td>
                </tr>
            </table>
        </form>
        <?php
		
		mysql_close($conn);
		exit();
	}
	
	if($_POST["type"] == "edit") {
		$id = str_replace("e_",'',mysql_real_escape_string($_POST["id"]));
		$sql_edit = "SELECT id, SpecialName FROM pricing_special WHERE id=$id LIMIT 1";
		$result_edit = mysql_query($sql_edit);
		$row_edit = mysql_fetch_assoc($result_edit);
		$specialname = stripslashes($row_edit["SpecialName"]);
		?>
        <form action="" method="post" >
			<table width="100%" border="0" cellpadding="5" cellspacing="2">
            	<tr>
                	<td>Price Name:<br/>
                    	<input type="hidden" id="id" name="id" value="<?=$row_edit["id"];?>" />
                    	<input type="text" id="SpecialName" name="SpecialName" value="<?=$specialname;?>" />
                    </td>
                </tr>
                <tr>
                	<td><input type="submit" id="btnUpdate" name="btnUpdate" value="Update"/></td>
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
            	<td colspan="3"><img src="images/plus.png" class="paddnew" style="float: right; width: 20px; cursor: pointer;" /></td>
            </tr>
            <tr>
                <td width="7%" class="headercg">ID</td>
                <td width="53%" class="headercg">Special</td>
                <td width="15%" class="headercg">Option</td>
            </tr>
            
            <?php
            
                $sql_special = "SELECT * FROM pricing_special";
                $result_special = mysql_query($sql_special);
                
                while($row_special = mysql_fetch_array($result_special)) {
                    ?>
                        <tr>
                            <td style="text-align:center;"><?=$row_special["id"];?></td>
                            <td><?php echo stripslashes($row_special["SpecialName"]); ?></td>
                            <td style="text-align: center;">
                                <img id="e_<?=$row_special["id"];?>" class="pedit" src="images/E.png"/>
                                <img id="<?=$row_special["id"];?>" class="pdelete" style="cursor: pointer;" src="images/D.png"/>
                            </td>
                        </tr>
                    <?php
                }
            ?>
        </table>
    </div>
<script>
	$(".paddnew").hover(
		function() {
			$(this).attr("src", "images/plus_hover.png");
		}, function() {
			$(this).attr("src", "images/plus.png");
	});
	$(".pedit").hover(
		function() {
			$(this).attr("src", "images/E_hover.png");
		},
		function() {
			$(this).attr("src", "images/E.png");
	});
		
	$(".pdelete").hover(
		function() {
			$(this).attr("src", "images/D_hover.png");
		},
		function() {
			$(this).attr("src", "images/D.png");
	});
	
	$(".pdelete").click(function() {
		var del = confirm("Delete Special?");
		
		if(del) {
			$.post("includes/inc_settingsPricing.php", {"type":"delete", "id":$(this).attr("id")}, 
				function(data) {
					alert(data);
					location.reload();
			});
		}
	});
	
	$(".paddnew").click(function() {
		$("#mainbox").load("includes/inc_settingsPricing.php", {"type":"new"});
	});
	
	$(".pedit").click(function() {
		$("#mainbox").load("includes/inc_settingsPricing.php", {"type":"edit", "id":$(this).attr("id")});
	});
	
	
</script>

