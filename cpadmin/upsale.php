<?php
/**
 * upsale admin module
 *
 * Version: 1.2
 * Updated: 26 Feb 2013
 * By: Richard Tuttle
 */

	include_once("includes/header.php");
	
  // was the Add button pressed?
	if(isset($_POST["btnAdd"])) {
		foreach($_POST["upsale"] as $value) {
			$sql_add = "INSERT INTO product_related(ProductID, RelatedID, `Type`) VALUES ($_POST[prodid], $value, 'upsale')";
			if(!mysql_query($sql_add)) {
				echo "Error Adding Upsale Products: ".mysql_error();
			}
		}
	}
	
  // was the Remobe button pressed?
	if(isset($_POST["btnRemove"])) {
		foreach($_POST["remove"] as $value) {
			$sql_remove = "DELETE FROM product_related WHERE id=$value LIMIT 1";
			if(!mysql_query($sql_remove)) {
				echo "Error Removing Upsale product: ".mysql_error();
			}
		}
	}
  include_once("includes/mainHeader.php");
?>
	<script language="javascript" type="text/javascript">
		$(document).ready(function() {
			$("#search").click(function() {
				$("#divSearch").load("includes/inc_related.php", {
					"type":"searchupsale", 
					"id":"<?=filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);?>",
					"sku":$("#searchsku").val(),
					"name":$("#searchname").val(),
					"pricelow":$("#searchpricelow").val(),
					"pricehigh":$("#searchpricehigh").val()
				});
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
            <div class="related">
            	<form action="" method="post" >
                	<input type="hidden" id="prodid" name="prodid" value="<?=filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);?>" />
            	<table cellpading="5" cellspacing="1">
                	<tr>
                    	<td colspan="3">
                        	<?php
                          $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
								$sql_prod = "SELECT BrowserName, RootSKU FROM products WHERE id='$id' LIMIT 1";
								$result_prod = mysql_query($sql_prod);
								$row_prod = mysql_fetch_assoc($result_prod);
							?>
                        	<h1>Upsale Products - <?=$row_prod["BrowserName"]." - ".$row_prod["RootSKU"];?>
                       		<br/><span>----------------------------------------------------------</span></h1></td>
                        <td><input type="submit" class="button" id="btnRemove" name="btnRemove" value="Remove" onclick="return confirm('Remove selected upsale products?');" /></td>
                    </tr>
                    <tr>
                    	<td style="width: 43px;"></td>
                        <td style="width: 160px;" class="header">SKU</td>
                        <td style="width: 409px;" class="header">Name</td>
                        <td style="width: 208px;" class="header">Price</td>
                    </tr>
                  </table>
                  <table cellpadding="5" cellspacing="0">
                    <?php
						
						$sql_upsale = "SELECT r.id, p.RootSKU, p.BrowserName, p.NoneMemberPrice FROM products p, product_related r WHERE p.id=r.RelatedID AND r.ProductID='$id' AND r.`Type`='upsale'";
						$result_upsale = mysql_query($sql_upsale);
						
						while($row_upsale = mysql_fetch_array($result_upsale)) {
							echo '<tr>';
							echo '<td class="checkbox"><input type="checkbox" id="remove[]" name="remove[]" value="'.$row_upsale["id"].'" /></td>';
							echo '<td class="sku">'.$row_upsale["RootSKU"].'</td>';
							echo '<td class="name">'.$row_upsale["BrowserName"].'</td>';
							echo '<td class="price">'.$row_upsale["NoneMemberPrice"].'</td>';
							echo '</tr>';
						}
					?>
                </table>
            	<div class="clear" style="margin-top: 50px;"></div>
                  <table cellpadding="5" cellspacing="1">
                  	<tr>
                    	<td style="width: 35px; height: 30px;"></td>
                        <td colspan="2"><h1>Add More Products</h1></td>
                        <td><input type="button" class="button" id="search" name="search" value="Search" /></td>
                    </tr>
                    <tr>
                    	<td></td>
                        <td class="header">SKU</td>
                        <td class="header">Name</td>
                        <td class="header">Price</td>
                    </tr>
                    <tr>
                    	<td style="width: 35px; height: 30px;"></td>
                        <td class="skusearch"><input class="text" type="text" id="searchsku" name="searchsku" /></td>
                        <td class="namesearch"><input class="text" type="text" id="searchname" name="searchname" /></td>
                        <td class="pricesearch"><input class="text" type="text" id="searchpricelow" name="searchpricelow" style="width: 95px;" /><input class="text" type="text" id="searchpricehigh" name="searchpricehigh" style="width: 95px; margin-left: 10px;" /></td>
                    </tr>                
                  </table>
                  <div id="divSearch"></div>
                  <table cellpadding="5" cellspacing="0" width="830px">
                  	<tr>
                    	<td><input type="submit" class="button" style="float: right;" id="btnAdd" name="btnAdd" value="Add" /></td>
                    </tr>
                  </table>
					</form>
            </div>
            <div class="clear"></div>
  		</div>
      <!-- Product Detail Div ends here --> 
    </div>
</body>
</html>
<?php mysql_close($conn); ?>