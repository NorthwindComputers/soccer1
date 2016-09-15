<?php

define("IMPOPTION_SETTINGS", "impoption_settings"); 

class impoption_settings {

	private $ID_SETTINGS;
	private $IDOPTION;
	private $DISPLAY_NAME;
	private $COLORS_NBR;
	private $FRONTEND_PREVIEW;
	private $NBR_IMAGES;

	public function setID_SETTINGS($pArg="0") {$this->ID_SETTINGS=$pArg;}
	public function setIDOPTION($pArg="0") {$this->IDOPTION=$pArg;}
	public function setDISPLAY_NAME($pArg="0") {$this->DISPLAY_NAME=$pArg;}
	public function setCOLORS_NBR($pArg="0") {$this->COLORS_NBR=$pArg;}
	public function setFRONTEND_PREVIEW($pArg="0") {$this->FRONTEND_PREVIEW=$pArg;}
	public function setNBR_IMAGES($pArg="0") {$this->NBR_IMAGES=$pArg;}

	public function getID_SETTINGS() {return $this->ID_SETTINGS;}
	public function getIDOPTION() {return $this->IDOPTION;}
	public function getDISPLAY_NAME() {return $this->DISPLAY_NAME;}
	public function getCOLORS_NBR() {return $this->COLORS_NBR;}
	public function getFRONTEND_PREVIEW() {return $this->FRONTEND_PREVIEW;}
	public function getNBR_IMAGES() {return $this->NBR_IMAGES;}

	public function readObject($array = array()) {
		$qry = "SELECT *".RET."FROM ".IMPOPTION_SETTINGS.RET;
		$and = "WHERE".RET;

		if($array['ID_SETTINGS'] != "") {
			$qry .= $and."ID_SETTINGS = '".$array['ID_SETTINGS']."'".RET;
			$and = "AND".RET;
		}

		if($array['IDOPTION'] != "") {
			$qry .= $and."IDOPTION = '".$array['IDOPTION']."'".RET;
			$and = "AND".RET;
		}

		if($array['DISPLAY_NAME'] != "") {
			$qry .= $and."DISPLAY_NAME = '".$array['DISPLAY_NAME']."'".RET;
			$and = "AND".RET;
		}

		if($array['COLORS_NBR'] != "") {
			$qry .= $and."COLORS_NBR = '".$array['COLORS_NBR']."'".RET;
			$and = "AND".RET;
		}

		if($array['FRONTEND_PREVIEW'] != "") {
			$qry .= $and."FRONTEND_PREVIEW = '".$array['FRONTEND_PREVIEW']."'".RET;
			$and = "AND".RET;
		}

		if($array['NBR_IMAGES'] != "") {
			$qry .= $and."NBR_IMAGES = '".$array['NBR_IMAGES']."'".RET;
			$and = "AND".RET;
		}
		$record = Database::select($qry);
		if(count($record[0]) == 0) {
			return array();
		} else {
			$record = $record[0];
			$this->setID_SETTINGS($record['ID_SETTINGS']);
			$this->setIDOPTION($record['IDOPTION']);
			$this->setDISPLAY_NAME($record['DISPLAY_NAME']);
			$this->setCOLORS_NBR($record['COLORS_NBR']);
			$this->setFRONTEND_PREVIEW($record['FRONTEND_PREVIEW']);
			$this->setNBR_IMAGES($record['NBR_IMAGES']);
			return true;
		}
	}

