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
				try {	
					$salt = $conn->query("SELECT * FROM salts WHERE name=$name LIMIT 1");
					return $salt;
				} catch(PDOException $e) {
					echo 'ERROR: ' . $e->getmessage();
				}
			} else {
				throw new Exception("Not connected to the database");
			}
		}

		/** Adds a new user to the database
		  * @param $name - the name of the new users as a string
		  * @param $passworf - the users password as a plaintext string
		  */
		public addUser($name, $password) {
			if ($conn != NULL) {
				try {	
					$salt = createSalt();
					if($conn->exec("INSERT INTO `salts` (`name`, `password`) VALUES ($name, $salt);") == 1) {
						$hash = hashPassword($password, $salt);
						//TO DO: user name needs to be escaped of special characters
						$conn->query("INSERT INTO `users` (`name`, `password`) VALUES ($name, $hash);");
					} else {
						throw new Exception("Salt could not be created");
					}
				} catch(PDOException $e) {
					echo 'ERROR: ' . $e->getmessage();
				}
			} else {
				throw new Exception("Not connected to the database");
			}
		}

		/** Hashes the plaintext passord with the gicen salt
		  * @param $password - as a plaintext string
		  * @param - $salt Blowfish salt 
		  * @return - the hashed password
		  */
		private hashPassword($password, $salt) {
			return crypt($password, $salt);
		}

		/** Creates a random salt compatible with Blowfish hashing
		  * @return - new random salt
		  */
		private createSalt() {
		    $salt = "";
		    $salt_chars = array_merge(range('A','Z'), range('a','z'), range(0,9));
		    for($i=0; $i < 22; $i++) {
		      $salt .= $salt_chars[array_rand($salt_chars)];
		    }
		    return sprintf('$2a$%02d$', $rounds) . $salt;
		}
	}

?>
