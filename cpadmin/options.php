<?php

	include("includes/header.php");
	
	$page = 'list';
	$id = '';
	if($_GET["id"] != '') {
		$page = "details";
		$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
	}
	if($_GET["id"] == 'new') {
		$page = "new";
		$id = 0;
	}
	
	if(isset($_POST["btnSave"])) {
		foreach($_POST as $key=>$value) {
			$$key = addslashes($value);
		}
		
		$sql_add  = "INSERT INTO options(BackendTitle, FrontendTitle, DisplaySearchNav, FilterBy, DisplayType, OptionCategory) ";
		$sql_add .= "VALUES('$BackendTitle', '$FrontendTitle', '$DisplaySearchNav', '$FilterBy', '$DisplayType', '$OptionCategory') ";
		if(!mysql_query($sql_add)) {
			echo "Error adding option: ".mysql_error();
		} else {
			if(!empty($_POST["category"])) {
				
				$categories = $_POST["category"];
				$c = count($categories);
				$lastid = mysql_insert_id();
				for($i=0; $i<$c; $i++) {
					$sql_cats = "INSERT INTO options_category(OptionID, CategoryID) VALUES($lastid, $categories[$i])";
					mysql_query($sql_cats);
				}
			}
			
			if(!empty($_POST["filter"])) {
				
				$filters = $_POST["filter"];
				$c = count($filters);
				for($i=0; $i<$c; $i++) {
					
					if($FilterBy == 'Brand') {
						$sql_ManuName = "SELECT Manufacturer FROM manufacturers WHERE id=$filters[$i] LIMIT 1";
						$result_ManuName = mysql_query($sql_ManuName);
						$row_ManuName = mysql_fetch_assoc($result_ManuName);
						$prange = $row_ManuName["Manufacturer"];
						
					} else {
						$prange = '';
					}
					
					$sql_filter = "INSERT INTO options_filter(OptionID, `Name`, FilterID, PriceRange) VALUES($lastid, '$FilterBy', $filters[$i], '$prange')";
					mysql_query($sql_filter);
				}
			}
		}
		$page = "list";
	}
	
	if(isset($_POST["btnUpdate"])) {
		foreach($_POST as $key=>$value) {
			$$key = addslashes($value);
		}
		
		$sql_update = "UPDATE options SET BackendTitle='$BackendTitle', FrontendTitle='$FrontendTitle', DisplaySearchNav='$DisplaySearchNav', FilterBy='$FilterBy', DisplayType='$DisplayType', OptionCategory='$OptionCategory' ";
		$sql_update .= "WHERE id=$id LIMIT 1";
		if(!mysql_query($sql_update)) {
			echo "Error updating option: ".mysql_error();
		} else {
		
			if(!empty($_POST["category"])) {
				$sql_delcats = "DELETE FROM options_category WHERE OptionID=$id";
				mysql_query($sql_delcats);
				
				$categories = $_POST["category"];
				$c = count($categories);
				for($i=0;$i<$c; $i++) {
					$sql_cats = "INSERT INTO options_category(OptionID, CategoryID) VALUES($id, $categories[$i])";
					mysql_query($sql_cats);
				}
			}
			if(!empty($_POST["filter"])) {
				$sql_delfil = "DELETE FROM options_filter WHERE OptionID=$id";
				mysql_query($sql_delfil);
				
				$filters = $_POST["filter"];
				$c = count($filters);
				for($i=0;$i<$c;$i++) {
					
					if($FilterBy == 'Brand') {
						$sql_ManuName = "SELECT Manufacturer FROM manufacturers WHERE id=$filters[$i] LIMIT 1";
						$result_ManuName = mysql_query($sql_ManuName);
						$row_ManuName = mysql_fetch_assoc($result_ManuName);
						$prange = $row_ManuName["Manufacturer"];
						
					} else {
						$prange = '';
					}
					$sql_filters = "INSERT INTO options_filter(OptionID, `Name`, FilterID, PriceRange) VALUES($id, '$FilterBy', $filters[$i], '$prange')";
					mysql_query($sql_filters);
				}
			}
			if($FilterBy == 'Price') {
				$sql_delprice = "DELETE FROM options_filter WHERE OptionID=$id AND Name<>'Price'";
				mysql_query($sql_delprice);
			}
		}
		$page = "list";
	}
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title></title>
	<link rel="stylesheet" href="css/styles.css" type="text/css" />
   	<link rel="stylesheet" href="css/jquery.ui.datepicker.css">
	<link rel="stylesheet" href="css/jquery.ui.theme.css">
	<link rel="stylesheet" href="css/jquery.treeview.css" type="text/css" />
	<script type="text/javascript" src="js/jquery-1.4.4.min.js"></script>
   	<script type="text/javascript" src="js/jquery.treeview.js"></script>
    <script type="text/javascript" src="js/jquery.ui.core.js"></script>
	<script type="text/javascript" src="js/jquery.ui.widget.js"></script>
	<script type="text/javascript" src="js/jquery.ui.datepicker.js"></script>
	<script language="javascript" type="text/javascript">
		$(document).ready(function() {
				$("#options").load("includes/inc_options.php", {"type":"<?=$page;?>", "id":"<?=$id;?>"});
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
      <!-- Product Detail Div starts from here -->
      	<div class="PD_main_form">
            <div class="orders" id="options">
            </div>
            <div class="clear"></div>
  		</div>
      <!-- Product Detail Div ends here --> 
    </div>

</body>
</html>
<?php mysql_close($conn); ?>