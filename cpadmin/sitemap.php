<?php
/****************************
 * Sitemap generator script
 *
 * by: Richard Tuttle
 * updated: 01 March 2016
 ****************************/
require_once("cpadmin/includes/db.php");

// get products
function getProds($catID) {
	$sql_products = "SELECT DISTINCT p.id, p.BrowserName, p.BrowserName2, p.BrowserName3 FROM products p, product_options o, product_browser b, category_items c WHERE p.id=o.ProductID AND p.id=c.ProductID AND p.Status='Enabled' AND p.AvailableQty>0 AND c.CategoryID='" . $catID . "'";
	$result_products = mysql_query($sql_products) or die(mysql_error());
	while ($row_products = mysql_fetch_array($result_products)) {
		$prodTitle = strtolower($row_products["BrowserName"] . '_' . $row_products["BrowserName2"] . '_' . $row_products["BrowserName3"]);
		$fullProdTitle = str_replace(array(" ", '/', '-', '?', '\\'), '_', $prodTitle);
		// echo "SQL 2: " . $sql_products . "</br>";
		?>
		<url>
		<loc>https://www.soccerone.com/<?php echo htmlentities($fullProdTitle) . '_p_' . $row_products["id"] . '.html'; ?></loc>
		<lastmod><?php echo date("Y-m-d"); ?></lastmod>
		<priority>0.5</priority>
		<changefreq>daily</changefreq>
		</url>
		<?php
	}
}

// get categories
function getSubCat($pid) {
	$sql_subnav = "SELECT id, Category FROM category WHERE Status='Enabled' AND ParentID='$pid'";
	$result_subnav = mysql_query($sql_subnav) or die(mysql_error());
	$num_subnav = mysql_num_rows($result_subnav);
	if ($num_subnav > 0) {
		while ($row_subnav = mysql_fetch_array($result_subnav)) {
			$moreSubSQL = mysql_query("SELECT * FROM category WHERE Status='Enabled' AND ParentID=" . $row_subnav["id"]);
			$moreRows = mysql_num_rows($moreSubSQL);
			$cateTitle = strtolower(str_replace(array(" ", '/', '-', '?', '\\'), "_", $row_subnav["Category"]));
			if ($moreRows > 0) {
				// echo "SQL 0: " . $sql_subnav; break;
				?>
				<url>
				<loc>https://www.soccerone.com/<?php echo htmlentities($cateTitle) . '-c-' . $row_subnav["id"] . '.html'; ?></loc>
				<lastmod><?php echo date("Y-m-d"); ?></lastmod>
				<priority>0.5</priority>
				<changefreq>daily</changefreq>
				</url>
				<?php
				// getProds($row_subnav["id"]);
				getSubCat($row_subnav["id"]);
			} else {
				//echo "SQL 1: " . $sql_subnav . "</br>";
				?>
				<url>
				<loc>https://www.soccerone.com/<?php echo htmlentities($cateTitle) . '-c-' . $row_subnav["id"] . '.html'; ?></loc>
				<lastmod><?php echo date("Y-m-d"); ?></lastmod>
				<priority>0.5</priority>
				<changefreq>daily</changefreq>
				</url>
				<?php
				getProds($row_subnav["id"]);
			}
		}
	}
}
		
$sql = "SELECT id, Category FROM category WHERE Status='Enabled' AND ParentID=0";
$result = mysql_query($sql) or die(mysql_error());
header('Content-Type: application/xml');
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
echo '<url><loc>https://www.soccerone.com</loc><lastmod>';
echo date("Y-m-d");
echo '</lastmod><changefreq>monthly</changefreq><priority>1.0</priority></url>';
while ($row = mysql_fetch_assoc($result)) {
	$cateTitle = strtolower(str_replace(array(" ", '/', '-', '?', '\\'), "_", $row["Category"]));
	getSubCat($row["id"]);
?>
 <url>
 <loc>https://www.soccerone.com/<?php echo htmlentities($cateTitle) . '-c-' . $row["id"] . '.html'; ?></loc>
 <lastmod><?php echo date("Y-m-d"); ?></lastmod>
 <changefreq>daily</changefreq>
 <priority>0.5</priority>
 </url>
 <?php 
} 
?>
</urlset>