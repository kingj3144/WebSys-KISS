<?php
// if (session_status() == PHP_SESSION_NONE) {
    session_start();
// }
if (!isset($_SESSION['loggedin']) || !$_SESSION['loggedin'] == true) {
  header("location:./index.php");
}
require_once "config.php";
require_once "database.php";

if (isset($_GET['itemid'])) {
	try {
		$db = new KissDatabase($config);
		$db->removeItemFromList($_GET['itemid']);
	} catch(Exception $e) {
		echo "ERROR: " . $e->getmessage();
	}
	header("location:./lists.php?listid=".$_GET['listid']);
}
elseif (isset($_GET['user'])) {
	try {
		$db = new KissDatabase($config);
		$db->removeUserAccess($_GET['listid'], $_GET['user']);
	} catch(Exception $e) {
		echo "ERROR: " . $e->getmessage();
	}
	header("location:./lists.php?listid=".$_GET['listid']);
}
elseif (isset($_GET['listid'])) {
	try {
		$db = new KissDatabase($config);
		$db->deletelist($_GET['listid']);
	} catch(Exception $e) {
		echo "ERROR: " . $e->getmessage();
	}
	header("location:./lists.php");
}

?>