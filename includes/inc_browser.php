<?php
/******************************************
 * frontend cat/prod/search include file  
 *                                                                  
 * Updated: 08 August 2016                   
 * By: Richard Tuttle                     
 *****************************************/

session_start(); // begin customer session
require_once '../cpadmin/includes/db.php'; // connect to the database
	
if ($_POST["type"]=="initCategId") {
	$_SESSION["categIDinit"] = $_POST["idCat"];
}
 
if ($_POST["type"] == "products") {
		$cat = $_POST["cat"];
		$cg = $_POST["cg"];
		$pitems = $_POST["items"];
		$ppage = $_POST["page"];
		$pri = $_POST["pricing"];
		$plimit = 5;

		if ($pitems == '') { 
			$pitems = 12; 
		}

		if ($ppage == '') { 
			$ppage = 1; 
		}

		if ($pitems != 'all') {
			$poffset = ($ppage - 1) * $pitems;
		} else {
			$poffset = 0;
		}
		
		if (isset($_SESSION["categIDinit"])) {
			$cat = $_SESSION["categIDinit"];
		}
		  
		if ($cat != "0" && $cat != '') {
			$sql_catdes = "SELECT Description FROM category WHERE id=$cat LIMIT 1";
			$result_catdes = mysql_query($sql_catdes);
			$row_catdes = mysql_fetch_assoc($result_catdes);
			$description = stripslashes($row_catdes["Description"]);
		} elseif ($cg != '') {
			$sql_cg = "SELECT Description FROM customer_group WHERE GroupName='$cg' LIMIT 1";
			$result_cg = mysql_query($sql_cg);
			$row_cg = mysql_fetch_assoc($result_cg);
			$description = stripslashes($row_cg["Description"]);
		}
		
		$sql_products = "SELECT DISTINCT p.id, p.BrowserName, p.BrowserName2, p.BrowserName3, p.NoneMemberPrice, p.VIPPrice, p.BrowserAddInfo, p.isSpecial, p.SpecialCategory, p.SpecialPrice, p.ProductType, o.ColorImage, p.ImprintCatID, p.ProductDetailName ";
		if ($cg != '') {
			$sql_products .= "FROM products p, product_options o, product_browser b WHERE p.id=o.ProductID AND p.Status='Enabled' AND p.AvailableQty>0 AND p.CustomerGroupAvailability='$cg'";
		} elseif($cat == '0') {
			$sql_products .= "FROM products p, product_options o, product_browser b WHERE p.id=o.ProductID AND p.Status='Enabled' AND p.AvailableQty>0 AND p.CustomerGroupAvailability=''";
		} elseif ($cat == "13") {
			$sql_products .= "FROM products p, product_options o, product_browser b WHERE p.id=o.ProductID AND p.isSpecial='True' AND p.Status='Enabled' AND p.showSpecial='yes' AND p.AvailableQty>0 AND p.CustomerGroupAvailability=''";
		} elseif($cat == "14") {
			$sql_products .= "FROM products p, product_options o, product_browser b WHERE p.id=o.ProductID AND current_date >= str_to_date(NewFromDate,'%c/%d/%Y') AND current_date <= str_to_date(NewToDate, '%c/%d/%Y') AND p.Status='Enabled' AND p.AvailableQty>0 AND p.CustomerGroupAvailability=''";
		} else {
			$sql_products .= "FROM products p, product_options o, product_browser b, category_items c WHERE p.id=o.ProductID AND p.id=c.ProductID AND p.Status='Enabled' AND p.AvailableQty>0 AND c.CategoryID=$cat AND p.CustomerGroupAvailability='' ";
		}

		$iscolor = 'no';
		if($_SESSION["filter"] != '') {
			$arrfilter = explode("|", $_SESSION["filter"]);
			$cfilter=count($arrfilter);
			for ($i = 0; $i < $cfilter; $i++) {
				$filter = explode('=', $arrfilter[$i]);
				if ($filter[0] == "Color" || $filter[0] == "Trim") { 
					$iscolor='yes'; 
				}
				if ($filter[0] == "Brand" || $filter[0] == "Style") {
					$sql_products .= " AND p.".$arrfilter[$i];
				} elseif ($filter[0] == "Price") {
					$price = explode("-",str_replace("'","",$filter[1]));
					$sql_products .= " AND p.VIPPrice>=$price[0] AND p.VIPPrice<=$price[1] ";
					$orderby = " ORDER BY CAST(p.VIPPrice AS DECIMAL(8, 2)) ";
				} elseif ($filter[0] == "Trim") {
					$sql_products .= " AND o.TrimColor=".$filter[1];
				} elseif ($filter[0] == "Color") {
					$colorfilter = str_replace("'","",$filter[1]);
					$sql_products .= " AND o.Color LIKE '$colorfilter%' ";
				} else {
					$sql_products .= " AND o.".$arrfilter[$i];
				}
			}
		}

		if ($iscolor=='no') {
			$sql_products .= " AND b.ProductID=p.id AND o.ColorImage=b.Image";
		} else {
			$sql_products = str_replace(", product_browser b", "", $sql_products);
		}
		
		if ($pri == "highlow") {
			$sql_products .= " ORDER BY p.VIPPrice * 1 DESC"; 
		} elseif ($pri == "lowhigh") {
			$sql_products .= " ORDER BY p.VIPPrice * 1 ASC";
		} elseif ($orderby != '') {
			$sql_products .= $orderby.", p.BrowserName ";
		} else {
			$sql_products .= " ORDER BY p.BrowserName";
		}
		
		// echo "SQL_PRODUCTS: " . $sql_products; exit; // testing use only

		if ($cat != "0") {
			if ($cat != "") {
				$sql_catname = "SELECT Category FROM category WHERE id='".$cat."' LIMIT 1";
				$result_catname = mysql_query($sql_catname); // or die($sql_catname);
				$row_catname = mysql_fetch_assoc($result_catname);
            	echo '<div id="catName">'.$row_catname["Category"].'</div>';
			}
			echo '<div class="clear"></div>';
            echo '<div id="catBanner">'.$description.'</div>';
            echo '<div class="clear"></div>';
		}
		
		$sql_totalprods = $sql_products;
		if ($pitems != 'all') {
			$sql_products .= " LIMIT $poffset, $pitems";
		}
	?>
    <?php
        $result_totalprods = mysql_query($sql_totalprods);
		$num_totalprods = @mysql_num_rows($result_totalprods);
		$maxpage = @ceil($num_totalprods/$pitems);

		if ($num_totalprods > 0) {
	?>
		<div id="sorter">
        <table cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td width="30%" style="text-align: left;">Sort by: <select id="pricing" name="pricing"><option value=""> - </option><option value="highlow" <?php if($pri=='highlow') { echo 'selected="selected"'; }?>>high/low</option><option value="lowhigh" <?php if($pri=='lowhigh') { echo 'selected="selected"'; }?>>low/high</option></select></td>
            <td width="40%" style="text-align: center;">
	<?php
			if ($pitems != 'all') {
				if ($ppage > $plimit) {
					echo '<a href="#'.($ppage-5).'" class="pager" style="padding-right: 10px;">[Prev 5]</a>';	
				}
				if ($ppage > 1) {
					echo '<a href="#'.($ppage-1).'" class="pager" style="padding-right: 10px;">[Prev]</a>';
				}
				if ($ppage > 3) {
					$pstart = $ppage-2;	
				} else {
					$pstart = 1;	
				}
				if (($pstart + 4) < $maxpage) {
					$pend = $pstart+4;
				} else {
					$pend = $maxpage;
				}
				
				for ($pgnum = $pstart; $pgnum <= $pend; $pgnum++) {
					if ($pgnum == $ppage) {
						echo '<span style="padding-right: 10px;">'.$pgnum.'</span>';
					} else {
						echo '<a href="#'.$pgnum.'" class="pager" style="padding-right: 10px;">'.$pgnum.'</a>';
					}
				}
				if ($ppage < $maxpage) {
					echo '<a href="#'.($ppage + 1).'" class="pager" style="padding-right: 10px;">[Next]</a>';
				}
				if (($ppage + $plimit) < $maxpage) {
					echo '<a href="#'.($ppage + 5).'" class="pager" style="paddimg-right: 10px;">[Next 5]</a>';	
				}
			}
	?>
        	</td>
            <td width="30%" style="text-align: right;">View: <select id="bpager" name="bpager"><option value="12" <?php if($pitems=='12') { echo 'selected="selected"'; } ?>>12 per page</option><option value="24" <?php if($pitems=='24') { echo 'selected="selected"'; } ?>>24 per page</option><option value="48" <?php if($pitems=='48') { echo 'selected="selected"'; } ?>>48 per page</option><option value="all" <?php if($pitems=='all') { echo 'selected="selected"'; } ?>>All</option></select></td>
        </tr>
        </table>
		</div>
    <?php
		}
		$result_products = mysql_query($sql_products);
		if (@mysql_num_rows($result_products)):
			while($row_products = mysql_fetch_array($result_products)) {
	?>
				 <div class="brow_prod"> 
					<a href="<?php echo str_replace(array(" ", '/', '-', '?', '\\'), '_', strtolower($row_products["BrowserName"].'_'.$row_products["BrowserName2"].'_'.$row_products["BrowserName3"])).'_p_'.$row_products["id"];?>.html">
						<?php
							if($row_products["ProductType"] == 'Bundle' && $iscolor=='yes') {
								$image = "images/productView/".$row_products["ColorImage"];
							} else {
								$image = "images/productImages/".$row_products["ColorImage"];
							}
							
							if(!file_exists("../".$image) || $row_products["ColorImage"] == '') {
								$image = "images/noImage.gif";
							}
	
						?>
	
						<img src="<?=$image;?>" alt="<?=$row_products['ProductDetailName'];?>">
					</a>
	                	<a href="<?php echo str_replace(array(" ", '/', '-', '?', '\\'), '_', strtolower($row_products["BrowserName"].'_'.$row_products["BrowserName2"].'_'.$row_products["BrowserName3"])).'_p_'.$row_products["id"];?>.html">
							<h1><?=stripslashes($row_products["BrowserName"])."&nbsp;";?></h1>
	                		<h1><?=stripslashes($row_products["BrowserName2"])."&nbsp;";?></h1>
	                		<h1><?=stripslashes($row_products["BrowserName3"])."&nbsp;";?></h1>
	                     </a>
					<?php
						if($row_products["isSpecial"] == "True") {
							echo "<h2>Non Member Price: $".$row_products["NoneMemberPrice"]."&nbsp;</h2>";
							echo "<h3>".stripslashes($row_products["SpecialCategory"]).": $".number_format($row_products["SpecialPrice"],2)."&nbsp;</h3>";
						} else {
					?>
							<h2>Non Member Price: $<?=$row_products["NoneMemberPrice"]."&nbsp;";?></h2>
							<h3>VIP PRICE: $<?=$row_products["VIPPrice"]."&nbsp;";?></h3>
					<?php } ?>
					<h4><?=stripslashes($row_products["BrowserAddInfo"])."&nbsp;";?></h4>
	                <?php if($row_products["ImprintCatID"]!="" && $row_products["ImprintCatID"]!=0){ ?>
	                			<img src="./images/imprint_availble.png" style="width:174px !important;height:19px !important;" />
	                <?php }?>
				  </div>
				<?php
			} 
		endif;
		$result_totalprods = mysql_query($sql_totalprods);
		$num_totalprods = @mysql_num_rows($result_totalprods);
		$maxpage = @ceil($num_totalprods/$pitems);

		if($num_totalprods>0) {
		?>
		
		<div id="sorter2">
        	<table cellpadding="0" cellspacing="0" width="100%">
            	<tr>
                	<td width="20%">&nbsp;</td>
                    <td width="60%" style="text-align: center; font-weight: bold;">
		<?php
			if($pitems != 'all') {
				if($ppage>$plimit) {
					echo '<a href="#'.($ppage-5).'" class="pager" style="padding-right: 10px;">[Prev 5]</a>';	
				}
			
				if($ppage>1) {
					echo '<a href="#'.($ppage-1).'" class="pager" style="padding-right: 10px;">[Prev]</a>';
				}
				
				if($ppage>3) {
					$pstart = $ppage-2;	
				} else {
					$pstart = 1;	
				}
				if(($pstart+4)<$maxpage) {
					$pend = $pstart+4;
				} else {
					$pend = $maxpage;
				}
				
				for($pgnum=$pstart; $pgnum<=$pend; $pgnum++) {
					if($pgnum==$ppage) {
						echo '<span style="padding-right: 10px;">'.$pgnum.'</span>';
					} else {
						echo '<a href="#'.$pgnum.'" class="pager" style="padding-right: 10px;">'.$pgnum.'</a>';
					}
				}
			
				if($ppage<$maxpage) {
					echo '<a href="#'.($ppage+1).'" class="pager" style="padding-right: 10px;">[Next]</a>';
				}
			
				if(($ppage+$plimit)<$maxpage) {
					echo '<a href="#'.($ppage+5).'" class="pager" style="paddimg-right: 10px;">[Next 5]</a>';	
				}
			}
		?>
        			</td>
                	<td width="20%" style="font-weight: bold; text-align: right;">
			View: 
			<select id="cpager" name="cpager">
				<option value="12" <?php if($pitems=='12') { echo 'selected="selected"'; } ?>>12 per page</option>
				<option value="24" <?php if($pitems=='24') { echo 'selected="selected"'; } ?>>24 per page</option>
				<option value="48" <?php if($pitems=='48') { echo 'selected="selected"'; } ?>>48 per page</option>
				<option value="all" <?php if($pitems=='all') { echo 'selected="selected"'; } ?>>All</option>
			</select>
            		</td>
        		</tr>
          	</table>
		</div>
		<script type="text/javascript">
		$("#bpager").change(function() {
			window.location="browser.php?c=<?=$cat;?>&cg=<?=$cg;?>&pr=<?=$pri;?>&p=<?=$ppage;?>&i="+$(this).val();
			return false;
		});
		
		$("#cpager").change(function() {
			window.location="browser.php?c=<?=$cat;?>&cg=<?=$cg;?>&p=<?=$ppage;?>&pr=<?=$pri;?>&i="+$(this).val();
			return false;						 
		});
		
		$("#pricing").change(function() {
			window.location="browser.php?c=<?=$cat;?>&cg=<?=$cg;?>&p=<?=$ppage;?>&i=<?=$pitems;?>&pr="+$(this).val();
			return false;
		});
		
		$(".pager").click(function() {
			var pg = $(this).attr('href');
			pg = pg.replace('#', '');
			window.location="browser.php?c=<?=$cat;?>&cg=<?=$cg;?>&p="+pg+"&i=<?=$pitems;?>&pr=<?=$pri;?>";
			return false;
		});
		</script>
<?php
	}
	mysql_close($conn);
	exit();
}  
	
