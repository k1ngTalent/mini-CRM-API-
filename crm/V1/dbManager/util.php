<?php

/**
 * 
 */
class util
{
	private $conn;



	function __construct()
	{
		require 'dbConnect.php';
		$db = new dbConnect();
		$this->conn = $db->connect();
	}
	public function forQue($query)
	{
		$stmt = $this->conn->prepare($query);
		if ($stmt->execute()) {
			return 1;
		} else {
			return 0;
		}

	}

	public function checkExist($tableName, $where)
	{
		$w = '';
		$v = '';

		$given_keys = array_keys($where);
		if (sizeof($where) > 1) {
			foreach ($given_keys as $value) {

				$value = $value . "=:" . $value;
			}
			$w .= implode("AND", $given_keys);
		} else {
			foreach ($where as $key => $value) {
				$w .= $key . "=:" . $key;
			}
		}
		$v = array();
		foreach ($where as $key => $value) {
			$v[$key] = $value;
		}


		$query = "SELECT * from $tableName WHERE $w LIMIT 1";
		$stmt = $this->conn->prepare($query);
		$stmt->execute($v);
		if ($stmt->rowCount() > 0) {
			return true;
		} else {
			return false;
		}

	}

	public function insert($tableName, $columnNames, $columnValues)
	{
		$k = '';
		$v = '';
		$c = '';
		$values = (array)$columnValues;
		$vv = array();
		$given_keys = array_keys($values);

		foreach ($columnNames as $value) {
			if (!in_array($value, $given_keys)) {
				$values[$value] = " ";
			}


		}
		$to_return = array();

		foreach ($columnNames as $value) {
			$c = $c . $value . ',';
			$k .= ":$value,";
			// $v.=" ':".$value."' => \"$values[$value]\",";
			$to_return[':' . $value] = $values[$value];


		}

		try {
			$query = "INSERT INTO " . $tableName . "(" . trim($c, ',') . ") VALUES (" . trim($k, ',') . ")";
			$stmt = $this->conn->prepare($query);
			$res = $stmt->execute($to_return);
			if ($res) {
				$response = "success";
			} else {
				$response = "error";
			}

			return $response;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}


	}

	public function delete($tableName, $where)
	{
		$w = '';
		$v = array();
		$z = array();
		$given_keys = array_keys($where);
		if (sizeof($where) > 1) {
			foreach ($given_keys as $value) {

				$value = $value . "=:" . $value;
				array_push($z, $value);
			}
			foreach ($where as $key => $value) {
				$v[':' . $key] = $value;
			}
			$w .= implode(" AND ", $z);
		} else {
			foreach ($where as $key => $value) {
				$v[':' . $key] = $value;
     	   // $v="'$key'. => .$value";
				$w = $key . "=:" . $key;
			}
		}

		$query = "DELETE FROM $tableName WHERE $w";
		$stmt = $this->conn->prepare($query);
		if ($stmt->execute($v)) {
			$message = "success";
		} else {
			$message = "error";
		}

		return $message;
	}

	public function update($tableName, $values, $where)
	{
		$w = '';
		$v = array();
		$values = (array)$values;
		$c = '';
		$to_return = array();
		foreach ($values as $key => $value) {
			$to_return[':' . $key] = $value;
		}
		$values = array_slice($values, 0, sizeof($value) - 2);
		foreach ($values as $key => $value) {
			$c = $c . $key . "=:" . $key . ',';


		}

		foreach ($where as $key => $value) {
			$w = $key . "=:" . $key;
		}

		$c = trim($c, ", ");

		try {

			$query = "UPDATE $tableName SET $c WHERE $w";

			$stmt = $this->conn->prepare($query);

			if ($stmt->execute($to_return)) {
				$message = "success";
			} else {
				$message = "error";
			}
			return $message;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}

	}

	public function getAll($query)
	{
		$stmt = $this->conn->prepare($query);
		$stmt->execute();

		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	public function getOne($query)
	{
		$stmt = $this->conn->prepare($query . 'LIMIT 1');
		$stmt->execute();
		return $stmt->fetch(PDO::FETCH_ASSOC);
	}

	public function getSession()
	{
		if (!isset($_SESSION)) {
			session_start();
		}
		$sess = array();
		if (isset($_SESSION['_id'])) {
			$sess["_id"] = $_SESSION['_id'];
			$sess["firstname"] = $_SESSION['firstname'];
			$sess["lastname"] = $_SESSION['lastname'];
			$sess["username"] = $_SESSION['username'];
		}

		return $sess;
	}

	public function destroySession()
	{
		if (!isset($_SESSION)) {
			session_start();
		}
		if (isset($_SESSION['_id'])) {
			session_destroy();
			$info = 'info';
			if (isset($_COOKIE[$info])) {
				setcookie($info, '', time() - $cookie_time);
			}
			$msg = "Logged Out Successfully...";
		} else {
			$msg = "Not logged in...";
		}
		return $msg;
	}

	public function random_char()
	{
		$char = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
		$char_length = 5;
		$cl = strlen($char);
		$randomize = '';
		for ($i = 0; $i < $char_length; $i++) {
			$randomize .= $char[rand(0, $cl - 1)];
		}
		return $randomize;
	}

}



?>