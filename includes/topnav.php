<?php
/*****************************
 * Frontend Top Navigation   
 *                                         
 * Updated: 05 April 2016      
 * By: Richard Tuttle        
 ****************************/
?>

<nav id="menu">
<ul id="nav">
	<li><a href="/">Home</a></li>
<?php
	function getSubCat($pid) {
		$sql_subnav = "SELECT id, Category FROM category WHERE Status='Enabled' AND ParentID=$pid ORDER BY Sort";
		$result_subnav = mysql_query($sql_subnav);
		$num_subnav = mysql_num_rows($result_subnav);
		if ($num_subnav > 0) {
			echo '<ul>';
			while ($row_subnav = mysql_fetch_array($result_subnav)) {
				$moreSubSQL = mysql_query("SELECT * FROM category WHERE Status='Enabled' AND ParentID=" . $row_subnav["id"]);
				$moreRows = mysql_num_rows($moreSubSQL);
				$cateTitle = strtolower(str_replace(array(" ", '/', '-', '?', '\\'), "_", $row_subnav["Category"]));
				if ($moreRows > 0) {
					echo '<li><a href="'.$cateTitle.'-c-'.$row_subnav["id"].'.html">'.$row_subnav["Category"].'</a>';
					getSubCat($row_subnav["id"]);
					echo '</li>';
				} else {
					echo '<li><a href="'.$cateTitle.'-c-'.$row_subnav["id"].'.html">'.$row_subnav["Category"].'</a></li>';
				}
			}
			echo '</ul>';
		}
	}
		  	
	$sql_nav = "SELECT id, Category FROM category WHERE Status='Enabled' AND ParentID=0 ORDER BY Sort";
	$result_nav = mysql_query($sql_nav);
	while ($row_nav = mysql_fetch_array($result_nav)) {
		$cateTitle = strtolower(str_replace(array(" ", '/', '-', '?', '\\'), "_", $row_nav["Category"]));
		echo '<li><a href="'.$cateTitle.'-c-'.$row_nav["id"].'.html">'.$row_nav["Category"].'</a>';
		getSubCat($row_nav["id"]);
		echo '</li>';
	}
?>
</ul>
</nav>