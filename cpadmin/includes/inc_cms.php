<?php
/**
 * CMS includes page
 *
 * Version: 1.2
 * Updated: 16 September 2013
 * By: Richard Tuttle
 */
 
session_start();
require_once 'db.php';
$isSecure = (!empty($_SERVER['HTTPS'])) && ($_SERVER['HTTPS'] != 'off');
$url = ($isSecure ? 'https://' : 'http://') . $host;
$basePath = str_replace('includes', '', dirname($_SERVER['SCRIPT_NAME']));
$url  .= $_SERVER['SERVER_NAME'].('/' == $basePath ? '' : $basePath);

// initial list view	    
if($_POST["type"] == "list") {
	$pageurl = "http://".$_SERVER["HTTP_HOST"]."/page.php?page=";
?> 
    <form action="" method="post">
    <table border="0">
    <tr>
        <th style="width:100px;">ID</th>
        <th style="width:420px; padding-left: 13px;">Page Title</th>
        <th style="width:200px;">Link</th>
        <th style="width:150px;">Last Update</th>
        <th style="width:110px; padding-left: 20px;">Action</th>
    </tr>
<?php
	$sql_cms = "SELECT * FROM cms";
	$result_cms = mysql_query($sql_cms);
	$c_num = 0;
	while ($row_cms=mysql_fetch_array($result_cms)) {
		if($c_num == 0) {
			$color = "#e7e7e7";
			$c_num++;
		} else {
			$color = "#dbdbdb";
			$c_num = 0;
		}
?>
    <tr>
        <td style="background-color: <?=$color;?>;"><?=$row_cms["id"];?></td>
        <td style="background-color: <?=$color;?>;"><?=$row_cms["PageTitle"];?></td>
		<td style="background-color: <?=$color;?>;">
<?php
		if($row_cms["Type"] == 'Footer') {
			echo "Footer (All Pages)";
		} elseif($row_cms["Type"] == 'Home') { 
?>
			<a href="<?=$pageurl."index.php";?>" target="_blank">View Home</a>	
<?php
		} elseif($row_cms["Type"] == 'Club') { 
?>
            <a href="<?=$pageurl."club.php";?>" target="_blank">Club</a>	
<?php
		} else { 
?>
            <a href="<?=$pageurl.$row_cms["PageName"];?>" target="_blank">View</a>
<?php 	}	?></td>
        <td style="background-color: <?=$color;?>;"><?=$row_cms["LastUpdated"];?></td>
        <td style="background-color: <?=$color;?>;"><a href="cms.php?p=edit&id=<?=$row_cms["id"];?>"><img class="cmsedit" src="images/E.png" /></a><img class="cmsdelete" id="<?=$row_cms["id"];?>" style="cursor: pointer;" src="images/D.png" /></td>
    </tr>
<?php
	}
?>
</table>
</form>
<script type="text/javascript">
$(".cmsedit").hover(function() {
	$(this).attr("src", "images/E_hover.png");
}, function() {
	$(this).attr("src", "images/E.png");
});
$(".cmsdelete").hover(function() {
	$(this).attr("src", "images/D_hover.png");
}, function() {
	$(this).attr("src", "images/D.png");
});
$(".cmsdelete").click(function() {
	var del = confirm("Delete this page?");
	if(del) {
		$.post("includes/inc_cms.php", {
			"type":"delete", 
			"id":+$(this).attr("id")
		}, function(data) {
			alert(data);
			location.reload();
		});
	}
});
</script>
<?php
	mysql_close($conn);
	exit();
}
	
