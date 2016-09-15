<?php
/**
 * category include admin view
 *
 * Version: 1.0
 * Updated: 09 September 2014
 * By: Richard Tuttle
 */
require 'db.php';
if ($_POST["type"]  == "categorydetails") {
	if ($_POST["id"] != '') {
		$id = $_POST['id'];
		$sql_details = "SELECT * FROM category WHERE id='$id' LIMIT 1";
		$result_details = mysql_query($sql_details);
		$row_details = mysql_fetch_assoc($result_details);
		foreach ($row_details as $key=>$value) {
			$$key = stripslashes($value);
		}
	}
?>
	<table cellpadding="5" cellspacing="1">
    <tr>
        <td style="vertical-align: top; width: 40%;">
        <strong>Category Name:</strong>
        <div class="clear"></div>
        <input type="hidden" id="rid" name="rid" value="<?=$id;?>" />
        <input type="hidden" id="parentid" name="parentid" value="<?=$ParentID;?>" />
        <input type="text" style="margin-bottom: 10px; width: 220px;" id="categoryname" name="categoryname" value="<?=$Category;?>" />
        <div class="clear"></div>
        <strong>Status:</strong>
        <div class="clear"></div>
        <select id="status" name="status" style="margin-bottom: 10px; width: 220px;">
            <option value="enabled" <?php if($Status=="enabled"){ echo ' selected="selected" '; } ?>>Enabled</option>
            <option value="disabled" <?php if($Status=="disabled"){ echo ' selected="selected" '; } ?>>Disabled</option>
        </select>
        <div class="clear"></div>
        <strong>Page Title:</strong>
        <div class="clear"></div>
        <input type="text" id="pagetitle" name="pagetitle" style="margin-bottom: 10px; width: 220px;" value="<?=$PageTitle;?>" />
        <div class="clear"></div>
        <strong>Custom URL:</strong>
        <div class="clear"></div>
        <input type="text" id="customurl" name="customurl" style="margin-bottom: 10px; width: 220px;" value="<?=$CustomURL;?>" />
        <div class="clear"></div></td>
        <td style="vertical-align: top; width: 5%;"></td>
        <td style="vertical-align: top; width: 55%;">
        <strong>Meta Keywords:</strong>
        <div class="clear"></div>
        <input type="text" id="metakeywords" name="metakeywords" style="margin-bottom: 10px; width: 400px;" value="<?=$MetaKeywords;?>" />
        <div class="clear"></div>
        <strong>Meta Tag:</strong>
        <div class="clear"></div>
        <textarea id="metatag" name="metatag" style="margin-bottom: 10px; width: 400px;"><?=$MetaTags;?></textarea>
        <div class="clear"></div>
        <strong>Meta Description:</strong>
        <div class="clear"></div>
        <textarea id="metadescription" name="metadescription" style="margin-bottom: 10px; width: 400px;"><?=$MetaDescription;?></textarea></td>
    </tr>
    <tr>
        <td colspan="3"><strong>Description:</strong>
        <div class="clear"></div>
        <textarea id="description" name="description" style="margin-bottom: 10px; width: 100%;"><?=$Description;?></textarea>
        <div class="clear"></div></td>
    </tr>
    </table>
<?php 
	if ($_POST["id"] != '') {
?>
    	<input type="button" style="background: #ef9800; float: left;" id="btnAddNew" name="btnAddNew" value="Add Category" onclick="addCategory();" />
        <input type="submit" style="background: #ef9800; float: left; margin-left: 5px; width: 120px;" id="btnDelete" name="btnDelete" value="Delete" onclick="return confirm('Delete Category?');" />
        <input type="button" style="background: #ef9800; float: left; margin-left: 5px; width: 120px;" id="btnMove" name="btnMove" onClick="moveCategory();" value="Move" />
        <input type="button" style="background: #ef9800; float: left; margin-left: 5px; width: 120px;" id="btnSort" name="btnSort" onClick="sortCategory('<?=$_POST["id"];?>')" value="Sort Categories" />
<?php 
	} 
?>
    <input type="submit" style="background: #ef9800; float: right;" id="btnSubmit" name="btnSubmit" value="Save / Update" />
<?php
  /*
    $isSecure = (!empty($_SERVER['HTTPS'])) && ($_SERVER['HTTPS'] != 'off');
    $url = ($isSecure ? 'https://' : 'http://') . $host;
    $basePath = str_replace('includes', '', dirname($_SERVER['SCRIPT_NAME']));
    $url  .= $_SERVER['SERVER_NAME'].('/' == $basePath ? '' : $basePath);
  */
?>
<script>
CKEDITOR.config.width = 800;
CKEDITOR.replace('description', {
    uiColor: '#9AB8F3'
});
</script>
<?php
	mysql_close($conn);
	exit();
}
	
