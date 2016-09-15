<?php
define("BUNDLE_TABS", "bundle_tabs"); 

class bundle_tabs {

	private $id;
	private $id_tab;
	private $bundleId;

	public function setid($pArg="0") {$this->id=$pArg;}
	public function setid_tab($pArg="0") {$this->id_tab=$pArg;}
	public function setbundleId($pArg="0") {$this->bundleId=$pArg;}

	public function getid() {return $this->id;}
	public function getid_tab() {return $this->id_tab;}
	public function getbundleId() {return $this->bundleId;}

	public function readObject($array = array()) {
		$qry = "SELECT *".RET."FROM ".BUNDLE_TABS.RET;
		$and = "WHERE".RET;

		if($array['id'] != "") {
			$qry .= $and."id = '".$array['id']."'".RET;
			$and = "AND".RET;
		}

		if($array['id_tab'] != "") {
			$qry .= $and."id_tab = '".$array['id_tab']."'".RET;
			$and = "AND".RET;
		}

		if($array['bundleId'] != "") {
			$qry .= $and."bundleId = '".$array['bundleId']."'".RET;
			$and = "AND".RET;
		}

		$record = Database::select($qry);
		if(count($record[0]) == 0) {
			return array();
		} else {
			$record = $record[0];
			$this->setid($record['id']);
			$this->setid_tab($record['id_tab']);
			$this->setbundleId($record['bundleId']);
			return true;
		}
	}

	public static function readArray($array = array()) {
		$qry = "SELECT *".RET."FROM ".BUNDLE_TABS.RET;
		$and = "WHERE".RET;

		if($array['id'] != "") {
			$qry .= $and."id = '".$array['id']."'".RET;
			$and = "AND".RET;
		}

		if($array['id_tab'] != "") {
			$qry .= $and."id_tab = '".$array['id_tab']."'".RET;
			$and = "AND".RET;
		}

		if($array['bundleId'] != "") {
			$qry .= $and."bundleId = '".$array['bundleId']."'".RET;
			$and = "AND".RET;
		}

		$recordset = Database::select($qry);
		$class_objects = array();
		if(is_array($recordset) == true) {
			while(list($i, $record) = each($recordset)) {
				$class_object = new bundle_tabs();
				$class_object->setid($record['id']);
				$class_object->setid_tab($record['id_tab']);
				$class_object->setbundleId($record['bundleId']);
				$class_objects[$class_object->getid()] = $class_object;
			}
		}
		return $class_objects;
	}

	public function insert() {
		if($this->getid() != '') {
			$qry  = "UPDATE ".BUNDLE_TABS.RET."SET".RET.
			"id = '".$this->getid()."',".RET.
			"id_tab = '".$this->getid_tab()."',".RET.
			"bundleId = '".$this->getbundleId()."'".RET.
			"WHERE id = ".$this->getid().RET;

			Database::insert($qry);
		} else {
			$qry  = "INSERT INTO ".BUNDLE_TABS." (".RET.
			"id_tab, bundleId".RET.
				") VALUES (".RET.
			"'".$this->getid_tab()."',".RET.
			"'".$this->getbundleId()."'".RET.
			")".RET;

			$this->setid(Database::insert($qry));
		}
	}

	public static function delete($array = array()) {
		$qry = "DELETE".RET."FROM ".BUNDLE_TABS.RET;
		$and = "WHERE".RET;

		if($array['id'] != "") {
			$qry .= $and."id = '".$array['id']."'".RET;
			$and = "AND".RET;
		}

		if($array['id_tab'] != "") {
			$qry .= $and."id_tab = '".$array['id_tab']."'".RET;
			$and = "AND".RET;
		}

		if($array['bundleId'] != "") {
			$qry .= $and."bundleId = '".$array['bundleId']."'".RET;
			$and = "AND".RET;
		}

		Database::delete($qry);
	}
}

?>