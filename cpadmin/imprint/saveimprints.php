<?php

if(isset($_POST["id"])){
	$id = substr(mysql_real_escape_string($_POST["id"]),3);
	$typename = mysql_real_escape_string($_POST["value"]);
	include("./Database.class.php");
	include("./optiontype.class.php");
	$optiontype = new optiontype();
	$params = array();
	$params["IDTYPE"] = $id;
	$optiontype->readObject($params);
	$optiontype2 = new optiontype();
	$optiontype2->setIDTYPE($id);
	$optiontype2->setOPTIONTYPE($typename);
	$optiontype2->setimptype($optiontype->getimptype());
	$optiontype2->insert();
	
	echo $typename;
}
?>