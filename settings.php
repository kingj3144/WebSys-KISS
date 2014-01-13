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
    try {
      $db = new KissDatabase($config);
      // CHANGE to session username
      if (isset($_POST['changeSettings'])){
        if(isset($_POST['name']) && $_POST['name'] != $_SESSION['name']){
          $db->updateName($_SESSION['username'], $_POST['name']);
          $_SESSION['name'] = $_POST['name'];
        }
        if(isset($_POST['email']) && $_POST['email'] != $_SESSION['email']){
          $db->updateEmail($_SESSION['username'], $_POST['email']);
          $_SESSION['email'] = $_POST['email'];
        }
      }

      if (isset($_POST['changePassword'])){
        if(isset($_POST['oldPassword']) && $_POST['newPassword'] == $_SESSION['confirmPassword']){
          if($db->verifyUser($_SESSION['username'], $_POST['oldPassword'])) {
            $db->
          }
        }
        if(isset($_POST['email']) && $_POST['email'] != $_SESSION['email']){
          $db->updateEmail($_SESSION['username'], $_POST['email']);
          $_SESSION['email'] = $_POST['email'];
        }
      }
    } catch (Execption $e) {
      echo "ERROR: " . $e->getmessage();
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
    <title>KISS - Settings</title>
    <!-- Bootstrap core CSS -->
    <link href="./bootstrap/css/bootstrap.css" rel="stylesheet">
    <link href="lists.css" rel="stylesheet">
  </head>

  <body>
    <ul class="nav nav-tabs">
      <li><div id="logo"><img src="list.png" alt="KISS" /></div></li>
      <li><a href="./lists.php">Lists</a></li>
      <li class="active"><a href="./settings.php">Settings</a></li>
      <li><a href="./logout.php">Logout</a></li>
    </ul>
    <div class="content">
      <div class="row">
        <div class="span5">
          <div class="settings">
            <form class="form-inline" role="form" action="settings.php" method="post">
              <div class="form-group">
                <label for="nameInput" class="span1"> Name: </label> 
                <input type="text" id="nameInput" name="name" value="<?php echo $_SESSION['name'] ?>" >
                <br>
                <label for="emailInput" class="span1"> Email: </label>
                <input type="text" id="emailInput" name="email" value="<?php echo $_SESSION['email'] ?>" >
                <br>
                <div class="span2">Username:  <?php echo $_SESSION['username'] ?> </div>
                <br>
                <div class="span1"></div><input type="submit" class="btn btn-default" name="changeSettings" value="Change">
              </div>
            </form>
            <form class="form-inline" role="form" action="settings.php" method="post">
              <div class="form-group">
                Change Password
                <label for="oldPassword" class="span1"> OldPassword: </label> 
                <input type="text" id="oldPassword" name="oldPassword">
                <br>
                <label for="newPassword" class="span1"> Email: </label>
                <input type="text" id="newPassword" name="newPassword">
                <br>
                <label for="confirmPassword" class="span1"> Email: </label>
                <input type="text" id="confirmPassword" name="confirmPassword">
                <br>
                <div class="span1"></div><input type="submit" class="btn btn-default" name="changePassword" value="Change">
              </div>
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
