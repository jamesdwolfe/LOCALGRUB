<?php
require 'config.php';
	   $newUsernameError="";
	   $newUsernameError2="";

	if (isset($_SESSION['logged_in']) && isset($_SESSION['username']) && !empty($_SESSION['logged_in']) && !empty($_SESSION['username']) &&
      isset($_POST['username']) && isset($_POST['username2']) && !empty($_POST['username']) && !empty($_POST['username2'])) {
			$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

			if($mysqli->connect_errno) {
				echo $mysqli->connect_error;
				exit();
			}

      $sql = "SELECT * FROM Users
            WHERE username = '" . $_POST['username'] . "';";
      $results = $mysqli->query($sql);
			if(!$results) {
				echo $mysqli->error;
				exit();
			}

      if($results->num_rows==0){

    			$sql = "SELECT * FROM Users
    						WHERE username = '" . $_SESSION['username'] . "';";
    			$results = $mysqli->query($sql);
    			if(!$results) {
    				echo $mysqli->error;
    				exit();
    			}

    			if($results->num_rows > 0){
    				$sql = "UPDATE Users SET username = '".$_POST['username']."'
    							WHERE username = '" . $_SESSION['username'] . "';";
    				$mysqli->query($sql);
  					$_SESSION['username'] = $_POST['username'];
  					header('Location: profile.php');
    			}
      } else {
        $newUsernameError="";
   	    $newUsernameError2="*Desired new username already in use.";
      }
	}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="main.css">
    <title>LG</title>
  </head>
  <body>

    <nav class="navbar navbar-expand-lg navbar-dark header">
    <a class="navbar-brand logoname" href="index.php"><img src="assets/logo.png" class="logo" alt="logo">LOCALGRUB</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">

      <form class="form-inline my-2 my-lg-0 ml-auto geocode">
        <input class="form-control mr-2 col-4" type="search" placeholder="City name" aria-label="City name" id="city" name="city">
        <input class="form-control mr-2 col-4" type="number" placeholder="Miles(max:25)" aria-label="Distance (miles)" id="miles" name="miles">
        <button class="btn btn-outline-light my-2 my-sm-0 col-3" type="submit">Search</button>
      </form>

      <?php if(!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']): ?>
      <ul class="nav navbar-nav ml-auto">
          <li class="nav-item"><a href="login.php" class="navbar-nav pull-right nav-link">Sign-In</a></li>
          <li class="nav-item"><a href="register.php" class="navbar-nav pull-right nav-link">Register</a></li>
      </ul>
      <?php else: ?>
      <ul class="nav navbar-nav ml-auto">
          <li class="nav-item"><a href="profile.php" class="navbar-nav pull-right nav-link">Profile</a></li>
          <li class="nav-item"><a href="logout.php" class="navbar-nav pull-right nav-link">Log out</a></li>
      </ul>
      <?php endif; ?>
    </div>
  </nav>

  <div class="logregbox logbox">
  	<img src="assets/logreg.png" alt="logregicon" class="logregicon">
  	<form class="logregform" method="POST" action="updateUsername.php">
  	  New Username: <br>
  	  <input id="username" type="text" name="username"><br>
      <em><h6 id="newUsernameError"><?php if($newUsernameError!=""){ echo($newUsernameError); };?></h6></em>
  	  Re-enter Username: <br>
  	  <input id="username2" type="text" name="username2"><br>
  	  <em><h6 id="newUsernameError2"><?php if($newUsernameError2!=""){ echo($newUsernameError2); };?></h6></em>
  	  <button type="submit">Confirm</button>
	 </form>
  </div>

  <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script>
    var $key="<?php echo(GOOGLE_API_KEY); ?>";


		$(".logregform").on("submit",function(event){
			if($("#username").val()==null || $("#username").val().trim()==""){
					event.preventDefault();
					$("#newUsernameError").text("*New username field is empty.");
			} else {
					$("#newUsernameError").text("");
			}
			if($("#username2").val()==null || $("#username2").val().trim()==""){
					event.preventDefault();
					$("#newUsernameError2").text("*Re-enter new username field is empty.");
			} else {
					$("#newUsernameError2").text("");
			}
      if($("#username").val().trim().length>0 && $("#username2").val().trim().length>0 && $("#username").val().trim()!=$("#username2").val().trim()){
          event.preventDefault();
          $("#newUsernameError").text("");
	        $("#newUsernameError2").text("*New username fields do not match.");
      }
		});

    $(document).on("submit", ".geocode", function(event) {
        event.preventDefault();
        var $city = $('#city').val();
        var $miles = $('#miles').val();
        window.location.replace("index.php?city="+$city+"&miles="+$miles);
    });
  </script>
  </body>
  </html>
