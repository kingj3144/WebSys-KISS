<?php 
	const DATABASE_CONNECTION_ERROR = "Not connected to the database";
	const USER_CREATION_ERROR = "User could not be created";
	const USER_NOT_FOUND_ERROR = "User could not be found";
	const USER_NOT_DELTED_ERROR = "User could not be deleted";
	const SALT_NOT_FOUND_ERROR = "Salt could not be found";
	const SALT_NOT_DELTED_ERROR = "Salt could not be deleted";
	const LIST_CREATION_ERROR = "List could not be created";
	const LIST_DELETE_ERROR = "List could not be deleted";
	
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
				if ($this->config['debug'] == 'on') {
					echo 'ERROR: ' . $e->getmessage();
				} else {
					throw $e;
				}
			}
		}

		/** Closes the connection to the mySql server
		  */
		public function close() {
			$this->conn = null;
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
					$this->conn->exec("CREATE TABLE IF NOT EXISTS users (
						username VARCHAR(32) PRIMARY KEY NOT NULL, 
						password VARCHAR(64) NOT NULL, 
						salt VARCHAR(64) NOT NULL,
						name VARCHAR(32), 
						email VARCHAR(32)
						) COLLATE utf8_unicode_ci");

					$this->conn->exec("CREATE TABLE IF NOT EXISTS lists (
						listid INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
						name VARCHAR(32) NOT NULL,
						username VARCHAR(32) NOT NULL,
						FOREIGN KEY(username) REFERENCES users(username)
						) COLLATE utf8_unicode_ci");

					$this->conn->exec("CREATE TABLE IF NOT EXISTS listitems (
						itemid INT PRIMARY KEY NOT NULL,
						username VARCHAR(32),
						item VARCHAR(64) NOT NULL,
						quantity INT,
						unit VARCHAR(16),
						listid INT NOT NULL,
						category VARCHAR(32),
						time DATETIME NOT NULL,
						FOREIGN KEY(username) REFERENCES users(username),
						FOREIGN KEY(listid) REFERENCES lists(listid)
						) COLLATE utf8_unicode_ci");

					$this->conn->exec("CREATE TABLE IF NOT EXISTS listaccess ( 
						username VARCHAR(32),
						listid INT NOT NULL,
						FOREIGN KEY(username) REFERENCES users(username),
						FOREIGN KEY(listid) REFERENCES lists(listid)
						) COLLATE utf8_unicode_ci");
					
				} catch(PDOException $e) {
					if ($this->config['debug'] == 'on') {
						echo 'ERROR: ' . $e->getmessage();
					} else {
						throw $e;
					}
				}
			} else {
				throw new Exception(DATABASE_CONNECTION_ERROR);
			}
		}

		/** Gets a user from the user table
		  * @param $name - name of the user to find
		  * @return - the query result 
			* This can be used to verify the initial status of a login part 1)
		  */
		public function getUserByName($name) {
			if ($this->conn != NULL) {
				$user = $this->conn->query("SELECT * FROM users WHERE username=$name LIMIT 1");
				return $user;
			} else {
				throw new Exception(DATABASE_CONNECTION_ERROR);
			}
		}

		/** Gets a salt from the salt table
		  * @param $name - name of the user to find
		  * @return - the query result 
		  */
		public function getSaltByUser($name) {
			if ($this->conn != NULL) {
				try {	
					$salt = $this->conn->query("SELECT salt FROM users WHERE username=$name LIMIT 1");
					return $salt;
				} catch(PDOException $e) {
					if ($this->config['debug'] == 'on') {
						echo 'ERROR: ' . $e->getmessage();
					} else {
						throw $e;
					}
				}
			} else {
				throw new Exception(DATABASE_CONNECTION_ERROR);
			}
		}

		/** Adds a new user to the database
		  * @param $name - the name of the new users as a string
		  * @param $passworf - the users password as a plaintext string
			* This will be used to generate a new user on the signup page
		  */
		public function addUser($username, $password, $name="", $email="") {
			if ($this->conn != NULL) {
				try {	
					$salt = $this->createSalt();
					$hash = $this->hashPassword($password, $salt);
					//TO DO: user name needs to be escaped of special characters
					if ($this->conn->exec("INSERT INTO `users` (
						`username`, `password`, `salt`, `name`, `email`) VALUES (
						'$username', '$hash', '$salt', '$name', '$email');") != 0) {
					} else {
						throw new Exception(USER_CREATION_ERROR);
					}
				} catch(PDOException $e) {
					if ($this->config['debug'] == 'on') {
						echo 'ERROR: ' . $e->getmessage();
					} else {
						throw $e;
					}
				}
			} else {
				throw new Exception(DATABASE_CONNECTION_ERROR);
			}
		}

		/** Hashes the plaintext passord with the given salt
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

		public function verifyUser($user, $password) {
			if ($this->conn != NULL) {
				foreach ($this->conn->query("SELECT salt FROM `users` WHERE username='$user'") as $return) {
					$salt = $return['salt'];
				}
				if (isset($salt)) {
					foreach ($this->conn->query("SELECT * FROM `users` WHERE username='$user'") as $return) {
						$hashPassword = $return['password'];
					}
					if (isset($hashPassword)) {
						$newhashPassword = $this->hashPassword($password, $salt);
						if ($newhashPassword == $hashPassword) {
							return true;
						} else {
							return false;
						}
					} else {
						throw new Exception(USER_NOT_FOUND_ERROR);
					}
				} else {
					throw new Exception(SALT_NOT_FOUND_ERROR);
				}
			} else {
				throw new Exception(DATABASE_CONNECTION_ERROR);
			}
		}

		/** Function removes a user and their associated salt from the database
		  * @param $username - the username of the uesr to be removed
		  */
		public function removeUser($username) {
			if ($this->conn != NULL) {
				if ($this->conn->exec("DELETE FROM users WHERE username='$username'") != 0) {
					// if ($this->conn->exec("DELETE FROM salts WHERE username='$username'") == 0) {
					// 	throw new Exception(SALT_NOT_DELTED_ERROR);
					// }
				} else {
					throw new Exception(USER_NOT_DELTED_ERROR);
				}
			} else {
				throw new Exception(DATABASE_CONNECTION_ERROR);
			}
		}

		public function newList($username, $listname) {
			if ($this->conn != NULL) {
				$query = $this->conn->prepare("INSERT INTO `lists` (`name`, `username`) VALUES ('$listname', '$username');");
				if(!$query){
					if($this->config['debug'] = 'on'){
						throw new Exception($query->errorInfo());
					}else{
						throw new Exception(LIST_CREATION_ERROR);
					}
				} else {
					$query->execute();
					$listid = $this->getListByName($username, $listname);
					$this->addUserToList($listid, $username);
					return $listid;
				}
			} else {
				throw new Exception(DATABASE_CONNECTION_ERROR);
			}
		}

		public function deletelist($listid) {
			if ($this->conn != NULL) {
				$query = $this->conn->prepare("DELETE FROM `lists` WHERE `listid`='$listid';");
				if(!$query){
					if($this->config['debug'] = 'on'){
						throw new Exception($query->errorInfo());
					}else{
						throw new Exception(LIST_DELETE_ERROR);
					}
				} else {
					$query->execute();
				}
			} else {
				throw new Exception(DATABASE_CONNECTION_ERROR);
			}
		}

		public function getListByName($username, $listname) {
			if ($this->conn != NULL) {
				$query = $this->conn->prepare("SELECT listid FROM `lists` WHERE `name`='$listname' and `username`='$username';");
				if(!$query){
					if($this->config['debug'] = 'on'){
						throw new Exception($query->errorInfo());
					}else{
						throw new Exception(LIST_CREATION_ERROR);
					}
				} else {
					$query->execute();
					return $query->fetch()['listid'];
				}
			} else {
				throw new Exception(DATABASE_CONNECTION_ERROR);
			}	
		}

		public function addUserToList($listid, $username) {
			if ($this->conn != NULL) {
				$query = $this->conn->prepare("INSERT INTO listaccess (`username`, `listid`) VALUES ('$username', '$listid')");
				$query->execute();
			} else {
				throw new Exception(DATABASE_CONNECTION_ERROR);
			}	
		}

		public function checkUserAccess($listid, $username) {
			if ($this->conn != NULL) {
				$query = $this->conn->prepare("SELECT * FROM listaccess WHERE `username`='$username' AND `listid`='$listid'");
				$query->execute();
				$result = $query->fetch();
				if($result != NULL){
					return true;
				} else {
					return false;
				}
			} else {
				throw new Exception(DATABASE_CONNECTION_ERROR);
			}	
		}

		public function removeUserAccess($listid, $username) {
			if ($this->conn != NULL) {
				$query = $this->conn->prepare("DELETE FROM `listaccess` WHERE `username`='$username' AND `listid`='$listid';");
				if(!$query){
					if($this->config['debug'] = 'on'){
						throw new Exception($query->errorInfo());
					}else{
						throw new Exception(LIST_DELETE_ERROR);
					}
				} else {
					$query->execute();
				}
			} else {
				throw new Exception(DATABASE_CONNECTION_ERROR);
			}	
		}

		public function addItemToList($username, $item, $listid, $category, $quantity, $unit) {
			if ($this->conn != NULL) {
				if($this->checkUserAccess($listid, $username)) {
					$query = $this->conn->prepare("INSERT INTO listitems 
						(username, item, listid, category, quantity, unit) VALUES 
						('$username', '$item', '$listid', '$category', '$quantity', $unit)");
					if(!$query){
						if($this->config['debug'] = 'on'){
							throw new Exception($query->errorInfo());
						}else{
							throw new Exception(LIST_DELETE_ERROR);
						}
					} else {
						$query->execute();
					}
				}
			} else {
				throw new Exception(DATABASE_CONNECTION_ERROR);
			}	
		}

		public function removeItemFromList($item, $listid) {
			if ($this->conn != NULL) {
				$query = $this->conn->prepare("DELETE FROM listitems WHERE `item`='$item' AND `listid`='$listid';");
				if(!$query){
					if($this->config['debug'] = 'on'){
						throw new Exception($query->errorInfo());
					}else{
						throw new Exception(LIST_DELETE_ERROR);
					}
				} else {
					$query->execute();
				}
			} else {
				throw new Exception(DATABASE_CONNECTION_ERROR);
			}
		}

		public function getItemsFromList($listid) {
			if ($this->conn != NULL) {
				$query = $this->conn->prepare("SELECT * FROM listitems WHERE `listid`='$listid'");
				$query->execute();
				return $query->fetchAll();
			} else {
				throw new Exception(DATABASE_CONNECTION_ERROR);
			}
		}

		public function getListsFromUser($username) {
			if ($this->conn != NULL) {
				$query = $this->conn->prepare("SELECT * FROM lists WHERE `username`='$username'");
				$query->execute();
				return $query->fetchAll();
			} else {
				throw new Exception(DATABASE_CONNECTION_ERROR);
			}
		}

		public function getListName($listid) {
			if ($this->conn != NULL) {
				$query = $this->conn->prepare("SELECT name FROM lists WHERE `listid`='$listid'");
				$query->execute();
				return $query->fetchAll();
			} else {
				throw new Exception(DATABASE_CONNECTION_ERROR);
			}
		}

		public function getAccessList($listid) {
			if ($this->conn != NULL) {
				$query = $this->conn->prepare("SELECT username FROM listaccess WHERE `listid`='$listid'");
				$query->execute();
				return $query->fetchAll();
			} else {
				throw new Exception(DATABASE_CONNECTION_ERROR);
			}
		}
	}
?>
