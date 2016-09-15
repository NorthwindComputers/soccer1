<?php
/**
 * category admin view
 *
 * Version: 1.2.2
 * Updated: 10 September 2014
 * By: Richard Tuttle
 */
 
if (isset($_GET['moveid'])) { $filtered_var = htmlspecialchars($_GET['moveid'], ENT_QUOTES); $_GET['moveid'] = $filtered_var;}
if (isset($_POST['moveid'])) { $filtered_var = htmlspecialchars($_POST['moveid'], ENT_QUOTES); $_POST['moveid'] = $filtered_var;}
if (isset($_REQUEST['moveid'])) { $filtered_var = htmlspecialchars($_REQUEST['moveid'], ENT_QUOTES); $_REQUEST['moveid'] = $filtered_var;}
if (isset($_GET['moveto'])) { $filtered_var = htmlspecialchars($_GET['moveto'], ENT_QUOTES); $_GET['moveto'] = $filtered_var;}
if (isset($_POST['moveto'])) { $filtered_var = htmlspecialchars($_POST['moveto'], ENT_QUOTES); $_POST['moveto'] = $filtered_var;}
if (isset($_REQUEST['moveto'])) { $filtered_var = htmlspecialchars($_REQUEST['moveto'], ENT_QUOTES); $_REQUEST['moveto'] = $filtered_var;}

include_once('includes/header.php');

if(isset($_POST["btnDelete"])) {
	$sql_delete = "DELETE FROM category WHERE id='$_POST[rid]' LIMIT 1";
	if (!mysql_query($sql_delete)) {
		echo "Error Removing Category: ".mysql_error();
	}
}
	
if(isset($_POST["btnMove"])) {
	$sql_move = "UPDATE category SET ParentID='$_POST[moveto]' WHERE id='$_POST[moveid]' LIMIT 1";
	if (!mysql_query($sql_move)) {
		echo "Error moving category: ".mysql_error();
	}
}
	
if(isset($_POST["btnSaveSort"])) {
	$order = str_replace("SORT[]=",'', $_POST["catOrder"]);
	$catorder = explode("&", $order);
	$sid = 1;
	foreach($catorder as $catid) {
		$sql_sortUpdate = "UPDATE category SET Sort='$sid' WHERE id='$catid' LIMIT 1";
		if(!mysql_query($sql_sortUpdate)) {
			$err = "Error updating category order!";
		}
		$sid++;
	}
}
	
if(isset($_POST["btnSubmit"])) {
	foreach ($_POST as $key=>$value) {
		$$key = addslashes($value);
	}
	if ($rid != '') {
		$sql = "UPDATE category SET Category='$categoryname', Status='$status', Description='$description', PageTitle='$pagetitle', CustomURL='$customurl', Image='', MetaTags='$metatag', MetaKeywords='$metakeyword', MetaDescription='$metadescription' WHERE id=$rid LIMIT 1";
	} else {
		if ($parentid == '') { $parentid = 0; }
		$sql = "INSERT INTO category(Category, ParentID, Status, Description, PageTitle, CustomURL, Image, MetaTags, MetaKeywords, MetaDescription) VALUES('$categoryname', $parentid, '$status', '$description', '$pagetitle', '$customurl', '', '$metatag', '$metakeywords', '$metadescription')";
	}
	if (!mysql_query($sql)) {
		echo "Error saving category: " . mysql_error();
	}
}
	
function getCategories($treetype, $name) {
	$sql_cat = "SELECT id, Category FROM category WHERE ParentID=0 ORDER BY Sort";
	$result_cat = mysql_query($sql_cat);
	echo '<ul id="'.$name.'" class="filetree">';
	while($row_cat=mysql_fetch_array($result_cat)) {
		echo "<li><span class=\"folder\"><a href=\"#categorydetails\" name=\"$treetype\" rel=\"$row_cat[id]\" style=\"padding: 0 0 0 5px;\" >$row_cat[Category]</a></span>";
		subCategories($row_cat["id"], $treetype);
		echo "</li>";
	}
	echo '</ul>';
}
	
