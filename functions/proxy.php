<?php

/*
  Simple Curl proxy for grabbing the Google maps JSON through this IP,
  since Google is not supporting JSONP and jQuery needs that callback

  See for more info: http://blog.mikecouturier.com/2009/11/jsonp-and-google-maps-api-geocoder-not.html

  Query URI: proxy.php?street=mehringdamm&number=55&area=kreuzberg&postal_code=10999&city=berlin&country=de
  Returns JSON: {"lat":52.491221,"lon":13.38742}

  Author: Roel van der Ven

*/

// What's the address we need to reverse geocode?
// Get the variables from the query:
$street = $_GET['street'];
$number = $_GET['number'];
$city = $_GET['city'];
$area = $_GET['area'];
$postal = $_GET['postal_code'];
$countrycode = $_GET['country'];

$address = urlencode($street.' '.$number.', '.$area.', '.$postal.', '.$city.', '.$countrycode);
$API = 'http://maps.google.com/maps/api/geocode/json?address='.$address.'&sensor=false';

function get_url($url) {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  $content = curl_exec($ch);
  curl_close($ch);
  return $content;
};

$variables = json_decode(get_url($API));

$latitude = $variables->results[0]->geometry->location->lat;
$longitude = $variables->results[0]->geometry->location->lng;
$location = array('lat' => $latitude, 'lon' => $longitude);

header('Content-type: application/json');
print json_encode($location);

?>