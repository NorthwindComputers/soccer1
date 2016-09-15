<?php


define("IMPCATEGORY_OPTION", "impcategory_option"); 

class impcategory_option {

	private $IDOPTION;
	private $IDCATEGORY;
	private $IDTYPE;
	private $OPTION_NAME;
	private $ADMIN_NOTES;
	private $NONSEQUENCE;
	private $NONSQUENCE_TEXT;
	private $RANK;
	private $logoType;
	private $CustomerGroup;

	public function setIDOPTION($pArg="0") {$this->IDOPTION=$pArg;}
	public function setIDCATEGORY($pArg="0") {$this->IDCATEGORY=$pArg;}
	public function setIDTYPE($pArg="0") {$this->IDTYPE=$pArg;}
	public function setOPTION_NAME($pArg="0") {$this->OPTION_NAME=$pArg;}
	public function setADMIN_NOTES($pArg="0") {$this->ADMIN_NOTES=$pArg;}
	public function setNONSEQUENCE($pArg="0") {$this->NONSEQUENCE=$pArg;}
	public function setNONSQUENCE_TEXT($pArg="0") {$this->NONSQUENCE_TEXT=$pArg;}
	public function setRANK($pArg="0") {$this->RANK=$pArg;}
	public function setlogoType($pArg="0") {$this->logoType=$pArg;}
	public function setCustomerGroup($pArg="0") {$this->CustomerGroup=$pArg;}

	public function getIDOPTION() {return $this->IDOPTION;}
	public function getIDCATEGORY() {return $this->IDCATEGORY;}
	public function getIDTYPE() {return $this->IDTYPE;}
	public function getOPTION_NAME() {return $this->OPTION_NAME;}
	public function getADMIN_NOTES() {return $this->ADMIN_NOTES;}
	public function getNONSEQUENCE() {return $this->NONSEQUENCE;}
	public function getNONSQUENCE_TEXT() {return $this->NONSQUENCE_TEXT;}
	public function getRANK() {return $this->RANK;}
	public function getlogoType() {return $this->logoType;}
	public function getCustomerGroup() {return $this->CustomerGroup;}

	public function readObject($array = array()) {
		$qry = "SELECT *".RET."FROM ".IMPCATEGORY_OPTION.RET;
		$and = "WHERE".RET;

		if($array['IDOPTION'] != "") {
			$qry .= $and."IDOPTION = '".$array['IDOPTION']."'".RET;
			$and = "AND".RET;
		}

		if($array['IDCATEGORY'] != "") {
			$qry .= $and."IDCATEGORY = '".$array['IDCATEGORY']."'".RET;
			$and = "AND".RET;
		}

		if($array['IDTYPE'] != "") {
			$qry .= $and."IDTYPE = '".$array['IDTYPE']."'".RET;
			$and = "AND".RET;
		}

		if($array['OPTION_NAME'] != "") {
			$qry .= $and."OPTION_NAME = '".$array['OPTION_NAME']."'".RET;
			$and = "AND".RET;
		}

		if($array['ADMIN_NOTES'] != "") {
			$qry .= $and."ADMIN_NOTES = '".$array['ADMIN_NOTES']."'".RET;
			$and = "AND".RET;
		}

		if($array['NONSEQUENCE'] != "") {
			$qry .= $and."NONSEQUENCE = '".$array['NONSEQUENCE']."'".RET;
			$and = "AND".RET;
		}

		if($array['NONSQUENCE_TEXT'] != "") {
			$qry .= $and."NONSQUENCE_TEXT = '".$array['NONSQUENCE_TEXT']."'".RET;
			$and = "AND".RET;
		}

		if($array['RANK'] != "") {
			$qry .= $and."RANK = '".$array['RANK']."'".RET;
			$and = "AND".RET;
		}

		if($array['logoType'] != "") {
			$qry .= $and."logoType = '".$array['logoType']."'".RET;
			$and = "AND".RET;
		}

		if($array['CustomerGroup'] != "") {
			$qry .= $and."CustomerGroup = '".$array['CustomerGroup']."'".RET;
			$and = "AND".RET;
		}

		$record = Database::select($qry);
		
		//print_r($record);
		
		if(count($record[0]) == 0) {
			return array();
		} else {
			$record = $record[0];
			$this->setIDOPTION($record['IDOPTION']);
			$this->setIDCATEGORY($record['IDCATEGORY']);
			$this->setIDTYPE($record['IDTYPE']);
			$this->setOPTION_NAME($record['OPTION_NAME']);
			$this->setADMIN_NOTES($record['ADMIN_NOTES']);
			$this->setNONSEQUENCE($record['NONSEQUENCE']);
			$this->setNONSQUENCE_TEXT($record['NONSQUENCE_TEXT']);
			$this->setRANK($record['RANK']);
			$this->setlogoType($record['logoType']);
			$this->setCustomerGroup($record['CustomerGroup']);
			return true;
		}
	}

