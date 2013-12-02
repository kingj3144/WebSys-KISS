<?php
// if (session_status() == PHP_SESSION_NONE) {
    session_start();
// }
if (!isset($_SESSION['loggedin']) || !$_SESSION['loggedin'] == true) {
  header("location:./index.php");
}
require_once "config.php";
require_once "database.php";

if(isset($_POST['addItem'])) {
	if (isset($_POST['itemName']) && isset($_POST['listid'])) {
		try {
			$db = new KissDatabase($config);
			$db->addItemToList($_SESSION['username'], 
				$_POST['itemName'], 
				$_POST['listid'], 
				null, 
				$_POST['quantity'], 
				$_POST['unit']);
				$db->close();
		} catch(Exception $e) {
			echo "ERROR: " . $e->getmessage();
		}
	}
} elseif (isset($_POST['addUser'])) {
	try {
		$db = new KissDatabase($config);
		$db->addUserToList($_POST['listid'], $_POST['username']);
	} catch(Exception $e) {
			echo "ERROR: " . $e->getmessage();
	}
} elseif (isset($_POST['addList'])) {
	try {		
		$db = new KissDatabase($config);
		$db->newList($_SESSION['username'], $_POST['listName']);
	} catch(Exception $e) {
			echo "ERROR: " . $e->getmessage();
	}
}
header("location:./lists.php?listid=" .$_POST['listid'] );
?>