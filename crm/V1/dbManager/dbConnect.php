<?php

/**
 * 
 */
class dbConnect
{
	private $conn;
	function connect()
	{
		$hostname = 'localhost';
		$database = 'crm';
		define('DB_USERNAME', 'root');
		define('DB_PASSWORD', '');
 	// include_once 'config.php';
		try {

			$this->conn = new PDO("mysql:host=$hostname;dbname=$database", DB_USERNAME, DB_PASSWORD);
			$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch (PDOException $e) {


			echo "Connection failed: " . $e->getMessage();


		}
		return $this->conn;
	}
}
?>