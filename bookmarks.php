<?php
require('config.php');

if (isset($_SESSION['logged_in']) && !empty($_SESSION['logged_in']) && $_SESSION['logged_in']) {

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
        $sql = "SELECT * FROM Bookmarks WHERE userID=".$userID." ORDER BY bookmarkID DESC;";
        $results = $mysqli->query($sql);
      } else {
        $error = "Username not found.";
      }

} else {
  $error = "Variable undefined.";
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
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
        <input class="form-control mr-2 col-4" type="number" placeholder="Miles(max:25)" aria-label="Miles(max:25)" id="miles" name="miles">
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

  <div id="bookmarksWrapper">
    <div id="bookmarksHeader"><?php echo(ucfirst($_SESSION['username']));?>'s Bookmarks</div>
  </div>

<div id="results">
  <?php while ( $row = $results->fetch_assoc()) : ?>
    <div class="card" value="<?php echo($row['businessID']);?>">
      <a href="<?php echo($row['url']);?>" class="card_url"><img class="card-img-top card_img" src="<?php echo($row['img']);?>"></a>
      <div class="card-body">
        <h3 class="card-title card_name"><?php echo($row['name']);?></h3>
          <p class="card-text"><strong>Location: <br></strong>
            <span class="card_address"><?php echo($row['address']);?></span><br>
            <span class="card_region"><?php echo($row['region']);?></span><br></p>
          <p class="card-text"><strong>Food categories:<br></strong>
            <span class="card_categories"><?php echo($row['categories']);?></span></p>
          <p class="card-text"><strong>Rating:<br></strong>
            <span class="card_rating"><?php echo($row['rating']);?></span></p>
      </div>
      <img src = "assets/bookmarked.png" style="cursor:pointer;" class="bookmarkImg">
    </div>
  <?php endwhile; ?>
</div>

  <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script>

    var $key="<?php echo(GOOGLE_API_KEY); ?>";

    $(document).on("submit", ".geocode", function(event) {
        event.preventDefault();
        var $city = $('#city').val();
        var $miles = $('#miles').val();
        window.location.replace("index.php?city="+$city+"&miles="+$miles);
    });

  $(document).on("click",".bookmarkImg",function(event){
    event.stopImmediatePropagation();
    event.preventDefault();
    $bookmarkImg = $(this);
    $parent = $(this).parent();
    $id = $parent.attr('value');

    if($bookmarkImg.attr("src")=="assets/not_bookmarked.png"){
      $name = $parent.find(".card_name").text();
      $url = $parent.find(".card_url").attr('href');
      $img = $parent.find(".card_img").attr('src');
      $address = $parent.find(".card_address").text();
      $region = $parent.find(".card_region").text();
      $distance = $parent.find(".card_distance").text();
      $categories = $parent.find(".card_categories").text();
      $rating = $parent.find(".card_rating").text();
      $.post('addToBookmarks.php',{id:$id,url:$url,img:$img,name:$name,address:$address,
      region:$region, distance:$distance, categories:$categories, rating:$rating},function(response){
        $bookmarkImg.attr("src","assets/bookmarked.png");
      });
    } else {
      $.post('removeFromBookmarks.php',{id:$id},function(response){
        $bookmarkImg.attr("src","assets/not_bookmarked.png");
      });
    }
  });

  </script>

</body>
</html>
