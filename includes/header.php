<?php
/********************************
 * Frontend Header include      
 *               
 * Updated: 25 August 2016         
 * By: Richard Tuttle           
 *******************************/
?>
<div class="header">
<?php
if (stristr($_SERVER['HTTP_USER_AGENT'], "Mobile")) {
?>
	<!-- MOBILE -->
	<div class="mobileLogo" align="center"><a href="index.php"><img src="images/mobileLogo.png" alt="SoccerOne.com" width="99%"></a></div>
    <div id="mobileInfo" align="center">Call us <strong><a href="tel:8882976386" style="color: #bf4f14;">(888) 297-6386</a></strong></div>
    <div id="mobileBar">
    	<div id="searchBox"><form method="get" action="browser.php" id="mobileSearch" style="display:inline;padding-left:5px;"><input type="text" id="search" class="mobileSearch" name="s" value="" placeholder="Search by keyword or item #" style="width:65%;"></form></div>
    	<div id="cartBox"><a href="cart.php" class="mobileCart"><?php
		if ($_SESSION["email"] != '') {
			$where = " OR EmailAddress='".$_SESSION["email"]."'";
		} else {
			$where = '';
		}
		$sql_citems = "SELECT SUM(QTY) AS TotalItems FROM shopping_cart WHERE (Type='Product' OR Type='Bundle' OR Type='VIP') AND BundleID IS NULL AND (singleid = '' OR singleid IS NULL) AND (SessionID='".session_id()."' $where)";
		$result_citems = mysql_query($sql_citems);
		if (!$result_citems) {
			die("Item Query failed: " . mysql_error());
		} else {
			$row_citems = mysql_fetch_assoc($result_citems);
		}
		if ($row_citems["TotalItems"] > 0) {
			echo "<span id='itemCount'>" . $row_citems["TotalItems"] . "</span>";
		}
		?></a></div>
		<div class="mobileHeader"><a href="#menu"></a></div>
    </div>
	<!-- END MOBILE -->
<?php } ?>
    <div class="logo1"><a href="index.php"><img src="images/browser_logo.png" alt="Home Page"></a></div>
    <div class="header_R" style="vertical-align: bottom;">
    	<table width="100%" style="margin-top: 30px;">
        <tr>
            <td style="vertical-align: bottom; text-align: left;"><?php
            if ($_SESSION["email"] != '') {
            		echo "Welcome ";
            		if ($_SESSION["name"]) {
            			echo ucfirst($_SESSION["name"]);
            		} elseif ($_SESSION["userid"]) {
            			echo ucfirst($_SESSION["userid"]);
            		} else {
            			echo "customer!";
            		}
			}
            ?>
            <br/><span class="need">Need Assistance?</span><h6>Call Toll Free: <a href="tel:8882976386">(888) 297-6386</a></h6><small><em>Hablamos Espa&ntilde;ol</em></small></td>
            <td style="vertical-align: bottom; width: 380px;">
            <div class="search">
            	<ul>
            		<li><a href="club.php">Club</a></li>
            		<li>|</li>
            		<li><a href="myaccount.php">
            <?php
					if ($_SESSION["email"] == '') {
						echo "Login or Register";
					} else {
						echo "My Account";
					}
			?>
					</a></li>
					<li>|</li>
					<li><a href="cart.php">Cart
            <?php
					if ($_SESSION["email"] != '') {
						$where = " OR EmailAddress='".$_SESSION["email"]."'";
					} else {
						$where = '';
					}
					$sql_citems = "SELECT SUM(QTY) AS TotalItems FROM shopping_cart WHERE (Type='Product' OR Type='Bundle' OR Type='VIP') AND BundleID IS NULL AND (singleid = '' OR singleid IS NULL) AND (SessionID='".session_id()."' $where)";
					$result_citems = mysql_query($sql_citems);
					if (!$result_citems) {
						die("Query failed: " . mysql_error());
					} else {
						$row_citems = mysql_fetch_assoc($result_citems);
					}
					if ($row_citems["TotalItems"] > 0) {
						echo "(".$row_citems["TotalItems"]." items)";
					}
			?>
					</a></li>
					<li>|</li>
            <?php 
					if ($_SESSION["email"] != '') {
			?>
					<li><a class="logout">Logout</a></li>
					<li>|</li>
            <?php 
            		} 
            ?>
            		<li><a href="page.php?page=ContactUs">Contact Us</a></li>
            	</ul>
                <div class="clear"></div>
                <input type="text" id="search" class="searchField" name="search" value="">
                <input type="submit" id="btnSearch" name="btnSearch" value="" class="search_btn">
                <div class="clear"></div>
                <div class="specialnav">
                    <ul class="specialnav">
                        <li class="specialnav"><a href="browser.php?c=14">New Items</a></li>
                        <li class="specialnav">|</li>
                        <li class="specialnav"><a href="browser.php?c=13">Specials</a></li>
                        <li class="specialnav">|</li>
            <?php
                        if ($_SESSION["email"] != '') {
                            $sql_cg = "SELECT CustomerGroup FROM customers WHERE EmailAddress='$_SESSION[email]' LIMIT 1";
                            $result_cg = mysql_query($sql_cg);
                            $row_cg = mysql_fetch_assoc($result_cg);
                            if ($row_cg["CustomerGroup"] != '') {
                                echo "<li class=\"specialnav\"><a href=\"browser.php?cg=$row_cg[CustomerGroup]\">$row_cg[CustomerGroup]</li>";
                            }
                        }
            ?>
                     </ul>
                </div>
            </div>
            </td>
    	</tr>
    	</table>
    	<div class="clear"></div>
    </div><!-- end header_r -->
    <div class="clear"></div>
</div><!-- end header -->
<script type="text/javascript">
$(".search_btn").click(function() {
	var sid = $("input:text[name=search]").val();
	// alert("SID : " + sid);
	if (sid == '') { // $("#search").val()
		alert('Please enter a search word');
	} else {
		window.location='browser.php?s='+sid; // $("#search").val();
	}
}); 

$('.search_btn').bind('keypress', function(e) {
	var code = (e.keyCode ? e.keyCode : e.which); 
	var sid = $("input:text[name=search]").val(); 
	if (code == 13) { 
		// if ($("#search").val() == '') {
		if (sid == '') {
			alert('Please enter a search word');
		} else {
			window.location='browser.php?s='+sid; // $("#search").val();
		}
	} 
});

$('.logout').click(function() {
	$.post("includes/inc_account.php", {
		"type":"logout"
	}, function() {
		window.location.href = "index.php";
	});
});
</script>