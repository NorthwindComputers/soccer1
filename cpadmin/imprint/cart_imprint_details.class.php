<?php
define("CART_IMPRINT_DETAILS", "cart_imprint_details"); 

class cart_imprint_details {

	private $id;
	private $SessionID;
	private $productID;
	private $optionID;
	private $option_price;
	private $email_address;
	private $order_id;

	public function setid($pArg="0") {$this->id=$pArg;}
	public function setSessionID($pArg="0") {$this->SessionID=$pArg;}
	public function setproductID($pArg="0") {$this->productID=$pArg;}
	public function setoptionID($pArg="0") {$this->optionID=$pArg;}
	public function setoption_price($pArg="0") {$this->option_price=$pArg;}
	public function setemail_address($pArg="0") {$this->email_address=$pArg;}
	public function setorder_id($pArg="0") {$this->order_id=$pArg;}

	public function getid() {return $this->id;}
	public function getSessionID() {return $this->SessionID;}
	public function getproductID() {return $this->productID;}
	public function getoptionID() {return $this->optionID;}
	public function getoption_price() {return $this->option_price;}
	public function getemail_address() {return $this->email_address;}
	public function getorder_id() {return $this->order_id;}

	public function readObject($array = array()) {
		$qry = "SELECT *".RET."FROM ".CART_IMPRINT_DETAILS.RET;
		$and = "WHERE".RET;

		if($array['id'] != "") {
			$qry .= $and."id = '".$array['id']."'".RET;
			$and = "AND".RET;
		}

		if($array['SessionID'] != "") {
			$qry .= $and."SessionID = '".$array['SessionID']."'".RET;
			$and = "AND".RET;
		}

		if($array['productID'] != "") {
			$qry .= $and."productID = '".$array['productID']."'".RET;
			$and = "AND".RET;
		}

		if($array['optionID'] != "") {
			$qry .= $and."optionID = '".$array['optionID']."'".RET;
			$and = "AND".RET;
		}

		if($array['option_price'] != "") {
			$qry .= $and."option_price = '".$array['option_price']."'".RET;
			$and = "AND".RET;
		}

		if($array['email_address'] != "") {
			$qry .= $and."email_address = '".$array['email_address']."'".RET;
			$and = "AND".RET;
		}

		if($array['order_id'] != "") {
			$qry .= $and."order_id = '".$array['order_id']."'".RET;
			$and = "AND".RET;
		}

		$record = Database::select($qry);
		if(count($record[0]) == 0) {
			return array();
		} else {
			$record = $record[0];
			$this->setid($record['id']);
			$this->setSessionID($record['SessionID']);
			$this->setproductID($record['productID']);
			$this->setoptionID($record['optionID']);
			$this->setoption_price($record['option_price']);
			$this->setemail_address($record['email_address']);
			$this->setorder_id($record['order_id']);
			return true;
		}
	}

	public static function readArray($array = array()) {
		$qry = "SELECT *".RET."FROM ".CART_IMPRINT_DETAILS.RET;
		$and = "WHERE".RET;

		if($array['id'] != "") {
			$qry .= $and."id = '".$array['id']."'".RET;
			$and = "AND".RET;
		}

		if($array['SessionID'] != "") {
			$qry .= $and."SessionID = '".$array['SessionID']."'".RET;
			$and = "AND".RET;
		}

		if($array['productID'] != "") {
			$qry .= $and."productID = '".$array['productID']."'".RET;
			$and = "AND".RET;
		}

		if($array['optionID'] != "") {
			$qry .= $and."optionID = '".$array['optionID']."'".RET;
			$and = "AND".RET;
		}

		if($array['option_price'] != "") {
			$qry .= $and."option_price = '".$array['option_price']."'".RET;
			$and = "AND".RET;
		}

		if($array['email_address'] != "") {
			$qry .= $and."email_address = '".$array['email_address']."'".RET;
			$and = "AND".RET;
		}

		if($array['order_id'] != "") {
			$qry .= $and."order_id = '".$array['order_id']."'".RET;
			$and = "AND".RET;
		}

		$recordset = Database::select($qry);
		$class_objects = array();
		if(is_array($recordset) == true) {
			while(list($i, $record) = each($recordset)) {
				$class_object = new cart_imprint_details();
				$class_object->setid($record['id']);
				$class_object->setSessionID($record['SessionID']);
				$class_object->setproductID($record['productID']);
				$class_object->setoptionID($record['optionID']);
				$class_object->setoption_price($record['option_price']);
				$class_object->setemail_address($record['email_address']);
				$class_object->setorder_id($record['order_id']);
				$class_objects[$class_object->getid()] = $class_object;
			}
		}
		return $class_objects;
	}

	public function insert() {
		if($this->getid() != '') {
			$qry  = "UPDATE ".CART_IMPRINT_DETAILS.RET."SET".RET.
			"id = '".$this->getid()."',".RET.
			"SessionID = '".$this->getSessionID()."',".RET.
			"productID = '".$this->getproductID()."',".RET.
			"optionID = '".$this->getoptionID()."',".RET.
			"option_price = '".$this->getoption_price()."',".RET.
			"email_address = '".$this->getemail_address()."',".RET.
			"order_id = '".$this->getorder_id()."'".RET.
			"WHERE id = ".$this->getid().RET;

			Database::insert($qry);
		} else {
			$qry  = "INSERT INTO ".CART_IMPRINT_DETAILS." (".RET.
			"SessionID, productID, optionID, option_price, email_address, order_id".RET.
				") VALUES (".RET.
			"'".$this->getSessionID()."',".RET.
			"'".$this->getproductID()."',".RET.
			"'".$this->getoptionID()."',".RET.
			"'".$this->getoption_price()."',".RET.
			"'".$this->getemail_address()."',".RET.
			"'".$this->getorder_id()."'".RET.
			")".RET;

			$this->setid(Database::insert($qry));
		}
	}

	public static function delete($array = array()) {
		$qry = "DELETE".RET."FROM ".CART_IMPRINT_DETAILS.RET;
		$and = "WHERE".RET;

		if($array['id'] != "") {
			$qry .= $and."id = '".$array['id']."'".RET;
			$and = "AND".RET;
		}

		if($array['SessionID'] != "") {
			$qry .= $and."SessionID = '".$array['SessionID']."'".RET;
			$and = "AND".RET;
		}

		if($array['productID'] != "") {
			$qry .= $and."productID = '".$array['productID']."'".RET;
			$and = "AND".RET;
		}

		if($array['optionID'] != "") {
			$qry .= $and."optionID = '".$array['optionID']."'".RET;
			$and = "AND".RET;
		}

		if($array['option_price'] != "") {
			$qry .= $and."option_price = '".$array['option_price']."'".RET;
			$and = "AND".RET;
		}

		if($array['email_address'] != "") {
			$qry .= $and."email_address = '".$array['email_address']."'".RET;
			$and = "AND".RET;
		}

		if($array['order_id'] != "") {
			$qry .= $and."order_id = '".$array['order_id']."'".RET;
			$and = "AND".RET;
		}

		Database::delete($qry);
	}
}

?>