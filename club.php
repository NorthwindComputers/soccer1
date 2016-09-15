<?php
/********************************
 * Club info & login page
 *
 * By: Richard Tuttle
 * Version: 1.1
 * Updated: 06 June 2013
 *******************************/
 
	require_once 'cpadmin/includes/db.php';
	session_start();
	
	$sql_club = "SELECT * FROM cms WHERE Type='Club' LIMIT 1";
	$result_club = mysql_query($sql_club);
	$num_club = mysql_num_rows($result_club);
	
	if($num_club>0) {
		$row_club = mysql_fetch_assoc($result_club);
		foreach($row_club as $key=>$value) {
			$$key = stripslashes($value);
		}
	}
	
	include_once("includes/mainHeader.php");
?>
<script>
	$(function() {
		$("#applycode").click(function() {
			if($("#customercode").val() == '') {
				alert('Please enter a valid customer code');
				return;
			}
			$.post("includes/inc_cart.php",{
				"type":"customercode", 
				"code":$("#customercode").val()
			}, function(data) {
				if(data == '') {
					$("#codeerror").html("Customer Code Not Found");
				} else {
					window.location="myaccount.php?p=register&CG="+$("#customercode").val();
				}
			});
		});

		$("#btnLogin").click(function() {
			if($("#EmailAddress").val() == '') {
				alert("Please enter a valid Email address");
				return;
			}
			if($("#Password").val() == '') {
				alert("Please enter your password");
				return;
			}

			$.post("includes/inc_account.php", {
				"type":"chkLogin", 
				"email":$("#EmailAddress").val(),
				"pass":$("#Password").val(),
				"captcha":$("#captcha-form").val()
			}, function(data) {
				if(data == 'Loggedin') {
					window.location='myaccount.php';
				} else {
					$("#loginerror").html('Invalid Email or Password.');
				}
			});
		});

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
			   this.style.height = this.contentWindow.document.body.offsetHeight + 'px';
		   });
		}
	});
</script>
</head>
<body>
<!-- Master Div starts from here -->
<div class="Master_div"> 
  <!-- Header Div starts from here -->
  <?php include_once('includes/header.php'); ?>
  <!-- Header Div ends here --> 
  <!-- Container Div starts from here -->
  <div class="container" style="padding-bottom: 0px;">
    <div class="navigation">
      <div class="navi_L"></div>
      <div class="navi_C">
        <?php include_once('includes/topnav.php'); ?>
        <div class="clear"></div>
      </div>
      <div class="navi_R"></div>
      <div class="clear"></div>
    </div>
    <div class="banner_box" style="text-align: center;">
      		<table cellpadding="5" cellspacing="1" width="100%">
            	<tr>
                	<td style="text-align: left;">
                	<script type="text/javascript">
                	$(document).ready(function() {
                		$("#info").load("includes/inc_desc.php?type=club");
                	});
                </script>
                    	<div class="iframe" id="info"></div>
                    </td>
                </tr>
            </table>
      		<table cellpadding="5" cellspacing="1" style="width: 90%; margin-left: 20px;">
            <tr>
                <td style="width: 50%; vertical-align: top;">
                <table cellpadding="5" cellspacing="0" style="padding: 5px; width: 100%; height: 80px; margin-top: 2px;">
                <tr>
                    <td class="coupon" style="color: #000000; text-align: left;">Enter your Customer Code Below</td>
                </tr>
                <tr>
                    <td style="vertical-align: top; text-align: left;"><input type="text" class="address" style="margin-right: 10px; width: 200px;" id="customercode" name="customercode" /><input type="button" class="button" id="applycode" name="applycode" value="Apply Code" /></td>
                </tr>
                <tr>
                    <td><span id="codeerror" style="color: #ff0000"></span>
                </td>
                </tr>
                </table></td>
            </tr>
            </table>
   </div> 
    <div class="clear"></div>
  </div>
	<table cellpadding="10" cellspacing="0" width="660" style="margin-left: 15px;">
    	<tr>
        	<td><!--
            	<?php
					$sql_home = "SELECT Content FROM cms WHERE Type='Home' LIMIT 1";
					$result_home = mysql_query($sql_home);
					$row_home = mysql_fetch_assoc($result_home);
					echo $row_home["Content"];
				?>
                -->
            </td>
        </tr>
    </table>
  <!-- Container Div ends here --> 
  <!-- Footer Starts from here -->
  <div class="footer">
  		<div class="foot_box">
	        <?php include_once("includes/footer.php"); ?>
        </div>
  </div>
  <!-- Footer Div ends here --> 
</div>
</body>
</html>