	public static function readArray($array = array()) {
		$qry = "SELECT *".RET."FROM ".IMPCATEGORY_OPTION.RET;
		$and = "WHERE".RET;

		if($array['IDOPTION'] != "") {
			$qry .= $and."IDOPTION = '".$array['IDOPTION']."'".RET;
			$and = "AND".RET;
		}

		if($array['IDCATEGORY'] != "") {
			$qry .= $and."IDCATEGORY = '".$array['IDCATEGORY']."'".RET;
			$and = "AND".RET;
		}

		if($array['IDTYPE'] != "") {
			$qry .= $and."IDTYPE = '".$array['IDTYPE']."'".RET;
			$and = "AND".RET;
		}

		if($array['OPTION_NAME'] != "") {
			$qry .= $and."OPTION_NAME = '".$array['OPTION_NAME']."'".RET;
			$and = "AND".RET;
		}

		if($array['ADMIN_NOTES'] != "") {
			$qry .= $and."ADMIN_NOTES = '".$array['ADMIN_NOTES']."'".RET;
			$and = "AND".RET;
		}

		if($array['NONSEQUENCE'] != "") {
			$qry .= $and."NONSEQUENCE = '".$array['NONSEQUENCE']."'".RET;
			$and = "AND".RET;
		}

		if($array['NONSQUENCE_TEXT'] != "") {
			$qry .= $and."NONSQUENCE_TEXT = '".$array['NONSQUENCE_TEXT']."'".RET;
			$and = "AND".RET;
		}

		if($array['RANK'] != "") {
			$qry .= $and."RANK = '".$array['RANK']."'".RET;
			$and = "AND".RET;
		}

		if($array['logoType'] != "") {
			$qry .= $and."logoType = '".$array['logoType']."'".RET;
			$and = "AND".RET;
		}

		if($array['CustomerGroup'] != "") {
			$qry .= $and."CustomerGroup = '".$array['CustomerGroup']."'".RET;
			$and = "AND".RET;
		}

		$recordset = Database::select($qry);
		
		//print_r($recordset);
		
		$class_objects = array();
		if(is_array($recordset) == true) {
			while(list($i, $record) = each($recordset)) {
				$class_object = new impcategory_option();
				$class_object->setIDOPTION($record['IDOPTION']);
				$class_object->setIDCATEGORY($record['IDCATEGORY']);
				$class_object->setIDTYPE($record['IDTYPE']);
				$class_object->setOPTION_NAME($record['OPTION_NAME']);
				$class_object->setADMIN_NOTES($record['ADMIN_NOTES']);
				$class_object->setNONSEQUENCE($record['NONSEQUENCE']);
				$class_object->setNONSQUENCE_TEXT($record['NONSQUENCE_TEXT']);
				$class_object->setRANK($record['RANK']);
				$class_object->setlogoType($record['logoType']);
				$class_object->setCustomerGroup($record['CustomerGroup']);
				$class_objects[$class_object->getIDOPTION()] = $class_object;
			}
		}
		return $class_objects;
	}

