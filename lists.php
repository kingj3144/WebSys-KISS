<?php
// if (session_status() == PHP_SESSION_NONE) {
    session_start();
    // }
if (!isset($_SESSION['loggedin']) || !$_SESSION['loggedin'] == true) {
  header("location:./index.php");
}
require_once "config.php";
require_once "database.php";

#Get lists from listid
function getListContent($listid) {
  try {
    require "config.php";
    $db = new KissDatabase($config);
  }
  catch (Exception $e) {
    echo "Error: " . $e->getMessage();
  }

  if (isset($_GET['listid'])) {
    echo "<h4>" . $db->getListName($_GET['listid']) .
          "<a href=\"remove.php?listid=" . $_GET['listid'] . "\">
          <i class=\"icon-trash\"></i></a></h4>";
    echo "<table class='table table-condensed'>";

    foreach($db->getItemsFromList($_GET['listid']) as $row) {
      echo "<tr><td>" . $row['item'] . "</td><td>" . $row['quantity'] . 
              " " . $row['unit'] . "</td><td><a href=\"remove.php?itemid=" . 
              $row['itemid'] . "\"><i class=\"icon-trash\">
              </i></a></td></tr>";
    }
  } 
  else {
    echo "<h4>" . $db->getListName($listid) . 
          "<a href=\"remove.php?listid=" . $listid. "\">
          <i class=\"icon-trash\"></i></a></h4>";
    echo "<table class='table table-condensed'>";

    foreach($db->getItemsFromList($listid) as $row) {
      echo "<tr><td>" . $row['item'] . "</td><td>" . $row['quantity'] . 
              " " . $row['unit'] . "</td><td><a href=\"remove.php?itemid=" . 
              $row['itemid'] . "\"><i class=\"icon-trash\">
              </i></a></td></tr>";
    }
  }
  echo "</table>";
}

# get access list
function getAccessList($listid) {
    try {
    require 'config.php';
    $db = new KissDatabase($config);
  }
  catch (Exception $e) {
    echo "Error: " . $e->getMessage();
  }

  echo "<h4>Access List</h4><ul>";

  if (isset($_GET['listid'])) {
    foreach($db->getAccessList($_GET['listid']) as $row) {
      $username = "<li>" . $row['username'];
      if ($_SESSION['username'] != $row['username']) {
        $username .= "<a href='remove.php?user=" . $row['username'] . "&listid=" 
                      . $_GET['listid'] . "'><i class='icon-trash'>
                      </i></a></td></tr>";
      }
      $username .= "</li>";
      echo $username;
    }
  }
  else {
    foreach($db->getAccessList($listid) as $row) {
      $username = "<li>" . $row['username'];
      if ($_SESSION['username'] != $row['username']) {
        $username .= "<a href='remove.php?user=" . $row['username'] . 
                      "&listid=" . $listid . "'><i class='icon-trash'>
                      </i></a></td></tr>";
      }
      $username .= "</li>";
      echo $username;
    }
  }

  echo "</ul>";
}

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>KISS - Lists</title>
    <!-- Bootstrap core CSS -->
    <link href="./bootstrap/css/bootstrap.css" rel="stylesheet">
    <!-- <link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap-glyphicons.css" rel="stylesheet"> -->
    <link href="lists.css" rel="stylesheet">
  </head>

  <body>
    <ul class="nav nav-tabs">
      <li><div id="logo"><img src="list.png" alt="KISS" /></div></li>
      <li class="active"><a href="./lists.php">Lists</a></li>
      <li><a href="./settings.php">Settings</a></li>
      <li><a href="./logout.php">Logout</a></li>
    </ul>

    <div id="menu" class="row">  
      <div class="span2">  
        <ul class="nav nav-pills nav-stacked">
        <?php require_once "displayListsPage.php";
              require_once "config.php";
              require_once "database.php";
              $db = new KissDatabase($config);
              // CHANGE to session username
              $listid = getLists($_SESSION['username']); ?>
        </ul>
        <?php addNewList(); ?>
      </div>
    </div>

  <div id="list">
    <div id="listContent">
      <?php getListContent($listid); ?>
      <!-- list generated here -->
    </div>
    <div id="listForms">
      <?php editList($listid); ?>
    </div>
  </div>

  <div id="accessList">
    <div id="accessContent">
      <?php getAccessList($listid); ?>
    </div>
    <div id="accessForms">
      <?php addEditors($listid); ?>
    </div>
  </div>

  <!-- Javascript -->
  <script src="generateLists.js"></script>
  <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
  <script src="./bootstrap/js/js/bootstrap.min.js"></script>
  </body>
</html>
