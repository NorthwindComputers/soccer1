<?php

define("CIMPRINT_CATEGORY", "cimprint_category"); 

class cimprint_category {

	private $IDCATEGORY;
	private $CATEGORY;
	private $ADMIN_NOTES;
	private $enabled;

	public function setIDCATEGORY($pArg="0") {$this->IDCATEGORY=$pArg;}
	public function setCATEGORY($pArg="0") {$this->CATEGORY=$pArg;}
	public function setADMIN_NOTES($pArg="0") {$this->ADMIN_NOTES=$pArg;}
	public function setenabled($pArg="0") {$this->enabled=$pArg;}

	public function getIDCATEGORY() {return $this->IDCATEGORY;}
	public function getCATEGORY() {return $this->CATEGORY;}
	public function getADMIN_NOTES() {return $this->ADMIN_NOTES;}
	public function getenabled() {return $this->enabled;}

	public function readObject($array = array()) {
		$qry = "SELECT *".RET."FROM ".CIMPRINT_CATEGORY.RET;
		$and = "WHERE".RET;

		if($array['IDCATEGORY'] != "") {
			$qry .= $and."IDCATEGORY = '".$array['IDCATEGORY']."'".RET;
			$and = "AND".RET;
		}

		if($array['CATEGORY'] != "") {
			$qry .= $and."CATEGORY = '".$array['CATEGORY']."'".RET;
			$and = "AND".RET;
		}

		if($array['ADMIN_NOTES'] != "") {
			$qry .= $and."ADMIN_NOTES = '".$array['ADMIN_NOTES']."'".RET;
			$and = "AND".RET;
		}

		if($array['enabled'] != "") {
			$qry .= $and."enabled = '".$array['enabled']."'".RET;
			$and = "AND".RET;
		}

		$record = Database::select($qry);
		if(count($record[0]) == 0) {
			return array();
		} else {
			$record = $record[0];
			$this->setIDCATEGORY($record['IDCATEGORY']);
			$this->setCATEGORY($record['CATEGORY']);
			$this->setADMIN_NOTES($record['ADMIN_NOTES']);
			$this->setenabled($record['enabled']);
			return true;
		}
	}

	public static function readArray($array = array()) {
		$qry = "SELECT *".RET."FROM ".CIMPRINT_CATEGORY.RET;
		$and = "WHERE".RET;

		if($array['IDCATEGORY'] != "") {
			$qry .= $and."IDCATEGORY = '".$array['IDCATEGORY']."'".RET;
			$and = "AND".RET;
		}

		if($array['CATEGORY'] != "") {
			$qry .= $and."CATEGORY = '".$array['CATEGORY']."'".RET;
			$and = "AND".RET;
		}

		if($array['ADMIN_NOTES'] != "") {
			$qry .= $and."ADMIN_NOTES = '".$array['ADMIN_NOTES']."'".RET;
			$and = "AND".RET;
		}

		if($array['enabled'] != "") {
			$qry .= $and."enabled = '".$array['enabled']."'".RET;
			$and = "AND".RET;
		}

		$recordset = Database::select($qry);
		$class_objects = array();
		if(is_array($recordset) == true) {
			while(list($i, $record) = each($recordset)) {
				$class_object = new cimprint_category();
				$class_object->setIDCATEGORY($record['IDCATEGORY']);
				$class_object->setCATEGORY($record['CATEGORY']);
				$class_object->setADMIN_NOTES($record['ADMIN_NOTES']);
				$class_object->setenabled($record['enabled']);
				$class_objects[$class_object->getIDCATEGORY()] = $class_object;
			}
		}
		return $class_objects;
	}

	public function insert() {
		if($this->getIDCATEGORY() != '') {
			$qry  = "UPDATE ".CIMPRINT_CATEGORY.RET."SET".RET.
			"IDCATEGORY = '".$this->getIDCATEGORY()."',".RET.
			"CATEGORY = '".$this->getCATEGORY()."',".RET.
			"ADMIN_NOTES = '".$this->getADMIN_NOTES()."',".RET.
			"enabled = '".$this->getenabled()."'".RET.
			"WHERE IDCATEGORY = ".$this->getIDCATEGORY().RET;

			Database::insert($qry);
		} else {
			$qry  = "INSERT INTO ".CIMPRINT_CATEGORY." (".RET.
			"CATEGORY, ADMIN_NOTES, enabled".RET.
				") VALUES (".RET.
			"'".$this->getCATEGORY()."',".RET.
			"'".$this->getADMIN_NOTES()."',".RET.
			"'".$this->getenabled()."'".RET.
			")".RET;

			$this->setIDCATEGORY(Database::insert($qry));
		}
	}

	public static function delete($array = array()) {
		$qry = "DELETE".RET."FROM ".CIMPRINT_CATEGORY.RET;
		$and = "WHERE".RET;

		if($array['IDCATEGORY'] != "") {
			$qry .= $and."IDCATEGORY = '".$array['IDCATEGORY']."'".RET;
			$and = "AND".RET;
		}

		if($array['CATEGORY'] != "") {
			$qry .= $and."CATEGORY = '".$array['CATEGORY']."'".RET;
			$and = "AND".RET;
		}

		if($array['ADMIN_NOTES'] != "") {
			$qry .= $and."ADMIN_NOTES = '".$array['ADMIN_NOTES']."'".RET;
			$and = "AND".RET;
		}

		if($array['enabled'] != "") {
			$qry .= $and."enabled = '".$array['enabled']."'".RET;
			$and = "AND".RET;
		}

		Database::delete($qry);
	}
}

?>