	public function insert() {
		if($this->getIDOPTION() != '') {
			$qry  = "UPDATE ".IMPCATEGORY_OPTION.RET."SET".RET.
			"IDOPTION = '".$this->getIDOPTION()."',".RET.
			"IDCATEGORY = '".$this->getIDCATEGORY()."',".RET.
			"IDTYPE = '".$this->getIDTYPE()."',".RET.
			"OPTION_NAME = '".$this->getOPTION_NAME()."',".RET.
			"ADMIN_NOTES = '".$this->getADMIN_NOTES()."',".RET.
			"NONSEQUENCE = '".$this->getNONSEQUENCE()."',".RET.
			"NONSQUENCE_TEXT = '".$this->getNONSQUENCE_TEXT()."',".RET.
			"RANK = '".$this->getRANK()."',".RET.
			"logoType = '".$this->getlogoType()."',".RET.
			"CustomerGroup = '".$this->getCustomerGroup()."'".RET.
			"WHERE IDOPTION = ".$this->getIDOPTION().RET;

			Database::insert($qry);
		} else {
			$qry  = "INSERT INTO ".IMPCATEGORY_OPTION." (".RET.
			"IDCATEGORY, IDTYPE, OPTION_NAME, ADMIN_NOTES, NONSEQUENCE, NONSQUENCE_TEXT, RANK, logoType, CustomerGroup".RET.
				") VALUES (".RET.
			"'".$this->getIDCATEGORY()."',".RET.
			"'".$this->getIDTYPE()."',".RET.
			"'".$this->getOPTION_NAME()."',".RET.
			"'".$this->getADMIN_NOTES()."',".RET.
			"'".$this->getNONSEQUENCE()."',".RET.
			"'".$this->getNONSQUENCE_TEXT()."',".RET.
			"'".$this->getRANK()."',".RET.
			"'".$this->getlogoType()."',".RET.
			"'".$this->getCustomerGroup()."'".RET.
			")".RET;

			$this->setIDOPTION(Database::insert($qry));
		}
	}

	public static function delete($array = array()) {
		$qry = "DELETE".RET."FROM ".IMPCATEGORY_OPTION.RET;
		$and = "WHERE".RET;

		if($array['IDOPTION'] != "") {
			$qry .= $and."IDOPTION = '".$array['IDOPTION']."'".RET;
			$and = "AND".RET;
		}

		if($array['IDCATEGORY'] != "") {
			$qry .= $and."IDCATEGORY = '".$array['IDCATEGORY']."'".RET;
			$and = "AND".RET;
		}

		if($array['IDTYPE'] != "") {
			$qry .= $and."IDTYPE = '".$array['IDTYPE']."'".RET;
			$and = "AND".RET;
		}

		if($array['OPTION_NAME'] != "") {
			$qry .= $and."OPTION_NAME = '".$array['OPTION_NAME']."'".RET;
			$and = "AND".RET;
		}

		if($array['ADMIN_NOTES'] != "") {
			$qry .= $and."ADMIN_NOTES = '".$array['ADMIN_NOTES']."'".RET;
			$and = "AND".RET;
		}

		if($array['NONSEQUENCE'] != "") {
			$qry .= $and."NONSEQUENCE = '".$array['NONSEQUENCE']."'".RET;
			$and = "AND".RET;
		}

		if($array['NONSQUENCE_TEXT'] != "") {
			$qry .= $and."NONSQUENCE_TEXT = '".$array['NONSQUENCE_TEXT']."'".RET;
			$and = "AND".RET;
		}

		if($array['RANK'] != "") {
			$qry .= $and."RANK = '".$array['RANK']."'".RET;
			$and = "AND".RET;
		}

		if($array['logoType'] != "") {
			$qry .= $and."logoType = '".$array['logoType']."'".RET;
			$and = "AND".RET;
		}

		if($array['CustomerGroup'] != "") {
			$qry .= $and."CustomerGroup = '".$array['CustomerGroup']."'".RET;
			$and = "AND".RET;
		}

		Database::delete($qry);
	}
}

?>