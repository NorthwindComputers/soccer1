<?php
	include("includes/header.php");
	
	
	if(isset($_POST["btnImpAdd"])) {
			
			$name = addslashes(mysql_real_escape_string($_POST["impName"]));
			$desc = addslashes(mysql_real_escape_string($_POST["impDesc"]));
			$file = '';
			
			if($_FILES["impBanner"]["error"] >0) {
				$mess = "Error Uploading Banner";
			} else {
				if(($_FILES["impBanner"]["type"] == "image/gif") ||
				   ($_FILES["impBanner"]["type"] == "image/jpeg") ||
				   ($_FILES["impBanner"]["type"] == "image/png") ||
				   ($_FILES["impBanner"]["type"] == "image/pjpeg")) {
					
					$path = "images/ImprintCategory/";
					$file = $_FILES["impBanner"]["name"];
					move_uploaded_file($_FILES["impBanner"]["tmp_name"],$path.$file);
					
				} else {
					$mess = "Invalid File Type";
				}
				
			}
			
			if($name != "") {
				$sql_imadd = "INSERT INTO imprint_categories(Name, Description, BannerImage) Values('$name', '$desc', '$file')";
				mysql_query($sql_imadd);
			}
	}
	
	if(isset($_POST["btnImpEdit"])) {
		
		$impCatID = mysql_real_escape_string($_POST["impCatID"]);
		$impName = addslashes(mysql_real_escape_string($_POST["impName"]));
		$impDesc = addslashes(mysql_real_escape_string($_POST["impDesc"]));
		$file = '';
		
		if($_FILES["impBanner"]["error"] >0) {
			$mess = "Error Uploading Banner";
		} else {
			$path = "images/ImprintCategory/";
			$file = $_FILES["impBanner"]["name"];
			
			move_uploaded_file($_FILES["impBanner"]["tmp_name"],$path.$file);
		}
		
		if($impName != "") {
			if($file != '') {
				$sql_impFile = ", BannerImage='$file' ";
			}
			$sql_impupdate = "UPDATE imprint_categories SET Name='$impName', Description='$impDesc' $sql_impFile WHERE id=$impCatID LIMIT 1";
			mysql_query($sql_impupdate);
		}
	}
	
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Imprint Categories</title>
	<link rel="stylesheet" href="css/styles.css" type="text/css" />
	<link rel="stylesheet" href="jqtransformplugin/jqtransform_view.css" type="text/css"  media="all" />
	<script type="text/javascript" src="js/jquery-1.4.4.min.js"></script>
	<script type="text/javascript" src="jqtransformplugin/jquery.jqtransform.js"></script>
	<script language="javascript" type="text/javascript">
		$(function(){
			$('form').jqTransform({imgPath:'jqtransformplugin/img/'});
		});
		$(document).ready(function(){
			$(".delCat").click(function(){
				var del = confirm("Delete Category?");
				
				if(del) {
					$.post('includes/inc_imprint.php', {"type":"delete", "impid":$(this).attr("rel")}, function(data) {
						alert(data);
						location.reload();
					});
				}
				return false;			
			});
			
			$(".editCat").click(function(){
				popUp("edit", $(this).attr("rel"));	
			});
			
			$('.window .close').click(function (e) {         
				e.preventDefault();         
				$('#mask, .window').hide();     
			});                
			
			$('#mask').click(function () {         
				$(this).hide();         
				$('.window').hide();     
			});
			
			$("#NewImpCat").click(function(){										   
				popUp("add", "");
			});	
			
			$("#imprintOptions").click(function(){									
				window.location.href="imprint_options.php";
			});
			
		});
		
		function popUp(pgType, catID) {
			
			var id = '#category';               
			var maskHeight = $(document).height();         
			var maskWidth = $(window).width();               
			
			$("#editcategory").html('<img src="images/loader.gif" />');
			$("#editcategory").load("includes/inc_imprint.php", {"type":pgType, "catID":catID});
			
			$('#mask').css({'width':maskWidth,'height':maskHeight});                   
			$('#mask').fadeIn(200);             
			$('#mask').fadeTo("slow",0.8);                 
			
			var winH = $(window).height();         
			var winW = $(window).width();                         
			
			$(id).css('top',  winH/2-$(id).height()/2);         
			$(id).css('left', winW/2-$(id).width()/2);
			$(id).fadeIn(2000);
			
		}
		
	</script>
	</head>

	<body>
    
<!-- Add/Edit Categories -->
<div id="edit">
   <div id="category" class="window" style="text-align: center;">
   		<div id="editcategory"></div>
   </div>
   <div id="mask"></div> 
</div>     
<!-- End Add/Edit Categories -->


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
			<div class="PV_center">
          		
                <div id="products" class="orders">
                    <table cellpadding="5" cellspacing="1" width="980px" style="margin-top: 20px;">
                        <tr>
                            <td class="headersmain" colspan="6" style="text-align: left; padding-left: 20px;">Imprint Categories
                                <input type="button" class="imprint" id="NewImpCat" value="New Category" />
                                <input type="button" class="imprint" id="imprintOptions" value="Imprint Options" />
                                
                            </td>
                        </tr>
                        <tr>
                              <td class="headers" style="width:100px;">ID</td>
                              <td class="headers left" style="width:300px;">Category Name</td>
                              <td class="headers left" style="width:480px;">Description</td>
                              <td class="headers" style="width:100px;">Actions</td>
                        </tr>
                        
                        <?php
							$sql_imcats = "SELECT * FROM imprint_categories ORDER BY id";
							$result_imcats = mysql_query($sql_imcats);
							
							$rw = 1;
							while($row_imcats = mysql_fetch_array($result_imcats)) {
								if($rw == 1) {
									$cls = "row1";
									$rw++;
								} else {
									$cls = "row2";
									$rw = 1;
								}
								?>
									<tr>
                                    	<td class="<?=$cls;?>"><?=stripslashes($row_imcats["id"]);?></td>
                                        <td class="<?=$cls;?> left"><?=stripslashes($row_imcats["Name"]);?></td>
                                        <td class="<?=$cls;?> left"><?=stripslashes($row_imcats["Description"]);?></td>
                                    	<td class="<?=$cls;?> center">
                                        	<div class="edit"><a class="editCat" title="View Details" alt="View Details" rel="<?=$row_imcats["id"];?>">&nbsp;</a></div>
                                        	<div class="delete"><a class="delCat" title="Delete Category" alt="Delete Category" rel="<?=$row_imcats["id"];?>">&nbsp;</a></div>
                                        </td>
                                    </tr>
								<?php
							}
						
						?>
                    </table>
                </div>
        	</div>
  		</div>
      	<!-- Products view Div ends here --> 
    </div>
</body>
</html>