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
		$db->removeUser("Jonah");
		echo "User remvoed";

	} catch(PDOException $e) {
		echo 'ERROR: ' . $e->getmessage();
	}

?>