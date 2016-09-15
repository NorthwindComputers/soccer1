<?php
	require 'db.php';
	
	$bsort = str_replace("item[]=", "", $_POST["sortOrder"]);
	$bsort = explode("&", $bsort);
	$id = $_GET['id'];
	$sql_geti = "SELECT id FROM product_images WHERE ProductID=$id ORDER BY SortOrder, id";
	$result_geti = mysql_query($sql_geti);
	
	$i = 1;
	while($row_geti = mysql_fetch_array($result_geti)) {
		$arrids[$i] = $row_geti["id"];
		$i++;
	}
	if($result_geti){ mysql_free_result($result_geti); }
	
	$count = count($bsort);
	for($a=0; $a<$count; $a++) {
		$sql_sort = "UPDATE product_images SET SortOrder=$a WHERE id=".$arrids[$bsort[$a]]." LIMIT 1";
		if(!mysql_query($sql_sort)) {
			$err .= " -- ".mysql_error();
		}
	}
	
	if($err == '') {
		echo "Image order updated";
	} else {
		echo $err;
	}
	
	mysql_close($conn);
?>