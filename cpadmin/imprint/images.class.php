<?php

define("IMAGES", "images"); 

class images {

	private $IDIMAGE;
	private $IDOPTION;
	private $IMAGEURL;
	private $IMG_NUMBER;

	public function setIDIMAGE($pArg="0") {$this->IDIMAGE=$pArg;}
	public function setIDOPTION($pArg="0") {$this->IDOPTION=$pArg;}
	public function setIMAGEURL($pArg="0") {$this->IMAGEURL=$pArg;}
	public function setIMG_NUMBER($pArg="0") {$this->IMG_NUMBER=$pArg;}

	public function getIDIMAGE() {return $this->IDIMAGE;}
	public function getIDOPTION() {return $this->IDOPTION;}
	public function getIMAGEURL() {return $this->IMAGEURL;}
	public function getIMG_NUMBER() {return $this->IMG_NUMBER;}

	public function readObject($array = array()) {
		$qry = "SELECT *".RET."FROM ".IMAGES.RET;
		$and = "WHERE".RET;

		if($array['IDIMAGE'] != "") {
			$qry .= $and."IDIMAGE = '".$array['IDIMAGE']."'".RET;
			$and = "AND".RET;
		}

		if($array['IDOPTION'] != "") {
			$qry .= $and."IDOPTION = '".$array['IDOPTION']."'".RET;
			$and = "AND".RET;
		}

		if($array['IMAGEURL'] != "") {
			$qry .= $and."IMAGEURL = '".$array['IMAGEURL']."'".RET;
			$and = "AND".RET;
		}

		if($array['IMG_NUMBER'] != "") {
			$qry .= $and."IMG_NUMBER = '".$array['IMG_NUMBER']."'".RET;
			$and = "AND".RET;
		}

		$record = Database::select($qry);
		if(count($record[0]) == 0) {
			return array();
		} else {
			$record = $record[0];
			$this->setIDIMAGE($record['IDIMAGE']);
			$this->setIDOPTION($record['IDOPTION']);
			$this->setIMAGEURL($record['IMAGEURL']);
			$this->setIMG_NUMBER($record['IMG_NUMBER']);
			return true;
		}
	}

	public static function readArray($array = array()) {
		$qry = "SELECT *".RET."FROM ".IMAGES.RET;
		$and = "WHERE".RET;

		if($array['IDIMAGE'] != "") {
			$qry .= $and."IDIMAGE = '".$array['IDIMAGE']."'".RET;
			$and = "AND".RET;
		}

		if($array['IDOPTION'] != "") {
			$qry .= $and."IDOPTION = '".$array['IDOPTION']."'".RET;
			$and = "AND".RET;
		}

		if($array['IMAGEURL'] != "") {
			$qry .= $and."IMAGEURL = '".$array['IMAGEURL']."'".RET;
			$and = "AND".RET;
		}

		if($array['IMG_NUMBER'] != "") {
			$qry .= $and."IMG_NUMBER = '".$array['IMG_NUMBER']."'".RET;
			$and = "AND".RET;
		}

		$recordset = Database::select($qry);
		$class_objects = array();
		$i=0;
		if(is_array($recordset) == true) {
			while(list($i, $record) = each($recordset)) {
				$class_object = new images();
				$class_object->setIDIMAGE($record['IDIMAGE']);
				$class_object->setIDOPTION($record['IDOPTION']);
				$class_object->setIMAGEURL($record['IMAGEURL']);
				$class_object->setIMG_NUMBER($record['IMG_NUMBER']);
				$class_objects[$i] = $class_object;
				$i++;
			}
		}
		return $class_objects;
	}

	public function insert() {
		if($this->getIDIMAGE() != '') {
			$qry  = "UPDATE ".IMAGES.RET."SET".RET.
			"IDIMAGE = '".$this->getIDIMAGE()."',".RET.
			"IDOPTION = '".$this->getIDOPTION()."',".RET.
			"IMAGEURL = '".$this->getIMAGEURL()."',".RET.
			"IMG_NUMBER = '".$this->getIMG_NUMBER()."'".RET.
			"WHERE IDIMAGE = ".$this->getIDIMAGE().RET;

			Database::insert($qry);
		} else {
			$qry  = "INSERT INTO ".IMAGES." (".RET.
			"IDOPTION, IMAGEURL, IMG_NUMBER".RET.
				") VALUES (".RET.
			"'".$this->getIDOPTION()."',".RET.
			"'".$this->getIMAGEURL()."',".RET.
			"'".$this->getIMG_NUMBER()."'".RET.
			")".RET;

			$this->setIDIMAGE(Database::insert($qry));
		}
	}

	public static function delete($array = array()) {
		$qry = "DELETE".RET."FROM ".IMAGES.RET;
		$and = "WHERE".RET;

		if($array['IDIMAGE'] != "") {
			$qry .= $and."IDIMAGE = '".$array['IDIMAGE']."'".RET;
			$and = "AND".RET;
		}

		if($array['IDOPTION'] != "") {
			$qry .= $and."IDOPTION = '".$array['IDOPTION']."'".RET;
			$and = "AND".RET;
		}

		if($array['IMAGEURL'] != "") {
			$qry .= $and."IMAGEURL = '".$array['IMAGEURL']."'".RET;
			$and = "AND".RET;
		}

		if($array['IMG_NUMBER'] != "") {
			$qry .= $and."IMG_NUMBER = '".$array['IMG_NUMBER']."'".RET;
			$and = "AND".RET;
		}

		Database::delete($qry);
	}
}

?>