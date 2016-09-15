<?php
/***********************************************
 * SoccerOne eCommerce CMS system             
 *                                             
 * Programming by: Richard Tuttle   
 * richard@northwind.us           
 *                                             
 * Copyright (c) 2009 - 2016                    
 * Last updated: 21 April 2016                 
 **********************************************/

// call database connection settings and start user session
require_once 'cpadmin/includes/db.php';
session_start();
if (!isset($_SESSION['org_referrer'])) {
	if (isset($_SERVER['HTTP_REFERER'])) {
		$_SESSION['org_referrer'] = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);
	} elseif (isset($_SERVER['HTTP_HOST'])) {
		$_SESSION['org_referrer'] = $_SERVER['HTTP_HOST'];
	} else {
		$_SESSION['org_referrer'] = "referer not set by " . $_SERVER['HTTP_USER_AGENT'];
	}
}

 $src1 = "";
 $src2 = "";
 $link1 = "";
 $link2 = "";
 $sql = "SELECT * FROM ads WHERE id=1";
 $query = mysql_query($sql);
 $result = mysql_fetch_assoc($query);
 $src1 = "../images/ads/".$result["box1AdImage"];
 $src2 = "../images/ads/".$result["box2AdImage"];
 $link1 = $result["box1Link"];
 $link2 = $result["box2Link"];
?>
<!DOCTYPE html>
<html>
<head lang="en-US">
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>SoccerOne || Your One-Stop Shop for all Soccer Equipment and Supplies</title>
	<meta name="description" content="SoccerOne, founded in 1993, is a national distributor of soccer goods, equipment, and educational materials to individuals and organizations across the United States. The SoccerOne management team and staff have been active within the soccer community as volunteer administrators, coaches, referees and players.">
	<meta name="keyword" content="soccer balls, soccer uniforms, soccer goals, soccer gloves, goalkeeper gear, soccer books, soccer training supplies, soccer gifts, adidas, Brine, High 5, Lotto, Pevo, Protime, Pugg, Puma, OSI, Official Sports, Reusch, Select, Uhlsport, UnderArmour, US Soccer, Xara, soccer referee uniforms US Soccer (USSF), American Youth Soccer Organization (AYSO), Cal-South, Cal-North">
	<link rel="stylesheet" href="css/jquery.mmenu.all.css">
	<link rel="stylesheet" href="css/css_slider.css" type="text/css">
	<link rel="stylesheet" href="css/css_styles.css" type="text/css" media="screen">
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript" src="js/restive.min.js"></script>
	<script type="text/javascript" src="js/s3Slider2.js"></script>
	<script type="text/javascript" src="js/jquery.mmenu.all.min.js"></script>
	<script>
	$(document).ready(function() {
		var elem_body = $('body');
		elem_body.restive({
			breakpoints: 	['240', '320', '480', '640', '720', '960', '1280'],
			classes: 	 	['rp_240', 'rp_320', 'rp_480', 'rp_640', 'rp_720', 'rp_960', 'rp_1280'],
			turbo_classes: 	'is_mobile=mobi,is_phone=phone,is_tablet=tablet',
			force_dip:		true
		});

	  if ($('body').hasClass("mobi")) { 
		  $('ul#nav').removeAttr('id');
			$("nav#menu").mmenu({
				"extensions": ['effect-slide-menu', 'pagedim-black'],
				"offCanvas": {"position": "right"},
				"navbars": [{
					"position": "bottom",
					"content": ["<small>&copy;2016 Youth Sports Publishing Inc. All Rights Reserved</small>"]
				}]
			});
		} 
  
		$('a.switch').on('click', function(event) {
			event.preventDefault();
			elem_body.removeClass("mobi phone");
		});
	
		$('#slider').s3Slider({
			timeOut: 5000
		});
	
		$('#slider').trigger("s3slidernext");
	});

	cr = -1;
	function setImg(el) {
		var id = new String(el.id);
		id = id.substring(1,id.length);
		$('#slider').s3Slider.defaults.cr = id;
		$('#slider').s3Slider.makeSlider();
	}

	function setDefaultCat1(filter) {
		var id = new String(filter.id);
		var cid = id.substring(id.indexOf(":")+1, id.length);
		var catName = id.substring(0,id.indexOf(":")) + ".html";

		$.post("includes/inc_browser.php", {
			"type":"initCategId", 
			"idCat":cid
		}, function(data) {
			var pathname = new String(window.location.pathname);
			pathname=pathname.substring(0,pathname.lastIndexOf("/") + 1);
			window.location.pathname = pathname + catName;
		});
		return false;
	}
	</script>
	<style>
	#imagebox-bar-controls #navb a { display: block; float: left; font-size: 0; height: 20px; margin: 0 0px; outline: 0 none; overflow: hidden; width: 20px; padding:0px 7px !important; margin-top:3px; }
	#imagebox-bar-controls #navb a.activeSlide {} 
	#slider { z-index:0 !important; }
	#sliderContent li { list-style:none !important; }
	</style>
