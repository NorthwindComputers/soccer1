<?php

define("COLORS_IMAGES", "colors_images"); 

class colors_images {

	private $id;
	private $idcolor;
	private $idimages;
	private $idoption;
	private $styleName;

	public function setid($pArg="0") {$this->id=$pArg;}
	public function setidcolor($pArg="0") {$this->idcolor=$pArg;}
	public function setidimages($pArg="0") {$this->idimages=$pArg;}
	public function setidoption($pArg="0") {$this->idoption=$pArg;}
	public function setstyleName($pArg="0") {$this->styleName=$pArg;}

	public function getid() {return $this->id;}
	public function getidcolor() {return $this->idcolor;}
	public function getidimages() {return $this->idimages;}
	public function getidoption() {return $this->idoption;}
	public function getstyleName() {return $this->styleName;}

	public function readObject($array = array()) {
		$qry = "SELECT *".RET."FROM ".COLORS_IMAGES.RET;
		$and = "WHERE".RET;

		if($array['id'] != "") {
			$qry .= $and."id = '".$array['id']."'".RET;
			$and = "AND".RET;
		}

		if($array['idcolor'] != "") {
			$qry .= $and."idcolor = '".$array['idcolor']."'".RET;
			$and = "AND".RET;
		}

		if($array['idimages'] != "") {
			$qry .= $and."idimages = '".$array['idimages']."'".RET;
			$and = "AND".RET;
		}

		if($array['idoption'] != "") {
			$qry .= $and."idoption = '".$array['idoption']."'".RET;
			$and = "AND".RET;
		}

		if($array['styleName'] != "") {
			$qry .= $and."styleName = '".$array['styleName']."'".RET;
			$and = "AND".RET;
		}

		$record = Database::select($qry);
		if(count($record[0]) == 0) {
			return array();
		} else {
			$record = $record[0];
			$this->setid($record['id']);
			$this->setidcolor($record['idcolor']);
			$this->setidimages($record['idimages']);
			$this->setidoption($record['idoption']);
			$this->setstyleName($record['styleName']);
			return true;
		}
	}

	public static function readArray($array = array()) {
		$qry = "SELECT *".RET."FROM ".COLORS_IMAGES.RET;
		$and = "WHERE".RET;

		if($array['id'] != "") {
			$qry .= $and."id = '".$array['id']."'".RET;
			$and = "AND".RET;
		}

		if($array['idcolor'] != "") {
			$qry .= $and."idcolor = '".$array['idcolor']."'".RET;
			$and = "AND".RET;
		}

		if($array['idimages'] != "") {
			$qry .= $and."idimages = '".$array['idimages']."'".RET;
			$and = "AND".RET;
		}

		if($array['idoption'] != "") {
			$qry .= $and."idoption = '".$array['idoption']."'".RET;
			$and = "AND".RET;
		}

		if($array['styleName'] != "") {
			$qry .= $and."styleName = '".$array['styleName']."'".RET;
			$and = "AND".RET;
		}

		$recordset = Database::select($qry);
		$class_objects = array();
		if(is_array($recordset) == true) {
			while(list($i, $record) = each($recordset)) {
				$class_object = new colors_images();
				$class_object->setid($record['id']);
				$class_object->setidcolor($record['idcolor']);
				$class_object->setidimages($record['idimages']);
				$class_object->setidoption($record['idoption']);
				$class_object->setstyleName($record['styleName']);
				$class_objects[$class_object->getid()] = $class_object;
			}
		}
		return $class_objects;
	}

	public function insert() {
		if($this->getid() != '') {
			$qry  = "UPDATE ".COLORS_IMAGES.RET."SET".RET.
			"id = '".$this->getid()."',".RET.
			"idcolor = '".$this->getidcolor()."',".RET.
			"idimages = '".$this->getidimages()."',".RET.
			"idoption = '".$this->getidoption()."',".RET.
			"styleName = '".$this->getstyleName()."'".RET.
			"WHERE id = ".$this->getid().RET;

			Database::insert($qry);
		} else {
			$qry  = "INSERT INTO ".COLORS_IMAGES." (".RET.
			"idcolor, idimages, idoption, styleName".RET.
				") VALUES (".RET.
			"'".$this->getidcolor()."',".RET.
			"'".$this->getidimages()."',".RET.
			"'".$this->getidoption()."',".RET.
			"'".$this->getstyleName()."'".RET.
			")".RET;

			$this->setid(Database::insert($qry));
		}
	}

	public static function delete($array = array()) {
		$qry = "DELETE".RET."FROM ".COLORS_IMAGES.RET;
		$and = "WHERE".RET;

		if($array['id'] != "") {
			$qry .= $and."id = '".$array['id']."'".RET;
			$and = "AND".RET;
		}

		if($array['idcolor'] != "") {
			$qry .= $and."idcolor = '".$array['idcolor']."'".RET;
			$and = "AND".RET;
		}

		if($array['idimages'] != "") {
			$qry .= $and."idimages = '".$array['idimages']."'".RET;
			$and = "AND".RET;
		}

		if($array['idoption'] != "") {
			$qry .= $and."idoption = '".$array['idoption']."'".RET;
			$and = "AND".RET;
		}

		if($array['styleName'] != "") {
			$qry .= $and."styleName = '".$array['styleName']."'".RET;
			$and = "AND".RET;
		}

		Database::delete($qry);
	}
}

?>