if ($_POST["type"] == "setfilter") {
		$ftype = $_POST["filtertype"];
		$fvalue = $_POST["filterdata"];
		$fid = $_POST["filterid"];
		
		if($_SESSION["filter"] != '') {
			$arrfids = explode("|", $_SESSION["filterid"]);
			$arrfilter = explode("|", $_SESSION["filter"]);
			
			$carr = count($arrfilter);
			
			for($i=0; $i<$carr; $i++) {
				$filter = explode("=", $arrfilter[$i]);
				if($filter[0] == $ftype) {
					unset($arrfilter[$i]);
					unset($arrfids[$i]);
				}
			}
			
			$_SESSION["filter"] = implode("|", $arrfilter);
			$_SESSION["filterid"] = implode("|", $arrfids);
			$_SESSION["filter"] .= "|".$ftype."='".$fvalue."'";
			$_SESSION["filterid"] .= "|".$fid;
		} else {
			$_SESSION["filter"] = $ftype."='".$fvalue."'";
			$_SESSION["filterid"] = $fid;
		}
		
		if(substr($_SESSION["filter"], 0, 1) == "|") {
			$_SESSION["filter"] = substr($_SESSION["filter"],1);
		}
		
		if(substr($_SESSION["filterid"], 0, 1) == "|") {
			$_SESSION["filterid"] = substr($_SESSION["filterid"],1);
		}
		
		echo '<span style="14px Arial,Helvetica,sans-serif;padding-left: 17px;"><div style="background: url(&quot;../images/price_range_bg.png&quot;) repeat-x scroll left top transparent; height: 28px; padding-top: 6px; padding-left: 18px;">Filter <a href="javascript:;" onClick="removefilter(\'\');" style="font-size: 12px;">[clear filters]</a></div></span>';
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
		
		for($i=0; $i<$carr; $i++) {
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
				///////////////////////////
				echo '<ul class="colors">';
				$sql_colors = "SELECT a.id, a.$ftype, a.Icon FROM ".strtolower($ftype)."s a, options_filter f WHERE f.FilterID=a.id AND f.OptionID=$arrfids[$i] AND a.$ftype='$fvalue' LIMIT 1";
				$result_colors = mysql_query($sql_colors);
				while($row_colors = mysql_fetch_array($result_colors)) {
					echo '<li><img src="images/'.$row_colors["Icon"].'" alt="'.$row_colors["Color"].'" /> [ <a onclick="removefilter(\''.str_replace("'","",$arrfilter[$i]).'\');" style="font-size: 13px;" href="javascript:;">'.$rem.'</a> ]</li>';
				}
				echo '</ul>';
				///////////////////////////
			} else {
				echo '<span style="font-weight: bold;"/>'.$ftype."</span>: ".$fvalue." [ <a onclick=\"removefilter('".str_replace("'","",$arrfilter[$i])."');\" style=\"font-size: 13px;\" href=\"javascript:;\">$rem</a> ]";
			}
			
			echo '<div class="clear" style="margin-top: 10px;"></div>';
		}
		echo '</div>';
		mysql_close($conn);
		exit();
}
	
