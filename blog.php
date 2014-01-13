<?php 
// if (session_status() == PHP_SESSION_NONE) {
    session_start();
    // }
    if (!isset($_SESSION['loggedin']) || !$_SESSION['loggedin'] == true) {
      header("location:./index.php");
    }
    require_once "displayListsPage.php";
    require_once "config.php";
    require_once "database.php";
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>KISS - Blog</title>
    <!-- Bootstrap core CSS -->
    <link href="./bootstrap/css/bootstrap.css" rel="stylesheet">
    <link href="lists.css" rel="stylesheet">
  </head>

  <body>
    <ul class="nav nav-tabs">
      <li><div id="logo"><img src="list.png" alt="KISS" /></div></li>
      <li><a href="./lists.php">Lists</a></li>
      <li><a href="./settings.php">Settings</a></li>
      <li class="active"><a href="./blog.php">Blog</a></li>
      <li><a href="./logout.php">Logout</a></li>
    </ul>
    <div class="content">
      <div class="row">
        <div class="span5">
          <div class="settings">
            <form class="form-inline" role="form" action="settings.php" method="post">
             <?php
             //Blog posts go here
             ?>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Javascript -->
    <script src="generateLists.js"></script>
    <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
    <script src="./bootstrap/js/js/bootstrap.min.js"></script>
  </body>
</html>
