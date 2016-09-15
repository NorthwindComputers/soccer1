<?php
/**
 * footer pages file
 *
 * Version: 1.2
 * Updated: 21 August 2014
 * By: Richard Tuttle
 */
	require_once 'cpadmin/includes/db.php';
	session_start();
	
	$cat = 0;
	$page = $_GET['page'];
	$sql_page = "SELECT * FROM cms WHERE PageName='$page' LIMIT 1";
	$result_page = mysql_query($sql_page);
	$row_page = mysql_fetch_assoc($result_page);
	$num_page = mysql_num_rows($result_page);

	if($num_page > 0) {
		foreach($row_page as $key=>$value) {
			$$key = stripslashes($value);
		}
	} else {
		$Content = '<span style="font-weight: bold; margin: 50px 0 100px 0;">Sorry, page not found.</span>';
	}

	include_once("includes/mainHeader.php");
?>
<script language="javascript" type="text/javascript">
		$(function() {
			$('form').jqTransform({
				imgPath:'jqtransformplugin/img/'
			});
			$(".colorfilter").click(function() {
				$("#divproducts").html('<img src="images/loader.gif" />');
				$.post("includes/inc_browser.php",{
					"type":"setfilter", 
					"filtertype":"Color", 
					"filterid":$(this).attr("href")
				}, function(data) {
						$("#divfilter").html(data);
						$("#divproducts").load("includes/inc_browser.php", {
							"type":"products", 
							"cat":"<?=$cat;?>"
						});
				});
				return false;
			});
			
			///////////////////////////////////////////////////
			var iFrames = $('iframe');
      
    		function iResize() {
    			for (var i = 0, j = iFrames.length; i < j; i++) {
    		  		iFrames[i].style.height = iFrames[i].contentWindow.document.body.offsetHeight + 'px';
				}
    	    }
    	    
        	if ($.browser.safari || $.browser.opera) { 
        		iFrames.load(function(){
					setTimeout(iResize, 0);
               	});
            
       			for (var i = 0, j = iFrames.length; i < j; i++) {
        			var iSource = iFrames[i].src;
        			iFrames[i].src = '';
        			iFrames[i].src = iSource;
               }
        	} else {
        	   iFrames.load(function() { 
        	      /* this.style.height = this.contentWindow.document.body.offsetHeight + 'px'; */
        	   });
        	}
			//////////////////////////////////////////////////////////////
		});
		
		function removefilter() {
			$("#divproducts").html('<img src="images/loader.gif" />');
			$.post("includes/inc_browser.php", {
				"type":"removefilter"
			}, function(data) {
				$("#divfilter").html('');
				$("#divproducts").load("includes/inc_browser.php", {
					"type":"products"
				});
			});
			return false;
		}
	</script>
</head>
<body>
<!-- Master Div starts from here -->
<div class="Master_div"> 
  <!-- Header Div starts from here -->
  <?php include('includes/header.php'); ?>
  <!-- Header Div ends here --> 
  <!-- Container Div starts from here -->
  <div class="container container1">
    <div class="navigation">
      <div class="navi_L"></div>
      <div class="navi_C">
        <?php include('includes/topnav.php'); ?>
        <div class="clear"></div>
      </div>
      <div class="navi_R"></div>
      <div class="clear"></div>
    </div>
<?php
	if($LeftNav == "Yes") {
?>
<div class="browser_color">
      <div id="divfilter"></div>
      <div class="clear"></div>
	<?php
		if($cat == 0) {
			$catname = "Categories";
		} else {
			$sql_cat = "SELECT Category FROM category WHERE id=$cat LIMIT 1";
			$result_cat = mysql_query($sql_cat);
			$row_cat = mysql_fetch_assoc($result_cat);
			$catname = $row_cat["Category"];
		}
	?>
		<h1><?=$catname;?></h1>
      		<div class="apparel_ul">
        	<ul>
	<?php
		$sql_sub = "SELECT id, Category FROM category WHERE ParentID=$cat";
		$result_sub = mysql_query($sql_sub);
		while($row_sub = mysql_fetch_array($result_sub)) {
			echo "<li><a href=\"browser.php?c=$row_sub[id]\">$row_sub[Category]</a></li>";
		}
	?>
	        </ul>
      		</div>
    </div>
<?php
	}
?>
    <!-- :: Product Images :::::::::::::::::::::::::::::::::::::::::::::::::: -->
    <script>
		$(document).ready(function() {
			$("#divproducts").load("includes/inc_desc.php?type=page&id=<?=$page;?>");
		});
	</script>
    <div id="divproducts" class="browser_product" style="padding-left: 30px; width: 940px;"></div>
    <div class="clear"></div>
  </div>
  <!-- Container Div ends here --> 
  <!-- Footer Starts from here -->
  <div class="footer">
	<div class="foot_box">
	<?php include("includes/footer.php"); ?>
	</div>
  </div>
  <!-- Footer Div ends here --> 
</div>
</body>
</html>
<?php mysql_close($conn); ?>