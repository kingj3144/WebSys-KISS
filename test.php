<?php 

	require_once "database.php";
	//An installation script to set up the mySQL database 
	require_once 'config.php';
	//config.php is a file that will contain the information needed to connect to the database
	try {
		echo "Setting up database <br>";
		$db = new KissDatabase($config);
		echo "Adding user <br>";
		$db->addUser("Jonah", "Password");
		if( !$db->verifyUser("Jonah", "Password") ){
			throw new Exception("verifyUser failed on positive test");
		} else {
			echo "User verified <br>";
		}
		if ($db->verifyUser("Jonah", "derp")) {
			throw new Exception("verifyUser failed on negative test");
		} else {
			echo "Improper verification denied <br>";
		}
		echo "Adding new list <br>";
		$listid = $db->newList("Jonah", "Groceries");

		if($listid != $db->getListByName("Jonah", "Groceries")){
			throw new Exception("Failed to get list");
		} else {
			echo "Get list successful<br>";
		}

		$db->addUser("Jon", "password");
		$db->addUserToList($listid, "Jon");
		if ( !$db->checkUserAccess($listid, "Jon") ){
			throw new Exception("Failed to get list Access");
		}
		if( $db->checkUserAccess($listid, "Joon") ) {
			throw new Exception("List Access failed");
		}

		$db->removeUserAccess($listid, "Jon");

		echo "Removing list <br>";
		$db->deleteList($listid);

		echo "Removing user <br>";	
		$db->removeUser("Jonah");
		$db->removeUser("Jon");
		echo "User remvoed<br>";


		$db->close();
		echo "Test Complete <br>";
	} catch(Exception $e) {
		echo 'ERROR: ' . $e->getmessage();
	}
?>