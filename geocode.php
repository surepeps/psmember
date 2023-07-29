<?php


function dump(...$data)
{
  echo "<pre>";
  print_r($data);
}



/**
 * Author: CodexWorld
 * Author URI: http://www.codexworld.com
 * Function Name: getLatLong()
 * $address => Full address.
 * Return => Latitude and longitude of the given address.
 **/
function getLatLong($address)
{
  if (!empty($address)) {
    //Formatted address
    $formattedAddr = str_replace(' ', '+', $address);
    //Google Map API URL
    $API_KEY = "AIzaSyCaKk9lOoS2QXFpxovEoX3lxWwT-poxTz0"; // Google Map Free API Key
    
    $url = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . $formattedAddr . '&sensor=false&key=' . $API_KEY;
    //Send request and receive json data by address
    $geocodeFromAddr = file_get_contents($url);
    $output = json_decode($geocodeFromAddr);
    //Get latitude and longitute from json data
    $data['latitude']  = $output->results[0]->geometry->location->lat;
    $data['longitude'] = $output->results[0]->geometry->location->lng;
    //Return latitude and longitude of the given address
    if (!empty($data)) {
      return $data;
    } else {
      return false;
    }
  } else {
    return false;
  }
}



$address = 'White House, Pennsylvania Avenue Northwest, Washington, DC, United States';

$latLong = getLatLong($address);
$latitude = $latLong['latitude'] ? $latLong['latitude'] : 'Not found';
$longitude = $latLong['longitude'] ? $latLong['longitude'] : 'Not found';


dump($latitude, $longitude);
exit;
