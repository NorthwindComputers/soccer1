<?php
	include("includes/header.php");
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
			$(".delOpt").click(function(){
				var del = confirm("Delete Option?");
				
				if(del) {
					$.post('includes/inc_imprint_options.php', {"type":"delete", "impoptid":$(this).attr("rel")}, function(data) {
						alert(data);
						location.reload();
					});
				}
				return false;
			});
			
			$(".editOpt").click(function(){
				window.location.href="imprint_options_details.php?optID="+$(this).attr("rel");							 
			});
			
			$("#impCategories").click(function(){
				window.location.href="imprint_new.php";
			});
			
			$("#impOptNew").click(function(){
				window.location.href="imprint_options_details.php";
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
			<div class="PV_center">
          		
                <div id="products" class="orders">
                    <table cellpadding="5" cellspacing="1" width="980px" style="margin-top: 20px;">
                        <tr>
                            <td class="headersmain" colspan="6" style="text-align: left; padding-left: 20px;">Imprint Options
                                <input type="button" class="imprint" id="impOptNew" value="New Option" />
                                <input type="button" class="imprint" id="impCategories" value="Imprint Categories" />
                                
                            </td>
                        </tr>
                        <tr>
                              <td class="headers" style="width:300px;">Name</td>
                              <td class="headers left" style="width:300px;">Category</td>
                              <td class="headers left" style="width:280px;">Type</td>
                              <td class="headers" style="width:100px;">Actions</td>
                        </tr>
                        
                        <?php
							$sql_imopts = "SELECT o.*, c.Name AS CatName FROM imprint_options o LEFT JOIN imprint_categories c on c.id = o.ImprintCategory ORDER BY c.Name, o.Name";
							$result_imopts = mysql_query($sql_imopts);
							
							$rw = 1;
							while($row_imopts = mysql_fetch_array($result_imopts)) {
								if($rw == 1) {
									$cls = "row1";
									$rw++;
								} else {
									$cls = "row2";
									$rw = 1;
								}
								?>
									<tr>
                                    	<td class="<?=$cls;?>"><?=stripslashes($row_imopts["Name"]);?></td>
                                        <td class="<?=$cls;?> left"><?=stripslashes($row_imopts["CatName"]);?></td>
                                        <td class="<?=$cls;?> left"><?=stripslashes($row_imopts["Type"]);?></td>
                                    	<td class="<?=$cls;?> center">
                                        	<div class="edit"><a class="editOpt" title="Edit Options" alt="Edit Options" rel="<?=$row_imopts["id"];?>">&nbsp;</a></div>
                                        	<div class="delete"><a class="delOpt" title="Delete Option" alt="Delete Options" rel="<?=$row_imopts["id"];?>">&nbsp;</a></div>
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