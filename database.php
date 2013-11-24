<?php 
	class KissDatabase
	{

		private $conn = NULL;

		private $config = NULL;

		public function __construct($configArray) {
			$this->config = $configArray;
			$this->connect();
			$this->init();
		}

		/** Connects to the mySql server
		  */
		public function connect() {
			try {
	  			$this->conn = new PDO('mysql:host='.$this->config['host'],$this->config['db_username'], $this->config['db_password']);
			} catch(PDOException $e) {
				echo 'ERROR: ' . $e->getmessage();
			}
		}

		/** Sets up the database and tables if they do not already exisit
		  */
		public function init() {
			if ($this->conn != NULL) {
				try {
					//Set up the database
					$this->conn->query("CREATE DATABASE IF NOT EXISTS " . $this->config['db_name'] . $this->config['db_version'] . 
						" DEFAULT COLLATE utf8_unicode_ci");
					$this->conn->query("USE " . $this->config['db_name'] . $this->config['db_version']);
					$this->conn->exec("CREATE TABLE IF NOT EXISTS users (name VARCHAR(32) PRIMARY KEY NOT NULL, password VARCHAR(64) NOT NULL) COLLATE utf8_unicode_ci");
					$this->conn->exec("CREATE TABLE IF NOT EXISTS salts (name VARCHAR(32) PRIMARY KEY NOT NULL, salt VARCHAR(64) NOT NULL) COLLATE utf8_unicode_ci");
					$this->conn->exec("CREATE TABLE IF NOT EXISTS items () COLLATE utf8_unicode_ci");
					
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
		public function getUserByName($name) {
			if ($this->conn != NULL) {
				$user = $this->conn->query("SELECT * FROM users WHERE name=$name LIMIT 1");
				return $user;
			} else {
				throw new Exception("Not connected to the database");
			}
		}

		/** Gets a salt from the salt table
		  * @param $name - name of the user to find
		  * @return - the query result 
		  */
		public function getSaltByUser($name) {
			if ($this->conn != NULL) {
				try {	
					$salt = $this->conn->query("SELECT * FROM salts WHERE name=$name LIMIT 1");
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
		public function addUser($name, $password) {
			if ($this->conn != NULL) {
				try {	
					$salt = $this->createSalt();
					if($this->conn->exec("INSERT INTO salts (name, salt) VALUES ('$name', '$salt');") != 0) {
						$hash = $this->hashPassword($password, $salt);
						//TO DO: user name needs to be escaped of special characters
						$this->conn->query("INSERT INTO `users` (`name`, `password`) VALUES ('$name', '$hash');");
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
		private function hashPassword($password, $salt) {
			return crypt($password, $salt);
		}

		/** Creates a random salt compatible with Blowfish hashing
		  * @return - new random salt
		  */
		private function createSalt($rounds = 7) {
	
		    $salt = "";
		    $salt_chars = array_merge(range('A','Z'), range('a','z'), range(0,9));
		    for($i=0; $i < 22; $i++) {
		      $salt .= $salt_chars[array_rand($salt_chars)];
		    }
		    return sprintf('$2a$%02d$', $rounds) . $salt;
		}
	}

?>
