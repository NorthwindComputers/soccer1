<?php

include("_config.inc.php");
class Database {

	public function Database() {
		$this->_conn = SPDO::getInstance();
	}

	public function select($pQry = "") {
		$pdo = SPDO::getInstance();
		$result = $pdo->query($pQry);
		$row = array ();
		if (!empty ($result)) {
			$row = $result->fetchAll(PDO::FETCH_ASSOC);
		}
		return $row;
	}

	public function insert($pQry = "") {
		$pdo = SPDO::getInstance();
		$result = $pdo->execute($pQry);
		return $pdo->lastInsertId();
	}

	public function delete($pQry = "") {
		$pdo = SPDO::getInstance();
		$result = $pdo->execute($pQry);
		return $result;
	}
}

class SPDO {

	private $PDOInstance = null;
	private static $instance = null;
	private $exception;

	private function __construct() {
		try {
			$this->PDOInstance = new PDO("mysql:host=".SERVER.";dbname=".BASE, USER, PASS);
		} catch (PDOException $e) {
			echo "Error connecting to MySQL!: ".$e->getMessage();
			exit();
		}
	}

	public static function getInstance() {
		if (is_null(self::$instance)) {
			self::$instance = new SPDO();
		}
		return self::$instance;
	}

	public function query($query) {
		return $this->PDOInstance->query($query);
	}

	public function prepare($query) {
		return $this->PDOInstance->prepare($query);
	}

	public function execute($query) {
		return $this->PDOInstance->exec($query);
	}

	public function lastInsertId() {
		return $this->PDOInstance->lastInsertId();
	}

	public function quote($query) {
		return $this->PDOInstance->quote($query);
	}

	public function getException() {
		return $this->exception;
	}
}

?>