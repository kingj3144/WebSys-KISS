<?php
	// if (session_status() == PHP_SESSION_NONE) {
		session_start();
    // }
    if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
    	header("location:./lists.php");
	}else{
		header("location:./sign-in.php");
	}

?>