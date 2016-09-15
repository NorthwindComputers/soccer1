<?php
/****************************************
 * Main browsing file                   
 *                                                          
 * Programming: Richard Tuttle          
 * Updated: 27 June 2016                
 ***************************************/

require_once 'cpadmin/includes/db.php';
session_start();
$cg = mysql_real_escape_string($_GET["cg"]);
if($_GET["c"] != '') { 
	$cat = mysql_real_escape_string($_GET["c"]);
} elseif ($_GET["catstring"] != '') { 
	$catname = substr($_SERVER['REQUEST_URI'],1,-5);
	$catname = str_replace("%20"," ",$catname);
	$catname = str_replace("--", "/", $catname);
	$catname = str_replace("%27", "&#39;", $catname);
	$catname = str_replace("'", "&#39;", $catname);
	if ($catname == '') {
		$catname = mysql_real_escape_string($_GET["catstring"]);
	}

	$sql_cat = "SELECT id FROM category WHERE Category='$catname' LIMIT 1";
	$result_cat = mysql_query($sql_cat);
	$num_cat = @mysql_num_rows($result_cat);

	if ($num_cat > 0) {
		$row_cat = mysql_fetch_assoc($result_cat);
		$cat = $row_cat["id"];
	} else {
		$cat = "0";	
	}
} else { 
	if ($cg != '') {
		$cat = '';
	} else {
		$cat = "0";
	}
}

if (isset($_SESSION["categIDinit"])) {
	$cat = $_SESSION["categIDinit"];
}

if ($cat !="0" && $cat != '14' && $cat != '13' && $cat != '') {
	$sql_catinfo = "SELECT * FROM category WHERE id=$cat LIMIT 1";
	$result_catinfo = mysql_query($sql_catinfo);
	$row_catinfo = mysql_fetch_assoc($result_catinfo);

	if (@mysql_num_rows($result_catinfo)) {
		foreach($row_catinfo as $key=>$value) {
			$$key = stripslashes($value);
		}
	}
} elseif($cg != '') {
	$sql_cg = "SELECT * FROM customer_group WHERE GroupName='$cg' LIMIT 1";
	$result_cg = mysql_query($sql_cg);
	$row_cg = mysql_fetch_array($result_cg);

	if (@mysql_num_rows($result_cg)) {
		foreach($row_cg as $key=>$value) {
			$$key = stripslashes($value);
		}
	}
}

$page = "products";

if ($_GET["s"] != '') {
	$search = mysql_real_escape_string($_GET["s"]);
	$page = "search";
	$pgTitle = "Search Results for ";
    $pgTitle .= $search;
}

if ($_SESSION["category"] != $cat) {
	$_SESSION["filter"] = '';
	$_SESSION["filterid"] = '';
	unset($_SESSION["filter"]);
	unset($_SESSION["filterid"]);
	$_SESSION["category"] = $cat;		
}	

if ($_GET["p"] != '') {
	$p = mysql_real_escape_string($_GET["p"]);
} else {
	$p = 1;
}

if ($_GET["i"] != '') {
	$i = mysql_real_escape_string($_GET["i"]);
} else {
	$i = 12;
}

include_once("includes/mainHeader.php"); 
?>
<script language="javascript" type="text/javascript">
$(function() {
	<?php if (stristr($_SERVER['HTTP_USER_AGENT'], "Mobile")) { } else { ?>
	$('form').jqTransform({
		imgPath:'jqtransformplugin/img/'
	});
	<?php } ?>
	$("#divproducts").load("includes/inc_browser.php", {
		"type":"<?=$page;?>", 
		"cat":"<?=mysql_real_escape_string($_GET['c']);?>", 
		"search":"<?=$search;?>", 
		"cg":"<?=$cg;?>", 
		"page":"<?=$p;?>", 
		"items":"<?=$i;?>",
		"pricing":"<?=mysql_real_escape_string($_GET['pr']);?>"
	});
	$(".colorfilter").click(function() {
		$("#divproducts").html('<img src="images/loader.gif">');
		$.post("includes/inc_browser.php", {
			"type":"setfilter", 
			"filtertype":"Color", 
			"filterid":$(this).attr("href")
		}, function(data) {
			$("#divfilter").html(data);
			$("#divproducts").load("includes/inc_browser.php", {
				"type":"products", 
				"cat":"<?=$cat;?>", 
				"cg":"<?=$cg;?>"
			});
		});
		return false;
	});
});

function removefilter(filter) {
	$("#divproducts").html('<img src="images/loader.gif">');
	$.post("includes/inc_browser.php", {
		"type":"removefilter", 
		"filter":filter
	}, function(data) {
		location.reload();
	});
	return false;
}
		
function setDefaultCat1(filter) {
	var id = new String(filter.id);
	var cid = id.substring(id.indexOf(":")+1,id.length);
	var catName = id.substring(0,id.indexOf(":"))+".html";
	$.post("includes/inc_browser.php", {
		"type":"initCategId", 
		"idCat":cid
	}, function(data) {
		var pathname = new String(window.location.pathname);
		pathname=pathname.substring(0,pathname.lastIndexOf("/")+1);
		window.location.pathname = pathname+catName;
	});
	return false;
}

