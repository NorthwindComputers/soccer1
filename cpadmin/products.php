<?php
/**********************************
 * Main product Admin Portal page
 * 
 * Version: 1.0
 * By: Richard Tuttle
 * Updated: 12 August 2013
 **********************************/
 
include_once("includes/header.php");
$pgTitle = "Products";
include_once("includes/mainHeader.php");
?>
<script language="javascript" type="text/javascript">
$(function() {
	$('form').jqTransform({imgPath:'jqtransformplugin/img/'});
});
$(document).ready(function() {
	$("#products").load("includes/inc_products.php", {
		"type":"view"
	});
	$("#searchProducts").click(function() {
		if ($("#filters").val() == '') {
			alert('Please select a filter');
			return false
		}	
		if ($("#prodsearch").val() == '') {
			alert('Please enter a search word');
			return false;
		}
		$("#products").html('<img src="images/loader.gif" />');
		$("#products").load("includes/inc_products.php", {
			"type":"productsearch",
			"filter":$("#filters").val(),
			"search":$("#prodsearch").val()
		});
	});
	$('#frmSearch').submit(function() {
		if ($("#filters").val() == '') {
			alert('Please select a filter');
			return false
		}
		if ($("#prodsearch").val() == '') {
			alert('Please enter a search word');
			return false;
		}
		$("#products").html('<img src="images/loader.gif" />');
		$("#products").load("includes/inc_products.php", {
			"type":"productsearch",
			"filter":$("#filters").val(),
			"search":$("#prodsearch").val()
		});
		return false;
	});
});
		
function copyprod(prodID) {
	var copy = confirm("Copy Product");
	if (copy) {
		$.post('includes/inc_products.php', {
			"type":"copy", 
			"prodid":prodID
		}, function(data) {
			alert(data);
			$("#products").load("includes/inc_products.php", {
				"type":"view"
			});
		});
	}
	return false;
}
		
function deleteprod(prodID) {
	var del = confirm("Delete Product?");
	if (del) {
		$.post('includes/inc_products.php', {
			"type":"delete", 
			"prodid":prodID
		}, function(data) {
			alert(data);
			$("#products").load("includes/inc_products.php", {
				"type":"view"
			});
		});
	}
	return false;
}
		
function qtyLoad(pager) {
	var totalnum = $("#totalview").val();
	$("#products").load('<img src="images/loader.gif" />');
	$("#products").load("includes/inc_products.php", {
		"type":"view", 
		"totalview":totalnum, 
		"pager":pager
	});
}
</script>
</head>
<body>
<!-- Master Div starts from here -->
<div class="Master_div"> 
	   <!-- Header Div starts from here -->
    	<div class="PD_header">
    		<div class="upper_head"></div>
    		<div class="navi">
          		<?php include_once('includes/menu_main.php'); ?>
          	<div class="clear"></div>
        	</div>
  		</div>
      <!-- Header Div ends here --> 
      <!-- Products view Div starts from here -->
      <div class="product_view">
    	<div class="PV_top">
          <div class="clear"></div>
          	<form action="" method="post">
                <select id="totalview" name="totalview" onChange="qtyLoad(1);">
                  <option value="100">100 products</option>
                  <option value="200">200 products</option>
                  <option value="300">300 products</option>
                  <option value="400">400 products</option>
                  <option value="500">500 products</option>
                </select>
          </form>
          <form id="frmSearch" action="" method="post" style="margin:0 0 0 270px;">
                <select id="filters" name="filters">
                  <option value="">Please select filter</option>
                  <option value="Name">Name</option>
                  <option value="ID">ID</option>
                </select>
                <input type="text" value="" id="prodsearch" name="prodsearch" />
                <img style="cursor: pointer;" id="searchProducts" src="images/go.png" alt="" />
        	</form>
        </div>
    <div class="PV_center">
          <div id="products" class="orders"><img src="images/loader.gif" /></div>
        </div>
  </div>
      <!-- Products view Div ends here --> 
    </div>
</body>
</html>