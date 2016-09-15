<?php

if(isset($_POST["id"])){
	$id = substr(mysql_real_escape_string($_POST["id"]),1);
	$tabname = mysql_real_escape_string($_POST["value"]);
	include("./Database.class.php");
	include("./imp_category_tabs.class.php");
	$imp_category_tabs2 = new imp_category_tabs();
	$params = array();
	$params["id_tab"] = $id;
	$imp_category_tabs2->readObject($params);
	$imp_category_tabs = new imp_category_tabs();
	$imp_category_tabs->setid_tab($id);
	$imp_category_tabs->settab_name($tabname);
	$imp_category_tabs->setimprint_categ_id($imp_category_tabs2->getimprint_categ_id());
	$imp_category_tabs->settab_parent($imp_category_tabs2->gettab_parent());
	$imp_category_tabs->setis_parent(0);
	$imp_category_tabs->insert();
	
	echo $tabname;
}
?>