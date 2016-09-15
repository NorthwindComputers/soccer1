<?php


define("IMPRINT_TABS", "imprint_tabs"); 

class imprint_tabs {

	private $id_imprint_tabs;
	private $imprint_categ_id;
	private $nb_tabs;

	public function setid_imprint_tabs($pArg="0") {$this->id_imprint_tabs=$pArg;}
	public function setimprint_categ_id($pArg="0") {$this->imprint_categ_id=$pArg;}
	public function setnb_tabs($pArg="0") {$this->nb_tabs=$pArg;}

	public function getid_imprint_tabs() {return $this->id_imprint_tabs;}
	public function getimprint_categ_id() {return $this->imprint_categ_id;}
	public function getnb_tabs() {return $this->nb_tabs;}

	public function readObject($array = array()) {
		$qry = "SELECT *".RET."FROM ".IMPRINT_TABS.RET;
		$and = "WHERE".RET;

		if($array['id_imprint_tabs'] != "") {
			$qry .= $and."id_imprint_tabs = '".$array['id_imprint_tabs']."'".RET;
			$and = "AND".RET;
		}

		if($array['imprint_categ_id'] != "") {
			$qry .= $and."imprint_categ_id = '".$array['imprint_categ_id']."'".RET;
			$and = "AND".RET;
		}

		if($array['nb_tabs'] != "") {
			$qry .= $and."nb_tabs = '".$array['nb_tabs']."'".RET;
			$and = "AND".RET;
		}

		$record = Database::select($qry);
		if(count($record[0]) == 0) {
			return array();
		} else {
			$record = $record[0];
			$this->setid_imprint_tabs($record['id_imprint_tabs']);
			$this->setimprint_categ_id($record['imprint_categ_id']);
			$this->setnb_tabs($record['nb_tabs']);
			return true;
		}
	}

	public static function readArray($array = array()) {
		$qry = "SELECT *".RET."FROM ".IMPRINT_TABS.RET;
		$and = "WHERE".RET;

		if($array['id_imprint_tabs'] != "") {
			$qry .= $and."id_imprint_tabs = '".$array['id_imprint_tabs']."'".RET;
			$and = "AND".RET;
		}

		if($array['imprint_categ_id'] != "") {
			$qry .= $and."imprint_categ_id = '".$array['imprint_categ_id']."'".RET;
			$and = "AND".RET;
		}

		if($array['nb_tabs'] != "") {
			$qry .= $and."nb_tabs = '".$array['nb_tabs']."'".RET;
			$and = "AND".RET;
		}

		$recordset = Database::select($qry);
		$class_objects = array();
		if(is_array($recordset) == true) {
			while(list($i, $record) = each($recordset)) {
				$class_object = new imprint_tabs();
				$class_object->setid_imprint_tabs($record['id_imprint_tabs']);
				$class_object->setimprint_categ_id($record['imprint_categ_id']);
				$class_object->setnb_tabs($record['nb_tabs']);
				$class_objects[$class_object->getid_imprint_tabs()] = $class_object;
			}
		}
		return $class_objects;
	}

	public function insert() {
		if($this->getid_imprint_tabs() != '') {
			$qry  = "UPDATE ".IMPRINT_TABS.RET."SET".RET.
			"id_imprint_tabs = '".$this->getid_imprint_tabs()."',".RET.
			"imprint_categ_id = '".$this->getimprint_categ_id()."',".RET.
			"nb_tabs = '".$this->getnb_tabs()."'".RET.
			"WHERE id_imprint_tabs = ".$this->getid_imprint_tabs().RET;

			Database::insert($qry);
		} else {
			$qry  = "INSERT INTO ".IMPRINT_TABS." (".RET.
			"imprint_categ_id, nb_tabs".RET.
				") VALUES (".RET.
			"'".$this->getimprint_categ_id()."',".RET.
			"'".$this->getnb_tabs()."'".RET.
			")".RET;

			$this->setid_imprint_tabs(Database::insert($qry));
		}
	}

	public static function delete($array = array()) {
		$qry = "DELETE".RET."FROM ".IMPRINT_TABS.RET;
		$and = "WHERE".RET;

		if($array['id_imprint_tabs'] != "") {
			$qry .= $and."id_imprint_tabs = '".$array['id_imprint_tabs']."'".RET;
			$and = "AND".RET;
		}

		if($array['imprint_categ_id'] != "") {
			$qry .= $and."imprint_categ_id = '".$array['imprint_categ_id']."'".RET;
			$and = "AND".RET;
		}

		if($array['nb_tabs'] != "") {
			$qry .= $and."nb_tabs = '".$array['nb_tabs']."'".RET;
			$and = "AND".RET;
		}

		Database::delete($qry);
	}
}

?>