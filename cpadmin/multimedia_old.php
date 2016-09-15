<?php
	include("includes/header.php");
	
	
	if(isset($_POST["btnSubmit"])) {
		
		$sql_reset = "DELETE FROM product_browser WHERE ProductID=$_POST[prodid]";
		mysql_query($sql_reset);
		
		$sql_update  = "INSERT INTO product_browser (ProductID, Image) ";
		$sql_update .= "VALUES($_POST[prodid], '$_POST[BrowserView]')";
		if(!mysql_query($sql_update)) {
			echo "Error Updating: ".mysql_error();
		}
		
		$bsort = str_replace("browser[]=", "", $_POST["browsersort"]);
		$bsort = explode("&",$bsort);
		
		$sql_bitems = "SELECT DISTINCT ColorImage FROM product_options WHERE ProductID=$_POST[prodid] ORDER BY ImageSort, ColorImage";
		$result_bitems = mysql_query($sql_bitems);
				
		$i = 1;
		while($row_bitems = mysql_fetch_array($result_bitems)) {
			$bitems[$i] = $row_bitems["ColorImage"];
			$i++;
		}
		
		$count = count($bsort);
		for($a=0; $a<$count; $a++) {
			$sql_sort = "UPDATE product_options SET ImageSort=$a WHERE ProductID=$_POST[prodid] AND ColorImage='".$bitems[$bsort[$a]]."'";
			if(!mysql_query($sql_sort)) {
				$err .= $sql_sort." -- ".mysql_error()."<br/>";
			}
		}
		
		if($err != '') {
			echo $err;
		}
	}
	
	if(isset($_POST["btnUpload"])) {
		
		if($_FILES["file"]["name"] != '') {
			if($_FILES["file"]["error"]>0) {
				echo "Error: ".$_FILES["file"]["error"];
			} else {
			
				$fileName = $_FILES["file"]["name"];
				$folderLoc = "images/productView/";
			
				move_uploaded_file($_FILES["file"]["tmp_name"], $folderLoc.$fileName);
				$sql_image = "INSERT INTO product_images(ProductID, Image) VALUES($_POST[prodid], '$fileName')";
				if(!mysql_query($sql_image)) {
					echo "Error adding Image: ".mysql_error();
				}
			}
		}
	}
	
	if(isset($_POST["btnUploadBrowser"])) {
		if($_FILES["browserimage"]["name"] != '') {
			if($_FILES["browserimage"]["error"]>0){
				echo "Error: ".$_FILES["browserimage"]["error"];
			} else {
				
				$fileName = $_FILES["browserimage"]["name"];
				$folderLoc = "images/productImages/";
				
				move_uploaded_file($_FILES["browserimage"]["tmp_name"], $folderLoc.$fileName);
				
				$sql_rem = "DELETE FROM product_browser WHERE ProductID=$_POST[prodid]";
				mysql_query($sql_rem);
				
				$sql_remo = "DELETE FROM product_options WHERE ProductID=$_POST[prodid]";
				mysql_query($sql_remo);
				
				$sql_browser = "INSERT INTO product_browser(ProductID, Image) VALUES($_POST[prodid], '$fileName')";
				mysql_query($sql_browser);
				
				$sql_options = "INSERT INTO product_options(ProductID, ColorImage) VALUES($_POST[prodid], '$fileName')";
				mysql_query($sql_options);
				
			}
		}
	}
	
	if($_GET["del"] != '') {
		
		$sql_getimg = "SELECT Image FROM product_images WHERE id=$_GET[del] LIMIT 1";
		$result_getimg = mysql_query($sql_getimg);
		$row_getimg = mysql_fetch_assoc($result_getimg);
		
		unlink("images/productView/".$row_getimg["Image"]);
		$sql_del = "DELETE FROM product_images WHERE id=$_GET[del] LIMIT 1";
		mysql_query($sql_del);
	
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title></title>
	<link rel="stylesheet" href="css/styles.css" type="text/css" />
	<link rel="stylesheet" href="jqtransformplugin/jqtransform.css" type="text/css"  media="all" />
	<script type="text/javascript" src="js/jquery-1.4.4.min.js"></script>
	<script type="text/javascript" src="js/jquery-ui-1.8.7.custom.min.js"></script>
	<script type="text/javascript" src="jqtransformplugin/jquery.jqtransform.js"></script>
	<script language="javascript" type="text/javascript">
		$(function(){
			$('form').jqTransform({imgPath:'jqtransformplugin/img/'});
		});
		$(document).ready(function() {
			$("#divSortPics").sortable({handle:'.sort'});
			$("#divSortBrowser").sortable({handle:'.sort'});
			
			$("#saveorder").click(function() {
					var neworder = $('#divSortPics').sortable('serialize');
					$.post("includes/inc_saveOrder.php?id=<?=$_GET["id"];?>&"+neworder,'', function(data) { alert(data); });	
			});
			
			$("#btnSubmit").click(function() {
				$("#browsersort").val($("#divSortBrowser").sortable('serialize'));
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
          <?php include('includes/menu.php'); ?>
          <div class="clear"></div>
        </div>
  </div>
      <!-- Header Div ends here --> 
      <!-- Product Detail Div starts from here -->
      <div class="PD_main_form">
    <div class="multimedia">
    	<?php
			$sql_prod = "SELECT BrowserName, RootSKU FROM products WHERE id=$_GET[id] LIMIT 1";
			$result_prod = mysql_query($sql_prod);
			$row_prod = mysql_fetch_assoc($result_prod);
		?>
          <h1>Product Options - <?=$row_prod["BrowserName"]." - ".$row_prod["RootSKU"];?><br />
        <span>----------------------------------------------------------</span></h1>
          <div class="clear"></div>
          <?php 
		  	
				$sql_bundle = "SELECT ProductType FROM products WHERE id=$_GET[id] LIMIT 1";
				$result_bundle = mysql_query($sql_bundle);
				$row_bundle = mysql_fetch_assoc($result_bundle);
			
				if($row_bundle["ProductType"] != "Bundle") {
		  ?>
          <h3>Based on color selection, here are available images:</h3>
          	
          <div class="clear"></div>
          <form action="" method="post">
          	<input type="hidden" id="prodid" name="prodid" value="<?=$_GET["id"];?>" />
            <input type="hidden" id="browsersort" name="browsersort" value="" />
      
      		<div id="divSortBrowser">
          <?php

				$sql_browser = "SELECT Image FROM product_browser WHERE ProductID=$_GET[id] LIMIT 1";
				$result_browser = mysql_query($sql_browser);
				$row_browser = mysql_fetch_assoc($result_browser);		  

		  		$sql_imgs = "SELECT DISTINCT ColorImage FROM product_options WHERE ProductID=$_GET[id] ORDER BY ImageSort, ColorImage";
				$result_imgs = mysql_query($sql_imgs);
				
				$s_num = 1;
				while($row_imgs = mysql_fetch_array($result_imgs)) {
					
				?>
                	<div id="browser_<?=$s_num;?>" class="media_box">
                    	<img class="sort" src="images/productImages/<?=$row_imgs["ColorImage"];?>" alt="<?=$row_imgs["AltText"];?>" style="width: 90px; height: 100px;" />
                        <h4 class="desc_text">Desciption</h4>
                        <div class="radio">
                        	<input type="radio" value="<?=$row_imgs["ColorImage"];?>" id="BrowserView" name="BrowserView" <?php if($row_imgs["ColorImage"] == $row_browser["Image"]) { echo ' checked '; } ?> />
                            <h5>Browser View</h5>
                        </div>
					</div>
                
                <?php
					$s_num++;
				}
		  ?>
          </div>
          <div class="clear"></div>
              <div class="clear"></div>
            <input type="submit" id="btnSubmit" name="btnSubmit" class="submit" value="" style="margin-top: 40px;"/>
        </form>
        <?php
			} else {
			?>
			
            <h3>Upload a browser image:</h3>
            <div class="clear"></div>
            <form action="" method="post" enctype="multipart/form-data">
            	<input type="hidden" id="prodid" name="prodid" value="<?=$_GET["id"];?>" />
            	<input type="file" id="browserimage" name="browserimage" />
                <input type="submit" id="btnUploadBrowser" name="btnUploadBrowser" value="Upload" />
            </form>
            <div class="clear"></div>
			<?php
				
				$sql_browserimg = "SELECT Image FROM product_browser WHERE ProductID=$_GET[id] LIMIT 1";
				$result_browserimg = mysql_query($sql_browserimg);
				$row_browserimg = mysql_fetch_assoc($result_browserimg);
				
				if($row_browserimg["Image"] != '') {
					echo '<img src="images/productImages/'.$row_browserimg["Image"].'" style="width: 90px; height: 100px;" />';
				}
			}
		?>
        <div class="clear"></div>
        <h1 style="clear: both; margin-top: 30px;">Additional Images<br/>
        <span>------------------------------------------------------------</span></h1>
        <div class="clear"></div>
          <h2>Drag and Drop to sort the images</h2>
          <div class="clear"></div>
          <form action="" method="post" enctype="multipart/form-data" >
          		<input type="hidden" id="prodid" name="prodid" value="<?=$_GET["id"];?>" />
        		<input type="file" id="file" name="file" value="Upload More Image" />
                <input type="submit" id="btnUpload" name="btnUpload" value="Upload" />
      		</form>
    <div class="clear"></div>
    
    <div id="divSortPics">
    
    	<?php
		
			$sql_view = "SELECT id, Image FROM product_images WHERE ProductID=$_GET[id] ORDER BY SortOrder, id";
			$result_view = mysql_query($sql_view);
			
			$v_num = 1;
			while($row_view = mysql_fetch_array($result_view)) {
				?>
				
                <div id="item_<?=$v_num;?>" class="media_box"> 
                    <img class="sort" src="images/productView/<?=$row_view["Image"];?>" alt="" style="width: 90px; height: 100px;" />
                    <h4 class="edit_text"><a href="multimedia.php?id=<?=$_GET["id"];?>&del=<?=$row_view["id"];?>" onClick="return confirm('Remove Image?');">Remove</a></h4>
                </div>
                
				<?php
				$v_num++;
			}
		?>
   
    </div>
    <div class="clear"></div>
	<input class="save" type="button" id="saveorder" name="saveorder" value="" />
  </div>
      <!-- Product Detail Div ends here --> 
    </div>
</body>
</html>
<?php mysql_close($conn); ?>