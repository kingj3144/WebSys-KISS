<?php 

	require_once "database.php";
	//An installation script to set up the mySQL database 
	require_once 'config.php';
	//config.php is a file that will contain the information needed to connect to the database
	try {
		$db = new KissDatabase($config);
		$db->addUser("Jonah", "Password");
	} catch(PDOException $e) {
		echo 'ERROR: ' . $e->getmessage();
	}

?>
$2a$07$y4l1Wuyze4AX8jI0HUiArU