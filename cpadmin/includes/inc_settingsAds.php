<?php
/***************************
 * Ads settings controller
 * Version: 1.2
 * By: Richard Tuttle
 * Last Updated: 01 May 2013
 ***************************/
	if(isset($_POST["btnUpload"])) {
		if($_FILES["ads1"]["error"] > 0) {
			$err = "Error:".$_FILES["ads1"]["error"];
		} else {
			$filename1 = $_FILES["ads1"]["name"];
			$folderloc = "../images/ads/";
			move_uploaded_file($_FILES["ads1"]["tmp_name"],$folderloc.$filename1);
		    $link1= $_POST["link1"];
			$sql_update = "UPDATE ads SET box1Link = '".$link1."' ";
			if($filename1!="")
				$sql_update.= ", box1AdImage = '".$filename1."' " ;
			$sql_update.="WHERE id =1;";
			if(!mysql_query($sql_update)) {
				$err = "Error saving ads 1: ".mysql_error();
			}
		}
	
		if($_FILES["ads2"]["error"] > 0) {
			$err = "Error:".$_FILES["ads2"]["error"];
		} else {
			$filename1 = $_FILES["ads2"]["name"];
			$folderloc = "../images/ads/";
			move_uploaded_file($_FILES["ads2"]["tmp_name"],$folderloc.$filename1);
		    $link2= $_POST["link2"];
			$sql_update = "UPDATE ads SET box2Link = '".$link2."' ";
			if($filename1!="")
				$sql_update.= ",box2AdImage = '".$filename1."' " ;
			$sql_update.="WHERE id =1;";
			if(!mysql_query($sql_update)) {
				$err = "Error saving ads 1: ".mysql_error();
			}
		}
	}
	
	if(isset($_POST["btnSave"])) {
		$l1 = addslashes($_POST["link1"]);
		$l2 = addslashes($_POST["link2"]);
		$sql_update = "UPDATE ads SET box1Link='$l1', box2Link='$l2'";
		mysql_query($sql_update);
	}
	
     $src1="";
	 $src2="";
	 $link1="";
	 $link2="";
	 $sql = "select * from ads where id=1";
	 $query = mysql_query($sql);
	 $result = mysql_fetch_assoc($query);
	 $src1 = "../images/ads/".$result["box1AdImage"];
	 $src2 = "../images/ads/".$result["box2AdImage"];
	 $link1 = stripslashes($result["box1Link"]);
	 $link2 = stripslashes($result["box2Link"]);
	?>
	<form action="" method="post" enctype="multipart/form-data" >
		<table width="100%" border="0" cellpadding="5" cellspacing="2">
		<tr>
            <td colspan="2"><strong>Upload new Ads For Box 1:</strong><br/>
			<input type="file" style="width: 250px;" name="ads1" id="ads1" /></td>
		</tr>
        <tr>
            <td colspan="2"><strong>Ads 1 Link:</strong><br/>
            <small><i>(include http:// with the web address)</i></small><br />
            <input type="text" name="link1" size="45" value="<?=$link1;?>" id="link1"/></td>
        </tr>
        <tr>
            <td colspan="2"><strong>Upload new Ads For Box 2:</strong><br/>
			<input type="file" style="width: 250px;" name="ads2" id="ads2" /></td>
        </tr>
		<tr>
            <td colspan="2"><strong>Ads 2 Link:</strong><br/>
            <small><i>(include http:// with the web address)</i></small><br />
            <input type="text" name="link2" size="45" value="<?=$link2;?>" id="link2"/>
            <input type="submit" id="btnUpload" name="btnUpload" value="Upload Ads" /></td>
   		</tr>
		<tr>
  			<td width="44%"><strong>Box1</strong></td>
  			<td width="56%"><strong>Box2</strong></td>
		</tr>
		<tr>
  			<td><a href="http://<?=$link1;?>"><img src="<?=$src1;?>" style="width:150px;height:150px;"/></a></td>
  			<td><a href="http://<?=$link2;?>"><img src="<?=$src2;?>" style="width:150px;height:150px;"/></a></td>
		</tr>
        <tr>
        	<td colspan="2"></td>
		</tr>
		</table>
		<table  style="width: 100%; margin-top: 20px;" cellpadding="5" cellspacing="5"></table>
		<hr/>
		<input type="hidden" id="bids" name="bids" value="<?=$bids;?>" />
		<input type="submit" style="margin-top: 10px;" id="btnSave" name="btnSave" value="Save" />
        </form>
	<script>
		$("#btnUpload").click(function() {
			if($("#ads1").val() == '' && $("#ads2").val()=="" $("#link1").val() == '' && $("#link2").val()=='') {
				alert('please select the file you would like to upload first');
				return false;
			}
		});

		function remBanner(id) {
			var del = confirm('Delete Banner');
			if(del) {
				$.post("includes/inc_settingsBanner.php", {"type":"delete", "id":id}, function(data) {
					alert(data);
					location.reload();
				});
			} else {
				return false;
			}
		}
	</script>