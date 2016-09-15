<?php


define("PRICING", "pricing"); 

class pricing {

	private $IDPRICING;
	private $IDOPTION;
	private $NONMEMBER_PRICE;
	private $NONSEQUENCE_PRICE;
	private $STARTQT_1;
	private $ENDQT_1;
	private $PRICE1;
	private $NONSEQUENCE_PRICE1;
	private $STARTQT_2;
	private $ENDQT_2;
	private $PRICE2;
	private $NONSEQUENCE_PRICE2;
	private $STARTQT_3;
	private $ENDQT_3;
	private $PRICE3;
	private $NONSEQUENCE_PRICE3;
	private $STARTQT_4;
	private $ENDQT_4;
	private $PRICE4;
	private $NONSEQUENCCE_PRICE4;
	private $setup_fee;

	public function setIDPRICING($pArg="0") {$this->IDPRICING=$pArg;}
	public function setIDOPTION($pArg="0") {$this->IDOPTION=$pArg;}
	public function setNONMEMBER_PRICE($pArg="0") {$this->NONMEMBER_PRICE=$pArg;}
	public function setNONSEQUENCE_PRICE($pArg="0") {$this->NONSEQUENCE_PRICE=$pArg;}
	public function setSTARTQT_1($pArg="0") {$this->STARTQT_1=$pArg;}
	public function setENDQT_1($pArg="0") {$this->ENDQT_1=$pArg;}
	public function setPRICE1($pArg="0") {$this->PRICE1=$pArg;}
	public function setNONSEQUENCE_PRICE1($pArg="0") {$this->NONSEQUENCE_PRICE1=$pArg;}
	public function setSTARTQT_2($pArg="0") {$this->STARTQT_2=$pArg;}
	public function setENDQT_2($pArg="0") {$this->ENDQT_2=$pArg;}
	public function setPRICE2($pArg="0") {$this->PRICE2=$pArg;}
	public function setNONSEQUENCE_PRICE2($pArg="0") {$this->NONSEQUENCE_PRICE2=$pArg;}
	public function setSTARTQT_3($pArg="0") {$this->STARTQT_3=$pArg;}
	public function setENDQT_3($pArg="0") {$this->ENDQT_3=$pArg;}
	public function setPRICE3($pArg="0") {$this->PRICE3=$pArg;}
	public function setNONSEQUENCE_PRICE3($pArg="0") {$this->NONSEQUENCE_PRICE3=$pArg;}
	public function setSTARTQT_4($pArg="0") {$this->STARTQT_4=$pArg;}
	public function setENDQT_4($pArg="0") {$this->ENDQT_4=$pArg;}
	public function setPRICE4($pArg="0") {$this->PRICE4=$pArg;}
	public function setNONSEQUENCCE_PRICE4($pArg="0") {$this->NONSEQUENCCE_PRICE4=$pArg;}
	public function setSetup_fee($pArg="0") {$this->setup_fee=$pArg;}

	public function getIDPRICING() {return $this->IDPRICING;}
	public function getIDOPTION() {return $this->IDOPTION;}
	public function getNONMEMBER_PRICE() {return $this->NONMEMBER_PRICE;}
	public function getNONSEQUENCE_PRICE() {return $this->NONSEQUENCE_PRICE;}
	public function getSTARTQT_1() {return $this->STARTQT_1;}
	public function getENDQT_1() {return $this->ENDQT_1;}
	public function getPRICE1() {return $this->PRICE1;}
	public function getNONSEQUENCE_PRICE1() {return $this->NONSEQUENCE_PRICE1;}
	public function getSTARTQT_2() {return $this->STARTQT_2;}
	public function getENDQT_2() {return $this->ENDQT_2;}
	public function getPRICE2() {return $this->PRICE2;}
	public function getNONSEQUENCE_PRICE2() {return $this->NONSEQUENCE_PRICE2;}
	public function getSTARTQT_3() {return $this->STARTQT_3;}
	public function getENDQT_3() {return $this->ENDQT_3;}
	public function getPRICE3() {return $this->PRICE3;}
	public function getNONSEQUENCE_PRICE3() {return $this->NONSEQUENCE_PRICE3;}
	public function getSTARTQT_4() {return $this->STARTQT_4;}
	public function getENDQT_4() {return $this->ENDQT_4;}
	public function getPRICE4() {return $this->PRICE4;}
	public function getNONSEQUENCCE_PRICE4() {return $this->NONSEQUENCCE_PRICE4;}
	public function getSetup_fee() {return $this->setup_fee;}

