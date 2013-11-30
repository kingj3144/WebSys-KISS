<?php
	if (session_status() == PHP_SESSION_NONE) {
		session_start();
    }
	if (isset($_SESSION['loggedin'])) {
		unset($_SESSION['loggedin']);
	}
	if (isset($_SESSION['username'])){
		unset($_SESSION['username']);
	}
	session_destroy();
	// echo "Logged out";
    header("location:./index.php");
?>