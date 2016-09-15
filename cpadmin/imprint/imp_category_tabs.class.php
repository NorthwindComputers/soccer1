<?php


define("IMP_CATEGORY_TABS", "imp_category_tabs"); 

class imp_category_tabs {

	private $id_tab;
	private $imprint_categ_id;
	private $tab_name;
	private $tab_rank;
	private $tab_parent;
	private $is_parent;

	public function setid_tab($pArg="0") {$this->id_tab=$pArg;}
	public function setimprint_categ_id($pArg="0") {$this->imprint_categ_id=$pArg;}
	public function settab_name($pArg="0") {$this->tab_name=$pArg;}
	public function settab_rank($pArg="0") {$this->tab_rank=$pArg;}
	public function settab_parent($pArg="0") {$this->tab_parent=$pArg;}
	public function setis_parent($pArg="0") {$this->is_parent=$pArg;}

	public function getid_tab() {return $this->id_tab;}
	public function getimprint_categ_id() {return $this->imprint_categ_id;}
	public function gettab_name() {return $this->tab_name;}
	public function gettab_rank() {return $this->tab_rank;}
	public function gettab_parent() {return $this->tab_parent;}
	public function getis_parent() {return $this->is_parent;}

	public function readObject($array = array()) {
		$qry = "SELECT *".RET."FROM ".IMP_CATEGORY_TABS.RET;
		$and = "WHERE".RET;

		if($array['id_tab'] != "") {
			$qry .= $and."id_tab = '".$array['id_tab']."'".RET;
			$and = "AND".RET;
		}

		if($array['imprint_categ_id'] != "") {
			$qry .= $and."imprint_categ_id = '".$array['imprint_categ_id']."'".RET;
			$and = "AND".RET;
		}

		if($array['tab_name'] != "") {
			$qry .= $and."tab_name = '".$array['tab_name']."'".RET;
			$and = "AND".RET;
		}

		if($array['tab_rank'] != "") {
			$qry .= $and."tab_rank = '".$array['tab_rank']."'".RET;
			$and = "AND".RET;
		}

		if($array['tab_parent'] != "") {
			$qry .= $and."tab_parent = '".$array['tab_parent']."'".RET;
			$and = "AND".RET;
		}

		if($array['is_parent'] != "") {
			$qry .= $and."is_parent = '".$array['is_parent']."'".RET;
			$and = "AND".RET;
		}

		$record = Database::select($qry);
		if(count($record[0]) == 0) {
			return array();
		} else {
			$record = $record[0];
			$this->setid_tab($record['id_tab']);
			$this->setimprint_categ_id($record['imprint_categ_id']);
			$this->settab_name($record['tab_name']);
			$this->settab_rank($record['tab_rank']);
			$this->settab_parent($record['tab_parent']);
			$this->setis_parent($record['is_parent']);
			return true;
		}
	}

	public static function readArray($array = array()) {
		$qry = "SELECT *".RET."FROM ".IMP_CATEGORY_TABS.RET;
		$and = "WHERE".RET;
		if($array['id_tab'] != "") {
			$qry .= $and."id_tab = '".$array['id_tab']."'".RET;
			$and = "AND".RET;
		}

		if($array['imprint_categ_id'] != "") {
			$qry .= $and."imprint_categ_id = '".$array['imprint_categ_id']."'".RET;
			$and = "AND".RET;
		}

		if($array['tab_name'] != "") {
			$qry .= $and."tab_name = '".$array['tab_name']."'".RET;
			$and = "AND".RET;
		}

		if($array['tab_rank'] != "") {
			$qry .= $and."tab_rank = '".$array['tab_rank']."'".RET;
			$and = "AND".RET;
		}

		if($array['tab_parent'] != "") {
			$qry .= $and."tab_parent = '".$array['tab_parent']."'".RET;
			$and = "AND".RET;
		}

		if($array['is_parent'] != "") {
			$qry .= $and."is_parent = '".$array['is_parent']."'".RET;
			$and = "AND".RET;
		}

		$recordset = Database::select($qry);
		$i=0;
		$class_objects = array();
	
		if(is_array($recordset) == true) {
			while(list($i, $record) = each($recordset)) {
				$class_object = new imp_category_tabs();
				$class_object->setid_tab($record['id_tab']);
				$class_object->setimprint_categ_id($record['imprint_categ_id']);
				$class_object->settab_name($record['tab_name']);
				$class_object->settab_rank($record['tab_rank']);
				$class_object->settab_parent($record['tab_parent']);
				$class_object->setis_parent($record['is_parent']);
				$class_objects[$i] = $class_object;
			}
		}
		return $class_objects;
	}

	public function insert() {
		if($this->getid_tab() != '') {
			$qry  = "UPDATE ".IMP_CATEGORY_TABS.RET."SET".RET.
			"id_tab = '".$this->getid_tab()."',".RET.
			"imprint_categ_id = '".$this->getimprint_categ_id()."',".RET.
			"tab_name = '".$this->gettab_name()."',".RET.
			"tab_rank = '".$this->gettab_rank()."',".RET.
			"tab_parent = '".$this->gettab_parent()."',".RET.
			"is_parent = '".$this->getis_parent()."'".RET.
			"WHERE id_tab = ".$this->getid_tab().RET;

			Database::insert($qry);
		} else {
			$qry  = "INSERT INTO ".IMP_CATEGORY_TABS." (".RET.
			"imprint_categ_id, tab_name, tab_rank, tab_parent, is_parent".RET.
				") VALUES (".RET.
			"'".$this->getimprint_categ_id()."',".RET.
			"'".$this->gettab_name()."',".RET.
			"'".$this->gettab_rank()."',".RET.
			"'".$this->gettab_parent()."',".RET.
			"'".$this->getis_parent()."'".RET.
			")".RET;

			$this->setid_tab(Database::insert($qry));
		}
	}

	public static function delete($array = array()) {
		$qry = "DELETE".RET."FROM ".IMP_CATEGORY_TABS.RET;
		$and = "WHERE".RET;

		if($array['id_tab'] != "") {
			$qry .= $and."id_tab = '".$array['id_tab']."'".RET;
			$and = "AND".RET;
		}

		if($array['imprint_categ_id'] != "") {
			$qry .= $and."imprint_categ_id = '".$array['imprint_categ_id']."'".RET;
			$and = "AND".RET;
		}

		if($array['tab_name'] != "") {
			$qry .= $and."tab_name = '".$array['tab_name']."'".RET;
			$and = "AND".RET;
		}

		if($array['tab_rank'] != "") {
			$qry .= $and."tab_rank = '".$array['tab_rank']."'".RET;
			$and = "AND".RET;
		}

		if($array['tab_parent'] != "") {
			$qry .= $and."tab_parent = '".$array['tab_parent']."'".RET;
			$and = "AND".RET;
		}

		if($array['is_parent'] != "") {
			$qry .= $and."is_parent = '".$array['is_parent']."'".RET;
			$and = "AND".RET;
		}

		Database::delete($qry);
	}
}

?>