<?php
	$path = explode("/",$_SERVER['PHP_SELF']);
	$page = $path[count($path)-1];
?>
<ul>
    <li <?php if($page == "index.php"){ echo 'class="head_nav_sel"'; } ?>><a href="index.php">Dashboard</a></li>
    <li <?php if($page == "products.php"){ echo 'class="head_nav_sel"'; } ?>><a href="products.php">Products</a></li>
    <li <?php if($page == "category.php"){ echo 'class="head_nav_sel"'; } ?>><a href="category.php">Category</a></li>
    <li <?php if($page == "orders.php"){ echo 'class="head_nav_sel"'; } ?>><a href="orders.php">Orders</a></li>
    <li <?php if($page == "cms.php"){ echo 'class="head_nav_sel"'; } ?>><a href="cms.php">CMS</a></li>
    <li <?php if($page == "settings.php"){ echo 'class="head_nav_sel"'; } ?>><a href="settings.php">Settings</a></li>
    <li <?php if($page == "customers.php"){ echo 'class="head_nav_sel"'; } ?>><a href="customers.php">Customers</a></li>
    <li <?php if($page == "options.php"){ echo 'class="head_nav_sel"'; } ?>><a href="options.php" style="width: 48px;padding-left: 21px; padding-right: 21px;">Options</a></li>
    <li <?php if($page == "imprint.php"){ echo 'class="head_nav_sel"'; } ?>><a href="imprint.php" style="padding-right: 41px; padding-left: 41px;">Imprint</a></li>
</ul>