function subCategories($parent, $treetype) {
	$sql_sub = "SELECT id, Category FROM category WHERE ParentID=$parent ORDER BY Sort";
	$result_sub = mysql_query($sql_sub);
	$num_sub = mysql_num_rows($result_sub);
	if($num_sub>0) {
		echo '<ul>';
		while($row_sub=mysql_fetch_array($result_sub)) {
			echo "<li><span class=\"folder\"><a href=\"#categorydetails\" name=\"$treetype\" rel=\"$row_sub[id]\" style=\"padding: 0 0 0 5px;\">$row_sub[Category]</a></span>";
			subCategories($row_sub["id"], $treetype);
			echo "</li>";
		}
		echo '</ul>';
	} 
}
include_once("includes/mainHeader.php");
?>
<script type="text/javascript" src="js/jquery.treeview.js"></script>
<script type="text/javascript" src="../ckeditor/ckeditor.js"></script>
<link rel="stylesheet" href="css/jquery.treeview.css" type="text/css" />
<script type="text/javascript">
$(document).ready(function() {
	$("#categories").treeview();
	$("#move").treeview();
	$('a[href="#categorydetails"]').click(function() {
		var treetype = $(this).attr("name");
		if (treetype=="main") {
			$("#divCategory").load("includes/inc_category.php", {
				"id":$(this).attr("rel"), 
				"type":"categorydetails"
			});
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

	$('.window .close').click(function (e) {         
		e.preventDefault();         
		$('#mask, .window').hide();     
	});                
		
	$('#mask').click(function () {         
		$(this).hide();         
		$('.window').hide();     
	});		
		
	$("#AddMainCat").click(function() {
		$("#divCategory").load("includes/inc_category.php", {
			"id":"", 
			"type":"categorydetails"
		});
	});
		
	$("#SortMainCat").click(function() {
		$("#divCategory").load("includes/inc_category.php", {
			"pid":"0", 
			"type":"sort"
		});
		$('html, body').animate({scrollTop:0}, 'fast'); 
	});	
});
	
function addCategory() {
	$("#parentid").val($("#rid").val());
	$("#rid").val('');
	$("#categoryname").val('');
	$("#description").val('');
	$("#pagetitle").val('');
	$("#customurl").val('');
	$("#metakeywords").val('');
	$("#metatag").val('');
	$("#metadescription").val('');
}
	
function moveCategory() {       
	var id = '#movecategory';               
	var maskHeight = $(document).height();         
	var maskWidth = $(window).width();               
	$("#movecat").html('<img src="images/loader.gif" />');
	$("#movecat").load("includes/inc_category.php", {
		"type":"move", 
		"moveid":$("#moveid").val()
	});   
	$('#mask').css({'width':maskWidth,'height':maskHeight});                   
	$('#mask').fadeIn(500);             
	$('#mask').fadeTo("slow",0.8);                 
	var winH = $(window).height();         
	var winW = $(window).width();                         
	$(id).css('top',  winH/2-$(id).height()/2);         
	$(id).css('left', winW/2-$(id).width()/2);
	$(id).fadeIn(2000);		  
}
	
function sortCategory(id) {
	$("#divCategory").html('<img src="../images/loader.gif" />');
	$("#divCategory").load("includes/inc_category.php", {
		"type":"sort",
		"pid":id
	});
}
</script>
</head>
<body>
<!-- Move Category -->
<div id="move">
   <div id="movecategory" class="window" style="text-align: center;">
            <div style="width: 450px; height: 380px; text-align: center; overflow: auto; text-align:left;">
            	<h2>Select Category to Move To:</h2>
                <div id="movecat"></div>
            </div>
            <form action="" method="post">
            	<input type="hidden" id="moveid" name="moveid" value="" />
                <input type="hidden" id="moveto" name="moveto" value="" />
                <input type="button" class="close" style="float: right; border: 1px solid #c9c9c9; background: #ef9800; width: 140px; height: 22px; margin-right: 10px;" id="btnClose" name="btnClose" value="Close" />
            	<input type="submit" style="float: right; border: 1px solid #c9c9c9; background: #ef9800; width: 140px; height: 22px;" id="btnMove" name="btnMove" value="Move" />
            </form>
            <!-- <a href="#" class="close">Close it</a> -->
   </div>
   <div id="mask"></div> 
</div> 
<!-- End More View -->
<!-- Master Div starts from here -->
<div class="Master_div"> 
	<!-- Header Div starts from here -->
  	<div class="PD_header">
    	<div class="upper_head"></div>
    	<div class="navi"><?php include('includes/menu_main.php'); ?>
      		<div class="clear"></div>
    	</div>
  	</div>
  	<!-- Header Div ends here --> 
  	<!-- Product Detail Div starts from here -->
  	<div class="options">
	<h1>Category<br /><span>----------------------------------------------------------</span></h1>
    <div class="clear"></div>
	<table width="100%" border="0" align="center" cellpadding="5" cellspacing="1">
  	<tr>
    	<td width="220" align="left" valign="top" class="setting"><form action="" method="post"><?php getCategories("main", "categories"); ?>
        <input type="button" style="font-size:13px; color: #fff; margin-top: 40px;" class="button" id="AddMainCat" name="AddMainCat" value="Add Main Category" /><br/>
        <input type="button" style="font-size:13px; color: #fff; margin-top: 5px;" class="button" id="SortMainCat" name="SortMainCat" value="Sort Main Category" /></form></td>
    	<td align="left" valign="top">
		<form action="" method="post" >
		<div id="divCategory"><?php 
		if ($err != '') {
			echo '<h1>'.$err.'</h1>';
		}
		?></div>
        </form></td>
  	</tr>
	</table>
</div>
</body>
</html>
<?php
	mysql_close($conn);
?>