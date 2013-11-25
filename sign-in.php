<?php
  //Here is the bulk of the sign-in/sign-up page. Here we will render the the user the opportunity to 
  //  do either action. Note that the idea as of now is to build this modularly so that we can make
  //  a single page and simply render each php file as needed. This might change in the future.
  
  require 'database.php';
	
	
	
  
  
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Kiss Signup/Login page">
    <meta name="author" content="Tornado Sharks">
<!--    <link rel="shortcut icon" href="../../assets/ico/favicon.png">-->

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
        <input type="text" class="form-control" placeholder="Username" autofocus/>
        <input type="password" class="form-control" placeholder="Password"/>
        <label class="checkbox">
          <input type="checkbox" value="remember-me"/> Remember me
        </label>
        <button class="btn btn-lg btn-primary btn-block button" type="submit">Sign in</button>
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
        <button class="btn btn-lg btn-primary btn-block button" type="submit">Create Account</button>
      </form>

    </div>
		</div>
		<footer>
			<img src="resources/css/images/logo.png" class="logo" />
		</footer>
  </body>
</html>
