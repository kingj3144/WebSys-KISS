<?php 

	require_once "database.php";
	//An installation script to set up the mySQL database 
	require_once 'config.php';
	//config.php is a file that will contain the information needed to connect to the database
	try {
		$db = new KissDatabase($config);
		// $db->addUser("Jonah", "Password");
		echo $db->verifyUser("Jonah", "Password");
	} catch(PDOException $e) {
		echo 'ERROR: ' . $e->getmessage();
	}

?>