function filter(filtertype, filterid, filterdata) {
	if(filterdata != '') {
		$("#divproducts").html('<img src="images/loader.gif">');
		$.post("includes/inc_browser.php", {
			"type":"setfilter", 
			"filtertype":filtertype, 
			"filterid":filterid, 
			"filterdata":filterdata
		}, function(data) {
			$("#divfilter").html(data);
			$("#divproducts").load("includes/inc_browser.php", {
				"type":"products", 
				"cat":"<?=$cat;?>"
			});
		});
	}
}
</script>
</head>
<body>
<div id="maindiv">
<div class="Master_div">
	<?php include_once('includes/header.php'); ?>
	<div class="container container1">
    <div class="navigation">
      <div class="navi_L"></div>
      <div class="navi_C">
        <?php include_once('includes/topnav.php'); ?>
        <div class="clear"></div>
      </div>
      <div class="navi_R"></div>
      <div class="clear"></div>
    </div>
    <div class="browser_color">
    <div class="clear"></div>
	<?php
		if ($cg != '' || $cat == "14" || $cat == "13") { 
			$cat = "0"; 
		}
		$sql_sub = "SELECT id, Category FROM category WHERE ParentID=$cat ORDER BY Sort";
		$result_sub = mysql_query($sql_sub);
		if (mysql_num_rows($result_sub) > 0) {
     ?>
		<h1>Choose a Category</h1>
      		<div class="apparel_ul">
        	<ul>
	<?php
		while($row_sub = mysql_fetch_array($result_sub)) {
			$cateTitle = str_replace("-", "_", $row_sub["Category"]);
			$cateTitle = strtolower(str_replace(" ", "_", $cateTitle));
			echo '<li><a href="'.$cateTitle.'-c-'.$row_sub["id"].'.html ">'.$row_sub["Category"].'</a>';
		}		
	?>
	        </ul>
      	</div>
      <?php } ?>
	<div id="divfilter">
