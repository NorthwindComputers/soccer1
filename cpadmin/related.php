<?php
/**
 * Related products
 *
 * Version: 1.2
 * Updated: 28 Feb 2013
 * By: Richard Tuttle
 */

	include_once("includes/header.php");
	
	if(isset($_POST["btnAdd"])) {
		foreach($_POST["related"] as $value) {
			$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
			$sql_add = "INSERT INTO product_related(ProductID, RelatedID, `Type`) VALUES ($id, $value, 'related')";
			
			if(!mysql_query($sql_add)) {
				echo "Error Adding Related Products: ";
			}
		}
	}
	
	if(isset($_POST["btnRemove"])) {
		foreach($_POST["remove"] as $value) {
			$sql_remove = "DELETE FROM product_related WHERE id=$value LIMIT 1";
			if(!mysql_query($sql_remove)) {
				echo "Error Removing related product: ".mysql_error();
			}
		}
	}

	$pgTitle = "Related Products";
	include_once("includes/mainHeader.php");
?>
	<script language="javascript" type="text/javascript">
	function searchProducts() {
		if($("#filters").val()=='') {
					alert('Please select a filter');
					return false
				}
				
				if($("#prodsearch").val()=='') {
					alert('Please enter a search word');
					return false;
				}
				var sfilter ="";
				var nfilter ="";
				if(document.getElementById("filters").value =="SKU")
				   sfilter = document.getElementById("prodsearch").value;
				 if(document.getElementById("filters").value =="NAME")
				   nfilter = document.getElementById("prodsearch").value;
				   
				$("#divSearch").load("includes/inc_related.php", {
					"type":"searchrelated", 
					"id":"<?=filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);?>",
					"sku":sfilter,
					"name":nfilter,
					"pricelow":$("#searchpricelow").val(),
					"pricehigh":$("#searchpricehigh").val()
				});
	}
		$(document).ready(function() {
			$(function(){
			$('#frmSearch').jqTransform({imgPath:'jqtransformplugin/img/'});
		});			
			$("#searchProducts").click(function(){
				
				searchProducts();
			});
			$('#frmSearch').submit(function() {
				searchProducts();
				  return false;
			});
			$("#search").click(function() {
				$("#divSearch").load("includes/inc_related.php", {
					"type":"searchrelated", 
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
            	<form action="" method="post" id="form1" >
                	<input type="hidden" id="prodid" name="prodid" value="<?=filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);?>" />
            	<table cellpading="5" cellspacing="1">
                	<tr>
                        <td colspan="3">
                        	<?php
                        		$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
								$sql_prod = "SELECT ProductDetailName, RootSKU FROM products WHERE id='$id' LIMIT 1";
								$result_prod = mysql_query($sql_prod);
								$row_prod = mysql_fetch_assoc($result_prod);
							?>
                        	<h1>Related Products - <?=$row_prod["ProductDetailName"]." - ".$row_prod["RootSKU"];?>
                             <br/><span>----------------------------------------------------------</span></h1></td>
                        <td><input type="submit" class="button" id="btnRemove" name="btnRemove" value="Remove" onclick="return confirm('Remove selected related products?');" /></td>
                    </tr>
                	<tr>
                	  <td colspan="4"></td>
               	  </tr>
                   <tr>
                   	 <td colspan="4" style="width: 43px;">
                </td>
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
						$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
						$sql_related = "SELECT r.id, p.RootSKU, p.ProductDetailName, p.NoneMemberPrice FROM products p, product_related r WHERE p.id=r.RelatedID AND r.ProductID='$id' AND r.`Type`='related'";
						$result_related = mysql_query($sql_related);
						
						while($row_related = mysql_fetch_array($result_related)) {
							echo '<tr>';
							echo '<td class="checkbox"><input type="checkbox" id="remove[]" name="remove[]" value="'.$row_related["id"].'" /></td>';
							echo '<td class="sku">'.$row_related["RootSKU"].'</td>';
							echo '<td class="name">'.$row_related["ProductDetailName"].'</td>';
							echo '<td class="price">'.$row_related["NoneMemberPrice"].'</td>';
							echo '</tr>';
						}
					?>
                  </table>
              </form>
            	<div class="clear" style="margin-top: 50px;"></div>
            <form id="frmSearch" action="" method="post" style="margin:0 0 0 270px;">
        <select id="filters" name="filters">
          <option value="">Please select filter</option>
          <option value="NAME">Name</option>
          <option value="SKU">SKU</option>
        </select>
    <input type="text" value="" id="prodsearch" name="prodsearch" />
                <img style="cursor: pointer;" id="searchProducts" src="images/go.png" alt="" />
        	</form>
            <form method="post" action="">
            		
                  <table cellpadding="5" cellspacing="1">
                  	<tr>
                    	<td style="width: 35px; height: 30px;"></td>
                        <td colspan="2"><h1>Add More Products</h1></td>
                        <td>&nbsp;</td>
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