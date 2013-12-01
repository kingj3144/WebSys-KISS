<?php
  //Here is the bulk of the sign-in/sign-up page. Here we will render the the user the opportunity to 
  //  do either action. Note that the idea as of now is to build this modularly so that we can make
  //  a single page and simply render each php file as needed. This might change in the future.
  
	require_once 'database.php';
	require 'config.php';
	if (session_status() == PHP_SESSION_NONE) {
		session_start();
    }
    if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
    	header("location:./index.php");
    }

	try {
		// Create connection to database
		$db = new KissDatabase($config);
	} catch(PDOException $e) {
		echo 'ERROR: ' . $e->getmessage();
	}
	//now we handle the first case in that the user is signing in not signing up.
	if (isset($_POST['login']) && $_POST['login'] == 'Login') {
		$uname = $_POST['username'];
		$pass = $_POST['pass'];
		try{
			//verify the user
			//verifyUser(username, password) returns either true or false
			$login = $db->verifyUser($uname, $pass);
			//getUserByName(username) returns a user object from mysql tables
		} catch(PDOException $e) {
			echo 'ERROR: ' . $e->getmessage();
		}
		
		if ($login == true){
			$user = $db->getUserByName($uname);
			echo var_dump($user);
			$_SESSION['username'] = $user['username'];
			$_SESSION['name']     = $user['name'];
			$_SESSION['email']    = $user['email'];
			$_SESSION['loggedin'] = true;
		}
		else {
			echo "Incorrect Username or Password!";
		}
	}
	//now for the signup case...
	else if (isset($_POST['signup']) && $_POST['signup'] == 'Signup') {
		if (!isset($_POST['email']) || 
			!isset($_POST['name']) || 
			!isset($_POST['uname']) || 
			!isset($_POST['pass']) || 
			!isset($_POST['verify_pass']) 
			|| empty($_POST['pass']) 
			|| empty($_POST['verify_pass'])) {

				$msg = "Please fill in all form fields.";
		}
		else if ($_POST['pass'] != $_POST['verify_pass']){
				$msg = "Passwords must match.";
		}
		else {
			//call addUser(username, password, name, email)
			try {
				$db->addUser($_POST['uname'], $_POST['pass'],$_POST['name'],$_POST['email']);
				$db->newList($_POST['uname'], "Groceries");
				$msg = "Account Created.";
				$user = $db->getUserByName($_POST['uname']);
				$_SESSION['username'] = $user['username'];
				$_SESSION['name']     = $user['name'];
				$_SESSION['email']    = $user['email'];
				$_SESSION['loggedin'] = true;
			} catch(PDOException $e) {
				echo 'ERROR: ' . $e->getmessage();
			}
		}
	}

	if(isset($_SESSION['username'])) {
		header('Location: index.php');
		exit();
	}

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Kiss Signup/Login page">
    <meta name="author" content="Tornado Sharks">

    <title>Login or Sign Up for KISS</title>

    <!-- Bootstrap core CSS -->
    <link href="resources/css/bootstrap/bootstrap.css" rel="stylesheet" />

    <!-- Custom styles for this template -->
    <link href="resources/css/bootstrap/signin.css" rel="stylesheet" />
		
		<!-- Master style sheet for the page -->
		<link href="resources/css/master.css" rel="stylesheet" />
  </head>

  <body>
		<img src="resources/css/images/fullName.png" class="masthead" alt="Kitchen Inventory Supply System" />
		<div class="sign-in">
			
    	<div class="container">

      <form class="form-signin" method="post">
        <h2 class="form-signin-heading">Please sign in</h2>
        <input type="text" class="form-control" placeholder="Username" name="username" autofocus/>
        <input type="password" name="pass" class="form-control" placeholder="Password"/>
        <label class="checkbox">
          <input type="checkbox" value="remember-me"/> Remember me
        </label>
        <button class="btn btn-lg btn-primary btn-block button" type="submit" name="login" value="Login">Sign in</button>
      </form>

    	</div> 
		</div>
		<div class="sign-up">
			<div class="container">

      <form class="form-signin" method="post">
        <h2 class="form-signin-heading">Please create an account:</h2>
				<input type="text" class="form-control" placeholder="Email Address" name="email"/>
				<input type="text" class="form-control" placeholder="Name" name="name" />
				<input type="text" class="form-control" placeholder="Username" name="uname" />
        <input type="password" class="form-control" placeholder="Password" name="pass" />
				<input type="password" class="form-control" placeholder="Verify Password" name="verify_pass" />
        <button class="btn btn-lg btn-primary btn-block button" type="submit" name="signup" value="Signup">Create Account</button>
      </form>

    </div>
		</div>
		<footer>
			<img src="resources/css/images/logo.png" class="logo" />
		</footer>
  </body>
</html>