	public static function readArray($array = array()) {
		$qry = "SELECT *".RET."FROM ".IMPOPTION_SETTINGS.RET;
		$and = "WHERE".RET;

		if($array['ID_SETTINGS'] != "") {
			$qry .= $and."ID_SETTINGS = '".$array['ID_SETTINGS']."'".RET;
			$and = "AND".RET;
		}

		if($array['IDOPTION'] != "") {
			$qry .= $and."IDOPTION = '".$array['IDOPTION']."'".RET;
			$and = "AND".RET;
		}

		if($array['DISPLAY_NAME'] != "") {
			$qry .= $and."DISPLAY_NAME = '".$array['DISPLAY_NAME']."'".RET;
			$and = "AND".RET;
		}

		if($array['COLORS_NBR'] != "") {
			$qry .= $and."COLORS_NBR = '".$array['COLORS_NBR']."'".RET;
			$and = "AND".RET;
		}

		if($array['FRONTEND_PREVIEW'] != "") {
			$qry .= $and."FRONTEND_PREVIEW = '".$array['FRONTEND_PREVIEW']."'".RET;
			$and = "AND".RET;
		}

		if($array['NBR_IMAGES'] != "") {
			$qry .= $and."NBR_IMAGES = '".$array['NBR_IMAGES']."'".RET;
			$and = "AND".RET;
		}

		$recordset = Database::select($qry);
		$class_objects = array();
		if(is_array($recordset) == true) {
			while(list($i, $record) = each($recordset)) {
				$class_object = new impoption_settings();
				$class_object->setID_SETTINGS($record['ID_SETTINGS']);
				$class_object->setIDOPTION($record['IDOPTION']);
				$class_object->setDISPLAY_NAME($record['DISPLAY_NAME']);
				$class_object->setCOLORS_NBR($record['COLORS_NBR']);
				$class_object->setFRONTEND_PREVIEW($record['FRONTEND_PREVIEW']);
				$class_object->setNBR_IMAGES($record['NBR_IMAGES']);
				$class_objects[$class_object->getID_SETTINGS()] = $class_object;
			}
		}
		return $class_objects;
	}

	public function insert() {
		if($this->getID_SETTINGS() != '') {
			$qry  = "UPDATE ".IMPOPTION_SETTINGS.RET."SET".RET.
			"ID_SETTINGS = '".$this->getID_SETTINGS()."',".RET.
			"IDOPTION = '".$this->getIDOPTION()."',".RET.
			"DISPLAY_NAME = '".$this->getDISPLAY_NAME()."',".RET.
			"COLORS_NBR = '".$this->getCOLORS_NBR()."',".RET.
			"FRONTEND_PREVIEW = '".$this->getFRONTEND_PREVIEW()."',".RET.
			"NBR_IMAGES = '".$this->getNBR_IMAGES()."'".RET.
			"WHERE ID_SETTINGS = ".$this->getID_SETTINGS().RET;

			Database::insert($qry);
		} else {
			$qry  = "INSERT INTO ".IMPOPTION_SETTINGS." (".RET.
			"IDOPTION, DISPLAY_NAME, COLORS_NBR, FRONTEND_PREVIEW, NBR_IMAGES".RET.
				") VALUES (".RET.
			"'".$this->getIDOPTION()."',".RET.
			"'".$this->getDISPLAY_NAME()."',".RET.
			"'".$this->getCOLORS_NBR()."',".RET.
			"'".$this->getFRONTEND_PREVIEW()."',".RET.
			"'".$this->getNBR_IMAGES()."'".RET.
			")".RET;

			$this->setID_SETTINGS(Database::insert($qry));
		}
	}

	public static function delete($array = array()) {
		$qry = "DELETE".RET."FROM ".IMPOPTION_SETTINGS.RET;
		$and = "WHERE".RET;

		if($array['ID_SETTINGS'] != "") {
			$qry .= $and."ID_SETTINGS = '".$array['ID_SETTINGS']."'".RET;
			$and = "AND".RET;
		}

		if($array['IDOPTION'] != "") {
			$qry .= $and."IDOPTION = '".$array['IDOPTION']."'".RET;
			$and = "AND".RET;
		}

		if($array['DISPLAY_NAME'] != "") {
			$qry .= $and."DISPLAY_NAME = '".$array['DISPLAY_NAME']."'".RET;
			$and = "AND".RET;
		}

		if($array['COLORS_NBR'] != "") {
			$qry .= $and."COLORS_NBR = '".$array['COLORS_NBR']."'".RET;
			$and = "AND".RET;
		}

		if($array['FRONTEND_PREVIEW'] != "") {
			$qry .= $and."FRONTEND_PREVIEW = '".$array['FRONTEND_PREVIEW']."'".RET;
			$and = "AND".RET;
		}

		if($array['NBR_IMAGES'] != "") {
			$qry .= $and."NBR_IMAGES = '".$array['NBR_IMAGES']."'".RET;
			$and = "AND".RET;
		}

		Database::delete($qry);
	}
}

?>