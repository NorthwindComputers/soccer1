<?php
    session_start();	
	include("./imprint/pricing.class.php");
	include("./imprint/Database.class.php");
	$pricing = new pricing();
	if(isset($_POST["idop"]) || isset($_SESSION["idop"])){
       	if(isset($_POST["idop"]))
	      $_SESSION["idop"] = mysql_real_escape_string($_POST["idop"]);

		$idp = $_SESSION["idop"];
		$array = array();
		$array["IDOPTION"] = $idp;
		$pricing->readObject($array);
		$qty = $_POST["qty"];
		$s[0] = $pricing->getSTARTQT_1();
		$s[1] = $pricing->getSTARTQT_2();		
		$s[2] = $pricing->getSTARTQT_3();
		$s[3] = $pricing->getSTARTQT_4();

		$e[0] = $pricing->getENDQT_1();
		$e[1] = $pricing->getENDQT_2();		
		$e[2] = $pricing->getENDQT_3();
		$e[3] = $pricing->getENDQT_4();

		$v[0] = $pricing->getPRICE1();
		$v[1] = $pricing->getPRICE2();
		$v[2] = $pricing->getPRICE3();
		$v[3] = $pricing->getPRICE4();
		$v[4] = $pricing->getNONMEMBER_PRICE();

		for ( $i = 0; $i < 4; $i++){
			if (($qty >= $s[$i]) && ($qty <= $e[$i]))
				break;
		}
		$formatted = sprintf("%.2f", $v[$i]);
		echo $formatted;
	}
?>
