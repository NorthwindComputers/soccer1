<?php

define("OPTIONS_COLOR", "options_color"); 

class options_color {

	private $idcolor;
	private $name;
	private $images;

	public function setidcolor($pArg="0") {$this->idcolor=$pArg;}
	public function setname($pArg="0") {$this->name=$pArg;}
	public function setimages($pArg="0") {$this->images=$pArg;}

	public function getidcolor() {return $this->idcolor;}
	public function getname() {return $this->name;}
	public function getimages() {return $this->images;}

	public function readObject($array = array()) {
		$qry = "SELECT *".RET."FROM ".OPTIONS_COLOR.RET;
		$and = "WHERE".RET;

		if($array['idcolor'] != "") {
			$qry .= $and."idcolor = '".$array['idcolor']."'".RET;
			$and = "AND".RET;
		}

		if($array['name'] != "") {
			$qry .= $and."name = '".$array['name']."'".RET;
			$and = "AND".RET;
		}

		if($array['images'] != "") {
			$qry .= $and."images = '".$array['images']."'".RET;
			$and = "AND".RET;
		}

		$record = Database::select($qry);
		if(count($record[0]) == 0) {
			return array();
		} else {
			$record = $record[0];
			$this->setidcolor($record['idcolor']);
			$this->setname($record['name']);
			$this->setimages($record['images']);
			return true;
		}
	}

	public static function readArray($array = array()) {
		$qry = "SELECT *".RET."FROM ".OPTIONS_COLOR.RET;
		$and = "WHERE".RET;

		if($array['idcolor'] != "") {
			$qry .= $and."idcolor = '".$array['idcolor']."'".RET;
			$and = "AND".RET;
		}

		if($array['name'] != "") {
			$qry .= $and."name = '".$array['name']."'".RET;
			$and = "AND".RET;
		}

		if($array['images'] != "") {
			$qry .= $and."images = '".$array['images']."'".RET;
			$and = "AND".RET;
		}

		$recordset = Database::select($qry);
		$class_objects = array();
		if(is_array($recordset) == true) {
			while(list($i, $record) = each($recordset)) {
				$class_object = new options_color();
				$class_object->setidcolor($record['idcolor']);
				$class_object->setname($record['name']);
				$class_object->setimages($record['images']);
				$class_objects[$class_object->getidcolor()] = $class_object;
			}
		}
		return $class_objects;
	}

	public function insert() {
		if($this->getidcolor() != '') {
			$qry  = "UPDATE ".OPTIONS_COLOR.RET."SET".RET.
			"idcolor = '".$this->getidcolor()."',".RET.
			"name = '".$this->getname()."',".RET.
			"images = '".$this->getimages()."'".RET.
			"WHERE idcolor = ".$this->getidcolor().RET;

			Database::insert($qry);
		} else {
			$qry  = "INSERT INTO ".OPTIONS_COLOR." (".RET.
			"name, images".RET.
				") VALUES (".RET.
			"'".$this->getname()."',".RET.
			"'".$this->getimages()."'".RET.
			")".RET;

			$this->setidcolor(Database::insert($qry));
		}
	}

	public static function delete($array = array()) {
		$qry = "DELETE".RET."FROM ".OPTIONS_COLOR.RET;
		$and = "WHERE".RET;

		if($array['idcolor'] != "") {
			$qry .= $and."idcolor = '".$array['idcolor']."'".RET;
			$and = "AND".RET;
		}

		if($array['name'] != "") {
			$qry .= $and."name = '".$array['name']."'".RET;
			$and = "AND".RET;
		}

		if($array['images'] != "") {
			$qry .= $and."images = '".$array['images']."'".RET;
			$and = "AND".RET;
		}

		Database::delete($qry);
	}
}

?>