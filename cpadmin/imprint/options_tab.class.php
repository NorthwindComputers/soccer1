<?php

define("OPTIONS_TAB", "options_tab"); 

class options_tab {

	private $id_option_tab;
	private $id_option;
	private $id_tab;

	public function setid_option_tab($pArg="0") {$this->id_option_tab=$pArg;}
	public function setid_option($pArg="0") {$this->id_option=$pArg;}
	public function setid_tab($pArg="0") {$this->id_tab=$pArg;}

	public function getid_option_tab() {return $this->id_option_tab;}
	public function getid_option() {return $this->id_option;}
	public function getid_tab() {return $this->id_tab;}

	public function readObject($array = array()) {
		$qry = "SELECT *".RET."FROM ".OPTIONS_TAB.RET;
		$and = "WHERE".RET;

		if($array['id_option_tab'] != "") {
			$qry .= $and."id_option_tab = '".$array['id_option_tab']."'".RET;
			$and = "AND".RET;
		}

		if($array['id_option'] != "") {
			$qry .= $and."id_option = '".$array['id_option']."'".RET;
			$and = "AND".RET;
		}

		if($array['id_tab'] != "") {
			$qry .= $and."id_tab = '".$array['id_tab']."'".RET;
			$and = "AND".RET;
		}

		$record = Database::select($qry);
		if(count($record[0]) == 0) {
			return array();
		} else {
			$record = $record[0];
			$this->setid_option_tab($record['id_option_tab']);
			$this->setid_option($record['id_option']);
			$this->setid_tab($record['id_tab']);
			return true;
		}
	}

	public static function readArray($array = array()) {
		$qry = "SELECT *".RET."FROM ".OPTIONS_TAB.RET;
		$and = "WHERE".RET;

		if($array['id_option_tab'] != "") {
			$qry .= $and."id_option_tab = '".$array['id_option_tab']."'".RET;
			$and = "AND".RET;
		}

		if($array['id_option'] != "") {
			$qry .= $and."id_option = '".$array['id_option']."'".RET;
			$and = "AND".RET;
		}

		if($array['id_tab'] != "") {
			$qry .= $and."id_tab = '".$array['id_tab']."'".RET;
			$and = "AND".RET;
		}

		$recordset = Database::select($qry);
		$class_objects = array();
		if(is_array($recordset) == true) {
			while(list($i, $record) = each($recordset)) {
				$class_object = new options_tab();
				$class_object->setid_option_tab($record['id_option_tab']);
				$class_object->setid_option($record['id_option']);
				$class_object->setid_tab($record['id_tab']);
				$class_objects[$class_object->getid_option_tab()] = $class_object;
			}
		}
		return $class_objects;
	}

	public function insert() {
		if($this->getid_option_tab() != '') {
			$qry  = "UPDATE ".OPTIONS_TAB.RET."SET".RET.
			"id_option_tab = '".$this->getid_option_tab()."',".RET.
			"id_option = '".$this->getid_option()."',".RET.
			"id_tab = '".$this->getid_tab()."'".RET.
			"WHERE id_option_tab = ".$this->getid_option_tab().RET;

			Database::insert($qry);
		} else {
			$qry  = "INSERT INTO ".OPTIONS_TAB." (".RET.
			"id_option, id_tab".RET.
				") VALUES (".RET.
			"'".$this->getid_option()."',".RET.
			"'".$this->getid_tab()."'".RET.
			")".RET;

			$this->setid_option_tab(Database::insert($qry));
		}
	}

	public static function delete($array = array()) {
		$qry = "DELETE".RET."FROM ".OPTIONS_TAB.RET;
		$and = "WHERE".RET;

		if($array['id_option_tab'] != "") {
			$qry .= $and."id_option_tab = '".$array['id_option_tab']."'".RET;
			$and = "AND".RET;
		}

		if($array['id_option'] != "") {
			$qry .= $and."id_option = '".$array['id_option']."'".RET;
			$and = "AND".RET;
		}

		if($array['id_tab'] != "") {
			$qry .= $and."id_tab = '".$array['id_tab']."'".RET;
			$and = "AND".RET;
		}

		Database::delete($qry);
	}
}

?>