	public function readObject($array = array()) {
		$qry = "SELECT *".RET."FROM ".PRICING.RET;
		$and = "WHERE".RET;

		if($array['IDPRICING'] != "") {
			$qry .= $and."IDPRICING = '".$array['IDPRICING']."'".RET;
			$and = "AND".RET;
		}

		if($array['IDOPTION'] != "") {
			$qry .= $and."IDOPTION = '".$array['IDOPTION']."'".RET;
			$and = "AND".RET;
		}

		if($array['NONMEMBER_PRICE'] != "") {
			$qry .= $and."NONMEMBER_PRICE = '".$array['NONMEMBER_PRICE']."'".RET;
			$and = "AND".RET;
		}

		if($array['NONSEQUENCE_PRICE'] != "") {
			$qry .= $and."NONSEQUENCE_PRICE = '".$array['NONSEQUENCE_PRICE']."'".RET;
			$and = "AND".RET;
		}

		if($array['STARTQT_1'] != "") {
			$qry .= $and."STARTQT_1 = '".$array['STARTQT_1']."'".RET;
			$and = "AND".RET;
		}

		if($array['ENDQT_1'] != "") {
			$qry .= $and."ENDQT_1 = '".$array['ENDQT_1']."'".RET;
			$and = "AND".RET;
		}

		if($array['PRICE1'] != "") {
			$qry .= $and."PRICE1 = '".$array['PRICE1']."'".RET;
			$and = "AND".RET;
		}

		if($array['NONSEQUENCE_PRICE1'] != "") {
			$qry .= $and."NONSEQUENCE_PRICE1 = '".$array['NONSEQUENCE_PRICE1']."'".RET;
			$and = "AND".RET;
		}

		if($array['STARTQT_2'] != "") {
			$qry .= $and."STARTQT_2 = '".$array['STARTQT_2']."'".RET;
			$and = "AND".RET;
		}

		if($array['ENDQT_2'] != "") {
			$qry .= $and."ENDQT_2 = '".$array['ENDQT_2']."'".RET;
			$and = "AND".RET;
		}

		if($array['PRICE2'] != "") {
			$qry .= $and."PRICE2 = '".$array['PRICE2']."'".RET;
			$and = "AND".RET;
		}

		if($array['NONSEQUENCE_PRICE2'] != "") {
			$qry .= $and."NONSEQUENCE_PRICE2 = '".$array['NONSEQUENCE_PRICE2']."'".RET;
			$and = "AND".RET;
		}

		if($array['STARTQT_3'] != "") {
			$qry .= $and."STARTQT_3 = '".$array['STARTQT_3']."'".RET;
			$and = "AND".RET;
		}

		if($array['ENDQT_3'] != "") {
			$qry .= $and."ENDQT_3 = '".$array['ENDQT_3']."'".RET;
			$and = "AND".RET;
		}

		if($array['PRICE3'] != "") {
			$qry .= $and."PRICE3 = '".$array['PRICE3']."'".RET;
			$and = "AND".RET;
		}

		if($array['NONSEQUENCE_PRICE3'] != "") {
			$qry .= $and."NONSEQUENCE_PRICE3 = '".$array['NONSEQUENCE_PRICE3']."'".RET;
			$and = "AND".RET;
		}

		if($array['STARTQT_4'] != "") {
			$qry .= $and."STARTQT_4 = '".$array['STARTQT_4']."'".RET;
			$and = "AND".RET;
		}

		if($array['ENDQT_4'] != "") {
			$qry .= $and."ENDQT_4 = '".$array['ENDQT_4']."'".RET;
			$and = "AND".RET;
		}

		if($array['PRICE4'] != "") {
			$qry .= $and."PRICE4 = '".$array['PRICE4']."'".RET;
			$and = "AND".RET;
		}

		if($array['NONSEQUENCCE_PRICE4'] != "") {
			$qry .= $and."NONSEQUENCCE_PRICE4 = '".$array['NONSEQUENCCE_PRICE4']."'".RET;
			$and = "AND".RET;
		}

		$record = Database::select($qry);
		if(count($record[0]) == 0) {
			return array();
		} else {
			$record = $record[0];
			$this->setIDPRICING($record['IDPRICING']);
			$this->setIDOPTION($record['IDOPTION']);
			$this->setNONMEMBER_PRICE($record['NONMEMBER_PRICE']);
			$this->setNONSEQUENCE_PRICE($record['NONSEQUENCE_PRICE']);
			$this->setSTARTQT_1($record['STARTQT_1']);
			$this->setENDQT_1($record['ENDQT_1']);
			$this->setPRICE1($record['PRICE1']);
			$this->setNONSEQUENCE_PRICE1($record['NONSEQUENCE_PRICE1']);
			$this->setSTARTQT_2($record['STARTQT_2']);
			$this->setENDQT_2($record['ENDQT_2']);
			$this->setPRICE2($record['PRICE2']);
			$this->setNONSEQUENCE_PRICE2($record['NONSEQUENCE_PRICE2']);
			$this->setSTARTQT_3($record['STARTQT_3']);
			$this->setENDQT_3($record['ENDQT_3']);
			$this->setPRICE3($record['PRICE3']);
			$this->setNONSEQUENCE_PRICE3($record['NONSEQUENCE_PRICE3']);
			$this->setSTARTQT_4($record['STARTQT_4']);
			$this->setENDQT_4($record['ENDQT_4']);
			$this->setPRICE4($record['PRICE4']);
			$this->setNONSEQUENCCE_PRICE4($record['NONSEQUENCCE_PRICE4']);
			$this->setSetup_fee($record['setup_fee']);
			return true;
		}
	}

