<?php
/**********************************
 * Banner administration page
 *
 * Version: 1.1
 * By: Richard Tuttle
 * Last updated: 10 June 2014
 **********************************/
 
// delete a banner
if ($_POST["type"] == "delete") {
	require 'db.php';
	$id = mysql_real_escape_string($_POST['id']);
	$sql_del = "DELETE FROM banner WHERE id='$id' LIMIT 1";
	if (!mysql_query($sql_del)) {
		echo "Error Deleteing banner: ".mysql_error();
	} else {
		echo "Banner deleted!";
	}		

	mysql_close($conn);
	exit();
}

// upload a banner
if (isset($_POST["btnUpload"])) {
	if ($_FILES["banner"]["error"] > 0) {
		$err = "Error:".$_FILES["banner"]["error"];
	} else {			
		if (($_FILES["banner"]["type"] == "image/gif") || ($_FILES["banner"]["type"] == "image/jpeg") || ($_FILES["banner"]["type"] == "image/png") || ($_FILES["banner"]["type"] == "image/pjpeg")) {
			$filename = $_FILES["banner"]["name"];
			$folderloc = "../images/banner/";
			move_uploaded_file($_FILES["banner"]["tmp_name"],$folderloc.$filename);
			$sql_maxsort = "SELECT MAX(Sort) AS MSort FROM banner";
			$result_maxsort = mysql_query($sql_maxsort);
			$row_maxsort = mysql_fetch_assoc($result_maxsort);
			$sortid = $row_maxsort["MSort"] + 1;
			$sql_update = "INSERT INTO banner(BannerImage, Sort) VALUES('$filename',$sortid)";
			if (!mysql_query($sql_update)) {
				$err = "Error saving banner: ".mysql_error();
			}
		} else {
			$err = "Error: Invalid file";
		}
	}		
}

// save page information 
if (isset($_POST["btnSave"])) {
	if ($_POST["bids"] != '') {
		$ids = explode("|", $_POST["bids"]);
		$c_ids = count($ids);
		$sids = explode("|", $_POST["sorts"]);
		$s_ids = count($sids);
		for ($i = 0; $i < $c_ids; $i++) {
			$sql_update = "UPDATE banner SET Link='".$_POST[$ids[$i]."_link"]."', Sort='".$_POST[$sids[$i]."_sort"]."' WHERE id=$ids[$i] LIMIT 1";
			// $sql_update = "UPDATE banner SET Link='".mysql_real_escape_string($_POST[$ids[$i])."_link"]."', Sort='".mysql_real_escape_string($_POST[$sids[$i])."_sort"]."' WHERE id=$ids[$i] LIMIT 1";
			// echo "SQL: " . $sql_update; exit;
			mysql_query($sql_update) or die("Banner Update Error: " . mysql_error());
		}
	}
}
?>
<form action="" method="post" enctype="multipart/form-data">
<table width="100%" border="0" cellpadding="5" cellspacing="2">
<tr>
    <td><strong>Upload new Banner:</strong><br/>
	<input type="file" style="width: 250px;" name="banner" id="banner" /> 
	<input type="submit" id="btnUpload" name="btnUpload" value="Upload Banner" />
    </td>
</tr>
<tr>
    <td></td>
</tr>
</table>
<table  style="width: 100%; margin-top: 20px;" cellpadding="5" cellspacing="5">
<?php
$sql_banner = "SELECT * FROM banner ORDER BY Sort ASC";
$result_banner = mysql_query($sql_banner);
while ($row_banner=mysql_fetch_array($result_banner)) {
	echo '<tr><td>';
	echo '<img src="../images/banner/'.$row_banner["BannerImage"].'"><br/>';
	echo '<small><i>(include http:// with the web address)</i></small><br />';
	echo 'Link: <input type="text" style="margin: 5px 0 5px 0; width: 300px;" id="'.$row_banner["id"].'_link" name="'.$row_banner["id"].'_link" value="'.$row_banner["Link"].'" /> ';
	echo 'Sort order: <input type="text" style="margin: 5px 0 5px 0; width: 20px;" id="'.$row_banner["Sort"].'_sort" name="'.$row_banner["Sort"].'_sort" value="'.$row_banner["Sort"].'" />';
	echo '<input type="button" style="margin-left: 10px;" id="btnDelete" name="btnDelete" value="Delete Banner" onClick="remBanner(\''.$row_banner["id"].'\')" />';
	echo '</td></tr>';
	$bids .= $row_banner["id"]."|";
	$sorts .= $row_banner["Sort"]."|";
}
$bids = substr($bids, 0, -1);
$sorts = substr($sorts, 0 , -1);
?>			
</table>
<hr/>
<input type="hidden" id="bids" name="bids" value="<?=$bids;?>" />
<input type="hidden" id="sorts" name="sorts" value="<?=$sorts;?>" />
<input type="submit" style="margin-top: 10px;" id="btnSave" name="btnSave" value="Save" />
</form>
<script>
$("#btnUpload").click(function() {
	if ($("#banner").val() == '') {
		alert('please select the file you would like to upload first');
		return false;
	}
});

function remBanner(id) {
	var del = confirm('Delete Banner');
	if (del) {
		$.post("includes/inc_settingsBanner.php", {
			"type":"delete",
			"id":id
		}, function(data) {
			alert(data);
			location.reload();
		});
	} else {
		return false;
	}
}
</script>