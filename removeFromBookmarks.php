<?php
require 'config.php';

if (isset($_SESSION['logged_in']) && !empty($_SESSION['logged_in']) && $_SESSION['logged_in'] &&
    isset($_POST['id']) && !empty($_POST['id'])) {

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
        $sql="DELETE FROM Bookmarks WHERE userID=" .  $userID . " AND businessID='" . $_POST['id'] . "';";
        $mysqli->query($sql);
      } else {
        $error = "Username not found.";
      }

} else {
  $error = "Variable undefined.";
}


 ?>