	public static function readArray($array = array()) {
		$qry = "SELECT *".RET."FROM ".PRICING.RET;
		$and = "WHERE".RET;

		if($array['IDPRICING'] != "") {
			$qry .= $and."IDPRICING = '".$array['IDPRICING']."'".RET;
			$and = "AND".RET;
		}

		if($array['IDOPTION'] != "") {
			$qry .= $and."IDOPTION = '".$array['IDOPTION']."'".RET;
			$and = "AND".RET;
		}

		if($array['NONMEMBER_PRICE'] != "") {
			$qry .= $and."NONMEMBER_PRICE = '".$array['NONMEMBER_PRICE']."'".RET;
			$and = "AND".RET;
		}

		if($array['NONSEQUENCE_PRICE'] != "") {
			$qry .= $and."NONSEQUENCE_PRICE = '".$array['NONSEQUENCE_PRICE']."'".RET;
			$and = "AND".RET;
		}

		if($array['STARTQT_1'] != "") {
			$qry .= $and."STARTQT_1 = '".$array['STARTQT_1']."'".RET;
			$and = "AND".RET;
		}

		if($array['ENDQT_1'] != "") {
			$qry .= $and."ENDQT_1 = '".$array['ENDQT_1']."'".RET;
			$and = "AND".RET;
		}

		if($array['PRICE1'] != "") {
			$qry .= $and."PRICE1 = '".$array['PRICE1']."'".RET;
			$and = "AND".RET;
		}

		if($array['NONSEQUENCE_PRICE1'] != "") {
			$qry .= $and."NONSEQUENCE_PRICE1 = '".$array['NONSEQUENCE_PRICE1']."'".RET;
			$and = "AND".RET;
		}

		if($array['STARTQT_2'] != "") {
			$qry .= $and."STARTQT_2 = '".$array['STARTQT_2']."'".RET;
			$and = "AND".RET;
		}

		if($array['ENDQT_2'] != "") {
			$qry .= $and."ENDQT_2 = '".$array['ENDQT_2']."'".RET;
			$and = "AND".RET;
		}

		if($array['PRICE2'] != "") {
			$qry .= $and."PRICE2 = '".$array['PRICE2']."'".RET;
			$and = "AND".RET;
		}

		if($array['NONSEQUENCE_PRICE2'] != "") {
			$qry .= $and."NONSEQUENCE_PRICE2 = '".$array['NONSEQUENCE_PRICE2']."'".RET;
			$and = "AND".RET;
		}

		if($array['STARTQT_3'] != "") {
			$qry .= $and."STARTQT_3 = '".$array['STARTQT_3']."'".RET;
			$and = "AND".RET;
		}

		if($array['ENDQT_3'] != "") {
			$qry .= $and."ENDQT_3 = '".$array['ENDQT_3']."'".RET;
			$and = "AND".RET;
		}

		if($array['PRICE3'] != "") {
			$qry .= $and."PRICE3 = '".$array['PRICE3']."'".RET;
			$and = "AND".RET;
		}

		if($array['NONSEQUENCE_PRICE3'] != "") {
			$qry .= $and."NONSEQUENCE_PRICE3 = '".$array['NONSEQUENCE_PRICE3']."'".RET;
			$and = "AND".RET;
		}

		if($array['STARTQT_4'] != "") {
			$qry .= $and."STARTQT_4 = '".$array['STARTQT_4']."'".RET;
			$and = "AND".RET;
		}

		if($array['ENDQT_4'] != "") {
			$qry .= $and."ENDQT_4 = '".$array['ENDQT_4']."'".RET;
			$and = "AND".RET;
		}

		if($array['PRICE4'] != "") {
			$qry .= $and."PRICE4 = '".$array['PRICE4']."'".RET;
			$and = "AND".RET;
		}

		if($array['NONSEQUENCCE_PRICE4'] != "") {
			$qry .= $and."NONSEQUENCCE_PRICE4 = '".$array['NONSEQUENCCE_PRICE4']."'".RET;
			$and = "AND".RET;
		}

		$recordset = Database::select($qry);
		$class_objects = array();
		if(is_array($recordset) == true) {
			while(list($i, $record) = each($recordset)) {
				$class_object = new pricing();
				$class_object->setIDPRICING($record['IDPRICING']);
				$class_object->setIDOPTION($record['IDOPTION']);
				$class_object->setNONMEMBER_PRICE($record['NONMEMBER_PRICE']);
				$class_object->setNONSEQUENCE_PRICE($record['NONSEQUENCE_PRICE']);
				$class_object->setSTARTQT_1($record['STARTQT_1']);
				$class_object->setENDQT_1($record['ENDQT_1']);
				$class_object->setPRICE1($record['PRICE1']);
				$class_object->setNONSEQUENCE_PRICE1($record['NONSEQUENCE_PRICE1']);
				$class_object->setSTARTQT_2($record['STARTQT_2']);
				$class_object->setENDQT_2($record['ENDQT_2']);
				$class_object->setPRICE2($record['PRICE2']);
				$class_object->setNONSEQUENCE_PRICE2($record['NONSEQUENCE_PRICE2']);
				$class_object->setSTARTQT_3($record['STARTQT_3']);
				$class_object->setENDQT_3($record['ENDQT_3']);
				$class_object->setPRICE3($record['PRICE3']);
				$class_object->setNONSEQUENCE_PRICE3($record['NONSEQUENCE_PRICE3']);
				$class_object->setSTARTQT_4($record['STARTQT_4']);
				$class_object->setENDQT_4($record['ENDQT_4']);
				$class_object->setPRICE4($record['PRICE4']);
				$class_object->setNONSEQUENCCE_PRICE4($record['NONSEQUENCCE_PRICE4']);
				$class_object->setSetup_fee($record['setup_fee']);
				$class_objects[$class_object->getIDPRICING()] = $class_object;
			}
		}
		return $class_objects;
	}

