<?php

class SQLHelper {

	private $isConnected = false;

	private $user;
	private $pass;
	private $host;
	private $dbName;
	private $conn;
	private $sql;
	
	protected static $instance = null;

	public static function get_instance() {

		if(SQLHelper::$instance == null)
			SQLHelper::$instance = new SQLHelper();
		return SQLHelper::$instance;
	}

	protected function __construct() {
		$this->user = "mong_leadgen";
		$this->pass = "?O&VFr;W0sU=";
		$this->host = "localhost";
		$this->dbName = "mong_leadgen";
		$this->connect();
	}

	private function connect() {
		$this->conn = mysqli_connect($this->host, $this->user, $this->pass, $this->dbName);
	}

	public function CALL($query, $returnArray = false) {
		if(!$this->isConnected) $this->connect();
		if($res = $this->conn->query($query)) {
			if(!$returnArray) {
				return $res;
			}
			$rows = array();
			while ($temp = mysqli_fetch_array($res)) {
				$rows[] = $temp;
			}
			mysqli_free_result($res);
			return $rows;
		}
		die(mysqli_error($this->conn));
		return false;
	}

	public function SELECT($query, $returnArray = false){
		if(!$this->isConnected) $this->connect();
		if($res = $this->conn->query($query)) {
			if(!$returnArray)
				return $res;

			$rows = array();
			while ($temp = mysqli_fetch_array($res)) {
				$rows[] = $temp;
			}
			mysqli_free_result($res);
			return $rows;
		}
		die(mysqli_error($this->conn));
		return false;
	}

	public function INSERT($query){
		if($this->conn->query($query)) return true;
		die(mysqli_error($this->conn));
		return false;
	}

	public function QUERY($query){
		if($this->conn->query($query)) return true;
		die(mysqli_error($this->conn));
		return false;
	}

	public function close() {
		$this->conn->close();
	}
}