<?php
	function cmp($a, $b) {
    	$av1 = explode("-",$a);
		$av2 = explode("-",$b);
		$v1 = doubleval($av1[0]);
		$v2= doubleval($av2[0]);
		if ($v1 == $v2) {
        	return 0;
	    }
    	return ($v1 < $v2) ? -1 : 1;
	}
		if ($_SESSION["filter"] != '') {
			echo '<span style="14px Arial,Helvetica,sans-serif;padding-left: 17px;"><div style="background: url("../images/price_range_bg.png") repeat-x scroll left top transparent; height: 28px; padding-top: 6px; padding-left: 18px;">Filter <a href="javascript:;" onClick="removefilter(\'\');" style="font-size: 12px;">[clear filters]</a></div></span>';
			echo '<div style="padding: 5px 10px 5px 10px; color: #fff;">';
			$pos = strpos($_SESSION["filterid"], "|");
			if($pos === false) {
				$arrfids = array($_SESSION["filterid"]);
				$arrfilter = array($_SESSION["filter"]);
			} else {
				$arrfids = explode("|", $_SESSION["filterid"]);
				$arrfilter = explode("|",$_SESSION["filter"]);
			}
			$carr = count($arrfids);
			for ($i=0; $i<$carr; $i++) {
				$ftype = '';
				$fvalue = '';
				$fval = explode("=", $arrfilter[$i]);
					$ftype = $fval[0];
					$fvalue = str_replace("'", "", $fval[1]);
					$sql_display = "SELECT DisplayType FROM options WHERE id=$arrfids[$i] LIMIT 1";
					$result_display = mysql_query($sql_display);
					$row_display = mysql_fetch_assoc($result_display);
					if($ftype == "Trim") {
						$rem = "remove trim";
						$ftype = "Color";
					} else {
						$rem = "remove";
					}
					if($row_display["DisplayType"] == "Imageblock") {
						echo '<ul class="colors">';
						$sql_colors = "SELECT a.id, a.$ftype, a.Icon FROM ".strtolower($ftype)."s a, options_filter f WHERE f.FilterID=a.id AND f.OptionID=$arrfids[$i] AND a.$ftype='$fvalue' LIMIT 1";
						$result_colors = mysql_query($sql_colors);
						while($row_colors = mysql_fetch_array($result_colors)) {
							echo '<li><img src="images/'.$row_colors["Icon"].'" title="'.$row_colors["Color"].'" alt="'.$row_colors["Color"].'" /> [ <a onclick="removefilter(\''.str_replace("'","",$arrfilter[$i]).'\');" style="font-size: 13px;" href="javascript:;">'.$rem.'</a> ]</li>';
						}
						echo '</ul>';
					} else {
						echo '<span style="font-weight: bold;"/>'.$ftype."</span>: ".$fvalue." [ <a onclick=\"removefilter('".str_replace("'","",$arrfilter[$i])."');\" style=\"font-size: 13px;\" href=\"javascript:;\">$rem</a> ]";
					}
					echo '<div class="clear" style="margin-top: 10px;"></div>';
				}
				echo '</div>';
			}
		?>
        </div>
	<!-- </div> -->
    <div class="clear"></div>
    <?php
		if($cat == 0) {
			$sql_filters = "SELECT DISTINCT id, FrontendTitle, FilterBy, DisplayType FROM options WHERE DisplaySearchNav = 'Yes, All Pages'";
		} else {
			$sql_filters = "SELECT DISTINCT o.id, o.FrontendTitle, o.FilterBy , o.DisplayType FROM options o, options_category c, options_sort s WHERE o.id=c.OptionID AND o.FilterBy=s.Name AND (c.CategoryID = $cat OR o.DisplaySearchNav = 'Yes, All Pages') ORDER BY s.Sort";
		}
		$result_filters = mysql_query($sql_filters);
		while($row_filters = mysql_fetch_array($result_filters)) {
			echo "<h1>$row_filters[FrontendTitle]</h1>";
			if($row_filters["DisplayType"] == "Imageblock") {
				echo '<ul class="colors">';
				$sql_images = "SELECT Name, FilterID FROM options_filter WHERE OptionID = $row_filters[id]";
				$result_images = mysql_query($sql_images);
				while($row_images = mysql_fetch_array($result_images)) {
					if($row_images["Name"] == "Trim") {
						$fieldname = "Color";
					} else {
						$fieldname = $row_images["Name"];
					}
					$sql_imgName = "SELECT id, $fieldname, Icon FROM ".strtolower($fieldname)."s WHERE id=$row_images[FilterID] LIMIT 1";
					$result_imgName = mysql_query($sql_imgName);
					$row_imgName = mysql_fetch_assoc($result_imgName);
					echo '<li><a onClick="filter(\''.$row_filters["FilterBy"].'\', \''.$row_filters["id"].'\', \''.$row_imgName[$fieldname].'\');" ><img src="images/'.$row_imgName["Icon"].'" title="'.$row_imgName[$fieldname].'" alt="'.$row_imgName[$fieldname].'" /></a></li>';
				}
				echo "</ul>";
			} else {
				echo '<form action="" method="post" style="margin:5px 0 5px 0; float:left;">';
				echo "<select id=\"$row_filters[FilterBy]\" onChange=\"filter('$row_filters[FilterBy]', '$row_filters[id]', this.value);\">";
				echo '	<option value="">Select...</option>';
				$avalues = array();
				$sql_options = "SELECT Name, FilterID, PriceRange FROM options_filter WHERE OptionID = $row_filters[id] ORDER BY PriceRange ASC";
				$result_options = mysql_query($sql_options);
				while($row_options = mysql_fetch_array($result_options)) {
					if($row_filters["FilterBy"] != 'Price') {
						$field = '';
						$table = '';
						switch($row_options["Name"]) {
							case "Brand":
								$field = "Manufacturer";
								$table = "manufacturers";
								break;
							case "Size":
								$field = "Size";
								$table = "sizes";
								break;
							case "Trim":
							case "Color":
								$field = "Color";
								$table = "colors";
								break;
							case "Style":
								$field = "Style";
								$table = "styles";
								break;
						}

						$sql_optname = "SELECT $field FROM $table WHERE id=$row_options[FilterID] LIMIT 1";
						$result_optname = mysql_query($sql_optname);
						$row_optname = mysql_fetch_assoc($result_optname);
						$value = $row_optname[$field];
						$avalues[] = ucwords($value);
					} else {
						$avalues[] = ucwords($row_options["PriceRange"]);
					}
				}
				if($row_filters["FilterBy"] != 'Price') 
					sort($avalues);
				else
					usort($avalues, "cmp");
				foreach ($avalues as $key => $val) {
					if($row_filters["FilterBy"] != 'Price') 
						echo "<option value=\"$avalues[$key]\">$avalues[$key]</option>";
					else
						echo "<option value='".$avalues[$key]."'>$".$avalues[$key]."</option>";
				}
				echo "</select>";
				echo "</form>";
			}
		echo "<div class=\"clear\"></div>";
		}
	?>
    </div>
    <!-- :: Product Images :::::::::::::::::::::::::::::::::::::::::::::::::: -->
    <div id="divproducts" class="browser_product">
		<img src="images/loader.gif" />      
    </div>
    <div class="clear"></div>
  </div>
  <!-- Container Div ends here --> 
  <!-- Footer Starts from here -->
  <div class="footer">
    <div class="foot_box">
	<?php include_once("includes/footer.php"); ?>
    </div>
  </div>
  <div class="mobileFooter"><hr><a href="page.php?page=terms">Terms &amp; Conditions</a> | <a href="page.php?page=privacy_policy">Privacy Policy</a><br><a href="page.php?page=shipping">Shipping Policy</a> | <a href="page.php?page=returns">Returns &amp; Exchanges</a></div>
  <!-- Footer Div ends here --> 
</div>
</div>
</body>
</html>
<?php mysql_close($conn); ?>