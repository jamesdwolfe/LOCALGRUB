<?php
 require 'config.php';

 if (isset($_SESSION['logged_in']) && !empty($_SESSION['logged_in']) && $_SESSION['logged_in'] &&
     isset($_POST['lat']) && !empty($_POST['lat']) && isset($_POST['lng']) && !empty($_POST['lng']) &&
     isset($_POST['city']) && !empty($_POST['city']) && isset($_POST['miles']) && !empty($_POST['miles'])) {

       $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

       if($mysqli->connect_errno) {
         echo $mysqli->connect_error;
         exit();
       }

       $sql = "SELECT * FROM Users
             WHERE username = '" . $_SESSION['username'] . "';";

       $results = $mysqli->query($sql);

       if(!$results) {
         echo $mysqli->error;
         exit();
       }

       if($results->num_rows > 0) {
         $row = $results->fetch_assoc();
         $userID=$row['userID'];
         $sql="INSERT INTO Searches (userID,lat,lng,city,miles)
         VALUES(" . $userID . ",'".$_POST['lat']."','".$_POST['lng']."','".$_POST['city']."'," . $_POST['miles'] . ");";
         $mysqli->query($sql);
       } else {
         $error = "Username not found.";
       }

 } else {
   $error = "Variable undefined.";
 }


  ?>
