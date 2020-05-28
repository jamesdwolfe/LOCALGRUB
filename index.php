<?php require('config.php'); ?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="main.css">
    <link id="mapstyle" rel="stylesheet" type="text/css" href="map.css">
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

<div id="map"></div>
<div id="results"></div>
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

<script>
  var $key="<?php echo(GOOGLE_API_KEY); ?>";
  var map;
  var cityCircle;
  var click = false;

   <?php if(isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
   var bookmarks = [];
    <?php
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
        $sql = "SELECT * FROM Bookmarks WHERE userID=".$userID.";";
        $results = $mysqli->query($sql);
      }
     ?>
     <?php while($row=$results->fetch_assoc()):?>
      bookmarks.push("<?php echo($row['businessID']);?>");
     <?php endwhile; ?>
   <?php endif; ?>

  function initMap() {
    map = new google.maps.Map(document.getElementById('map'), {
      zoomControl: true,
      mapTypeControl: false,
      scaleControl: true,
      streetViewControl: false,
      rotateControl: true,
      fullscreenControl: false,
      center: {lat: 39.72, lng: -101.39},
      zoom: 3.5,
      styles: [
           {elementType: 'geometry', stylers: [{color: '#242f3e'}]},
           {elementType: 'labels.text.stroke', stylers: [{color: '#242f3e'}]},
           {elementType: 'labels.text.fill', stylers: [{color: '#746855'}]},
           {
             featureType: 'administrative.locality',
             elementType: 'labels.text.fill',
             stylers: [{color: '#d59563'}]
           },
           {
             featureType: 'poi',
             elementType: 'labels.text.fill',
             stylers: [{color: '#d59563'}]
           },
           {
             featureType: 'poi.park',
             elementType: 'geometry',
             stylers: [{color: '#263c3f'}]
           },
           {
             featureType: 'poi.park',
             elementType: 'labels.text.fill',
             stylers: [{color: '#6b9a76'}]
           },
           {
             featureType: 'road',
             elementType: 'geometry',
             stylers: [{color: '#38414e'}]
           },
           {
             featureType: 'road',
             elementType: 'geometry.stroke',
             stylers: [{color: '#212a37'}]
           },
           {
             featureType: 'road',
             elementType: 'labels.text.fill',
             stylers: [{color: '#9ca5b3'}]
           },
           {
             featureType: 'road.highway',
             elementType: 'geometry',
             stylers: [{color: '#746855'}]
           },
           {
             featureType: 'road.highway',
             elementType: 'geometry.stroke',
             stylers: [{color: '#1f2835'}]
           },
           {
             featureType: 'road.highway',
             elementType: 'labels.text.fill',
             stylers: [{color: '#f3d19c'}]
           },
           {
             featureType: 'transit',
             elementType: 'geometry',
             stylers: [{color: '#2f3948'}]
           },
           {
             featureType: 'transit.station',
             elementType: 'labels.text.fill',
             stylers: [{color: '#d59563'}]
           },
           {
             featureType: 'water',
             elementType: 'geometry',
             stylers: [{color: '#17263c'}]
           },
           {
             featureType: 'water',
             elementType: 'labels.text.fill',
             stylers: [{color: '#515c6d'}]
           },
           {
             featureType: 'water',
             elementType: 'labels.text.stroke',
             stylers: [{color: '#17263c'}]
           }
         ]
    });

    cityCircle = new google.maps.Circle({
      map: map,
      center: {lat:0, lng:0},
      radius: 0
    });

    <?php if(isset($_GET['lat']) && !empty($_GET['lat']) && isset($_GET['lng']) && !empty($_GET['lng']) && isset($_GET['miles']) && !empty($_GET['miles'])): ?>
          search(<?php echo($_GET['lat']);?>,<?php echo($_GET['lng']);?>,null,<?php echo($_GET['miles']);?>);
    <?php elseif(isset($_GET['city']) && !empty($_GET['city'])): ?>
      $city="<?php echo($_GET['city']);?>";
      $miles="<?php echo($_GET['miles']);?>";
      $.get("https://maps.googleapis.com/maps/api/geocode/json?address="+$city+"&key="+$key, function(response) {
          $lat = response.results[0].geometry.location.lat;
          $lng = response.results[0].geometry.location.lng;
          search($lat,$lng,$city,$miles);
      });
    <?php else: ?>
      var infoWindow = new google.maps.InfoWindow;
      //Try HTML5 geolocation.
      if (navigator.geolocation) {
         navigator.geolocation.getCurrentPosition(function(position) {
           var pos = {
             lat: position.coords.latitude,
             lng: position.coords.longitude
           };

           infoWindow.setPosition(pos);
           infoWindow.setContent('Location found.');
           infoWindow.open(map);
           map.setCenter(pos);
           map.setZoom(14);
         }, function() {
          infoWindow.setPosition(map.getCenter());
          infoWindow.setContent('Error: Unable to find your current location.');
          infoWindow.open(map);
         });
      } else {
         // Browser doesn't support Geolocation
         infoWindow.setPosition(map.getCenter());
         infoWindow.setContent('Error: Your browser doesn\'t support geolocation.');
         infoWindow.open(map);
      }
    <?php endif; ?>

    google.maps.event.addListener(map, "click", function (event) {
        var $lat = parseFloat(event.latLng.lat());
        var $lng = parseFloat(event.latLng.lng());
        search($lat,$lng,null,1);
    });
  }

  $(document).on("submit", ".geocode", function(event) {
      event.preventDefault();
      var $city = $('#city').val();
      var $miles = parseInt($('#miles').val());
      $.get("https://maps.googleapis.com/maps/api/geocode/json?address="+$city+"&key="+$key, function(response) {
          $lat = response.results[0].geometry.location.lat;
          $lng = response.results[0].geometry.location.lng;
          search($lat,$lng,$city,$miles);
      });
  });

  function search($lat,$lng,$loc,$miles){
    //configure radius wrt meters
    if($miles==null || $miles=="" || isNaN($miles) || $miles<=1){
      $radius=1609;
      $miles=1;
    } else if (parseInt($miles)*1609>40000){
      $radius=40000;
      $miles=25;
    } else {
      $radius=parseInt($miles)*1609;
    }

    //set circle circle
    cityCircle.setMap(null);
    var newCityCircle = new google.maps.Circle({
      clickable:false,
      strokeColor: '#FF0000',
      strokeOpacity: 0.8,
      strokeWeight: 2,
      fillColor: '#FF0000',
      fillOpacity: 0.20,
      map: map,
      center: {lat:$lat, lng:$lng},
      radius: $radius
    });
    cityCircle=newCityCircle;
    map.fitBounds(cityCircle.getBounds());

    var myurl = "<?php echo(YELP_API_HOST);?><?php echo(YELP_SEARCH_PATH);?>?term=food&open_now=true&limit=30&radius="+$radius+"&latitude="+$lat+"&longitude="+$lng;

    $.ajax({
       url: myurl,
       headers: {
        'Authorization':'Bearer <?php echo(YELP_API_KEY);?>',
    },
       method: 'GET',
       dataType: 'json',
       success: function(data){
           var totalresults = data.total;
           $("#results").text("");
           if (totalresults > 0){
               if($("#mapstyle").attr("href")=="map.css"){
                   $("#mapstyle").attr("href","map2.css");
               }
               data.businesses.sort((a, b) => Number(a.distance) - Number(b.distance));
               if($loc==null || $loc==""){
                 $loc=data.businesses[0].location.city;
               }
               $.post("addToSearches.php",{lat:$lat,lng:$lng,city:$loc,miles:$miles},function(response){});
               $.each(data.businesses, function(i, item) {
                   var id = item.id;
                   var url = item.url;
                   if(item.image_url==""){var image = "assets/noimg.png";} else {var image = item.image_url;}
                   var name = item.name;
                   var rating = item.rating;
                   var reviewcount = item.review_count;
                   var address = item.location.address1;
                   var city = item.location.city;
                   var country = item.location.country;
                   var zipcode = item.location.zip_code;
                   var distance = (parseFloat(item.distance)*0.00062137).toFixed(2);
                   var categories="";
                   for(var i = 0; i < item.categories.length; i++){
                     if(i==item.categories.length-1){ categories+=item.categories[i].title;}
                     else { categories+=item.categories[i].title + ", ";}
                   }
                   <?php if(!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']): ?>
                     var bookmark = '';
                   <?php else: ?>
                     var bookmark = '<img src = "assets/not_bookmarked.png" style="cursor:pointer;" class="bookmarkImg">';
                     for(var i = 0; i < bookmarks.length; i++){
                       if(bookmarks[i]==id){
                         bookmark = '<img src = "assets/bookmarked.png" style="cursor:pointer;" class="bookmarkImg">';
                         break;
                       }
                     }
                   <?php endif; ?>
                   $('#results').append('<div class="card" value="'+id+'"><a href="'+url+'" class="card_url"><img class="card-img-top card_img" src="'+image+'"></a><div class="card-body"><h3 class="card-title card_name">'+name+'</h3><p class="card-text"><strong>Location: <br></strong><span class="card_address">'+address+', '+zipcode+'</span><br><span class="card_region">'+city+', '+country+'</span><br><span class="card_distance">'+distance+' miles from your search</span></p><p class="card-text"><strong>Food categories:<br></strong><span class="card_categories">'+categories+'</span></p><p class="card-text"><strong>Rating:<br></strong><span class="card_rating">'+rating+'/5 with '+reviewcount+' reviews.</span></p></div>'+bookmark+'</div>');
               });
           } else {
               $('#results').append('<h5 style="margin:auto;">No results found!</h5>');
           }
       }
    });
  }

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
        bookmarks.push($id);
        $bookmarkImg.attr("src","assets/bookmarked.png");
      });
    } else {
      $.post('removeFromBookmarks.php',{id:$id},function(response){;
        for(var i = bookmarks.length; i>-1; i--){
          if (bookmarks[i] == $id) bookmarks.splice(i, 1);
        }
        $bookmarkImg.attr("src","assets/not_bookmarked.png");
      });
    }
  });

</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBPrAFv9uzeTWCSpY48V-RAjA429nTfclo&callback=initMap"
async defer></script>

</body>
</html>
