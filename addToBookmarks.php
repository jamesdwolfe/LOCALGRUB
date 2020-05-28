<?php
require 'config.php';

if (isset($_SESSION['logged_in']) && !empty($_SESSION['logged_in']) && $_SESSION['logged_in'] &&
    isset($_POST['url']) && !empty($_POST['url']) && isset($_POST['img']) && !empty($_POST['img']) &&
    isset($_POST['name']) && !empty($_POST['name']) && isset($_POST['address']) && !empty($_POST['address']) &&
    isset($_POST['region']) && !empty($_POST['region']) && isset($_POST['distance']) && !empty($_POST['distance']) &&
    isset($_POST['categories']) && !empty($_POST['categories']) && isset($_POST['rating']) && !empty($_POST['rating']) &&
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
        $_POST['id'] = mysqli_real_escape_string($mysqli,$_POST['id']);
        $_POST['url'] = mysqli_real_escape_string($mysqli,$_POST['url']);
        $_POST['img'] = mysqli_real_escape_string($mysqli,$_POST['img']);
        $_POST['name'] = mysqli_real_escape_string($mysqli,$_POST['name']);
        $sql="INSERT INTO Bookmarks (userID,businessID,url,img,name,address,region,distance,categories,rating)
        VALUES(" . $userID . ",'".$_POST['id']."','".$_POST['url']."','".$_POST['img']."','".$_POST['name']."','".$_POST['address']."','".$_POST['region']."','".$_POST['distance']."','".$_POST['categories']."','".$_POST['rating']."');";
        $mysqli->query($sql);
        return $sql;
      } else {
        $error = "Username not found.";
      }

} else {
  $error = "Variable undefined.";
}


 ?>
