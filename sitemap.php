<?php
/****************************
 * Sitemap generator script
 *
 * by: Richard Tuttle
 * updated: 29 July 2016
 ****************************/
require_once("cpadmin/includes/db.php");
$today = date("Y-m-d");
$mainURL = "https://www.soccerone.com";

// get products
function getProds($catID, $xml, $xml_url, $xml_urlset) {
	$sql_products = "SELECT DISTINCT p.id, p.BrowserName, p.BrowserName2, p.BrowserName3 FROM products p, product_options o, product_browser b, category_items c WHERE p.id=o.ProductID AND p.id=c.ProductID AND p.Status='Enabled' AND p.AvailableQty>0 AND c.CategoryID='" . $catID . "'";
	$result_products = mysql_query($sql_products) or die(mysql_error());
	while ($row_products = mysql_fetch_array($result_products)) {
		$prodTitle = strtolower($row_products["BrowserName"] . '_' . $row_products["BrowserName2"] . '_' . $row_products["BrowserName3"]);
		$fullProdTitle = str_replace(array(" ", '/', '-', '?', '\\'), '_', $prodTitle);
		$subURL = htmlentities($fullProdTitle) . '_p_' . $row_products["id"] . '.html';
		$xml_url = $xml->createElement("url");
		$xml_loc = $xml->createElement("loc", "$GLOBALS[mainURL]/$subURL");
		$xml_mod = $xml->createElement("lastmod", "$GLOBALS[today]");
		$xml_freq = $xml->createElement("changefreq", "weekly");
		$xml_pri = $xml->createElement("priority", "0.5");
		$xml_url->appendChild($xml_loc);
		$xml_url->appendChild($xml_mod);
		$xml_url->appendChild($xml_freq);
		$xml_url->appendChild($xml_pri);
		$xml_urlset->appendChild($xml_url);
	}
	return $xml;
}

// get categories
function getSubCat($pid, $xml, $xml_url, $xml_urlset) {
	$sql_subnav = "SELECT id, Category FROM category WHERE Status='Enabled' AND ParentID='$pid'";
	$result_subnav = mysql_query($sql_subnav) or die(mysql_error());
	$num_subnav = mysql_num_rows($result_subnav);
	if ($num_subnav > 0) {
		while ($row_subnav = mysql_fetch_array($result_subnav)) {
			$moreSubSQL = mysql_query("SELECT * FROM category WHERE Status='Enabled' AND ParentID=" . $row_subnav["id"]);
			$moreRows = mysql_num_rows($moreSubSQL);
			$cateTitle = strtolower(str_replace(array(" ", '/', '-', '?', '\\'), "_", $row_subnav["Category"]));
			$subURL = htmlentities($cateTitle) . '-c-' . $row_subnav["id"] . '.html';
			if ($moreRows > 0) {
				$xml_url = $xml->createElement("url");
				$xml_loc = $xml->createElement("loc", "$GLOBALS[mainURL]/$subURL");
				$xml_mod = $xml->createElement("lastmod", "$GLOBALS[today]");
				$xml_freq = $xml->createElement("changefreq", "weekly");
				$xml_pri = $xml->createElement("priority", "0.5");
				$xml_url->appendChild($xml_loc);
				$xml_url->appendChild($xml_mod);
				$xml_url->appendChild($xml_freq);
				$xml_url->appendChild($xml_pri);
				$xml_urlset->appendChild($xml_url);
				getSubCat($row_subnav["id"], $xml, $xml_url, $xml_urlset);
			} else {
				$xml_url = $xml->createElement("url");
				$xml_loc = $xml->createElement("loc", "$GLOBALS[mainURL]/$subURL");
				$xml_mod = $xml->createElement("lastmod", "$GLOBALS[today]");
				$xml_freq = $xml->createElement("changefreq", "weekly");
				$xml_pri = $xml->createElement("priority", "0.5");
				$xml_url->appendChild($xml_loc);
				$xml_url->appendChild($xml_mod);
				$xml_url->appendChild($xml_freq);
				$xml_url->appendChild($xml_pri);
				$xml_urlset->appendChild($xml_url);
				getProds($row_subnav["id"], $xml, $xml_url, $xml_urlset);
			}
		}
	}
	return $xml;
}
		
$sql = "SELECT id, Category FROM category WHERE Status='Enabled' AND ParentID=0";
$result = mysql_query($sql) or die(mysql_error());
$xml = new DOMDocument("1.0", "UTF-8");
$xml_urlset = $xml->createElement("urlset");
$xml_urlset->setAttribute("xmlns", "http://www.sitemaps.org/schemas/sitemap/0.9");
$xml_url = $xml->createElement("url");
$xml_loc = $xml->createElement("loc", "$mainURL");
$xml_mod = $xml->createElement("lastmod", "$today");
$xml_freq = $xml->createElement("changefreq", "weekly");
$xml_pri = $xml->createElement("priority", "1.0");
$xml_url->appendChild($xml_loc);
$xml_url->appendChild($xml_mod);
$xml_url->appendChild($xml_freq);
$xml_url->appendChild($xml_pri);
$xml_urlset->appendChild($xml_url);
while ($row = mysql_fetch_assoc($result)) {
	$cateTitle = strtolower(str_replace(array(" ", '/', '-', '?', '\\'), "_", $row["Category"]));
	getSubCat($row["id"], $xml, $xml_url, $xml_urlset);
	$subURL = htmlentities($cateTitle) . '-c-' . $row["id"] . '.html';
	$xml_url = $xml->createElement("url");
	$xml_loc = $xml->createElement("loc", "$mainURL/$subURL");
	$xml_mod = $xml->createElement("lastmod", "$today");
	$xml_freq = $xml->createElement("changefreq", "weekly");
	$xml_pri = $xml->createElement("priority", "0.5");
	$xml_url->appendChild($xml_loc);
	$xml_url->appendChild($xml_mod);
	$xml_url->appendChild($xml_freq);
	$xml_url->appendChild($xml_pri);
	$xml_urlset->appendChild($xml_url);
}
$xml->appendChild($xml_urlset);
$xml->formatOutput = true;
$xml->save("/sitemap.xml");
?>