if ($_POST["type"] == "removefilter") {
	if ($_POST["filter"] == '') {
		$_SESSION["filter"] = '';
		$_SESSION["filterid"] = '';
		unset($_SESSION["filter"]);
		unset($_SESSION["filterid"]);
	} else {
		$filter = $_POST["filter"];
		$arrfids = explode("|", $_SESSION["filterid"]);
		$arrfilter = explode("|",$_SESSION["filter"]);
		$_SESSION["filterid"] = '';
		$_SESSION["filter"] = '';
		$carr = count($arrfids);
		for ($i = 0; $i < $carr; $i++) {
			if (str_replace("'","", $arrfilter[$i]) != $filter) {
				if ($_SESSION["filterid"] == '') {
					$_SESSION["filterid"] = $arrfids[$i];
					$_SESSION["filter"] = $arrfilter[$i];
				} else {
					$_SESSION["filterid"] .= "|".$arrfids[$i];
					$_SESSION["filter"] .= "|".$arrfilter[$i];
				}
			}
		}
	}
	mysql_close($conn);
	exit();
}

// search function
if ($_POST["type"] == "search") {
	$pitems = $_POST["items"];
	$ppage = $_POST["page"];
	$plimit = 5;
	if ($pitems == '') { 
		$pitems = 12; 
	}
	if ($ppage == '') { 
		$ppage = 1; 
	}

	if ($pitems != 'all') {
		$poffset = ($ppage - 1) * $pitems;
	} else {
		$poffset = 0;
	}

	$search = $_POST["search"];
	
	function search_split_terms($terms) {
		$terms = preg_replace("/\"(.*?)\"/e", "search_transform_term('\$1')", $terms);
		$terms = preg_split("/\s+|,/", $terms);
		$out = array();
		foreach ($terms as $term) {
			$term = preg_replace("/\{WHITESPACE-([0-9]+)\}/e", "chr(\$1)", $term);
			$term = preg_replace("/\{COMMA\}/", ",", $term);
			$out[] = $term;
		}
		return $out;
	}
	function search_transform_term($term) {
		$term = preg_replace("/(\s)\e", "'{WHITESPACE-'.ord('\$1').'}'", $term);
		$term = preg_replace("/,/", "{COMMA}", $term);
		return $term;
	}
	function search_escape_rlike($string) {
		return preg_replace("/([.\[\]*^\$])/", '\\\$1', $string);
	}
	function search_db_escape_terms($terms) {
		$out = array();
		foreach ($terms as $term) {
			$out[] = '[[:<:]]'.AddSlashes(search_escape_rlike($term)).'[[:>:]]';
		}
		return $out;
	}
	$terms = search_split_terms($search);
	$terms_db = search_db_escape_terms($terms);
	$parts = array();
	foreach ($terms_db as $term_db) {
		$parts[] = "(p.BrowserName RLIKE '$term_db' OR p.BrowserName2 RLIKE '$term_db' OR p.BrowserName3 RLIKE '$term_db' OR p.BrowserAddInfo RLIKE '$term_db' OR p.RootSKU RLIKE '$term_db' OR d.ShortDescription RLIKE '$term_db' OR d.ProductDescription RLIKE '$term_db' OR p.Brand RLIKE '$term_db')";
	}
	$parts = implode(' AND ', $parts);
	
	$sql_search  = "SELECT DISTINCT p.id, p.BrowserName, p.BrowserName2, p.BrowserName3, p.NoneMemberPrice, p.VIPPrice, p.BrowserAddInfo, p.isSpecial, p.SpecialCategory, p.SpecialPrice, p.Brand, o.ColorImage FROM manufacturers m, products p, product_options o, product_browser b, product_descriptions d WHERE p.id=o.ProductID AND p.id=d.ProductID AND p.Status='Enabled' AND p.AvailableQty>0 AND b.ProductID=p.id AND o.ColorImage=b.Image AND p.CustomerGroupAvailability='' AND $parts";
	// echo "SQL: " . $sql_search . "<br />"; exit; // testing use only
	// $sql_search .= "(p.BrowserName LIKE '%$search%' OR p.BrowserName2 LIKE '%$search%' OR p.BrowserName3 LIKE '%$search%' OR p.BrowserAddInfo LIKE '%$search%' OR p.RootSKU LIKE '%$search%' OR d.ShortDescription LIKE '%$search%' OR d.ProductDescription LIKE '%$search%')";
	$result_search = mysql_query($sql_search);
	$num_totalprods = @mysql_num_rows($result_search);
	echo '<div style="width: 100%; margin: 0px 10px 15px 10px;">Your search for "'.$search.'" returned the follow items: '.intval($num_totalprods).'<br><br>Customer Service: We’re always here to help; no search required. Email us at customerservice@soccerone.com or call us at (888) 297-6386.</div>';
	echo '<div class="clear"></div>';
	$maxpage = @ceil($num_totalprods / $pitems);
	if ($num_totalprods > 0) {
?>
		<div style="width: 95%; margin: 10px; text-align: right; background-color: #e1e1e1; float: left; padding: 5px;">
        <table cellpadding="0" cellspacing="0" width="100%">
        <tr>
                	<td width="20%">&nbsp;</td>
                    <td width="60%" style="text-align: center; font-weight: bold;">
		<?php
			if($pitems != 'all') {
				if($ppage>$plimit) {
					echo '<a href="#'.($ppage-5).'" class="pager" style="padding-right: 10px;">[Prev 5]</a>';	
				}
			
				if($ppage>1) {
					echo '<a href="#'.($ppage-1).'" class="pager" style="padding-right: 10px;">[Prev]</a>';
				}
				
				if($ppage>3) {
					$pstart = $ppage-2;	
				} else {
					$pstart = 1;	
				}

				if(($pstart+4)<$maxpage) {
					$pend = $pstart+4;
				} else {
					$pend = $maxpage;
				}
				
				for($pgnum=$pstart; $pgnum<=$pend; $pgnum++) {
					if($pgnum==$ppage) {
						echo '<span style="padding-right: 10px;">'.$pgnum.'</span>';
					} else {
						echo '<a href="#'.$pgnum.'" class="pager" style="padding-right: 10px;">'.$pgnum.'</a>';
					}
				}
			
				if($ppage<$maxpage) {
					echo '<a href="#'.($ppage+1).'" class="pager" style="padding-right: 10px;">[Next]</a>';
				}
			
				if(($ppage+$plimit)<$maxpage) {
					echo '<a href="#'.($ppage+5).'" class="pager" style="paddimg-right: 10px;">[Next 5]</a>';	
				}
			}
		?>
        			</td>
                	<td width="20%" style="font-weight: bold; text-align: right;">
			View: 
			<select id="bpager" name="bpager">
				<option value="12" <?php if($pitems=='12') { echo 'selected="selected"'; } ?>>12 per page</option>
				<option value="24" <?php if($pitems=='24') { echo 'selected="selected"'; } ?>>24 per page</option>
				<option value="48" <?php if($pitems=='48') { echo 'selected="selected"'; } ?>>48 per page</option>
				<option value="all" <?php if($pitems=='all') { echo 'selected="selected"'; } ?>>All</option>
			</select>
            		</td>
        		</tr>
          	</table>
		</div>
        <?php
		}
		
		if($pitems != 'all') {
			$sql_search .= " LIMIT $poffset, $pitems";
		}

		$result_searchData = mysql_query($sql_search);
		while($row_search = mysql_fetch_array($result_searchData)) {
			?>
			<div class="brow_prod">
				<a href="<?php echo str_replace(array(" ", '/', '-', '?', '\\'), '_', strtolower($row_search["BrowserName"].'_'.$row_search["BrowserName2"].'_'.$row_search["BrowserName3"])).'_p_'.$row_search["id"];?>.html">
					<?php
						$image = "images/productImages/".$row_search["ColorImage"];

						if(!file_exists("../".$image) || $row_search["ColorImage"] == '') {
							$image = "images/noImage.gif";
						}
					?>
					<img src="<?=$image;?>" alt="" />
				</a>
				<a href="<?php echo str_replace(array(" ", '/', '-', '?', '\\'), '_', strtolower($row_search["BrowserName"].'_'.$row_search["BrowserName2"].'_'.$row_search["BrowserName3"])).'_p_'.$row_search["id"];?>.html">
				<h1><?=$row_search["BrowserName"];?></h1>
				<h1><?=$row_search["BrowserName2"];?></h1>
				<h1><?=$row_search["BrowserName3"];?></h1>
                </a>
				<?php
					if($row_search["isSpecial"] == "True") {
						echo "<h3>$row_search[SpecialCategory]: $".number_format($row_search["SpecialPrice"], 2)."</h3>";
					} else {
				?>
					<h2>Non Member Price: $<?=$row_search["NoneMemberPrice"];?></h2>
					<h3>VIP PRICE: $<?=$row_search["VIPPrice"];?></h3>
				<?php } ?>
				<h4><?=$row_search["BrowserAddInfo"];?></h4>
			</div>
			<?php
		}

		if($num_totalprods>0) {
		?>
		
		<div style="width: 95%; margin: 10px; text-align: right; background-color: #e1e1e1; float: left; padding: 5px;">
        	<table cellpadding="0" cellspacing="0" width="100%">
            	<tr>
                	<td width="20%">&nbsp;</td>
                    <td width="60%" style="text-align: center; font-weight: bold;">
		<?php
			if($pitems != 'all') {
				if($ppage>$plimit) {
					echo '<a href="#'.($ppage-5).'" class="pager" style="padding-right: 10px;">[Prev 5]</a>';	
				}
			
				if($ppage>1) {
					echo '<a href="#'.($ppage-1).'" class="pager" style="padding-right: 10px;">[Prev]</a>';
				}
				
				if($ppage>3) {
					$pstart = $ppage-2;	
				} else {
					$pstart = 1;	
				}
				
				if(($pstart+4)<$maxpage) {
					$pend = $pstart+4;
				} else {
					$pend = $maxpage;
				}
				
				for($pgnum=$pstart; $pgnum<=$pend; $pgnum++) {
					if($pgnum==$ppage) {
						echo '<span style="padding-right: 10px;">'.$pgnum.'</span>';
					} else {
						echo '<a href="#'.$pgnum.'" class="pager" style="padding-right: 10px;">'.$pgnum.'</a>';
					}
				}
			
				if($ppage<$maxpage) {
					echo '<a href="#'.($ppage+1).'" class="pager" style="padding-right: 10px;">[Next]</a>';
				}
			
				if(($ppage+$plimit)<$maxpage) {
					echo '<a href="#'.($ppage+5).'" class="pager" style="paddimg-right: 10px;">[Next 5]</a>';	
				}
			}
		?>
        			</td>
                	<td width="20%" style="font-weight: bold; text-align: right;">
			View: 
			<select id="cpager" name="cpager">
				<option value="12" <?php if($pitems=='12') { echo 'selected="selected"'; } ?>>12 per page</option>
				<option value="24" <?php if($pitems=='24') { echo 'selected="selected"'; } ?>>24 per page</option>
				<option value="48" <?php if($pitems=='48') { echo 'selected="selected"'; } ?>>48 per page</option>
				<option value="all" <?php if($pitems=='all') { echo 'selected="selected"'; } ?>>All</option>
			</select>
            		</td>
        		</tr>
          	</table>
		</div>
		<script>
			$("#bpager").change(function() {
				window.location="browser.php?s=<?=$search;?>&type=search&p=<?=$ppage;?>&i="+$(this).val();
				return false;
			});
			$("#cpager").change(function() {
				window.location="browser.php?s=<?=$search;?>&type=search&p=<?=$ppage;?>&i="+$(this).val();
				return false;						 
			});
			$(".pager").click(function(){
				var pg = $(this).attr('href');
				pg = pg.replace('#', '');
				window.location="browser.php?s=<?=$search;?>&type=search&p="+pg+"&i=<?=$pitems;?>";
				return false;
			});
		</script>
        <?php
		}		
		mysql_close($conn);
		exit();
	}
?>