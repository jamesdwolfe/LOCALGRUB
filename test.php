<?php
require 'config.php';

function request($host, $path, $url_params = array()) {
    // Send Yelp API Call
    try {
        $curl = curl_init();
        if (FALSE === $curl)
            throw new Exception('Failed to initialize');
        $url = $host . $path . "?" . http_build_query($url_params);
        curl_setopt_array($curl, array(
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "authorization: Bearer " . YELP_API_KEY,
                "cache-control: no-cache",
            ),
        ));
        $response = curl_exec($curl);
        if (FALSE === $response)
            throw new Exception(curl_error($curl), curl_errno($curl));
        $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if (200 != $http_status)
            throw new Exception($response, $http_status);
        curl_close($curl);
    } catch(Exception $e) {
        echo "<hr>" . $e->getMessage() . "<hr>";
    }
    return $response;
}

function search($term, $latitude, $longitude, $radius, $limit) {
    $url_params = array();
    $url_params['term'] = $term;
    //$url_params['location'] = 'cityname';
    $url_params['latitude'] = $latitude;
    $url_params['longitude'] = $longitude;
    $url_params['radius'] = $radius;
    $url_params['limit'] = $limit;
    //$url_params['open_now'] = true;
    return request(YELP_API_HOST, YELP_SEARCH_PATH, $url_params);
}

$response = search("food",34.021063,-118.286841,ceil(1609.34),50);
$response = json_decode($response);
foreach($response->businesses as $b){
  var_dump($b->name);
  var_dump($b->review_count);
  var_dump($b->rating);
  var_dump($b->location->address1);
  var_dump($b->coordinates->latitude);
  var_dump($b->coordinates->longitude);
  var_dump($b->image_url);
  var_dump($b->url);
  foreach($b->categories as $c){
    var_dump($c->title);
  }
  var_dump(round($b->distance*0.00062137,2));
  echo "<hr>";

  usort($response->businesses, function ($a, $b) {
    return ($b->rating + ($b->review_count/50)) <=> ($a->rating + ($a->review_count/50));
  });
}

?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
    <title></title>
    <style media="screen">
      .card-img-top {
        width: 100%;
        height: 20rem;
        object-fit: cover;
        border-radius:23px 23px 0px 0px;
        border-bottom: 2px solid red;
      }
      .card {
        width:25rem;
        border:2px solid red;
        border-radius:25px;
        margin: 5px auto;
      }
      .parent{
        max-width:1250px;
        display:flex;
        flex-wrap: wrap;
        margin: 0 auto;
      }
      .card .btn {
        background-color:red;
      }
    </style>
  </head>
  <body>

    <div class="parent">
    <?php foreach($response->businesses as $b) : ?>
      <div class="card">
        <?php if(!empty($b->image_url)) : ?>
        <a href="<?php echo $b->url;?>"><img class="card-img-top" src="<?php echo $b->image_url;?>"></a>
        <?php else: ?>
        <a href="<?php echo $b->url;?>"><img class="card-img-top" src="assets/noimg.png"></a>
        <?php endif; ?>
        <div class="card-body">
          <h3 class="card-title"><?php echo $b->name .":";?></h3>
          <p class="card-text"><strong>Location:<br></strong> <?php echo $b->location->address1 . ", " . $b->location->zip_code . "<br>" . $b->location->city . ", " . $b->location->country . "<br>" . round($b->distance*0.00062137,2) . " miles from your search"; ?></p>
          <p class="card-text"><strong>Food categories:<br></strong>
          <?php $numItems = count($b->categories); $i = 0;
          foreach($b->categories as $c){
          if(++$i===$numItems){ echo $c->title;}
          else { echo $c->title . ", ";}} ?>
          </p>
          <p class="card-text"><strong>Rating:<br></strong> <?php echo $b->rating; ?>/5 with <?php echo $b->review_count; ?> reviews.</p>
        </div>
      </div>
    <?php endforeach; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
  </body>
</html>
