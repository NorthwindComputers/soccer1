<?php

define("OPTIONTYPE", "optiontype"); 

class optiontype {

	private $IDTYPE;
	private $OPTIONTYPE;
	private $imptype;

	public function setIDTYPE($pArg="0") {$this->IDTYPE=$pArg;}
	public function setOPTIONTYPE($pArg="0") {$this->OPTIONTYPE=$pArg;}
	public function setimptype($pArg="0") {$this->imptype=$pArg;}

	public function getIDTYPE() {return $this->IDTYPE;}
	public function getOPTIONTYPE() {return $this->OPTIONTYPE;}
	public function getimptype() {return $this->imptype;}

	public function readObject($array = array()) {
		$qry = "SELECT *".RET."FROM ".OPTIONTYPE.RET;
		$and = "WHERE".RET;

		if($array['IDTYPE'] != "") {
			$qry .= $and."IDTYPE = '".$array['IDTYPE']."'".RET;
			$and = "AND".RET;
		}

		if($array['OPTIONTYPE'] != "") {
			$qry .= $and."OPTIONTYPE = '".$array['OPTIONTYPE']."'".RET;
			$and = "AND".RET;
		}

		if($array['imptype'] != "") {
			$qry .= $and."imptype = '".$array['imptype']."'".RET;
			$and = "AND".RET;
		}

		$record = Database::select($qry);
		if(count($record[0]) == 0) {
			return array();
		} else {
			$record = $record[0];
			$this->setIDTYPE($record['IDTYPE']);
			$this->setOPTIONTYPE($record['OPTIONTYPE']);
			$this->setimptype($record['imptype']);
			return true;
		}
	}

	public static function readArray($array = array()) {
		$qry = "SELECT *".RET."FROM ".OPTIONTYPE.RET;
		$and = "WHERE".RET;

		if($array['IDTYPE'] != "") {
			$qry .= $and."IDTYPE = '".$array['IDTYPE']."'".RET;
			$and = "AND".RET;
		}

		if($array['OPTIONTYPE'] != "") {
			$qry .= $and."OPTIONTYPE = '".$array['OPTIONTYPE']."'".RET;
			$and = "AND".RET;
		}

		if($array['imptype'] != "") {
			$qry .= $and."imptype = '".$array['imptype']."'".RET;
			$and = "AND".RET;
		}

		$recordset = Database::select($qry);
		$class_objects = array();
		if(is_array($recordset) == true) {
			while(list($i, $record) = each($recordset)) {
				$class_object = new optiontype();
				$class_object->setIDTYPE($record['IDTYPE']);
				$class_object->setOPTIONTYPE($record['OPTIONTYPE']);
				$class_object->setimptype($record['imptype']);
				$class_objects[$class_object->getIDTYPE()] = $class_object;
			}
		}
		return $class_objects;
	}

	public function insert() {
		if($this->getIDTYPE() != '') {
			$qry  = "UPDATE ".OPTIONTYPE.RET."SET".RET.
			"IDTYPE = '".$this->getIDTYPE()."',".RET.
			"OPTIONTYPE = '".$this->getOPTIONTYPE()."',".RET.
			"imptype = '".$this->getimptype()."'".RET.
			"WHERE IDTYPE = ".$this->getIDTYPE().RET;

			Database::insert($qry);
		} else {
			$qry  = "INSERT INTO ".OPTIONTYPE." (".RET.
			"OPTIONTYPE, imptype".RET.
				") VALUES (".RET.
			"'".$this->getOPTIONTYPE()."',".RET.
			"'".$this->getimptype()."'".RET.
			")".RET;

			$this->setIDTYPE(Database::insert($qry));
		}
	}

	public static function delete($array = array()) {
		$qry = "DELETE".RET."FROM ".OPTIONTYPE.RET;
		$and = "WHERE".RET;

		if($array['IDTYPE'] != "") {
			$qry .= $and."IDTYPE = '".$array['IDTYPE']."'".RET;
			$and = "AND".RET;
		}

		if($array['OPTIONTYPE'] != "") {
			$qry .= $and."OPTIONTYPE = '".$array['OPTIONTYPE']."'".RET;
			$and = "AND".RET;
		}

		if($array['imptype'] != "") {
			$qry .= $and."imptype = '".$array['imptype']."'".RET;
			$and = "AND".RET;
		}

		Database::delete($qry);
	}
}

?>