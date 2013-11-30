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
    <link href="lists.css" rel="stylesheet">
  </head>

  <body>
    <ul class="nav nav-tabs">
      <li><div id="logo"><img src="list.png" alt="KISS" /></div></li>
      <li class="active"><a href="#">Lists</a></li>
      <li><a href="#">Settings</a></li>
      <li><a href="#">Logout</a></li>
    </ul>

    <div id="menu" class="row">  
      <div class="span2">  
        <ul class="nav nav-pills nav-stacked">
        <?php require_once "displayListsPage.php";
              require_once "config.php";
              require_once "database.php";
              $db = new KissDatabase($config);
              $listid = getLists("jenn"); ?>
        </ul>
      </div>
    </div>

  <div id="list">
    <?php getListContent($listid);
          editList(); ?>
  </div>

  <div id="accessList">
    <h4>Access List</h4>
    <p> Test </p>
    <?php addEditors(); ?>
  </div>

    <!-- Javascript -->
    <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
    <script src="./bootstrap/js/js/bootstrap.min.js"></script>
  </body>
</html>
