<?php
/**
 * CMS admin page
 *
 * Updated: 10 June 2015
 * By: Richard Tuttle
 */

	include_once("includes/header.php");
	// print_r($_SESSION);

	$p = mysql_real_escape_string($_GET["p"]);
	$saveNewButton = $_POST["btnSaveNew"];
	$saveUpdateButton = $_POST["btnSaveUpdate"];
	$deleteButton = $_POST["btnDelete"];
	
	if($p == '') {
		$page = "list";
	} else {
		$page = $p;
	}
	
	if(isset($saveNewButton)) {
		foreach($_POST as $key=>$value) {
			$$key = addslashes($value);
		}

		if($Type == "Footer") {
			$sql_foot = "UPDATE cms SET Type='Page' WHERE Type='Footer'";
			mysql_query($sql_foot);
		}
		
		if($Type == "Home") {
			$sql_home = "UPDATE cms SET Type='Page' WHERE Type='Home'";
			mysql_query($sql_home);
		}
		
		if($Type == "Club") {
			$sql_home = "UPDATE cms SET Type='Page' WHERE Type='Club'";
			mysql_query($sql_home);
		}
		
		$sql_new  = "INSERT INTO cms(PageName, LastUpdated, updatedBy, LeftNav, PageTitle, MetaKeywords, MetaDescription, Content, prevContent, Type) VALUES('$PageName', current_date(),'" . $_SESSION['userid'] . "', '$LeftNav', '$PageTitle', '$MetaKeywords', '$MetaDescription', '$Content', '', '$Type')";
		if(!mysql_query($sql_new)) {
			echo "error adding page: " . mysql_error();
		} else {
			$page = "list";
		}

		$to = 'richard@northwind.us,kensel@northwind.us';
		$sub = "CMS uploaded with new page!";
		$msg = $_SESSION['userid'] . " just uploaded a NEW page to the CMS and the " . $PageName . " of the database.  FYI!";
		mail($to, $sub, $msg);
	}
	
	if(isset($saveUpdateButton)) {
		foreach($_POST as $key=>$value) {
			$$key = addslashes($value);
		}

		if($Type == "Footer") {
			$sql_foot = "UPDATE cms SET Type='Page' WHERE Type='Footer'";
			mysql_query($sql_foot);
		}
		
		if($Type == "Home") {
			$sql_home = "UPDATE cms SET Type='Page' WHERE Type='Home'";
			mysql_query($sql_home);
		}
		
		if($Type == "Club") {
			$sql_home = "UPDATE cms SET Type='Page' WHERE Type='Club'";
			mysql_query($sql_home);
		}

		// get current content and save to prevContent before updating
		$cGrab = "SELECT Content FROM cms WHERE id='$id' LIMIT 1";
		$cGrabResult = mysql_query($cGrab);
		$cGrabRow = mysql_fetch_assoc($cGrabResult);
		$sql_update  = "UPDATE cms SET PageName='$PageName', LastUpdated=current_date(), updatedBy='" . $_SESSION['userid'] . "', LeftNav='$LeftNav', PageTitle='$PageTitle', MetaKeywords='$MetaKeywords', MetaDescription='$MetaDescription', Content='$Content', prevContent='" . $cGrabRow['Content'] . "', Type='$Type'";
		$sql_update .= "WHERE id=$id";
		if(!mysql_query($sql_update)) {
			echo "error updating page: " . mysql_error();
		}
		// check for content differences
		//$old = $cGrabRow['Content'];
		//$new = $Content;
		//$diff = xdiff_string_diff($old, $new, 1);
		$to = 'richard@northwind.us,kensel@northwind.us';
		$sub = "CMS content updated!";
		$msg =  "FYI! " . $_SESSION['userid'] . " just updated the CMS page " . $PageName . " in the database.\n\n";
		//if (is_string($diff)) {
		//	$msg .= "The content difference from the previous version is:\n";
		//	$msg .= $diff;
		//} else {
		//	$msg .= "No difference in content found.";
		//}
		mail($to, $sub, $msg);
	}

	if(isset($deleteButton)) {
		$sql_del = "DELETE FROM cms WHERE id=$id LIMIT 1";
		mysql_query($sql_del);
		$page = "list";
	}

	$pgTitle = "Content Management System";
	include_once("includes/mainHeader.php");
?>
	<script type="text/javascript" src="../ckeditor/ckeditor.js"></script>
	<script language="javascript" type="text/javascript">
		$(function(){
			$(".cmsaddnew").hover(function() {
					$(this).attr("src", "images/plus_hover.png");
				}, function() {
					$(this).attr("src", "images/plus.png");
			});
			$("#cmscontent").load("includes/inc_cms.php", {
				"type":"<?=$page;?>", 
				"id":"<?=$_GET['id'];?>"
			});
		});
	</script>
	</head>
	<body>
<!-- Master Div starts from here -->
<div class="Master_div"> 
	   <!-- Header Div starts from here -->
    	<div class="PD_header">
    		<div class="upper_head"></div>
    		<div class="navi">
          		<?php include('includes/menu_main.php'); ?>
          	<div class="clear"></div>
        	</div>
  		</div>
      <!-- Header Div ends here --> 
      <!-- Products view Div starts from here -->
      <div class="product_view">
    	<div class="PV_top">
          <div class="clear"></div>
        </div>
        <div style="float: right;"><a href="cms.php?p=new"><img src="images/plus.png" class="cmsaddnew" /></a></div>
    <div id="cmscontent" class="PV_center"></div>
  </div>
      <!-- Products view Div ends here --> 
    </div>
</body>
</html>