	public function insert() {
		if($this->getIDPRICING() != '') {
			$qry  = "UPDATE ".PRICING.RET."SET".RET.
			"IDPRICING = '".$this->getIDPRICING()."',".RET.
			"IDOPTION = '".$this->getIDOPTION()."',".RET.
			"NONMEMBER_PRICE = '".$this->getNONMEMBER_PRICE()."',".RET.
			"NONSEQUENCE_PRICE = '".$this->getNONSEQUENCE_PRICE()."',".RET.
			"STARTQT_1 = '".$this->getSTARTQT_1()."',".RET.
			"ENDQT_1 = '".$this->getENDQT_1()."',".RET.
			"PRICE1 = '".$this->getPRICE1()."',".RET.
			"NONSEQUENCE_PRICE1 = '".$this->getNONSEQUENCE_PRICE1()."',".RET.
			"STARTQT_2 = '".$this->getSTARTQT_2()."',".RET.
			"ENDQT_2 = '".$this->getENDQT_2()."',".RET.
			"PRICE2 = '".$this->getPRICE2()."',".RET.
			"NONSEQUENCE_PRICE2 = '".$this->getNONSEQUENCE_PRICE2()."',".RET.
			"STARTQT_3 = '".$this->getSTARTQT_3()."',".RET.
			"ENDQT_3 = '".$this->getENDQT_3()."',".RET.
			"PRICE3 = '".$this->getPRICE3()."',".RET.
			"NONSEQUENCE_PRICE3 = '".$this->getNONSEQUENCE_PRICE3()."',".RET.
			"STARTQT_4 = '".$this->getSTARTQT_4()."',".RET.
			"ENDQT_4 = '".$this->getENDQT_4()."',".RET.
			"PRICE4 = '".$this->getPRICE4()."',".RET.
			"NONSEQUENCCE_PRICE4 = '".$this->getNONSEQUENCCE_PRICE4()."',".RET.
			"setup_fee  = '".$this->getSetup_fee()."'".RET.
			"WHERE IDPRICING = ".$this->getIDPRICING().RET;
	
			Database::insert($qry);
		} else {
			$qry  = "INSERT INTO ".PRICING." (".RET.
			"IDOPTION, NONMEMBER_PRICE, NONSEQUENCE_PRICE, STARTQT_1, ENDQT_1, PRICE1, NONSEQUENCE_PRICE1, STARTQT_2, ENDQT_2, PRICE2, NONSEQUENCE_PRICE2, STARTQT_3, ENDQT_3, PRICE3, NONSEQUENCE_PRICE3, STARTQT_4, ENDQT_4, PRICE4, NONSEQUENCCE_PRICE4, setup_fee ".RET.
				") VALUES (".RET.
			"'".$this->getIDOPTION()."',".RET.
			"'".$this->getNONMEMBER_PRICE()."',".RET.
			"'".$this->getNONSEQUENCE_PRICE()."',".RET.
			"'".$this->getSTARTQT_1()."',".RET.
			"'".$this->getENDQT_1()."',".RET.
			"'".$this->getPRICE1()."',".RET.
			"'".$this->getNONSEQUENCE_PRICE1()."',".RET.
			"'".$this->getSTARTQT_2()."',".RET.
			"'".$this->getENDQT_2()."',".RET.
			"'".$this->getPRICE2()."',".RET.
			"'".$this->getNONSEQUENCE_PRICE2()."',".RET.
			"'".$this->getSTARTQT_3()."',".RET.
			"'".$this->getENDQT_3()."',".RET.
			"'".$this->getPRICE3()."',".RET.
			"'".$this->getNONSEQUENCE_PRICE3()."',".RET.
			"'".$this->getSTARTQT_4()."',".RET.
			"'".$this->getENDQT_4()."',".RET.
			"'".$this->getPRICE4()."',".RET.
			"'".$this->getNONSEQUENCCE_PRICE4()."',".RET.
			"'".$this->getSetup_fee()."'".RET.
			")".RET;

			$this->setIDPRICING(Database::insert($qry));
		}
	}

