<?php
	$path = explode("/",$_SERVER['PHP_SELF']);
	$page = $path[count($path)-1];
?>
<ul>
	<li><a href="index.php" style="width: 44px;">Home</a></li>
    <li <?php if($page == "product_detail.php"){ echo 'class="head_nav_sel"'; } ?> ><a href="product_detail.php?id=<?=$_GET['id'];?>" style="padding-left: 9px; padding-right: 9px;">General Information</a></li>
    <?php
		if($_GET["id"] == '') {
			echo "<li><a style=\"width: 644px;\">&nbsp;&nbsp;</a></li>";
		} else { ?>
    <li <?php if($page == "pricing.php"){ echo 'class="head_nav_sel"'; } ?>><a href="pricing.php?id=<?=$_GET['id'];?>" style="padding-left: 9px; padding-right: 9px;">Options &amp; Pricing</a></li>
    <li <?php if($page == "multimedia.php"){ echo 'class="head_nav_sel"'; } ?>><a href="multimedia.php?id=<?=$_GET['id'];?>">MultiMedia</a></li>
    <li <?php if($page == "shipping.php"){ echo 'class="head_nav_sel"'; } ?>><a href="shipping.php?id=<?=$_GET['id'];?>">Shipping</a></li>
    <li <?php if($page == "related.php"){ echo 'class="head_nav_sel"'; } ?>><a href="related.php?id=<?=$_GET['id'];?>">Related Products</a></li>
    <li <?php if($page == "upsale.php"){ echo 'class="head_nav_sel"'; } ?>><a href="upsale.php?id=<?=$_GET['id'];?>">Upsales</a></li>
     <li <?php if($page == "imprint.php"){ echo 'class="head_nav_sel"'; } ?>><a href="imprint.php?id=<?=$_GET['id'];?>" style="padding-right: 42px; padding-left: 42px;">Imprint</a></li>
    <?php } ?>
</ul>