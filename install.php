<?php 
	//An installation script to set up the mySQL database 
	require 'config.php';
	//config.php is a file that will contain the information needed to connect to the database
	class KissDatabase
	{
		private $conn = NULL;

		/** Connects to the mySql server
		  */
		public funtion connect() {
			try {
	  		$conn = new PDO('mysql:host='.$config['host'],$config['db_username'], $config['db_password']);
			} catch(PDOException $e) {
				echo 'ERROR: ' . $e->getmessage();
			}
		}

		/** Sets up the database and tables if they do not already exisit
		  */
		public init() {
			if ($conn != NULL) {
				try {
					//Set up the database
					$conn->query("CREATE DATABASE IF NOT EXISTS" . $config['db_name'] . $config['db_versioin'] . 
						"; DEFAULT COLLATE utf8_unicode_ci");
					$conn->query("USE " . $config['db_name'] . $config['db_versioin']);
					$conn->exec("CREATE TABLE IF NOT EXISTS users (name VARCHAR(32) PRIMARY KEY NOT NULL, password VARCHAR(64) NOT NULL)");
					$conn->exec("CREATE TABLE IF NOT EXISTS salts (name VARCHAR(32) PRIMARY KEY NOT NULL, salt VARCHAR(64) NOT NULL)");
					$conn->exec("CREATE TABLE IF NOT EXISTS items ()");
					
				} catch(PDOException $e) {
					echo 'ERROR: ' . $e->getmessage();
				}
			} else {
				throw new Exception("Not connected to the database");
			}
		}

		/** Gets a user from the user table
		  * @param $name - name of the user to find
		  * @return - the query result 
		  */
		public getUserByName($name) {
			if ($conn != NULL) {
				$user = $conn->query("SELECT * FROM users WHERE name=$name LIMIT 1");
				return $user;
			} else {
				throw new Exception("Not connected to the database");
			}
		}

		/** Gets a salt from the salt table
		  * @param $name - name of the user to find
		  * @return - the query result 
		  */
		public getSaltByUser($name) {
			if ($conn != NULL) {
				$salt = $conn->query("SELECT * FROM salts WHERE name=$name LIMIT 1");
				return $salt;
			} else {
				throw new Exception("Not connected to the database");
			}
		}

		public addUser($name, $password) {}

		private hashPassword($password) {}

		private createSalt() {}
	}

?>