	public static function delete($array = array()) {
		$qry = "DELETE".RET."FROM ".PRICING.RET;
		$and = "WHERE".RET;

		if($array['IDPRICING'] != "") {
			$qry .= $and."IDPRICING = '".$array['IDPRICING']."'".RET;
			$and = "AND".RET;
		}

		if($array['IDOPTION'] != "") {
			$qry .= $and."IDOPTION = '".$array['IDOPTION']."'".RET;
			$and = "AND".RET;
		}

		if($array['NONMEMBER_PRICE'] != "") {
			$qry .= $and."NONMEMBER_PRICE = '".$array['NONMEMBER_PRICE']."'".RET;
			$and = "AND".RET;
		}

		if($array['NONSEQUENCE_PRICE'] != "") {
			$qry .= $and."NONSEQUENCE_PRICE = '".$array['NONSEQUENCE_PRICE']."'".RET;
			$and = "AND".RET;
		}

		if($array['STARTQT_1'] != "") {
			$qry .= $and."STARTQT_1 = '".$array['STARTQT_1']."'".RET;
			$and = "AND".RET;
		}

		if($array['ENDQT_1'] != "") {
			$qry .= $and."ENDQT_1 = '".$array['ENDQT_1']."'".RET;
			$and = "AND".RET;
		}

		if($array['PRICE1'] != "") {
			$qry .= $and."PRICE1 = '".$array['PRICE1']."'".RET;
			$and = "AND".RET;
		}

		if($array['NONSEQUENCE_PRICE1'] != "") {
			$qry .= $and."NONSEQUENCE_PRICE1 = '".$array['NONSEQUENCE_PRICE1']."'".RET;
			$and = "AND".RET;
		}

		if($array['STARTQT_2'] != "") {
			$qry .= $and."STARTQT_2 = '".$array['STARTQT_2']."'".RET;
			$and = "AND".RET;
		}

		if($array['ENDQT_2'] != "") {
			$qry .= $and."ENDQT_2 = '".$array['ENDQT_2']."'".RET;
			$and = "AND".RET;
		}

		if($array['PRICE2'] != "") {
			$qry .= $and."PRICE2 = '".$array['PRICE2']."'".RET;
			$and = "AND".RET;
		}

		if($array['NONSEQUENCE_PRICE2'] != "") {
			$qry .= $and."NONSEQUENCE_PRICE2 = '".$array['NONSEQUENCE_PRICE2']."'".RET;
			$and = "AND".RET;
		}

		if($array['STARTQT_3'] != "") {
			$qry .= $and."STARTQT_3 = '".$array['STARTQT_3']."'".RET;
			$and = "AND".RET;
		}

		if($array['ENDQT_3'] != "") {
			$qry .= $and."ENDQT_3 = '".$array['ENDQT_3']."'".RET;
			$and = "AND".RET;
		}

		if($array['PRICE3'] != "") {
			$qry .= $and."PRICE3 = '".$array['PRICE3']."'".RET;
			$and = "AND".RET;
		}

		if($array['NONSEQUENCE_PRICE3'] != "") {
			$qry .= $and."NONSEQUENCE_PRICE3 = '".$array['NONSEQUENCE_PRICE3']."'".RET;
			$and = "AND".RET;
		}

		if($array['STARTQT_4'] != "") {
			$qry .= $and."STARTQT_4 = '".$array['STARTQT_4']."'".RET;
			$and = "AND".RET;
		}

		if($array['ENDQT_4'] != "") {
			$qry .= $and."ENDQT_4 = '".$array['ENDQT_4']."'".RET;
			$and = "AND".RET;
		}

		if($array['PRICE4'] != "") {
			$qry .= $and."PRICE4 = '".$array['PRICE4']."'".RET;
			$and = "AND".RET;
		}

		if($array['NONSEQUENCCE_PRICE4'] != "") {
			$qry .= $and."NONSEQUENCCE_PRICE4 = '".$array['NONSEQUENCCE_PRICE4']."'".RET;
			$and = "AND".RET;
		}

		Database::delete($qry);
	}
	
	public function updateSetupFee() {
		if($this->getIDOPTION() != '') {
			$qry  = "UPDATE ".PRICING.RET."SET".RET. 
			"setup_fee  = '".$this->getSetup_fee()."'".RET.
			"WHERE IDOPTION = ".$this->getIDOPTION().RET;
			Database::insert($qry);
		}  
	}
}

?>