// create a new entry
if($_POST["type"] == "new") {
?>
    <form action="" method="post">
    <table border="0" width="900px">
    <tr>
        <td class="cmsback">Page Title<br/><input type="text" id="PageTitle" name="PageTitle" /></td>
        <td class="cmsback">Page Name<br/><input type="text" id="PageName" name="PageName" /></td>
    </tr>
	<tr>
        <td class="cmsback">Meta Keywords<br/><textarea id="MetaKeywords" name="MetaKeywords"></textarea></td>
        <td class="cmsback">Meta Description<br/><textarea id="MetaDescription" name="MetaDescription"></textarea></td>
    </tr>
	<tr>
		<td class="cmsback">Left Nav<br/><select id="LeftNav" name="LeftNav"><option value="Yes">Yes</option><option value="No">No</option></select></td>
		<td class="cmsback">Type<br/><select id="Type" name="Type"><option selected="selected" value="Page">Page</option><option value="Home">Home Page</option><option value="Footer">Footer</option><option value="Club">Club</option></select></td>
	</tr>
    <tr>
        <td colspan="2" class="cmsback" style="width: 100%;">Page Content<br/><textarea id="Content" name="Content"></textarea></td>
    </tr>
    <tr>
        <td colspan="2" style="background-color: #fff !important;"><input type="submit" class="savebutton" id="btnSaveNew" name="btnSaveNew" value="Save" /></td>
    </tr>
    </table>
    </form>
    <script type="text/javascript">
	CKEDITOR.replace('Content');
	</script>
<?php
	mysql_close($conn);
	exit();
}
	
// edit a section
if($_POST["type"] == "edit") {
	$id = mysql_real_escape_string($_POST["id"]);
	$sql_cms = "SELECT * FROM cms WHERE id=$id LIMIT 1";
	$result_cms = mysql_query($sql_cms);
	$row_cms = mysql_fetch_assoc($result_cms);
	foreach($row_cms as $key=>$value) {
			$$key = stripslashes($value);
	}
?>
	<form action="" method="post">
    <table border="0" width="900px">
    <tr>
        <td class="cmsback">Page Title<br/><input type="hidden" id="id" name="id" value="<?=$id;?>" /><input type="text" id="PageTitle" name="PageTitle" value="<?=$PageTitle;?>" /></td>
        <td class="cmsback">Page Name<br/><input type="text" id="PageName" name="PageName" value="<?=$PageName;?>" /></td>
    </tr>
    <tr>
        <td class="cmsback">Meta Keywords<br/><textarea id="MetaKeywords" name="MetaKeywords"><?=$MetaKeywords;?></textarea></td>
        <td class="cmsback">Meta Description<br/><textarea id="MetaDescription" name="MetaDescription"><?=$MetaDescription;?></textarea></td>
    </tr>
	<tr>
		<td class="cmsback">Left Nav<br/><select id="LeftNav" name="LeftNav"><option <?php if($LeftNav == "Yes") { echo ' selected="selected" '; } ?> value="Yes">Yes</option><option <?php if($LeftNav == "No") { echo ' selected="selected" '; } ?> value="No">No</option></select></td>
		<td class="cmsback">Type<br/><select id="Type" name="Type"><option <?php if($Type == "Page") { echo ' selected="selected" '; } ?> value="Page">Page</option><option <?php if($Type == "Home") { echo ' selected="selected" '; } ?> value="Home">Home Page</option><option <?php if($Type == "Footer") { echo 'selected="selected" '; } ?> value="Footer">Footer</option><option <?php if($Type == "Club") { echo 'selected="selected" '; } ?> value="Club">Club</option></select></td>
	</tr>
    <tr>
        <td colspan="2" class="cmsback" style="width: 100%;">Page Content<br/><textarea id="Content" name="Content"><?=$Content;?></textarea></td>
    </tr>
    <tr>
        <td colspan="2" style="background-color: #fff !important;"><input type="submit" class="savebutton" id="btnSaveUpdate" name="btnSaveUpdate" value="Save" /><input type="submit" class="savebutton" style="margin-left: 10px;" id="btnDelete" name="btnDelete" value="Delete" onClick="return confirm('Delete Page?');" /></td>
    </tr>
    </table>
    </form>
    <script type="text/javascript">
	CKEDITOR.replace('Content');
	</script>
<?php
	mysql_close($conn);
	exit();
}

// delete an entry
if($_POST["type"] == "delete") {
	$id = mysql_real_escape_string($_POST["id"]);
	$sql_del = "DELETE FROM cms WHERE id=$id LIMIT 1";
	if(!mysql_query($sql_del)) {
		echo "Error removing page";
	} else {
		echo "Page removed!";
	}
	mysql_close($conn);
	exit();
}
?>