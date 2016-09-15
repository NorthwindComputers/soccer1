<?php
    session_start();	
	include("imprint/pricing.class.php");
	$pricing = new pricing();
	if(isset($_POST["idop"]) || isset($_SESSION["idop"])){
       	if(isset($_POST["idop"]))
	      $_SESSION["idop"] = $_POST["idop"];

		$idp = $_SESSION["idop"];
		$array = array();
		$array["IDOPTION"] = $idp;
		$pricing->readObject($array);

		echo $pricing->getENDQT_1();
	}
?>
