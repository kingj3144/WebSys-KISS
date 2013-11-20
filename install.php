<?php 
//An installation script to set up the mySQL database 
  require 'config.php';
  //config.php is a file that will contain the information needed to connect to the database
  
  try {
    $conn = new PDO('mysql:host='.$config['host'].';dbname='.$config['db_name'],$config['db_username'], $config['db_password']);
  } catch(PDOException $e) {
    echo 'ERROR: ' . $e->getmessage();
  }


?>