if ($_POST["type"] == 'move') {
	$moveid = mysql_real_escape_string($_POST["moveid"]);
	$treetype = "move";
	function subCategories($parent, $treetype, $moveid) {
		$sql_sub = "SELECT id, Category FROM category WHERE ParentID=$parent AND id<>$moveid";
		$result_sub = mysql_query($sql_sub);
		$num_sub = mysql_num_rows($result_sub);
		if($num_sub>0) {
			echo '<ul>';
			while($row_sub=mysql_fetch_array($result_sub)) {
				echo "<li><span class=\"folder\"><a href=\"#categorydetails\" name=\"$treetype\" rel=\"$row_sub[id]\" style=\"padding: 0 0 0 5px;\">$row_sub[Category]</a></span>";
				subCategories($row_sub["id"], $treetype, $moveid);
				echo "</li>";
			}
			echo '</ul>';
		} 
	}
		
	$sql_cat = "SELECT id, Category FROM category WHERE ParentID=0 AND id<>$moveid";
	$result_cat = mysql_query($sql_cat);
	echo '<ul id="move" class="filetree">';
	while($row_cat=mysql_fetch_array($result_cat)) {
		echo "<li><span class=\"folder\"><a href=\"#categorydetails\" name=\"$treetype\" rel=\"$row_cat[id]\" style=\"padding: 0 0 0 5px;\" >$row_cat[Category]</a></span>";
		subCategories($row_cat["id"], $treetype, $moveid);
		echo "</li>";
	}
	echo '</ul>';
?>
    <script>
	$('a[href="#categorydetails"]').click(function() {
		var treetype = $(this).attr("name");
		if(treetype=="main") {
			$("#divCategory").load("includes/inc_category.php", {"id":$(this).attr("rel"), "type":"categorydetails"});
			$('a[href="#categorydetails"]').css("color", "").css("font-weight", "normal");
			$(this).css("color", "#ff0000").css("font-weight", "bold");
			$("#moveid").val($(this).attr("rel"));
		} else {
			$("#moveto").val($(this).attr("rel"));
			$('a[name="move"]').css("color", "").css("font-weight", "normal");
			$(this).css("color", "#ff0000").css("font-weight", "bold");
		}
		return false;
	});
    </script>
<?php
	mysql_close($conn);
	exit();
}
	
if ($_POST["type"] == "sort") {
	$pid = $_POST["pid"];
	$sql_subcats = "SELECT id, Category, Sort FROM category WHERE ParentID=$pid ORDER BY Sort";
	$result_subcats = mysql_query($sql_subcats);
?>
    <h1 style="margin-bottom: 20px;">Click and drag to sort categories:</h1>
	<div id="divSortCats" class="sort" style="width: 250px; float: left; height: 100%;">
<?php
	$sid = 1;
	while($row_subcats=mysql_fetch_array($result_subcats)) {
		echo '<div id="SORT_'.$row_subcats["id"].'" class="item" style="width: 250px;">'.$row_subcats["Category"].'</div>';
		$sid++;
	}
?>
	</div>
    <div class="clear"></div>
	<br/>
    <input type="hidden" id="catOrder" name="catOrder" value="" />
     <input type="button" id="btnSortCancel" value="Cancel" />
    <input type="submit" id="btnSaveSort" name="btnSaveSort" value="Save Order" />
    <script>
	$("#divSortCats").sortable();
    $("#btnSortCancel").click(function() {
		$("#divCategory").load("includes/inc_category.php", {"id":"<?=$pid;?>", "type":"categorydetails"});
	});
	$("#btnSaveSort").click(function() {
		$("#catOrder").val($("#divSortCats").sortable('serialize'));
	});
    </script>
<?php
	mysql_close($conn);
	exit();
}
?>