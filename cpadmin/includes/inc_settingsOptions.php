<?php
if (isset($_POST["btnSubmit2"])) {
	require 'db.php';
	// $postIDS = $_POST["ids"];
	$ids = explode(",", $_POST["ids"]);
	$cat = $_POST["cat"];
	foreach ($ids as $val_id) {
		// $pcv = $_POST[$cat.$val_id];
		// if ($pcv != '') {
		if($_POST[$cat.$val_id] != '') {
			if($_POST[$cat.$val_id."_id"] != '') {
				if($cat == "colors") {
					$sql = "UPDATE colors SET Color='".$_POST[$cat.$val_id]."', SKU='".$_POST[$cat.$val_id."_sku"]."', Icon='".$_POST[$cat.$val_id."_img"]."', Category='$_POST[Category]' WHERE id=".$_POST[$cat.$val_id."_id"]." LIMIT 1";
				}
				if($cat == "sizes") {
					$sql = "UPDATE sizes SET Size='".$_POST[$cat.$val_id]."', SKU='".$_POST[$cat.$val_id."_sku"]."', Icon='".$_POST[$cat.$val_id."_img"]."', Category='$_POST[Category]' WHERE id=".$_POST[$cat.$val_id."_id"]." LIMIT 1";
				}
			} else {
				if($cat == "colors") {
					$sql = "INSERT INTO colors(Color, SKU, Icon, Category) VALUES('".$_POST[$cat.$val_id]."', '".$_POST[$cat.$val_id."_sku"]."', '".$_POST[$cat.$val_id."_img"]."', '$_POST[Category]')";
				}
				if($cat == "sizes") {
					$sql = "INSERT INTO sizes(Size, SKU, Icon, Category) VALUES('".$_POST[$cat.$val_id]."', '".$_POST[$cat.$val_id."_sku"]."', '".$_POST[$cat.$val_id."_img"]."', '$_POST[Category]')";
				}
			}
		}
			
		if (!mysql_query($sql)) {
			echo "Error saving option: ".mysql_error();
		}
	}
}
	
if ($_GET["category"] == "colors") {
	$cat = "colors";
	$field = "Color";
} else {
	$cat = "sizes";
	$field = "Size";
}
?>
<script type="text/javascript">
var arrItems = new Array();
function removeopt(cat, id, rid) {			
	var rem = confirm('Delete Option?');
	if (rem) {			
		$('div').remove("#div"+cat+id);
		for (var i=0; i<arrItems.lenght; i++) {
			if (arrItems[i] == id) {
				arrItems.splice(i,1);
			}
		}				

		if (rid != '') {
			$.post("includes/inc_settingsOptions_add.php", {
				"type":"del",
				"cat":cat,
				"rid":rid
			}, function(data) {
				alert(data);
			});
		}
	}
}
function addopt() {
	var num = $("#num").val();
	num = parseInt(num)+1;
	arrItems.push(num);
	$("#num").val(num);
	$.post("includes/inc_settingsOptions_add.php", {
		"num":num, 
		"cat":$("#category").val(), 
		"type":"productcolor"
	}, function(data) {
		$("#divOptions").append(data);
	});
}

function setIDs() {
	$("#ids").val(arrItems);
}
</script>
<form action="" method="post">
<input type="hidden" id="num" name="num" value="1"/>
	<input type="hidden" id="ids" name="ids" value="" />
    <input type="hidden" id="cat" name="cat" value="<?=$cat;?>" />
    <table width="100%" border="0" cellpadding="5" cellspacing="1">
        <tr class="setting">
          <td width="50%">Category</td>
          <td width="50%">Product Category</td>
        </tr>
        <tr>
          <td><select id="category" onChange="window.location='settings.php?p=Options&category='+this.value;">
			<option value="colors" <?php if($cat=="colors") { echo ' selected="selected" '; } ?>>Colors</option>
			<option value="sizes" <?php if($cat=="sizes") { echo ' selected="selected" '; } ?>>Sizes</option>
		  </select></td>
          <td><select id="Category" name="Category" onChange="loadoptions();">
			<option value="">Select Category...</option>
			<?php
				$sql_category = "SELECT Name FROM attribute_category WHERE Type='$cat'";
				$result_category = mysql_query($sql_category);
				while($row_category = mysql_fetch_array($result_category)) {
					echo "<option value=\"$row_category[Name]\">$row_category[Name]</option>";
				}
			?>
          	</select></td>
        </tr>
    </table>
    <table width="100%" border="0" cellpadding="5" cellspacing="1">
    	<tr>
          <td class="header" style=" background: #ff8400;">Name</td>
          <td class="header" style=" background: #94CEEF;">Icon</td>
          <td class="header" style=" background: #92BB61;">SKU</td>
	  	  <td class="remove" style="background: #FF0000;">Remove</td>
        </tr>
    </table>
	<div id="optionlist"><img src="../images/loader.gif" /></div>
</form>
<iframe id="imgUpload" name="imgUpload" src="includes/imgUpload.php" style="width: 0px; height: 0px; border: 0px; visibility: hidden;" ></iframe>
<script type="text/javascript">
$("#optionlist").load("includes/inc_settingsOptions_add.php", {
	"type":"optionlist", 
	"cat":"<?=$cat;?>", 
	"optcat": $("#Category").val()
});
		
function loadoptions() {
	$("#optionlist").html('<img src="../images/loader.gif" />');
	$("#optionlist").load("includes/inc_settingsOptions_add.php", {
		"type":"optionlist", 
		"cat":"<?=$cat;?>", 
		"optcat": $("#Category").val()
	});
}
</script>