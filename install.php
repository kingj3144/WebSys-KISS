<?php 
	//An installation script to set up the mySQL database 
	require 'config.php';
	//config.php is a file that will contain the information needed to connect to the database
	class KissDatabase
	{
		private $conn = NULL;

		/**
		  * Connects to the mySql server
		  */
		public funtion connect() {
			try {
	  		$conn = new PDO('mysql:host='.$config['host'],$config['db_username'], $config['db_password']);
			} catch(PDOException $e) {
				echo 'ERROR: ' . $e->getmessage();
			}
		}

		/**
		  * Sets up the database and tables if they do not already exisit
		  */
		public init() {
			if ($conn != NULL) {
				try {
					//Set up the database
					$conn->query("CREATE DATABASE IF NOT EXISTS" . $config['db_name'] . $config['db_versioin'] . 
						"; DEFAULT COLLATE utf8_unicode_ci");
					$conn->query("USE " . $config['db_name'] . $config['db_versioin']);
					$conn->exec("CREATE TABLE IF NOT EXISTS users ()");
					$conn->exec("CREATE TABLE IF NOT EXISTS salts ()");
					$conn->exec("CREATE TABLE IF NOT EXISTS items ()");
					
				} catch(PDOException $e) {
					echo 'ERROR: ' . $e->getmessage();
				}
			}
		}
	}

?>