</head>
<body>
<div class="Master_div"><?php include_once('includes/header.php'); ?>
	<div class="container" style="padding-bottom: 0px;">
    	<div class="navigation">
      		<div class="navi_L"></div>
      		<div class="navi_C"><?php include_once('includes/topnav.php'); ?>
        		<div class="clear"></div>
      		</div>
      		<div class="navi_R"></div>
      		<div class="clear"></div>
    	</div>
   		<div class="banner_box">      
        	<div id="slider"><ul id="sliderContent">
            <?php
				$sql_banner = "SELECT BannerImage, Link FROM banner ORDER BY Sort";
				$result_banner = mysql_query($sql_banner);
				$num = 1;
				while($row_banner=mysql_fetch_array($result_banner)) {
			?>
            <li class="sliderImage"><a href="<?=$row_banner["Link"];?>"><img class="respImg" src="images/banner/<?=$row_banner["BannerImage"];?>" alt="<?=$num;?>" id="img<?php echo $num;?>" /></a>
    		<span class="top"></span></li>
            <?php
					$num++;
				}
			?>
            <div class="clear sliderImage"></div></ul>
        </div>
        <div id="imagebox-bar">
        	<div id="imagebox-bar-controls">
                <div id="navb" style="margin-top: 1px; padding-top: 11px;"> <?php for($i=1;$i<=$num-1;$i++){?><a id="s<?php echo $i; ?>" href="#" <?php if($i==0){ ?>class="activeSlide" <?php }else{ ?> class=""<?php }?> onclick="setImg(this);"><img src="./images/button<?php echo $i;?>.jpg" /></a><?php }?></div>
            </div>
        </div>
    </div>
    <div class="advertisements"><a href="<?=$link1;?>"><img src="<?=$src1;?>"></a><br><br><a href="<?=$link2;?>"><img src="<?=$src2;?>"></a></div>
    <div class="clear"></div>
  </div>
<!-- MOBILE ONLY -->
  <div class="mobileArea" align="center">
  <?php
	$sql_banner = "SELECT BannerImage, Link FROM banner ORDER BY Sort LIMIT 3";
	$result_banner = mysql_query($sql_banner);
	while ($row_banner=mysql_fetch_array($result_banner)) {
		echo '<p class="mImg"><a href="' . $row_banner["Link"] . '"><img class="mobileImg" src="images/banner/' . $row_banner["BannerImage"] . '"></a></p>';
	}
  ?>
  <p class="mImg"><a href="<?=$link1;?>"><img src="<?=$src1;?>"></a></p>
  <div id="mSocial"><a href="https://www.facebook.com/thesoccerone"><img class="mSocialImg" alt="facebook" src="images/productImages/images/socmed-facebook.png"></a><a href="https://www.twitter.com/socceronedotcom"><img class="mSocialImg" alt="facebook" src="images/productImages/images/socmed-twitter.png"></a><a href="https://www.instagram.com/socceronedotcom"><img class="mSocialImg" alt="facebook" src="images/productImages/images/socmed-instagram.png"></a><a href="https://plus.google.com/108375095222323925203"><img class="mSocialImg" alt="facebook" src="images/productImages/images/socmed-googleplus.png"></a></div>
  <p><a href="myaccount.php" class="indexbtn"><?php if ($_SESSION["email"] == '') { echo "Login or Register"; } else { echo "My Account"; } ?></a></p>
  <p><a href="page.php?page=locationsmobile" class="indexbtn">Store Locations</a></p>
  <p><a href="page.php?page=policies" class="indexbtn">Policies</a></p>
  <p><a href="#" class="indexbtn switch">Full Site</a></p>
</div>
<!-- END MOBILE -->
  <table cellpadding="10" cellspacing="0" width="100%" style="padding: 0 30px 0 15px;">
  <tr>
    <td><?php
		$sql_home = "SELECT Content FROM cms WHERE Type='Home' LIMIT 1";
		$result_home = mysql_query($sql_home);
		$row_home = mysql_fetch_assoc($result_home); 
		echo $row_home["Content"];
	?></td>
  </tr>
  </table>
  <div class="footer">
  		<div class="foot_box"><?php include_once("includes/footer.php"); ?></div>
  </div>
  <div class="mobileFooter">&copy;2016 Youth Sports Publishing, Inc. All Rights Reserved</div>
</div>
<?php require_once("includes/ga.php"); ?>
</body>
</html>