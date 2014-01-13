<?php 
	define('DATABASE_CONNECTION_ERROR', "Not connected to the database");
	define('USER_CREATION_ERROR', "User could not be created");
	define('USER_NOT_FOUND_ERROR' , "User could not be found");
	define('USER_NOT_DELTED_ERROR' , "User could not be deleted");
	define('SALT_NOT_FOUND_ERROR' , "Salt could not be found");
	define('SALT_NOT_DELTED_ERROR' , "Salt could not be deleted");
	define('LIST_CREATION_ERROR' , "List could not be created");
	define('LIST_DELETE_ERROR', "List could not be deleted");
	define('USER_ACCESS_ERROR' , "The users does not have access to do that");

	class KissDatabase
	{

		private $conn = NULL;

		private $config = NULL;

		/** Constructs the database interface
		  */
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
						isAdmin BOOLEAN NOT NULL DEFAULT 0,
						) COLLATE utf8_unicode_ci");

					$this->conn->exec("CREATE TABLE IF NOT EXISTS lists (
						listid INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
						name VARCHAR(32) NOT NULL,
						username VARCHAR(32) NOT NULL,
						FOREIGN KEY(username) REFERENCES users(username)
						) COLLATE utf8_unicode_ci");

					$this->conn->exec("CREATE TABLE IF NOT EXISTS listitems (
						itemid INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
						username VARCHAR(32) NOT NULL,
						item VARCHAR(64) NOT NULL,
						quantity INT,
						unit VARCHAR(16),
						listid INT NOT NULL,
						category VARCHAR(32),
						time DATETIME,
						FOREIGN KEY(username) REFERENCES users(username),
						FOREIGN KEY(listid) REFERENCES lists(listid)
						) COLLATE utf8_unicode_ci");

					$this->conn->exec("CREATE TABLE IF NOT EXISTS listaccess ( 
						username VARCHAR(32) NOT NULL,
						listid INT NOT NULL,
						FOREIGN KEY(username) REFERENCES users(username),
						FOREIGN KEY(listid) REFERENCES lists(listid)
						) COLLATE utf8_unicode_ci");

					$this->conn->exec("CREATE TABLE IF NOT EXISTS blog ( 
						blogid INT PRIMARY KEY,
						username VARCHAR(32) NOT NULL,
						message TEXT,
						time DATETIME DEFAULT CURRENT_TIMESTAMP,
						FOREIGN KEY(username) REFERENCES users(username),
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
				$query = $this->conn->prepare("SELECT * FROM `users` WHERE `username`='$name' LIMIT 1");
				$query->execute();
				if($query){
					return $query->fetch();
				} else {
					throw new Exception(USER_NOT_FOUND_ERROR);
					
				}
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

		/** Verifies that the user is in the database and that the provided password is correct
		  * @param $user - username to check 
		  * @param $password - Plaintext password to check
		  * @return boolean of verification state
		  */
		public function verifyUser($user, $password) {
			if ($this->conn != NULL) {
				$query = $this->conn->prepare("SELECT salt FROM `users` WHERE username='$user'");
				$query->execute();

				$s = $query->fetch();
				$salt = $s['salt'];

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

		/** Function checks is the users has administrative privileges
		  * @param $username - the username of the user to be removed
		  */
		public function isAdmin($username) {
			if ($this->conn != NULL) {
				$query = $this->conn->prepare("SELECT * FROM `users` WHERE username='$user' AND admin=1");
				$query->execute();
				$a = $query->fetch();
				if($a) {
					return True;
				} else {
					return False;
				}
			} else {
				throw new Exception(DATABASE_CONNECTION_ERROR);
			}
		}

		/** Function removes a user and their associated salt from the database
		  * @param $username - the username of the user to be removed
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

		/** Creates a new list with the provided users as the owner, also gives the owner access to this list
		  * @param $username - Username of the creator/owner of the new list
		  * @param $listname - Name that will be displayed when the list is accessed
		  * @return the listid of the created list
		  */
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

		/** Removes all items from a list, all access to the list, and removes the list
		  * @param $listid - the listid of the list to be removed
		  */
		public function deletelist($listid) {
			if ($this->conn != NULL) {

				$query = $this->conn->prepare(
					"DELETE FROM `listitems` WHERE `listid`='$listid';
					DELETE FROM `listaccess` WHERE `listid`='$listid';
					DELETE FROM `lists` WHERE `listid`='$listid';");
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

		/** Given the username of the owner and the name of the list finds the listid
		  * @param $username - the username of hte list owner
		  * @param $listname - the name of the list
		  * @return - the listid of the list if found
		  */
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
					$r= $query->fetch();
					return $r['listid'];
				}
			} else {
				throw new Exception(DATABASE_CONNECTION_ERROR);
			}	
		}

		/** Gives a users access to a list
		  * @param $listid - The listid of the list
		  * @param $username - The username of the users to grant access
		  */
		public function addUserToList($listid, $username) {
			if ($this->conn != NULL) {
				$query = $this->conn->prepare("INSERT INTO listaccess (`username`, `listid`) VALUES ('$username', '$listid')");
				$query->execute();
			} else {
				throw new Exception(DATABASE_CONNECTION_ERROR);
			}	
		}

		/** Checks if a user ahs access to a list
		  * @param $listid - The list to check
		  * @param $username - The username to check
		  * @return boolean - ture for access, false otherwise
		  */
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

		/** Removes access to a list for a user
		  * @param $listid - The list
		  * @param $username - The username
		  */
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

		/** Adds a new item to a given list, if that users has access to the list
		  * @param $username - The username o f the user trying to add an item
		  * @param $item - The name of the item to add
		  * @param $listid - The listid of the list that will contain the item
		  * @param $category - The category of the item, defaults to null
		  * @param $quantity - The amount of the item
		  * @param $unit - The unit to use when displaying the quantity
		  */
		public function addItemToList($username, $item, $listid, $category=NULL, $quantity, $unit) {
			if ($this->conn != NULL) {
				if($this->checkUserAccess($listid, $username)) {
					$query = $this->conn->prepare("INSERT INTO `listitems` 
						(`itemid`, `username`, `item`, `quantity`, `unit`, `listid`, `category`, `time`)
						 VALUES (NULL, '$username', '$item', '$quantity', '$unit', '$listid', '$category', NULL);");
					if(!$query){
						if($this->config['debug'] = 'on'){
							throw new Exception($query->errorInfo());
						}else{
							throw new Exception(LIST_ITEM_ADD_ERROR);
						}
					} else {
						$query->execute();
					}
				} else {
					throw new Exception(USER_ACCESS_ERROR);
				}
			} else {
				throw new Exception(DATABASE_CONNECTION_ERROR);
			}	
		}

		/** Removes an item from a list based on itemid
		  * @param $itemid - The itemid of the item to be removed
		  */
		public function removeItemFromList($itemid) {
			if ($this->conn != NULL) {
				$query = $this->conn->prepare("DELETE FROM listitems WHERE `itemid`='$itemid';");
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

		/** Get all the items from a given list
		  * @param $listid - The listid number
		  * @return array - an array with the results from the query
		  */
		public function getItemsFromList($listid) {
			if ($this->conn != NULL) {
				$query = $this->conn->prepare("SELECT * FROM listitems WHERE `listid`='$listid'");
				$query->execute();
				return $query->fetchAll();
			} else {
				throw new Exception(DATABASE_CONNECTION_ERROR);
			}
		}

		/** Gets all lists a user has access to
		  * @param $username 
		  * @return array - an array with the results from the query
		  */
		public function getListsFromUser($username) {
			if ($this->conn != NULL) {
				$query = $this->conn->prepare("SELECT * FROM listaccess WHERE `username`='$username'");
				$query->execute();
				return $query->fetchAll();
			} else {
				throw new Exception(DATABASE_CONNECTION_ERROR);
			}
		}

		/** Given the listid get the list name
		  * @param $listid - The listid of the list
		  * @return string - the name of the list
		  */
		public function getListName($listid) {
			if ($this->conn != NULL) {
				$query = $this->conn->prepare("SELECT name FROM lists WHERE `listid`='$listid'");
				$query->execute();
				$r = $query->fetch();
				return $r['name'];
			} else {
				throw new Exception(DATABASE_CONNECTION_ERROR);
			}
		}

		/** Gets the usernames of all users who have access to the list
		  * @param $listid - The listid of the list
		  * @return array - array of usernames 
		  */
		public function getAccessList($listid) {
			if ($this->conn != NULL) {
				$query = $this->conn->prepare("SELECT username FROM listaccess WHERE `listid`='$listid'");
				$query->execute();
				return $query->fetchAll();
			} else {
				throw new Exception(DATABASE_CONNECTION_ERROR);
			}
		}

		/** Changes the user's email
		  * @param $username - username of the user
		  * @param $email - The new email for the user
		  */
		public function updateEmail($username, $email) {
			if ($this->conn != NULL) {
				$query = $this->conn->prepare("UPDATE `users` SET `email`='$email' WHERE `username`='$username'");
				$query->execute();
			} else {
				throw new Exception(DATABASE_CONNECTION_ERROR);
			}	
		}

		/** Changes the user's name
		  * @param $username - username of the user
		  * @param $name - The new name for the user
		  */
		public function updateName($username, $name) {
			if ($this->conn != NULL) {
				$query = $this->conn->prepare("UPDATE `users` SET `name`='$name' WHERE `username`='$username'");
				$query->execute();
			} else {
				throw new Exception(DATABASE_CONNECTION_ERROR);
			}	
		}

		/** Checks if a user is the owner of a list
		  * @param $username - Username of the user
		  * @param listid - The listid of the list
		  * @return boolean - True if the uses is the owener, false otherwise
		  */
		public function isOwner($username, $listid) {
			if ($this->conn != NULL) {
				$query = $this->conn->prepare("SELECT * FROM lists WHERE `username`='$username' AND `listid`='$listid'");
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
	}
?>
